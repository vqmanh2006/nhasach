<?php
declare(strict_types=1);

final class CustomerController extends Controller
{
    private Customer $customers;

    public function __construct()
    {
        $this->customers = new Customer();
    }

    private function input(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 150) {
            throw new InvalidArgumentException('Tên khách hàng không được để trống và không quá 150 ký tự.');
        }

        $email = trim((string) ($_POST['email'] ?? '')) ?: null;
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email khách hàng không hợp lệ.');
        }

        return [
            'name' => $name,
            'phone' => trim((string) ($_POST['phone'] ?? '')) ?: null,
            'email' => $email,
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'notes' => trim((string) ($_POST['notes'] ?? '')) ?: null,
        ];
    }

    public function index(): void
    {
        $this->requireRole('admin', 'staff');
        $search = trim((string) ($_GET['search'] ?? ''));
        $this->view('customers/index', [
            'customers' => $this->customers->all($search),
            'search' => $search,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin', 'staff');
        $this->view('customers/create', [
            'customer' => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        try {
            $data = $this->input();
            $this->customers->create($data);
            $_SESSION['flash'] = 'Đã thêm khách hàng thành công.';
            $this->redirect('/customers');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Không thể thêm khách hàng.';
            $this->view('customers/create', [
                'customer' => $_POST,
                'error' => $error,
                'csrfToken' => $this->csrfToken(),
            ]);
        }
    }

    public function edit(): void
    {
        $this->requireRole('admin', 'staff');
        $customer = $this->customers->find((int) ($_GET['id'] ?? 0));
        if (!$customer) {
            $this->redirect('/customers');
        }
        $this->view('customers/edit', [
            'customer' => $customer,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function update(): void
    {
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $this->customers->update($id, $this->input());
            $_SESSION['flash'] = 'Đã cập nhật khách hàng thành công.';
            $this->redirect('/customers');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Không thể cập nhật khách hàng.';
            $this->view('customers/edit', [
                'customer' => array_merge($_POST, ['id' => $id]),
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
            $this->customers->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Đã xóa khách hàng thành công.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Không thể xóa khách hàng này.';
        }
        $this->redirect('/customers');
    }
}
