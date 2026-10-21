<?php
$title = 'Quản lý danh mục';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
$flash = $_SESSION['flash'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);
?>
<main class="main-content">
    <div class="page-heading"><div><h1>Quản lý danh mục</h1><p class="muted">Phân loại sách trong nhà sách.</p></div><a class="btn btn-primary" href="/categories/create">+ Thêm danh mục</a></div>
    <?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="content-card">
        <form class="search-bar" method="get" action="/categories"><input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm theo tên hoặc mô tả..."><button class="btn btn-primary" type="submit">Tìm kiếm</button></form>
        <div class="table-wrap"><table><thead><tr><th>Tên danh mục</th><th>Mô tả</th><th>Số sách</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead><tbody>
        <?php foreach ($categories as $category): ?><tr><td><strong><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></strong></td><td><?= htmlspecialchars((string) ($category['description'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((int) $category['book_count']) ?></td><td><?= htmlspecialchars((string) $category['created_at'], ENT_QUOTES, 'UTF-8') ?></td><td class="actions"><a href="/categories/edit?id=<?= (int) $category['id'] ?>">Sửa</a><form method="post" action="/categories/delete" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int) $category['id'] ?>"><button type="submit" class="link-danger">Xóa</button></form></td></tr><?php endforeach; ?>
        <?php if (!$categories): ?><tr><td colspan="5" class="empty-cell">Chưa có danh mục phù hợp.</td></tr><?php endif; ?></tbody></table></div>
    </section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
