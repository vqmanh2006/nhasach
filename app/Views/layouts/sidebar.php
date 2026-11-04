<?php
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
$isActive = static function (string $path) use ($currentPath): string {
    return ($path === '/' ? $currentPath === '/' : str_starts_with($currentPath, $path)) ? ' active' : '';
};
?><aside class="sidebar"><nav><a class="nav-link<?= $isActive('/') ?>" href="/"><span>📊</span> Tổng quan</a><a class="nav-link<?= $isActive('/books') ?>" href="/books"><span>📚</span> Quản lý sách</a><a class="nav-link<?= $isActive('/categories') ?>" href="/categories"><span>🏷️</span> Danh mục</a><a class="nav-link<?= $isActive('/customers') ?>" href="/customers"><span>👥</span> Khách hàng</a><a class="nav-link<?= $isActive('/sales') ?>" href="/sales"><span>🧾</span> Bán hàng</a><a class="nav-link" href="/logout"><span>🚪</span> Đăng xuất</a></nav></aside>
