<?php
$title = 'Đăng nhập';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="auth-page">
<main class="auth-card">
    <div class="brand-mark">NS</div>
    <h1>Đăng nhập hệ thống</h1>
    <p class="muted">Quản lý nhà sách trên nền web</p>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="/login" class="form-grid">
        <label>Email<input type="email" name="email" value="<?= htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required autofocus></label>
        <label>Mật khẩu<input type="password" name="password" required></label>
        <button class="btn btn-primary" type="submit">Đăng nhập</button>
    </form>
    <p class="demo-note">Demo: admin@nhasach.local / password</p>
</main>
</body>
</html>
