<?php
declare(strict_types=1);

const APP_NAME = 'Nhà sách trí tuệ';
const BASE_PATH = __DIR__ . '/..';
const PUBLIC_PATH = BASE_PATH . '/public';
const SESSION_NAME = 'nhasach_session';

function app_url(string $path = '/'): string
{
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $baseUrl = rtrim($scriptDirectory === '/' || $scriptDirectory === '.' ? '' : $scriptDirectory, '/');
    $path = '/' . ltrim($path, '/');

    return $baseUrl . $path;
}
