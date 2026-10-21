<?php
$title = 'Dashboard';
$user = $_SESSION['user'] ?? null;
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="page-heading"><div><h1>Tổng quan</h1><p class="muted">Chào mừng <?= htmlspecialchars((string) ($user['name'] ?? 'bạn'), ENT_QUOTES, 'UTF-8') ?> đến với hệ thống.</p></div><a class="btn btn-primary" href="#">+ Tạo đơn hàng</a></div>
    <?php if (!empty($error)): ?><div class="alert alert-warning"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="stats-grid">
        <article class="stat-card"><span class="stat-icon">📚</span><div><span class="stat-label">Tổng số sách</span><strong><?= number_format((int) $stats['books']) ?></strong></div></article>
        <article class="stat-card"><span class="stat-icon">🏷️</span><div><span class="stat-label">Danh mục</span><strong><?= number_format((int) $stats['categories']) ?></strong></div></article>
        <article class="stat-card"><span class="stat-icon">👥</span><div><span class="stat-label">Khách hàng</span><strong><?= number_format((int) $stats['customers']) ?></strong></div></article>
        <article class="stat-card"><span class="stat-icon">🧾</span><div><span class="stat-label">Đơn hàng</span><strong><?= number_format((int) $stats['sales']) ?></strong></div></article>
    </section>
    <section class="content-card"><h2>Giới thiệu hệ thống</h2><p>Đây là trang quản trị nhà sách. Bạn có thể mở rộng các module quản lý sách, danh mục, khách hàng và bán hàng từ menu bên trái.</p><div class="quick-links"><a href="#">Quản lý sách <span>→</span></a><a href="#">Quản lý khách hàng <span>→</span></a><a href="#">Xem báo cáo <span>→</span></a></div></section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
