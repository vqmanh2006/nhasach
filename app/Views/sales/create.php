<?php
$title = 'Tạo đơn hàng';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="page-heading">
        <div>
            <h1>Tạo đơn hàng</h1>
            <p class="muted">Nhập thông tin khách hàng và sản phẩm bán ra.</p>
        </div>
        <a href="<?= app_url("/") ?>sales">← Danh sách đơn hàng</a>
    </div>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php require BASE_PATH . '/app/Views/sales/_form.php'; ?>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
