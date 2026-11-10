<?php
declare(strict_types=1);

final class User extends Model
{
    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, phone, address) VALUES (:name, :email, :password, :role, :phone, :address)'
        );
        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        return $statement->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $fields = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ];

        $sql = 'UPDATE users SET name = :name, phone = :phone, address = :address';
        $params = [
            'id' => $id,
            'name' => $fields['name'],
            'phone' => $fields['phone'],
            'address' => $fields['address'],
        ];

        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = :id';
        $statement = $this->db->prepare($sql);
        return $statement->execute($params);
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, name, email, phone, role, address, status, created_at FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }
}
