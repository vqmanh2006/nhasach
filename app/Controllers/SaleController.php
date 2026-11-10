<?php
declare(strict_types=1);

final class SaleController extends Controller
{
    private Sale $sales;

    public function __construct()
    {
        $this->sales = new Sale();
    }

    private function input(): array
    {
        $customerId = (int) ($_POST['customer_id'] ?? 0);
        if ($customerId <= 0) {
            throw new InvalidArgumentException('Vui lòng chọn khách hàng.');
        }

        $bookIds = $_POST['book_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        if (!is_array($bookIds) || !is_array($quantities) || $bookIds === [] || $quantities === []) {
            throw new InvalidArgumentException('Vui lòng chọn ít nhất một sản phẩm để bán.');
        }

        $books = $this->sales->books();
        $bookMap = [];
        foreach ($books as $book) {
            $bookMap[(int) $book['id']] = (float) $book['price'];
        }

        $items = [];
        foreach ($bookIds as $index => $bookId) {
            $bookId = (int) $bookId;
            $quantity = (int) ($quantities[$index] ?? 0);
            if ($bookId <= 0 || $quantity <= 0) {
                continue;
            }

            if (!isset($bookMap[$bookId])) {
                throw new InvalidArgumentException('Có một sản phẩm không hợp lệ trong đơn hàng.');
            }

            $items[] = [
                'book_id' => $bookId,
                'quantity' => $quantity,
                'unit_price' => $bookMap[$bookId],
            ];
        }

        if ($items === []) {
            throw new InvalidArgumentException('Vui lòng chọn ít nhất một sản phẩm hợp lệ.');
        }

        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item['unit_price'] * (int) $item['quantity'];
        }

        return [
            'customer_id' => $customerId,
            'user_id' => (int) ($_SESSION['user']['id'] ?? 0),
            'status' => $_POST['status'] ?? 'hoan_thanh',
            'total_amount' => $total,
            'items' => $items,
        ];
    }

    public function index(): void
    {
        $this->requireRole('admin', 'staff');
        $search = trim((string) ($_GET['search'] ?? ''));
        $this->view('sales/index', [
            'sales' => $this->sales->all($search),
            'search' => $search,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin', 'staff');
        $this->view('sales/create', [
            'customers' => $this->sales->customers(),
            'books' => $this->sales->books(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        try {
            $data = $this->input();
            $this->sales->create($data);
            $_SESSION['flash'] = 'Đã tạo đơn bán hàng thành công.';
            $this->redirect('/sales');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Không thể tạo đơn bán hàng. Vui lòng kiểm tra tồn kho.';
            $this->view('sales/create', [
                'customers' => $this->sales->customers(),
                'books' => $this->sales->books(),
                'error' => $error,
                'csrfToken' => $this->csrfToken(),
            ]);
        }
    }

    public function delete(): void
    {
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        try {
            $this->sales->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Đã hủy đơn bán hàng thành công.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Không thể hủy đơn hàng này.';
        }
        $this->redirect('/sales');
    }
}
