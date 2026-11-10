<?php
declare(strict_types=1);

final class User extends Model
{
    private function accountQuery(): string
    {
        return "(SELECT id_nhan_vien AS id, ho_ten AS name, dia_chi_email AS email,
                        mat_khau AS password, CASE vai_tro WHEN 'quan_tri' THEN 'admin' ELSE 'staff' END AS role,
                        so_dien_thoai AS phone, NULL AS address, dang_hoat_dong AS is_active,
                        'staff' AS account_type
                 FROM nhan_vien
                 UNION ALL
                 SELECT id_khach_hang AS id, ho_ten AS name, dia_chi_email AS email,
                        mat_khau AS password, 'customer' AS role,
                        so_dien_thoai AS phone, dia_chi AS address, dang_hoat_dong AS is_active,
                        'customer' AS account_type
                 FROM khach_hang)";
    }

    public function create(array $data): int
    {
        if ($data['role'] === 'customer') {
            $statement = $this->db->prepare(
                'INSERT INTO khach_hang (ho_ten, dia_chi_email, so_dien_thoai, mat_khau, dia_chi, dang_hoat_dong)
                 VALUES (:name, :email, :phone, :password, :address, 1)'
            );
        } else {
            $role = $data['role'] === 'admin' ? 'quan_tri' : 'nhan_vien';
            $statement = $this->db->prepare(
                'INSERT INTO nhan_vien (ho_ten, dia_chi_email, so_dien_thoai, mat_khau, vai_tro, dang_hoat_dong)
                 VALUES (:name, :email, :phone, :password, :role, 1)'
            );
            $data['role'] = $role;
        }
        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => $data['role'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM ' . $this->accountQuery() . ' AS tai_khoan WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        return $statement->fetch() ?: null;
    }

    public function findById(int $id, string $role = ''): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM ' . $this->accountQuery() . ' AS tai_khoan WHERE id = ? AND (:role = \'\' OR role = :role2) LIMIT 1');
        $statement->execute(['id' => $id, 'role' => $role, 'role2' => $role]);
        return $statement->fetch() ?: null;
    }

    public function updateProfile(int $id, array $data, string $role = 'customer'): bool
    {
        if ($role === 'customer') {
            $sql = 'UPDATE khach_hang SET ho_ten = :name, so_dien_thoai = :phone, dia_chi = :address';
            if (!empty($data['password'])) {
                $sql .= ', mat_khau = :password';
            }
            $sql .= ' WHERE id_khach_hang = :id';
        } else {
            $sql = 'UPDATE nhan_vien SET ho_ten = :name, so_dien_thoai = :phone';
            if (!empty($data['password'])) {
                $sql .= ', mat_khau = :password';
            }
            $sql .= ' WHERE id_nhan_vien = :id';
        }
        $params = ['id' => $id, 'name' => $data['name'], 'phone' => $data['phone'] ?? null, 'address' => $data['address'] ?? null];
        if (!empty($data['password'])) {
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->db->prepare($sql)->execute($params);
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, name, email, phone, role, address, is_active, account_type FROM ' . $this->accountQuery() . ' AS tai_khoan ORDER BY id DESC'
        )->fetchAll();
    }
}
