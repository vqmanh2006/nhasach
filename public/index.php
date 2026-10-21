<?php
declare(strict_types=1);

session_name('nhasach_session');
session_start();

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/app/Models/Book.php';
require_once BASE_PATH . '/app/Controllers/BookController.php';

$router = new Router();

$router->get('/', static function (): void {
    $stats = ['books' => 0, 'categories' => 0, 'customers' => 0, 'sales' => 0];
    try {
        $db = Database::connection();
        $stats['books'] = (int) $db->query('SELECT COUNT(*) FROM books')->fetchColumn();
        $stats['categories'] = (int) $db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        $stats['customers'] = (int) $db->query('SELECT COUNT(*) FROM customers')->fetchColumn();
        $stats['sales'] = (int) $db->query('SELECT COUNT(*) FROM sales')->fetchColumn();
    } catch (Throwable $exception) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra cấu hình trong config/database.php.';
    }
    require BASE_PATH . '/app/Views/dashboard/index.php';
});

$router->get('/login', static function (): void {
    require BASE_PATH . '/app/Views/auth/login.php';
});

$router->post('/login', static function (): void {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $error = 'Email hoặc mật khẩu không chính xác.';
    try {
        $statement = Database::connection()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $user = $statement->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
            header('Location: /');
            exit;
        }
    } catch (Throwable $exception) {
        $error = 'Không thể đăng nhập lúc này. Vui lòng kiểm tra cơ sở dữ liệu.';
    }
    require BASE_PATH . '/app/Views/auth/login.php';
});

$router->get('/logout', static function (): void {
    $_SESSION = [];
    session_destroy();
    header('Location: /login');
    exit;
});

$router->get('/books', [BookController::class, 'index']);
$router->get('/books/create', [BookController::class, 'create']);
$router->post('/books', [BookController::class, 'store']);
$router->get('/books/edit', [BookController::class, 'edit']);
$router->post('/books/update', [BookController::class, 'update']);
$router->post('/books/delete', [BookController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
