<?php
$title = 'Trang khách hàng';
$user = $_SESSION['user'];
$search = trim((string) ($_GET['search'] ?? ''));
$flash = $_SESSION['flash'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);
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
        Xin chào, <strong><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?></strong>
        <a href="<?= app_url("/") ?>profile">Tài khoản</a>
        <a href="<?= app_url("/") ?>customer/cart">Giỏ hàng (<?= count($_SESSION['cart'] ?? []) ?>)</a>
        <a href="<?= app_url("/") ?>logout">Đăng xuất</a>
    </div>
</header>
<main class="customer-content">
    <?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($flashError): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
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
            <form class="customer-search" method="get" action="<?= app_url("/") ?>customer">
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
                        <?php if ((int) $book['stock_quantity'] > 0): ?>
                            <form method="post" action="<?= app_url("/") ?>customer/cart/add" class="customer-add-form">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                <input type="number" name="quantity" min="1" max="<?= (int) $book['stock_quantity'] ?>" value="1">
                                <button class="btn btn-primary" type="submit">Thêm vào giỏ</button>
                            </form>
                        <?php else: ?><span class="customer-out-stock">Hết hàng</span><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$books): ?>
                <p class="customer-empty">Không tìm thấy sách phù hợp.</p>
            <?php endif; ?>
        </div>
    </section>
    <section class="customer-section">
        <div class="customer-section-heading"><div><h2>Đơn hàng của tôi</h2><p class="muted">Lịch sử mua hàng gần đây.</p></div></div>
        <?php if ($orders): ?><div class="customer-orders"><?php foreach ($orders as $order): ?><div><strong>#<?= (int) $order['id'] ?></strong><span><?= number_format((float) $order['total_amount'], 0, ',', '.') ?> đ</span><span><?= htmlspecialchars((string) $order['status'], ENT_QUOTES, 'UTF-8') ?></span><small><?= htmlspecialchars((string) $order['created_at'], ENT_QUOTES, 'UTF-8') ?></small></div><?php endforeach; ?></div><?php else: ?><p class="muted">Bạn chưa có đơn hàng nào.</p><?php endif; ?>
    </section>
</main>
<footer class="customer-footer">© <?= date('Y') ?> <?= APP_NAME ?></footer>
</body>
</html>
