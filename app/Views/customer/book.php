<?php
$title = (string) $book['title'];
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= app_url("/") ?>css/style.css">
</head>
<body class="customer-page">
<header class="customer-topbar">
    <a class="topbar-brand" href="<?= app_url("/") ?>customer">Nhà sách <span>trí tuệ</span></a>
    <div class="customer-user">
        <strong><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?></strong>
        <a href="<?= app_url("/") ?>customer/cart">Giỏ hàng</a>
        <a href="<?= app_url("/") ?>logout">Đăng xuất</a>
    </div>
</header>
<main class="customer-content">
    <a class="book-back-link" href="<?= app_url("/") ?>customer">← Quay lại danh sách sách</a>
    <section class="customer-section book-detail">
        <div class="book-detail-cover">
            <?php if (!empty($book['cover_image'])): ?>
                <img src="<?= app_url('/' . ltrim((string) $book['cover_image'], '/')) ?>" alt="Ảnh bìa <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>📖<?php endif; ?>
        </div>
        <div class="book-detail-info">
            <span class="customer-book-category"><?= htmlspecialchars((string) ($book['category_name'] ?? 'Sách hay'), ENT_QUOTES, 'UTF-8') ?></span>
            <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="book-detail-author">Tác giả: <?= htmlspecialchars((string) $book['author'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="book-detail-meta">Nhà xuất bản: <?= htmlspecialchars((string) ($book['publisher'] ?? 'Chưa cập nhật'), ENT_QUOTES, 'UTF-8') ?></p>
            <strong class="book-detail-price"><?= number_format((float) $book['price'], 0, ',', '.') ?> đ</strong>
            <h2>Giới thiệu nội dung</h2>
            <p class="book-description"><?= nl2br(htmlspecialchars((string) ($book['description'] ?? 'Chưa có mô tả cho sách này.'), ENT_QUOTES, 'UTF-8')) ?></p>
            <?php if ((int) $book['stock_quantity'] > 0): ?>
                <form method="post" action="<?= app_url("/") ?>customer/cart/add" class="customer-add-form">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                    <input type="number" name="quantity" min="1" max="<?= (int) $book['stock_quantity'] ?>" value="1">
                    <button class="btn btn-primary" type="submit">Thêm vào giỏ hàng</button>
                </form>
            <?php else: ?><span class="customer-out-stock">Hết hàng</span><?php endif; ?>
        </div>
    </section>
</main>
<footer class="customer-footer">© <?= date('Y') ?> <?= APP_NAME ?></footer>
</body>
</html>
