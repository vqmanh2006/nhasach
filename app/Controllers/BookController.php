<?php
declare(strict_types=1);

final class BookController extends Controller
{
    private const MAX_IMAGE_SIZE = 5242880;
    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private Book $books;

    public function __construct()
    {
        $this->books = new Book();
    }

    private function uploadCoverImage(?string $currentImage = null): ?string
    {
        $file = $_FILES['cover_image'] ?? null;
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentImage;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Không thể tải ảnh bìa lên.');
        }
        if ((int) $file['size'] > self::MAX_IMAGE_SIZE) {
            throw new InvalidArgumentException('Ảnh bìa không được vượt quá 5MB.');
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if (!isset(self::ALLOWED_IMAGE_TYPES[$mimeType]) || @getimagesize((string) $file['tmp_name']) === false) {
            throw new InvalidArgumentException('Ảnh bìa phải là JPG, PNG hoặc WebP.');
        }

        $uploadDirectory = PUBLIC_PATH . '/uploads/books';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh bìa.');
        }
        $filename = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_IMAGE_TYPES[$mimeType];
        if (!move_uploaded_file((string) $file['tmp_name'], $uploadDirectory . '/' . $filename)) {
            throw new RuntimeException('Không thể lưu ảnh bìa.');
        }

        if ($currentImage !== null && is_file(PUBLIC_PATH . '/' . ltrim($currentImage, '/'))) {
            @unlink(PUBLIC_PATH . '/' . ltrim($currentImage, '/'));
        }
        return 'uploads/books/' . $filename;
    }

    private function input(?string $currentImage = null): array
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
            'cover_image' => $this->uploadCoverImage($currentImage),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    public function index(): void
    {
        $this->requireRole('admin', 'staff');
        $search = trim((string) ($_GET['search'] ?? ''));
        $this->view('books/index', [
            'books' => $this->books->all($search),
            'search' => $search,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin', 'staff');
        $this->view('books/create', [
            'categories' => $this->books->categories(),
            'book' => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'staff');
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
        $this->requireRole('admin', 'staff');
        $book = $this->books->find((int) ($_GET['id'] ?? 0));
        if (!$book) {
            $this->redirect('/books');
        }
        $this->view('books/edit', ['categories' => $this->books->categories(), 'book' => $book, 'csrfToken' => $this->csrfToken()]);
    }

    public function update(): void
    {
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $book = $this->books->find($id);
            $this->books->update($id, $this->input($book['cover_image'] ?? null));
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
        $this->requireRole('admin', 'staff');
        $this->verifyCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $book = $this->books->find($id);
            $this->books->delete($id);
            if (!empty($book['cover_image']) && is_file(PUBLIC_PATH . '/' . ltrim($book['cover_image'], '/'))) {
                @unlink(PUBLIC_PATH . '/' . ltrim($book['cover_image'], '/'));
            }
            $_SESSION['flash'] = 'Đã xóa sách thành công.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Không thể xóa sách đã phát sinh trong hóa đơn.';
        }
        $this->redirect('/books');
    }
}
