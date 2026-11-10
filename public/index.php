<?php
require_once __DIR__ . '/../config/constants.php';

session_name(SESSION_NAME);
session_start();

require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/app/Models/Book.php';
require_once BASE_PATH . '/app/Controllers/BookController.php';
require_once BASE_PATH . '/app/Models/User.php';
require_once BASE_PATH . '/app/Controllers/AuthController.php';
require_once BASE_PATH . '/app/Controllers/CustomerPortalController.php';
require_once BASE_PATH . '/app/Models/Category.php';
require_once BASE_PATH . '/app/Controllers/CategoryController.php';
require_once BASE_PATH . '/app/Models/Customer.php';
require_once BASE_PATH . '/app/Controllers/CustomerController.php';
require_once BASE_PATH . '/app/Models/Sale.php';
require_once BASE_PATH . '/app/Controllers/SaleController.php';

$router = new Router();

$router->get('/', static function (): void {
    if (($_SESSION['user']['role'] ?? '') === 'customer') {
        (new CustomerPortalController())->index();
        return;
    }
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

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/profile', [AuthController::class, 'profile']);
$router->post('/profile', [AuthController::class, 'updateProfile']);
$router->get('/customer', [CustomerPortalController::class, 'index']);
$router->post('/customer/cart/add', [CustomerPortalController::class, 'addToCart']);
$router->get('/customer/cart', [CustomerPortalController::class, 'showCart']);
$router->post('/customer/cart/update', [CustomerPortalController::class, 'updateCart']);
$router->post('/customer/cart/remove', [CustomerPortalController::class, 'removeFromCart']);
$router->post('/customer/order', [CustomerPortalController::class, 'checkout']);

$router->get('/books', [BookController::class, 'index']);
$router->get('/books/create', [BookController::class, 'create']);
$router->post('/books', [BookController::class, 'store']);
$router->get('/books/edit', [BookController::class, 'edit']);
$router->post('/books/update', [BookController::class, 'update']);
$router->post('/books/delete', [BookController::class, 'delete']);

$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories', [CategoryController::class, 'store']);
$router->get('/categories/edit', [CategoryController::class, 'edit']);
$router->post('/categories/update', [CategoryController::class, 'update']);
$router->post('/categories/delete', [CategoryController::class, 'delete']);

$router->get('/customers', [CustomerController::class, 'index']);
$router->get('/customers/create', [CustomerController::class, 'create']);
$router->post('/customers', [CustomerController::class, 'store']);
$router->get('/customers/edit', [CustomerController::class, 'edit']);
$router->post('/customers/update', [CustomerController::class, 'update']);
$router->post('/customers/delete', [CustomerController::class, 'delete']);

$router->get('/sales', [SaleController::class, 'index']);
$router->get('/sales/create', [SaleController::class, 'create']);
$router->post('/sales', [SaleController::class, 'store']);
$router->post('/sales/delete', [SaleController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
