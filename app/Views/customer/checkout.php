<?php
$title = 'Thông tin giao hàng';
$user = $user ?? $_SESSION['user'];
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
        <a href="<?= app_url("/") ?>customer/cart">Quay lại giỏ hàng</a>
        <a href="<?= app_url("/") ?>logout">Đăng xuất</a>
    </div>
</header>
<main class="customer-content">
    <section class="customer-section checkout-layout">
        <div>
            <h1>Thông tin giao hàng</h1>
            <p class="muted">Vui lòng nhập thông tin để chúng tôi giao sách đến đúng nơi.</p>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>
            <form method="post" action="<?= app_url("/") ?>customer/order" class="form-grid checkout-form">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <label>Họ và tên người nhận
                    <input type="text" name="name" value="<?= htmlspecialchars((string) ($user['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Số điện thoại
                    <input type="tel" name="phone" value="<?= htmlspecialchars((string) ($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Địa chỉ giao hàng cụ thể
                    <textarea name="delivery_address" rows="4" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required><?= htmlspecialchars((string) ($user['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </label>
                <button class="btn btn-primary" type="submit">Xác nhận đặt hàng</button>
            </form>
        </div>
        <aside class="checkout-summary">
            <h2>Đơn hàng</h2>
            <?php foreach ($items as $item): ?>
                <div class="checkout-item">
                    <span><?= htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8') ?> × <?= (int) $item['quantity'] ?></span>
                    <strong><?= number_format((float) $item['subtotal'], 0, ',', '.') ?> đ</strong>
                </div>
            <?php endforeach; ?>
            <div class="checkout-total"><span>Tổng cộng</span><strong><?= number_format($total, 0, ',', '.') ?> đ</strong></div>
        </aside>
    </section>
</main>
<footer class="customer-footer">© <?= date('Y') ?> <?= APP_NAME ?></footer>
</body>
</html>
