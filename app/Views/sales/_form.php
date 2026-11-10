<?php $editing = false; ?><form method="post" action="<?= app_url("/") ?>sales" class="content-card form-grid">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <div class="form-columns">
        <label>Khách hàng *
            <select name="customer_id" required>
                <option value="">-- Chọn khách hàng --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= (int) $customer['id'] ?>"><?= htmlspecialchars((string) $customer['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Trạng thái
            <select name="status">
                <option value="completed">Hoàn thành</option>
                <option value="cancelled">Hủy</option>
            </select>
        </label>
    </div>

    <div class="content-card">
        <h3>Sản phẩm</h3>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="book_id[]" value="<?= (int) $book['id'] ?>">
                                <?= htmlspecialchars((string) $book['title'], ENT_QUOTES, 'UTF-8') ?> (<?= number_format((float) $book['price'], 0, ',', '.') ?>₫)
                            </label>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" min="1" value="1" style="width: 100px;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div>
        <button class="btn btn-primary" type="submit">Tạo đơn hàng</button>
        <a class="btn btn-light" href="<?= app_url("/") ?>sales">Hủy</a>
    </div>
</form>
