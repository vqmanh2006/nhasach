<?php
$title = 'Quản lý khách hàng';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
$flash = $_SESSION['flash'] ?? null;
$errorFlash = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);
?>
<main class="main-content">
    <div class="page-heading">
        <div>
            <h1>Quản lý khách hàng</h1>
            <p class="muted">Danh sách khách hàng và thông tin liên hệ.</p>
        </div>
        <a class="btn btn-primary" href="<?= app_url("/") ?>customers/create">+ Thêm khách hàng</a>
    </div>
    <?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($errorFlash): ?><div class="alert alert-danger"><?= htmlspecialchars($errorFlash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="content-card">
        <form class="search-bar" method="get" action="<?= app_url("/") ?>customers">
            <input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm theo tên, email, số điện thoại...">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars((string) ($customer['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars((string) ($customer['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($customer['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($customer['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($customer['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="row-actions">
                                    <a class="btn btn-light" href="<?= app_url("/") ?>customers/edit?id=<?= (int) $customer['id'] ?>">Sửa</a>
                                    <form method="post" action="<?= app_url("/") ?>customers/delete" onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?');">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">
                                        <button class="btn btn-danger" type="submit">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$customers): ?><tr><td colspan="6" class="empty-cell">Chưa có khách hàng phù hợp.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
