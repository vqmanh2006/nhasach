<?php $editing = isset($customer['id']); ?><form method="post" action="<?= $editing ? '/customers/update' : '/customers' ?>" class="content-card form-grid">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $customer['id'] ?>"><?php endif; ?>
    <div class="form-columns">
        <label>Tên khách hàng *<input name="name" value="<?= htmlspecialchars((string) ($customer['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
        <label>Số điện thoại<input name="phone" value="<?= htmlspecialchars((string) ($customer['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
        <label>Email<input type="email" name="email" value="<?= htmlspecialchars((string) ($customer['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
        <label>Địa chỉ<input name="address" value="<?= htmlspecialchars((string) ($customer['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
        <label>Ghi chú<textarea name="notes" rows="5"><?= htmlspecialchars((string) ($customer['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
    </div>
    <div>
        <button class="btn btn-primary" type="submit"><?= $editing ? 'Lưu thay đổi' : 'Thêm khách hàng' ?></button>
        <a class="btn btn-light" href="<?= app_url("/") ?>customers">Hủy</a>
    </div>
</form>
