<?php
$title = 'Bán hàng';
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/layouts/sidebar.php';
$flash = $_SESSION['flash'] ?? null;
$errorFlash = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);
?>
<main class="main-content">
    <div class="page-heading">
        <div>
            <h1>Quản lý bán hàng</h1>
            <p class="muted">Theo dõi đơn hàng và doanh thu của cửa hàng.</p>
        </div>
        <a class="btn btn-primary" href="<?= app_url("/") ?>sales/create">+ Tạo đơn hàng</a>
    </div>
    <?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($errorFlash): ?><div class="alert alert-danger"><?= htmlspecialchars($errorFlash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="content-card">
        <form class="search-bar" method="get" action="<?= app_url("/") ?>sales">
            <input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm theo khách hàng hoặc trạng thái...">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Người bán</th>
                        <th>Tổng tiền</th>
                        <th>Địa chỉ giao hàng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td>#<?= (int) $sale['id'] ?></td>
                            <td><?= htmlspecialchars((string) ($sale['customer_name'] ?? 'Khách vãng lai'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($sale['user_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((float) $sale['total_amount'], 0, ',', '.') ?>₫</td>
                            <td><?= htmlspecialchars((string) ($sale['delivery_address'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $sale['status'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($sale['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="post" action="<?= app_url("/") ?>sales/delete" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $sale['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Hủy</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$sales): ?><tr><td colspan="8" class="empty-cell">Chưa có đơn hàng nào.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
