<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLogin(): void
    {
        require BASE_PATH . '/app/Views/auth/login.php';
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $error = 'Email hoặc mật khẩu không chính xác.';

        try {
            $user = $this->users->findByEmail($email);
            if ($user && password_verify($password, (string) $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => (string) $user['name'],
                    'email' => (string) $user['email'],
                    'role' => (string) $user['role'],
                    'phone' => $user['phone'] ?? null,
                    'address' => $user['address'] ?? null,
                ];
                $this->redirect('/');
            }
        } catch (Throwable $exception) {
            $error = 'Không thể đăng nhập lúc này. Vui lòng kiểm tra cơ sở dữ liệu.';
        }

        require BASE_PATH . '/app/Views/auth/login.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $address = trim((string) ($_POST['address'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
                require BASE_PATH . '/app/Views/auth/register.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ.';
                require BASE_PATH . '/app/Views/auth/register.php';
                return;
            }

            if ($password !== $confirmPassword) {
                $error = 'Mật khẩu xác nhận không khớp.';
                require BASE_PATH . '/app/Views/auth/register.php';
                return;
            }

            if (strlen($password) < 6) {
                $error = 'Mật khẩu tối thiểu 6 ký tự.';
                require BASE_PATH . '/app/Views/auth/register.php';
                return;
            }

            if ($this->users->findByEmail($email)) {
                $error = 'Email đã tồn tại trong hệ thống.';
                require BASE_PATH . '/app/Views/auth/register.php';
                return;
            }

            $userId = $this->users->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'customer',
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
            ]);

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => 'customer',
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
            ];
            $this->redirect('/');
        }

        require BASE_PATH . '/app/Views/auth/register.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }

    public function profile(): void
    {
        $this->requireLogin();
        $user = $this->users->findById((int) ($_SESSION['user']['id'] ?? 0));
        $this->view('profile/index', ['user' => $user, 'csrfToken' => $this->csrfToken()]);
    }

    public function updateProfile(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();

        $id = (int) ($_SESSION['user']['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '') {
            $_SESSION['flash_error'] = 'Tên người dùng không được để trống.';
            $this->redirect('/profile');
        }

        $data = [
            'name' => $name,
            'phone' => $phone !== '' ? $phone : null,
            'address' => $address !== '' ? $address : null,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        $this->users->updateProfile($id, $data);
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $data['phone'];
        $_SESSION['user']['address'] = $data['address'];
        $_SESSION['flash'] = 'Cập nhật thông tin tài khoản thành công.';
        $this->redirect('/profile');
    }
}
