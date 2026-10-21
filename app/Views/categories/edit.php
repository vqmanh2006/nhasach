<?php
$title = 'Sửa danh mục';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content"><div class="page-heading"><div><h1>Sửa danh mục</h1><p class="muted">Cập nhật thông tin danh mục.</p></div><a href="/categories">← Danh sách danh mục</a></div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php require BASE_PATH . '/app/Views/categories/_form.php'; ?></main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
