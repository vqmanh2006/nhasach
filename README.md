# Nhà sách Pro

Ứng dụng quản lý nhà sách bằng PHP MVC, PDO, MySQL/MariaDB và JavaScript thuần.

## Cài đặt

1. Trỏ Apache DocumentRoot vào thư mục `public/` hoặc bật module rewrite.
2. Import `database/nhasach.sql` vào MySQL/MariaDB.
3. Kiểm tra `config/database.php` hoặc đặt `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
4. Mở `/login`.

Tài khoản demo: `admin@nhasach.local` / `password`.

## Route hiện có

- `GET /`: dashboard thống kê cơ bản.
- `GET /login`: form đăng nhập.
- `POST /login`: xử lý đăng nhập.
- `GET /logout`: đăng xuất.

## Cấu trúc

- `public/index.php`: front controller.
- `core/`: Router, PDO Database, Model và Controller.
- `config/`: cấu hình.
- `app/Views/`: giao diện.
- `database/nhasach.sql`: schema và dữ liệu mẫu.
