CREATE DATABASE IF NOT EXISTS `nhasach` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nhasach`;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(30) NULL UNIQUE,
    author VARCHAR(180) NOT NULL,
    publisher VARCHAR(180) NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    cover_image VARCHAR(255) NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_books_title (title),
    INDEX idx_books_category (category_id),
    CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(190) NULL,
    address VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_customers_name (name),
    INDEX idx_customers_phone (phone)
) ENGINE=InnoDB;

CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('completed','cancelled') NOT NULL DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sales_created_at (created_at),
    CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_sales_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_sale_items_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, role) VALUES
('Quản trị viên', 'admin@nhasach.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC8b9wT6jJ5wY3N5yQeK', 'admin');

INSERT INTO categories (name, description) VALUES
('Văn học', 'Tiểu thuyết, truyện ngắn và tác phẩm văn học.'),
('Kinh tế', 'Sách kinh doanh, tài chính và quản trị.'),
('Kỹ năng sống', 'Sách phát triển bản thân và kỹ năng.'),
('Thiếu nhi', 'Sách dành cho trẻ em.');

INSERT INTO books (category_id, title, isbn, author, publisher, price, stock_quantity, description) VALUES
(1, 'Dế Mèn phiêu lưu ký', '9786042000011', 'Tô Hoài', 'Kim Đồng', 65000, 25, 'Tác phẩm văn học thiếu nhi kinh điển.'),
(2, 'Đắc nhân tâm', '9786042000028', 'Dale Carnegie', 'Tổng hợp TP.HCM', 86000, 18, 'Nghệ thuật thu phục lòng người.'),
(3, 'Nhà giả kim', '9786042000035', 'Paulo Coelho', 'Nhã Nam', 79000, 12, 'Hành trình theo đuổi ước mơ.');

INSERT INTO customers (name, phone, email, address) VALUES
('Nguyễn Văn An', '0901234567', 'an@example.com', 'Hà Nội'),
('Trần Thị Bình', '0912345678', 'binh@example.com', 'Hà Nội');
