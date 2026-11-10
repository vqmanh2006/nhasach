<?php
declare(strict_types=1);

final class Sale extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT s.*, c.name AS customer_name, u.name AS user_name
                FROM sales s
                LEFT JOIN customers c ON c.id = s.customer_id
                LEFT JOIN users u ON u.id = s.user_id';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE c.name LIKE :search OR u.name LIKE :search OR s.status LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY s.created_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sale = $this->db->prepare(
            'SELECT s.*, c.name AS customer_name, u.name AS user_name
             FROM sales s
             LEFT JOIN customers c ON c.id = s.customer_id
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.id = ? LIMIT 1'
        );
        $sale->execute([$id]);
        $result = $sale->fetch();
        if (!$result) {
            return null;
        }

        $items = $this->db->prepare(
            'SELECT si.*, b.title AS book_title, b.price AS book_price
             FROM sale_items si
             LEFT JOIN books b ON b.id = si.book_id
             WHERE si.sale_id = ?
             ORDER BY si.id ASC'
        );
        $items->execute([$id]);
        $result['items'] = $items->fetchAll();
        return $result;
    }

    public function customers(): array
    {
        return $this->db->query('SELECT id, name FROM customers ORDER BY name ASC')->fetchAll();
    }

    public function books(): array
    {
        return $this->db->query('SELECT id, title, price, stock_quantity FROM books WHERE is_active = 1 ORDER BY title ASC')->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT id, total_amount, status, created_at FROM sales WHERE user_id = ? ORDER BY created_at DESC'
        );
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    public function createCustomerOrder(int $userId, array $user, array $cart, string $deliveryAddress): int
    {
        $customer = $this->db->prepare('SELECT id FROM customers WHERE email = ? LIMIT 1');
        $customer->execute([(string) $user['email']]);
        $customerId = (int) ($customer->fetchColumn() ?: 0);
        if ($customerId === 0) {
            $insertCustomer = $this->db->prepare(
                'INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)'
            );
            $insertCustomer->execute([$user['name'], $user['phone'] ?? null, $user['email'], $deliveryAddress]);
            $customerId = (int) $this->db->lastInsertId();
        } else {
            $updateCustomer = $this->db->prepare('UPDATE customers SET address = ? WHERE id = ?');
            $updateCustomer->execute([$deliveryAddress, $customerId]);
        }

        $bookIds = array_map('intval', array_keys($cart));
        if ($bookIds === []) {
            throw new InvalidArgumentException('Giỏ hàng đang trống.');
        }
        $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
        $statement = $this->db->prepare(
            "SELECT id, price, stock_quantity, is_active FROM books WHERE id IN ($placeholders)"
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
            if (!isset($bookMap[$bookId]) || !(int) $bookMap[$bookId]['is_active'] || $quantity < 1 || $quantity > (int) $bookMap[$bookId]['stock_quantity']) {
                throw new InvalidArgumentException('Một sản phẩm trong giỏ hàng không đủ tồn kho.');
            }
            $items[] = ['book_id' => $bookId, 'quantity' => $quantity, 'unit_price' => (float) $bookMap[$bookId]['price']];
        }
        $total = array_sum(array_map(static fn (array $item): float => $item['quantity'] * $item['unit_price'], $items));
        return $this->create([
            'customer_id' => $customerId,
            'user_id' => $userId,
            'total_amount' => $total,
            'status' => 'completed',
            'delivery_address' => $deliveryAddress,
            'items' => $items,
        ]);
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $sale = $this->db->prepare(
                'INSERT INTO sales (customer_id, user_id, total_amount, status, delivery_address)
                 VALUES (:customer_id, :user_id, :total_amount, :status, :delivery_address)'
            );
            $sale->execute([
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'total_amount' => $data['total_amount'],
                'status' => $data['status'],
                'delivery_address' => $data['delivery_address'] ?? null,
            ]);
            $saleId = (int) $this->db->lastInsertId();

            foreach ($data['items'] as $item) {
                $bookId = (int) ($item['book_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                if ($bookId <= 0 || $quantity <= 0) {
                    throw new InvalidArgumentException('Chi tiết hóa đơn không hợp lệ.');
                }

                $price = (float) ($item['unit_price'] ?? 0);
                $insertItem = $this->db->prepare(
                    'INSERT INTO sale_items (sale_id, book_id, quantity, unit_price)
                     VALUES (:sale_id, :book_id, :quantity, :unit_price)'
                );
                $insertItem->execute([
                    'sale_id' => $saleId,
                    'book_id' => $bookId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                ]);

                $stockUpdate = $this->db->prepare(
                    'UPDATE books
                     SET stock_quantity = stock_quantity - :deduct_quantity
                     WHERE id = :book_id AND stock_quantity >= :required_quantity'
                );
                $stockUpdate->execute([
                    'deduct_quantity' => $quantity,
                    'book_id' => $bookId,
                    'required_quantity' => $quantity,
                ]);

                if ($stockUpdate->rowCount() !== 1) {
                    throw new RuntimeException('Sách không đủ tồn kho để bán.');
                }
            }

            $this->db->commit();
            return $saleId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $items = $this->db->prepare('SELECT book_id, quantity FROM sale_items WHERE sale_id = ?');
            $items->execute([$id]);
            $itemRows = $items->fetchAll();

            foreach ($itemRows as $item) {
                $stockRestore = $this->db->prepare(
                    'UPDATE books SET stock_quantity = stock_quantity + :quantity WHERE id = :book_id'
                );
                $stockRestore->execute([
                    'quantity' => (int) $item['quantity'],
                    'book_id' => (int) $item['book_id'],
                ]);
            }

            $deleteItems = $this->db->prepare('DELETE FROM sale_items WHERE sale_id = ?');
            $deleteItems->execute([$id]);

            $deleteSale = $this->db->prepare('DELETE FROM sales WHERE id = ?');
            $deleteSale->execute([$id]);

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
