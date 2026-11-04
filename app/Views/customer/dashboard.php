<?php
$title = 'Trang khách hàng';
$user = $_SESSION['user'];
$search = trim((string) ($_GET['search'] ?? ''));
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="customer-page">
<header class="customer-topbar">
    <a class="topbar-brand" href="/customer">Nhà sách <span>trí tuệ</span></a>
    <div class="customer-user">
        Xin chào, <strong><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?></strong>
        <a href="/profile">Tài khoản</a>
        <a href="/logout">Đăng xuất</a>
    </div>
</header>
<main class="customer-content">
    <section class="customer-hero">
        <div>
            <p class="customer-eyebrow">KHÔNG GIAN ĐỌC SÁCH</p>
            <h1>Chào mừng bạn đến với Nhà sách trí tuệ</h1>
            <p>Khám phá những cuốn sách phù hợp cho hành trình học tập và phát triển của bạn.</p>
        </div>
        <span class="customer-hero-icon">📚</span>
    </section>
    <section class="customer-section">
        <div class="customer-section-heading">
            <div>
                <h2>Sách đang có tại nhà sách</h2>
                <p class="muted">Tìm kiếm theo tên sách, tác giả hoặc ISBN.</p>
            </div>
            <form class="customer-search" method="get" action="/customer">
                <input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm sách...">
                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            </form>
        </div>
        <div class="customer-book-grid">
            <?php foreach ($books as $book): ?>
                <?php if (!(int) $book['is_active']) continue; ?>
                <article class="customer-book-card">
                    <div class="customer-book-cover">📖</div>
                    <div class="customer-book-body">
                        <span class="customer-book-category"><?= htmlspecialchars((string) ($book['category_name'] ?? 'Sách hay'), ENT_QUOTES, 'UTF-8') ?></span>
                        <h3><?= htmlspecialchars((string) $book['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars((string) $book['author'], ENT_QUOTES, 'UTF-8') ?></p>
                        <strong><?= number_format((float) $book['price'], 0, ',', '.') ?> đ</strong>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$books): ?>
                <p class="customer-empty">Không tìm thấy sách phù hợp.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<footer class="customer-footer">© <?= date('Y') ?> <?= APP_NAME ?></footer>
</body>
</html>
