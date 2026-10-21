<?php
$title = 'Quản lý sách';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
$flash = $_SESSION['flash'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);
?>
<main class="main-content">
    <div class="page-heading"><div><h1>Quản lý sách</h1><p class="muted">Danh sách và thông tin tồn kho.</p></div><a class="btn btn-primary" href="/books/create">+ Thêm sách</a></div>
    <?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="content-card">
        <form class="search-bar" method="get" action="/books"><input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm theo tên sách, ISBN, tác giả..."><button class="btn btn-primary" type="submit">Tìm kiếm</button></form>
        <div class="table-wrap"><table><thead><tr><th>Sách</th><th>ISBN</th><th>Danh mục</th><th>Giá</th><th>Tồn kho</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
        <?php foreach ($books as $book): ?><tr><td><strong><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></small></td><td><?= htmlspecialchars((string) ($book['isbn'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) ($book['category_name'] ?? 'Chưa phân loại'), ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((float) $book['price'], 0, ',', '.') ?> đ</td><td><?= number_format((int) $book['stock_quantity']) ?></td><td><span class="badge <?= $book['is_active'] ? 'badge-success' : 'badge-muted' ?>"><?= $book['is_active'] ? 'Đang bán' : 'Ẩn' ?></span></td><td class="actions"><a href="/books/edit?id=<?= (int) $book['id'] ?>">Sửa</a><form method="post" action="/books/delete" onsubmit="return confirm('Bạn có chắc muốn xóa sách này?')"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($this->csrfToken(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int) $book['id'] ?>"><button type="submit" class="link-danger">Xóa</button></form></td></tr><?php endforeach; ?>
        <?php if (!$books): ?><tr><td colspan="7" class="empty-cell">Chưa có sách phù hợp.</td></tr><?php endif; ?></tbody></table></div>
    </section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
