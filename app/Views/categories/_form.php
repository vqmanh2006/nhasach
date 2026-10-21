<?php $editing = isset($category['id']); ?><form method="post" action="<?= $editing ? '/categories/update' : '/categories' ?>" class="content-card form-grid book-form">
<input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $category['id'] ?>"><?php endif; ?>
<label>Tên danh mục *<input name="name" maxlength="150" value="<?= htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
<label>Mô tả<textarea name="description" rows="5" maxlength="1000"><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
<div><button class="btn btn-primary" type="submit"><?= $editing ? 'Lưu thay đổi' : 'Thêm danh mục' ?></button> <a class="btn btn-light" href="/categories">Hủy</a></div>
</form>
