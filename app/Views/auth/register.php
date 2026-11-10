<?php
$title = 'Đăng ký';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= app_url("/") ?>css/style.css">
</head>
<body class="auth-page">
<main class="auth-card">
    <div class="brand-mark">NS</div>
    <h1>Tạo tài khoản khách hàng</h1>
    <p class="muted">Đăng ký để sử dụng dịch vụ của Nhà sách trí tuệ.</p>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="<?= app_url("/") ?>register" class="form-grid">
        <label>Họ và tên<input type="text" name="name" value="<?= htmlspecialchars((string) ($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required autofocus></label>
        <label>Email<input type="email" name="email" value="<?= htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
        <label>Số điện thoại<input type="text" name="phone" value="<?= htmlspecialchars((string) ($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
        <label>Địa chỉ<input type="text" name="address" value="<?= htmlspecialchars((string) ($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
        <label>Mật khẩu<input type="password" name="password" required minlength="6"></label>
        <label>Xác nhận mật khẩu<input type="password" name="confirm_password" required minlength="6"></label>
        <button class="btn btn-primary" type="submit">Đăng ký</button>
    </form>
    <p class="demo-note">Đã có tài khoản? <a href="<?= app_url("/") ?>login">Đăng nhập</a></p>
</main>
</body>
</html>
