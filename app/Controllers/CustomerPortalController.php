<?php
declare(strict_types=1);

final class CustomerPortalController extends Controller
{
    private Book $books;
    private Sale $sales;

    public function __construct()
    {
        $this->books = new Book();
        $this->sales = new Sale();
    }

    private function requireCustomer(): void
    {
        $this->requireLogin();
    }

    private function cart(): array
    {
        return is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
    }

    public function index(): void
    {
        $this->requireCustomer();
        $this->view('customer/dashboard', [
            'books' => $this->books->all(trim((string) ($_GET['search'] ?? ''))),
            'search' => trim((string) ($_GET['search'] ?? '')),
            'orders' => $this->sales->forUser((int) $_SESSION['user']['id']),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function addToCart(): void
    {
        $this->requireCustomer();
        $this->verifyCsrf();
        $book = $this->books->find((int) ($_POST['book_id'] ?? 0));
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        if (!$book || !(int) $book['is_active'] || (int) $book['stock_quantity'] < $quantity) {
            $_SESSION['flash_error'] = 'Sách không tồn tại hoặc không đủ số lượng.';
        } else {
            $cart = $this->cart();
            $id = (string) $book['id'];
            $cart[$id] = min((int) ($cart[$id] ?? 0) + $quantity, (int) $book['stock_quantity']);
            $_SESSION['cart'] = $cart;
            $_SESSION['flash'] = 'Đã thêm sách vào giỏ hàng.';
        }
        $this->redirect('/customer');
    }

    public function showCart(): void
    {
        $this->requireCustomer();
        $items = [];
        $total = 0.0;
        foreach ($this->cart() as $id => $quantity) {
            $book = $this->books->find((int) $id);
            if (!$book || !(int) $book['is_active'] || (int) $book['stock_quantity'] < 1) {
                continue;
            }
            $quantity = min(max(1, (int) $quantity), (int) $book['stock_quantity']);
            $book['quantity'] = $quantity;
            $book['subtotal'] = $quantity * (float) $book['price'];
            $total += $book['subtotal'];
            $items[] = $book;
        }
        $this->view('customer/cart', ['items' => $items, 'total' => $total, 'csrfToken' => $this->csrfToken()]);
    }

    public function updateCart(): void
    {
        $this->requireCustomer();
        $this->verifyCsrf();
        $cart = [];
        foreach ((array) ($_POST['quantity'] ?? []) as $id => $quantity) {
            $book = $this->books->find((int) $id);
            $quantity = (int) $quantity;
            if ($book && $quantity > 0) {
                $cart[(string) $id] = min($quantity, (int) $book['stock_quantity']);
            }
        }
        $_SESSION['cart'] = array_filter($cart);
        $this->redirect('/customer/cart');
    }

    public function removeFromCart(): void
    {
        $this->requireCustomer();
        $this->verifyCsrf();
        $cart = $this->cart();
        unset($cart[(string) ($_POST['book_id'] ?? '')]);
        $_SESSION['cart'] = $cart;
        $this->redirect('/customer/cart');
    }

    public function checkout(): void
    {
        $this->requireCustomer();
        $this->verifyCsrf();
        $deliveryAddress = trim((string) ($_POST['delivery_address'] ?? ''));
        if ($deliveryAddress === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập địa chỉ giao hàng cụ thể.';
            $this->redirect('/customer/cart');
        }
        try {
            $this->sales->createCustomerOrder((int) $_SESSION['user']['id'], $_SESSION['user'], $this->cart(), $deliveryAddress);
            $_SESSION['cart'] = [];
            $_SESSION['flash'] = 'Đặt hàng thành công. Cảm ơn bạn đã mua sách.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = $exception instanceof InvalidArgumentException
                ? $exception->getMessage()
                : 'Không thể đặt hàng lúc này. Vui lòng kiểm tra lại tồn kho.';
        }
        $this->redirect('/customer');
    }
}
