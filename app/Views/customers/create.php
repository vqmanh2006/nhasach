<?php
$customer = $customer ?? [];
$title = 'Thêm khách hàng';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="page-heading">
        <div>
            <h1>Thêm khách hàng</h1>
            <p class="muted">Tạo thông tin khách hàng mới.</p>
        </div>
        <a href="<?= app_url("/") ?>customers">← Danh sách khách hàng</a>
    </div>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php require BASE_PATH . '/app/Views/customers/_form.php'; ?>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
