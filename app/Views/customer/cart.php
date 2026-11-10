<?php
$title = 'Giỏ hàng';
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $title ?> - <?= APP_NAME ?></title><link rel="stylesheet" href="/css/style.css"></head>
<body class="customer-page">
<header class="customer-topbar"><a class="topbar-brand" href="/customer">Nhà sách <span>trí tuệ</span></a><div class="customer-user"><strong><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?></strong><a href="/customer">Tiếp tục mua sách</a><a href="/logout">Đăng xuất</a></div></header>
<main class="customer-content"><section class="customer-section"><div class="customer-section-heading"><div><h1>Giỏ hàng của bạn</h1><p class="muted">Kiểm tra sản phẩm trước khi đặt hàng.</p></div></div>
<?php if (!$items): ?><p class="customer-empty">Giỏ hàng đang trống. <a href="/customer">Khám phá sách</a></p><?php else: ?>
<form method="post" action="/customer/cart/update"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><div class="table-wrap"><table><thead><tr><th>Sách</th><th>Đơn giá</th><th>Số lượng</th><th>Tạm tính</th><th></th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr><td><strong><?= htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) $item['author'], ENT_QUOTES, 'UTF-8') ?></small></td><td><?= number_format((float) $item['price'], 0, ',', '.') ?> đ</td><td><input class="cart-quantity" type="number" name="quantity[<?= (int) $item['id'] ?>]" min="1" max="<?= (int) $item['stock_quantity'] ?>" value="<?= (int) $item['quantity'] ?>"></td><td><?= number_format((float) $item['subtotal'], 0, ',', '.') ?> đ</td><td><button class="link-danger" formaction="/customer/cart/remove" name="book_id" value="<?= (int) $item['id'] ?>">Xóa</button></td></tr><?php endforeach; ?></tbody></table></div><div class="cart-summary"><strong>Tổng cộng: <?= number_format($total, 0, ',', '.') ?> đ</strong><button class="btn btn-light" type="submit">Cập nhật giỏ</button><button class="btn btn-primary" formaction="/customer/order" type="submit">Đặt hàng</button></div></form>
<?php endif; ?></section></main><footer class="customer-footer">© <?= date('Y') ?> <?= APP_NAME ?></footer></body></html>
