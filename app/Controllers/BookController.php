<?php
declare(strict_types=1);

final class BookController extends Controller
{
    private Book $books;

    public function __construct()
    {
        $this->books = new Book();
    }

    private function requireLogin(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    private function input(): array
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $author = trim((string) ($_POST['author'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock_quantity'] ?? 0);
        if ($title === '' || $author === '' || $price < 0 || $stock < 0) {
            throw new InvalidArgumentException('Vui lòng nhập tên sách, tác giả, giá và tồn kho hợp lệ.');
        }
        return [
            'category_id' => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
            'title' => $title,
            'isbn' => trim((string) ($_POST['isbn'] ?? '')) ?: null,
            'author' => $author,
            'publisher' => trim((string) ($_POST['publisher'] ?? '')) ?: null,
            'price' => $price,
            'stock_quantity' => $stock,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    public function index(): void
    {
        $this->requireLogin();
        $search = trim((string) ($_GET['search'] ?? ''));
        $this->view('books/index', [
            'books' => $this->books->all($search),
            'search' => $search,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->view('books/create', [
            'categories' => $this->books->categories(),
            'book' => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        try {
            $this->books->create($this->input());
            $_SESSION['flash'] = 'Đã thêm sách thành công.';
            $this->redirect('/books');
        } catch (Throwable $exception) {
            $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Không thể thêm sách. ISBN có thể đã tồn tại.';
            $this->view('books/create', ['categories' => $this->books->categories(), 'book' => $_POST, 'error' => $error, 'csrfToken' => $this->csrfToken()]);
        }
    }

    public function edit(): void
    {
        $this->requireLogin();
        $book = $this->books->find((int) ($_GET['id'] ?? 0));
        if (!$book) {
            $this->redirect('/books');
        }
        $this->view('books/edit', ['categories' => $this->books->categories(), 'book' => $book, 'csrfToken' => $this->csrfToken()]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $this->books->update($id, $this->input());
            $_SESSION['flash'] = 'Đã cập nhật sách thành công.';
            $this->redirect('/books');
        } catch (Throwable $exception) {
            $book = array_merge($_POST, ['id' => $id]);
            $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Không thể cập nhật sách. ISBN có thể đã tồn tại.';
            $this->view('books/edit', ['categories' => $this->books->categories(), 'book' => $book, 'error' => $error, 'csrfToken' => $this->csrfToken()]);
        }
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();
        try {
            $this->books->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Đã xóa sách thành công.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Không thể xóa sách đã phát sinh trong hóa đơn.';
        }
        $this->redirect('/books');
    }
}
