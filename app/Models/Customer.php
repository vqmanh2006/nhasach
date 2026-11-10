<?php
declare(strict_types=1);

final class Customer extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT id_khach_hang AS id, ho_ten AS name, so_dien_thoai AS phone,
                       dia_chi_email AS email, dia_chi AS address, NULL AS notes, ngay_tao AS created_at
                FROM khach_hang';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE ho_ten LIKE :search OR so_dien_thoai LIKE :search OR dia_chi_email LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY ngay_tao DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id_khach_hang AS id, ho_ten AS name, so_dien_thoai AS phone, dia_chi_email AS email, dia_chi AS address, NULL AS notes FROM khach_hang WHERE id_khach_hang = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare('INSERT INTO khach_hang (ho_ten, so_dien_thoai, dia_chi_email, dia_chi, dang_hoat_dong) VALUES (:name, :phone, :email, :address, 1)');
        $statement->execute(['name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'], 'address' => $data['address']]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->prepare('UPDATE khach_hang SET ho_ten = :name, so_dien_thoai = :phone, dia_chi_email = :email, dia_chi = :address WHERE id_khach_hang = :id')
            ->execute(['id' => $id, 'name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'], 'address' => $data['address']]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM khach_hang WHERE id_khach_hang = ?')->execute([$id]);
    }
}
