<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require BASE_PATH . '/app/Views/' . $template . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('Yêu cầu không hợp lệ. Vui lòng tải lại trang.');
        }
    }
}
