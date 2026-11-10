<?php
declare(strict_types=1);

final class Sale extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT dh.id_don_hang AS id, dh.tong_thanh_toan AS total_amount,
                       dh.trang_thai AS status, dh.dia_chi_giao_hang AS delivery_address,
                       dh.ngay_dat AS created_at, kh.ho_ten AS customer_name,
                       nv.ho_ten AS user_name
                FROM don_hang dh
                LEFT JOIN khach_hang kh ON kh.id_khach_hang = dh.id_khach_hang
                LEFT JOIN nhan_vien nv ON nv.id_nhan_vien = dh.id_nhan_vien';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE kh.ho_ten LIKE :search OR nv.ho_ten LIKE :search OR dh.trang_thai LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY dh.ngay_dat DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT dh.id_don_hang AS id, dh.tong_thanh_toan AS total_amount,
                    dh.trang_thai AS status, dh.dia_chi_giao_hang AS delivery_address,
                    kh.ho_ten AS customer_name, nv.ho_ten AS user_name
             FROM don_hang dh
             LEFT JOIN khach_hang kh ON kh.id_khach_hang = dh.id_khach_hang
             LEFT JOIN nhan_vien nv ON nv.id_nhan_vien = dh.id_nhan_vien
             WHERE dh.id_don_hang = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $result = $statement->fetch();
        if (!$result) {
            return null;
        }
        $items = $this->db->prepare(
            'SELECT ct.*, ct.ten_san_pham_luc_dat AS book_title, ct.don_gia AS book_price
             FROM chi_tiet_don_hang ct WHERE ct.id_don_hang = ? ORDER BY ct.id_chi_tiet_don_hang'
        );
        $items->execute([$id]);
        $result['items'] = $items->fetchAll();
        return $result;
    }

    public function customers(): array
    {
        return $this->db->query('SELECT id_khach_hang AS id, ho_ten AS name FROM khach_hang ORDER BY ho_ten')->fetchAll();
    }

    public function books(): array
    {
        return $this->db->query(
            "SELECT id_san_pham AS id, ten_san_pham AS title, gia_ban AS price, so_luong_ton AS stock_quantity
             FROM san_pham WHERE trang_thai <> 'ngung_kinh_doanh' ORDER BY ten_san_pham"
        )->fetchAll();
    }

    public function forUser(int $customerId): array
    {
        $statement = $this->db->prepare(
            'SELECT id_don_hang AS id, tong_thanh_toan AS total_amount, trang_thai AS status, ngay_dat AS created_at
             FROM don_hang WHERE id_khach_hang = ? ORDER BY ngay_dat DESC'
        );
        $statement->execute([$customerId]);
        return $statement->fetchAll();
    }

    public function createCustomerOrder(int $customerId, array $user, array $cart, string $deliveryAddress): int
    {
        $customer = $this->db->prepare('SELECT id_khach_hang FROM khach_hang WHERE dia_chi_email = ? LIMIT 1');
        $customer->execute([(string) $user['email']]);
        $customerId = (int) ($customer->fetchColumn() ?: 0);
        if ($customerId === 0) {
            $insertCustomer = $this->db->prepare(
                'INSERT INTO khach_hang (ho_ten, dia_chi_email, so_dien_thoai, dia_chi, mat_khau, dang_hoat_dong)
                 VALUES (?, ?, ?, ?, NULL, 1)'
            );
            $insertCustomer->execute([$user['name'], $user['email'], $user['phone'] ?? null, $deliveryAddress]);
            $customerId = (int) $this->db->lastInsertId();
        } else {
            $this->db->prepare('UPDATE khach_hang SET dia_chi = ? WHERE id_khach_hang = ?')
                ->execute([$deliveryAddress, $customerId]);
        }
        $bookIds = array_map('intval', array_keys($cart));
        if ($bookIds === []) {
            throw new InvalidArgumentException('Giỏ hàng đang trống.');
        }
        $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
        $statement = $this->db->prepare(
            "SELECT id_san_pham AS id, ten_san_pham AS title, ma_san_pham AS product_code,
                    gia_ban AS price, so_luong_ton AS stock_quantity, trang_thai
             FROM san_pham WHERE id_san_pham IN ($placeholders)"
        );
        $statement->execute($bookIds);
        $bookMap = [];
        foreach ($statement->fetchAll() as $book) {
            $bookMap[(int) $book['id']] = $book;
        }
        $items = [];
        foreach ($cart as $bookId => $quantity) {
            $bookId = (int) $bookId;
            $quantity = (int) $quantity;
            if (!isset($bookMap[$bookId]) || $bookMap[$bookId]['trang_thai'] === 'ngung_kinh_doanh' || $quantity < 1 || $quantity > (int) $bookMap[$bookId]['stock_quantity']) {
                throw new InvalidArgumentException('Một sản phẩm trong giỏ hàng không đủ tồn kho.');
            }
            $items[] = ['book_id' => $bookId, 'title' => $bookMap[$bookId]['title'], 'product_code' => $bookMap[$bookId]['product_code'], 'quantity' => $quantity, 'unit_price' => (float) $bookMap[$bookId]['price']];
        }
        $total = array_sum(array_map(static fn (array $item): float => $item['quantity'] * $item['unit_price'], $items));
        return $this->create([
            'customer_id' => $customerId,
            'user_id' => null,
            'total_amount' => $total,
            'status' => 'cho_xac_nhan',
            'delivery_address' => $deliveryAddress,
            'items' => $items,
        ]);
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $order = $this->db->prepare(
                "INSERT INTO don_hang
                 (ma_don_hang, id_khach_hang, id_nhan_vien, kenh_ban, hinh_thuc_nhan_hang,
                  trang_thai, phuong_thuc_thanh_toan, dia_chi_giao_hang, tong_tien_hang, tong_thanh_toan)
                 VALUES (:code, :customer_id, :staff_id, 'truc_tuyen', 'giao_hang',
                         :status, 'tien_mat', :address, :total, :total)"
            );
            $order->execute([
                'code' => 'DH-' . date('YmdHis') . '-' . random_int(100, 999),
                'customer_id' => $data['customer_id'] ?? null,
                'staff_id' => $data['user_id'] ?? null,
                'status' => $this->normalizeStatus((string) ($data['status'] ?? 'cho_xac_nhan')),
                'address' => $data['delivery_address'] ?? null,
                'total' => $data['total_amount'],
            ]);
            $orderId = (int) $this->db->lastInsertId();
            foreach ($data['items'] as $item) {
                $productId = (int) $item['book_id'];
                $quantity = (int) $item['quantity'];
                $insertItem = $this->db->prepare(
                    'INSERT INTO chi_tiet_don_hang
                     (id_don_hang, id_san_pham, ten_san_pham_luc_dat, ma_san_pham_luc_dat, so_luong, don_gia, thanh_tien)
                     VALUES (:order_id, :product_id, :title, :product_code, :quantity, :price, :subtotal)'
                );
                $insertItem->execute([
                    'order_id' => $orderId, 'product_id' => $productId,
                    'title' => $item['title'] ?? 'Sản phẩm', 'product_code' => $item['product_code'] ?? ('SP-' . $productId),
                    'quantity' => $quantity, 'price' => $item['unit_price'],
                    'subtotal' => $quantity * (float) $item['unit_price'],
                ]);
                $stock = $this->db->prepare(
                    "UPDATE san_pham SET so_luong_ton = so_luong_ton - :deduct_quantity,
                     trang_thai = IF(so_luong_ton - :status_quantity <= 0, 'het_hang', trang_thai)
                     WHERE id_san_pham = :product_id AND so_luong_ton >= :required_quantity"
                );
                $stock->execute([
                    'deduct_quantity' => $quantity,
                    'status_quantity' => $quantity,
                    'required_quantity' => $quantity,
                    'product_id' => $productId,
                ]);
                if ($stock->rowCount() !== 1) {
                    throw new RuntimeException('Sản phẩm không đủ tồn kho để bán.');
                }

            }
            $this->db->commit();
            return $orderId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    private function normalizeStatus(string $status): string
    {
        return match ($status) {
            'completed' => 'hoan_thanh',
            'cancelled' => 'da_huy',
            default => in_array($status, ['cho_xac_nhan', 'da_xac_nhan', 'dang_dong_goi', 'dang_giao', 'cho_den_nhan', 'hoan_thanh', 'da_huy', 'yeu_cau_tra_hang', 'da_tra_hang'], true)
                ? $status
                : 'cho_xac_nhan',
        };
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $items = $this->db->prepare('SELECT id_san_pham AS book_id, so_luong AS quantity FROM chi_tiet_don_hang WHERE id_don_hang = ?');
            $items->execute([$id]);
            foreach ($items->fetchAll() as $item) {
                $this->db->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton + :quantity, trang_thai = IF(trang_thai = \'het_hang\', \'con_hang\', trang_thai) WHERE id_san_pham = :product_id')
                    ->execute(['quantity' => (int) $item['quantity'], 'product_id' => (int) $item['book_id']]);
            }
            $this->db->prepare('DELETE FROM don_hang WHERE id_don_hang = ?')->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }
}
