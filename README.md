# Nhà sách trí tuệ

Ứng dụng quản lý nhà sách xây dựng bằng PHP MVC, PDO, MySQL/MariaDB và
JavaScript thuần. Hệ thống hỗ trợ quản lý sách, danh mục, khách hàng và
đơn hàng bán sách trong một giao diện quản trị bằng tiếng Việt.

## Tính năng

- Đăng nhập và đăng xuất tài khoản quản trị.
- Giao diện riêng cho khách hàng sau khi đăng nhập.
- Dashboard hiển thị tổng số sách, danh mục, khách hàng và đơn hàng.
- Quản lý sách: thêm, sửa, xóa, tìm kiếm, cập nhật giá và tồn kho.
- Tải ảnh bìa khi thêm hoặc chỉnh sửa sách; hỗ trợ JPG, PNG và WebP tối đa 5MB.
- Quản lý danh mục sách.
- Quản lý thông tin khách hàng.
- Tạo và hủy đơn hàng; tồn kho được cập nhật theo giao dịch.
- Đặt hàng có địa chỉ giao hàng cụ thể và admin theo dõi được địa chỉ trong đơn.
- Bảo vệ biểu mẫu bằng CSRF token và sử dụng prepared statements với PDO.

## Yêu cầu

- PHP 8.0 trở lên với extension `PDO` và `pdo_mysql`.
- MySQL 5.7+/8.0 hoặc MariaDB tương thích.
- Apache với `mod_rewrite` được bật, hoặc máy chủ PHP có thể chuyển mọi
  request về `public/index.php`.

## Cài đặt

1. Clone dự án và trỏ DocumentRoot của Apache vào thư mục `public/`, hoặc
   mở dự án tại `http://localhost/nhasach/public/` khi đặt trong XAMPP.
2. Tạo cơ sở dữ liệu bằng file `database/nhasach.sql`:

   ```bash
   mysql -u root -p < database/nhasach.sql
   ```

3. Cấu hình kết nối trong `config/database.php`, hoặc đặt các biến môi
   trường `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` và `DB_PASSWORD`.
4. Đảm bảo web server có quyền đọc toàn bộ mã nguồn và truy cập thư mục
   `public/`.
5. Mở `/` trên trình duyệt. Đây là link truy cập chính; hệ thống sẽ tự
   hiển thị giao diện quản trị hoặc khách hàng theo tài khoản đăng nhập.

Không cần cài package bên ngoài; frontend sử dụng CSS và JavaScript có sẵn
trong thư mục `public/`.

## Tài khoản demo

Tài khoản được tạo khi import cơ sở dữ liệu:

```text
Email: admin@nhasach.local
Mật khẩu: password
```

Hãy đổi hoặc xóa tài khoản mẫu trước khi đưa hệ thống lên môi trường thật.

## Các route chính

| Method | Route | Mô tả |
| --- | --- | --- |
| GET | `/` | Dashboard thống kê |
| GET/POST | `/login` | Hiển thị và xử lý đăng nhập |
| GET/POST | `/register` | Tạo tài khoản khách hàng |
| GET | `/customer` | Route nội bộ cho trang mua sắm khách hàng |
| GET/POST | `/customer/cart` | Xem và cập nhật giỏ hàng |
| POST | `/customer/order` | Đặt hàng từ giỏ hàng |
| GET/POST | `/profile` | Xem và cập nhật hồ sơ tài khoản |
| GET | `/logout` | Đăng xuất |
| GET | `/books` | Danh sách và tìm kiếm sách |
| GET/POST | `/books/create`, `/books` | Thêm sách |
| GET/POST | `/books/edit`, `/books/update` | Sửa sách |
| POST | `/books/delete` | Xóa sách |
| GET | `/categories` | Danh sách danh mục |
| GET/POST | `/categories/create`, `/categories` | Thêm danh mục |
| GET/POST | `/categories/edit`, `/categories/update` | Sửa danh mục |
| POST | `/categories/delete` | Xóa danh mục |
| GET | `/customers` | Danh sách khách hàng |
| GET/POST | `/customers/create`, `/customers` | Thêm khách hàng |
| GET/POST | `/customers/edit`, `/customers/update` | Sửa khách hàng |
| POST | `/customers/delete` | Xóa khách hàng |
| GET | `/sales` | Danh sách đơn hàng |
| GET/POST | `/sales/create`, `/sales` | Tạo đơn hàng |
| POST | `/sales/delete` | Hủy đơn hàng |

## Cấu trúc thư mục

```text
app/
├── Controllers/   # Xử lý request và nghiệp vụ
├── Models/        # Truy vấn dữ liệu
└── Views/         # Giao diện PHP
config/            # Hằng số ứng dụng và cấu hình database
core/              # Router, Database, Model, Controller dùng chung
database/          # Schema và dữ liệu mẫu
public/            # DocumentRoot, CSS, JavaScript và front controller
└── uploads/books/  # Ảnh bìa sách do admin tải lên
```

## Luồng xử lý

`public/index.php` khởi tạo session, nạp các thành phần cốt lõi, đăng ký
route và chuyển request đến controller tương ứng. Controller gọi model để
đọc hoặc ghi dữ liệu, sau đó render view trong `app/Views/`. Giao diện dùng
layout chung tại `app/Views/layouts/`.

## Cấu hình tên hiển thị

Tên thương hiệu hiện tại là **Nhà sách trí tuệ**. Có thể thay đổi tên dùng
trên tiêu đề trang và footer tại hằng `APP_NAME` trong
`config/constants.php`; thương hiệu trên thanh điều hướng nằm trong
`app/Views/layouts/header.php`.

## Đăng ký tài khoản khách hàng

Mở `http://localhost:8080/register` để tạo tài khoản khách hàng, hoặc chọn
liên kết đăng ký trên trang `/login`. Tài khoản mới được lưu với role
`customer` và được đăng nhập tự động sau khi đăng ký thành công.

Nếu database đã được tạo từ phiên bản cũ, chạy thêm file
`database/migrations/001_add_customer_accounts.sql` trong phpMyAdmin trước
khi đăng ký tài khoản đầu tiên. Nếu import lại toàn bộ database từ đầu, file
`database/nhasach.sql` đã bao gồm các cột và role cần thiết.

Nếu tài khoản demo báo sai mật khẩu dù đã nhập `password`, chạy file
`database/migrations/002_reset_demo_admin_password.sql` trong phpMyAdmin,
sau đó đăng nhập lại bằng `admin@nhasach.local` / `password`.

Nếu database đã tồn tại từ trước, chạy thêm migration
`database/migrations/004_add_delivery_address.sql` để bổ sung địa chỉ giao
hàng cho đơn hàng. Admin/staff có thể mở trang khách hàng bằng liên kết
**Xem trang khách hàng** trên thanh đầu trang; sách và tồn kho dùng chung
database nên thay đổi từ trang quản trị sẽ được cập nhật ngay.

Để bật lựa chọn thanh toán khi đặt hàng trên database cũ, chạy thêm
`database/migrations/005_add_payment_method.sql`. Trang chi tiết sách dùng
route `/customer/book?id=...`; phương thức online hiện hiển thị QR mẫu để
kiểm thử giao diện, chưa kết nối ngân hàng thật.
