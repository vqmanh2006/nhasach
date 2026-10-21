<?php
declare(strict_types=1);

final class CategoryController extends Controller
{
    private Category $categories;

    public function __construct()
    {
        $this->categories = new Category();
    }

    private function requireLogin(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    private function input(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 150) {
            throw new InvalidArgumentException('Tên danh mục không được để trống và không dài quá 150 ký tự.');
        }

        return [
            'name' => $name,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
        ];
    }

    public function index(): void
    {
        $this->requireLogin();
        $search = trim((string) ($_GET['search'] ?? ''));
        $this->view('categories/index', [
            'categories' => $this->categories->all($search),
            'search' => $search,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->view('categories/create', [
            'category' => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        try {
            $data = $this->input();
            $this->categories->create($data['name'], $data['description']);
            $_SESSION['flash'] = 'Đã thêm danh mục thành công.';
            $this->redirect('/categories');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException
                ? $exception->getMessage()
                : 'Không thể thêm danh mục. Tên danh mục có thể đã tồn tại.';
            $this->view('categories/create', [
                'category' => $_POST,
                'error' => $error,
                'csrfToken' => $this->csrfToken(),
            ]);
        }
    }

    public function edit(): void
    {
        $this->requireLogin();
        $category = $this->categories->find((int) ($_GET['id'] ?? 0));
        if (!$category) {
            $this->redirect('/categories');
        }
        $this->view('categories/edit', [
            'category' => $category,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $data = $this->input();
            $this->categories->update($id, $data['name'], $data['description']);
            $_SESSION['flash'] = 'Đã cập nhật danh mục thành công.';
            $this->redirect('/categories');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException
                ? $exception->getMessage()
                : 'Không thể cập nhật danh mục. Tên danh mục có thể đã tồn tại.';
            $this->view('categories/edit', [
                'category' => array_merge($_POST, ['id' => $id]),
                'error' => $error,
                'csrfToken' => $this->csrfToken(),
            ]);
        }
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        try {
            $this->categories->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Đã xóa danh mục thành công.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Không thể xóa danh mục đang được sử dụng bởi sách.';
        }
        $this->redirect('/categories');
    }
}
