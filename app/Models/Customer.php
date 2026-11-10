<?php
declare(strict_types=1);

final class Customer extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM customers';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE name LIKE :search OR phone LIKE :search OR email LIKE :search OR address LIKE :search OR notes LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY created_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM customers WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO customers (name, phone, email, address, notes)
             VALUES (:name, :phone, :email, :address, :notes)'
        );
        $statement->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE customers SET name = :name, phone = :phone, email = :email, address = :address, notes = :notes WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM customers WHERE id = ?');
        return $statement->execute([$id]);
    }
}
