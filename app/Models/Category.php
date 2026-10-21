<?php
declare(strict_types=1);

final class Category extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT c.*, COUNT(b.id) AS book_count
                FROM categories c
                LEFT JOIN books b ON b.category_id = c.id';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE c.name LIKE :search OR c.description LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' GROUP BY c.id ORDER BY c.name ASC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(string $name, ?string $description): int
    {
        $statement = $this->db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $statement->execute(['name' => $name, 'description' => $description]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, ?string $description): bool
    {
        $statement = $this->db->prepare('UPDATE categories SET name = :name, description = :description WHERE id = :id');
        return $statement->execute(['id' => $id, 'name' => $name, 'description' => $description]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        return $statement->execute([$id]);
    }
}
