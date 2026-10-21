<?php
declare(strict_types=1);

final class Book extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT b.*, c.name AS category_name FROM books b LEFT JOIN categories c ON c.id = b.category_id';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE b.title LIKE :search OR b.isbn LIKE :search OR b.author LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY b.created_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM books WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function categories(): array
    {
        return $this->db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO books (category_id, title, isbn, author, publisher, price, stock_quantity, description, is_active)
             VALUES (:category_id, :title, :isbn, :author, :publisher, :price, :stock_quantity, :description, :is_active)'
        );
        $statement->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE books SET category_id = :category_id, title = :title, isbn = :isbn, author = :author,
             publisher = :publisher, price = :price, stock_quantity = :stock_quantity,
             description = :description, is_active = :is_active WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM books WHERE id = ?');
        return $statement->execute([$id]);
    }
}
