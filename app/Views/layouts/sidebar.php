<?php
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($baseUrl !== '' && $baseUrl !== '/' && str_starts_with($currentPath, $baseUrl)) {
    $currentPath = substr($currentPath, strlen($baseUrl)) ?: '/';
}
$isActive = static function (string $path) use ($currentPath): string {
    return ($path === '/' ? $currentPath === '/' : str_starts_with($currentPath, $path)) ? ' active' : '';
};
?><aside class="sidebar"><nav><a class="nav-link<?= $isActive('/') ?>" href="<?= app_url("/") ?>"><span>📊</span> Tổng quan</a><a class="nav-link<?= $isActive('/books') ?>" href="<?= app_url("/") ?>books"><span>📚</span> Quản lý sách</a><a class="nav-link<?= $isActive('/categories') ?>" href="<?= app_url("/") ?>categories"><span>🏷️</span> Danh mục</a><a class="nav-link<?= $isActive('/customers') ?>" href="<?= app_url("/") ?>customers"><span>👥</span> Khách hàng</a><a class="nav-link<?= $isActive('/sales') ?>" href="<?= app_url("/") ?>sales"><span>🧾</span> Bán hàng</a><a class="nav-link" href="<?= app_url("/") ?>logout"><span>🚪</span> Đăng xuất</a></nav></aside>
