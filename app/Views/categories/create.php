<?php
$title = 'Thêm danh mục';
$category = $category ?? [];
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content"><div class="page-heading"><div><h1>Thêm danh mục</h1><p class="muted">Tạo danh mục mới cho sách.</p></div><a href="/categories">← Danh sách danh mục</a></div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php require BASE_PATH . '/app/Views/categories/_form.php'; ?></main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
