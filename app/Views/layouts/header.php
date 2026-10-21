<?php $title = $title ?? APP_NAME; ?><!doctype html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - <?= APP_NAME ?></title><link rel="stylesheet" href="/css/style.css"></head><body>
<header class="topbar"><a class="topbar-brand" href="/">Nhà sách <span>Pro</span></a><div class="topbar-user"><?= htmlspecialchars((string) ($_SESSION['user']['name'] ?? 'Khách'), ENT_QUOTES, 'UTF-8') ?> <a href="/logout">Đăng xuất</a></div></header>
