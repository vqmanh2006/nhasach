<?php
$title = 'Thông tin cá nhân';
$userSession = $_SESSION['user'] ?? null;
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="page-heading">
        <div>
            <h1>Thông tin cá nhân</h1>
            <p class="muted">Quản lý hồ sơ và mật khẩu tài khoản của bạn.</p>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <section class="content-card">
        <form method="post" action="<?= app_url("/") ?>profile" class="form-grid">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <label>Họ và tên<input type="text" name="name" value="<?= htmlspecialchars((string) ($user['name'] ?? $userSession['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Email<input type="email" value="<?= htmlspecialchars((string) ($user['email'] ?? $userSession['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled></label>
            <label>Số điện thoại<input type="text" name="phone" value="<?= htmlspecialchars((string) ($user['phone'] ?? $userSession['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Địa chỉ<input type="text" name="address" value="<?= htmlspecialchars((string) ($user['address'] ?? $userSession['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Mật khẩu mới (để trống nếu không đổi)<input type="password" name="password"></label>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
