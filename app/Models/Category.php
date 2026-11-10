<?php
declare(strict_types=1);

final class Category extends Model
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT dm.id_danh_muc AS id, dm.ten_danh_muc AS name, dm.mo_ta AS description,
                       COUNT(sp.id_san_pham) AS book_count
                FROM danh_muc dm
                LEFT JOIN san_pham sp ON sp.id_danh_muc = dm.id_danh_muc';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE dm.ten_danh_muc LIKE :search OR dm.mo_ta LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' GROUP BY dm.id_danh_muc ORDER BY dm.ten_danh_muc ASC';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id_danh_muc AS id, ten_danh_muc AS name, mo_ta AS description FROM danh_muc WHERE id_danh_muc = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(string $name, ?string $description): int
    {
        $statement = $this->db->prepare('INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES (:name, :description)');
        $statement->execute(['name' => $name, 'description' => $description]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, ?string $description): bool
    {
        return $this->db->prepare('UPDATE danh_muc SET ten_danh_muc = :name, mo_ta = :description WHERE id_danh_muc = :id')
            ->execute(['id' => $id, 'name' => $name, 'description' => $description]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM danh_muc WHERE id_danh_muc = ?')->execute([$id]);
    }
}
