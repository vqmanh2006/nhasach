<?php
declare(strict_types=1);

final class Book extends Model
{
    public function all(string $search = '', ?int $categoryId = null): array
    {
        $sql = "SELECT sp.id_san_pham AS id, sp.id_danh_muc AS category_id,
                       sp.ten_san_pham AS title, sp.ma_isbn AS isbn, sp.tac_gia AS author,
                       sp.nha_xuat_ban AS publisher, sp.gia_ban AS price, sp.so_luong_ton AS stock_quantity,
                       sp.duong_dan_hinh_anh AS cover_image, sp.mo_ta AS description,
                       IF(sp.trang_thai = 'ngung_kinh_doanh', 0, 1) AS is_active,
                       sp.ngay_tao AS created_at, dm.ten_danh_muc AS category_name
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON dm.id_danh_muc = sp.id_danh_muc";
        $conditions = [];
        $params = [];
        if ($search !== '') {
            $conditions[] = '(sp.ten_san_pham LIKE :search OR sp.ma_isbn LIKE :search OR sp.tac_gia LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }
        if ($categoryId !== null) {
            $conditions[] = 'sp.id_danh_muc = :category_id';
            $params['category_id'] = $categoryId;
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY sp.ngay_tao DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            "SELECT id_san_pham AS id, id_danh_muc AS category_id, ten_san_pham AS title,
                    ma_isbn AS isbn, tac_gia AS author, nha_xuat_ban AS publisher,
                    gia_ban AS price, so_luong_ton AS stock_quantity,
                    duong_dan_hinh_anh AS cover_image, mo_ta AS description,
                    IF(trang_thai = 'ngung_kinh_doanh', 0, 1) AS is_active
             FROM san_pham WHERE id_san_pham = ? LIMIT 1"
        );
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function categories(): array
    {
        return $this->db->query('SELECT id_danh_muc AS id, ten_danh_muc AS name FROM danh_muc ORDER BY ten_danh_muc')->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            "INSERT INTO san_pham
             (id_danh_muc, ma_san_pham, ten_san_pham, loai_san_pham, tac_gia, nha_xuat_ban, ma_isbn,
              mo_ta, duong_dan_hinh_anh, gia_ban, so_luong_ton, trang_thai)
             VALUES (:category_id, :product_code, :title, 'sach', :author, :publisher, :isbn,
                     :description, :cover_image, :price, :stock_quantity, :status)"
        );
        $statement->execute($this->parameters($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $params = $this->parameters($data);
        $params['id'] = $id;
        return $this->db->prepare(
            'UPDATE san_pham SET id_danh_muc = :category_id, ten_san_pham = :title, tac_gia = :author,
             nha_xuat_ban = :publisher, ma_isbn = :isbn, mo_ta = :description,
             duong_dan_hinh_anh = :cover_image, gia_ban = :price, so_luong_ton = :stock_quantity,
             trang_thai = :status WHERE id_san_pham = :id'
        )->execute($params);
    }

    private function parameters(array $data): array
    {
        if ((int) ($data['category_id'] ?? 0) <= 0) {
            throw new InvalidArgumentException('Vui lòng chọn thể loại sách.');
        }
        return [
            'category_id' => (int) $data['category_id'],
            'product_code' => $data['product_code'] ?? ('BOOK-' . strtoupper(bin2hex(random_bytes(5)))),
            'title' => $data['title'],
            'author' => $data['author'],
            'publisher' => $data['publisher'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'description' => $data['description'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'status' => !($data['is_active'] ?? 0) ? 'ngung_kinh_doanh' : ((int) $data['stock_quantity'] > 0 ? 'con_hang' : 'het_hang'),
        ];
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM san_pham WHERE id_san_pham = ?')->execute([$id]);
    }
}
