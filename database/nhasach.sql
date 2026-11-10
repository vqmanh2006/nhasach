CREATE DATABASE IF NOT EXISTS nha_sach_tri_tue
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE nha_sach_tri_tue;

CREATE TABLE nhan_vien (
    id_nhan_vien BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(150) NOT NULL,
    dia_chi_email VARCHAR(190) NOT NULL UNIQUE,
    so_dien_thoai VARCHAR(20) NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('quan_tri', 'nhan_vien') NOT NULL DEFAULT 'nhan_vien',
    dang_hoat_dong TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE khach_hang (
    id_khach_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(150) NOT NULL,
    dia_chi_email VARCHAR(190) NULL UNIQUE,
    so_dien_thoai VARCHAR(20) NULL UNIQUE,
    mat_khau VARCHAR(255) NULL,
    dia_chi VARCHAR(500) NULL,
    ngay_sinh DATE NULL,
    gioi_tinh ENUM('nam', 'nu', 'khac') NULL,
    diem_tich_luy INT UNSIGNED NOT NULL DEFAULT 0,
    dang_hoat_dong TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE danh_muc (
    id_danh_muc BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_danh_muc_cha BIGINT UNSIGNED NULL,
    ten_danh_muc VARCHAR(120) NOT NULL,
    mo_ta TEXT NULL,
    dang_hoat_dong TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_danh_muc_cha
        FOREIGN KEY (id_danh_muc_cha) REFERENCES danh_muc(id_danh_muc)
        ON DELETE SET NULL ON UPDATE CASCADE,
    UNIQUE KEY uq_danh_muc_cha_ten (id_danh_muc_cha, ten_danh_muc),
    INDEX idx_danh_muc_hoat_dong (dang_hoat_dong)
) ENGINE=InnoDB;

CREATE TABLE san_pham (
    id_san_pham BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_danh_muc BIGINT UNSIGNED NOT NULL,
    ma_san_pham VARCHAR(50) NOT NULL UNIQUE,
    ten_san_pham VARCHAR(200) NOT NULL,
    loai_san_pham ENUM('sach', 'do_choi', 'do_dung_hoc_tap', 'do_an', 'khac') NOT NULL DEFAULT 'khac',
    tac_gia VARCHAR(150) NULL,
    nha_xuat_ban VARCHAR(150) NULL,
    ma_isbn VARCHAR(20) NULL UNIQUE,
    mo_ta TEXT NULL,
    duong_dan_hinh_anh VARCHAR(500) NULL,
    gia_nhap DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    gia_ban DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    so_luong_ton INT UNSIGNED NOT NULL DEFAULT 0,
    nguong_canh_bao_ton INT UNSIGNED NOT NULL DEFAULT 5,
    trang_thai ENUM('con_hang', 'het_hang', 'ngung_kinh_doanh') NOT NULL DEFAULT 'con_hang',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_san_pham_danh_muc
        FOREIGN KEY (id_danh_muc) REFERENCES danh_muc(id_danh_muc)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_san_pham_danh_muc_trang_thai (id_danh_muc, trang_thai),
    INDEX idx_san_pham_ten (ten_san_pham),
    INDEX idx_san_pham_ton_kho (so_luong_ton, nguong_canh_bao_ton)
) ENGINE=InnoDB;

CREATE TABLE su_kien (
    id_su_kien BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_su_kien VARCHAR(150) NOT NULL,
    mo_ta TEXT NULL,
    bat_dau_luc DATETIME NOT NULL,
    ket_thuc_luc DATETIME NOT NULL,
    trang_thai ENUM('sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc', 'da_huy') NOT NULL DEFAULT 'sap_dien_ra',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_su_kien_thoi_gian_trang_thai (bat_dau_luc, ket_thuc_luc, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE phieu_giam_gia (
    id_phieu_giam_gia BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_su_kien BIGINT UNSIGNED NULL,
    ma_phieu VARCHAR(50) NOT NULL UNIQUE,
    mo_ta VARCHAR(255) NULL,
    loai_giam_gia ENUM('phan_tram', 'so_tien') NOT NULL,
    gia_tri_giam DECIMAL(12,2) NOT NULL,
    gia_tri_don_hang_toi_thieu DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    so_tien_giam_toi_da DECIMAL(12,2) NULL,
    gioi_han_luot_dung INT UNSIGNED NULL,
    so_luot_da_dung INT UNSIGNED NOT NULL DEFAULT 0,
    bat_dau_luc DATETIME NOT NULL,
    ket_thuc_luc DATETIME NOT NULL,
    dang_hoat_dong TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_phieu_giam_gia_su_kien
        FOREIGN KEY (id_su_kien) REFERENCES su_kien(id_su_kien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_phieu_giam_gia_hoat_dong_thoi_gian (dang_hoat_dong, bat_dau_luc, ket_thuc_luc)
) ENGINE=InnoDB;

CREATE TABLE gio_hang (
    id_gio_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_khach_hang BIGINT UNSIGNED NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_gio_hang_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_gio_hang_khach_hang (id_khach_hang)
) ENGINE=InnoDB;

CREATE TABLE chi_tiet_gio_hang (
    id_chi_tiet_gio_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_gio_hang BIGINT UNSIGNED NOT NULL,
    id_san_pham BIGINT UNSIGNED NOT NULL,
    so_luong INT UNSIGNED NOT NULL DEFAULT 1,
    ngay_them TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_chi_tiet_gio_hang_gio_hang
        FOREIGN KEY (id_gio_hang) REFERENCES gio_hang(id_gio_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_chi_tiet_gio_hang_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_chi_tiet_gio_hang_san_pham (id_gio_hang, id_san_pham)
) ENGINE=InnoDB;

CREATE TABLE don_hang (
    id_don_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_don_hang VARCHAR(30) NOT NULL UNIQUE,
    id_khach_hang BIGINT UNSIGNED NULL,
    id_nhan_vien BIGINT UNSIGNED NULL,
    id_phieu_giam_gia BIGINT UNSIGNED NULL,
    kenh_ban ENUM('truc_tuyen', 'tai_quay') NOT NULL,
    hinh_thuc_nhan_hang ENUM('giao_hang', 'nhan_tai_cua_hang', 'mua_tai_quay') NOT NULL,
    trang_thai ENUM(
        'cho_xac_nhan',
        'da_xac_nhan',
        'dang_dong_goi',
        'dang_giao',
        'cho_den_nhan',
        'hoan_thanh',
        'da_huy',
        'yeu_cau_tra_hang',
        'da_tra_hang'
    ) NOT NULL DEFAULT 'cho_xac_nhan',
    phuong_thuc_thanh_toan ENUM('tien_mat', 'chuyen_khoan', 'the', 'khac') NOT NULL DEFAULT 'tien_mat',
    trang_thai_thanh_toan ENUM('chua_thanh_toan', 'da_thanh_toan', 'da_hoan_tien') NOT NULL DEFAULT 'chua_thanh_toan',
    ten_nguoi_nhan VARCHAR(150) NULL,
    sdt_nguoi_nhan VARCHAR(20) NULL,
    dia_chi_giao_hang VARCHAR(500) NULL,
    tong_tien_hang DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    so_tien_giam DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    phi_giao_hang DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tong_thanh_toan DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ma_phieu_giam_gia_da_ap_dung VARCHAR(50) NULL,
    diem_da_tich INT UNSIGNED NOT NULL DEFAULT 0,
    diem_da_dung INT UNSIGNED NOT NULL DEFAULT 0,
    ghi_chu_khach_hang TEXT NULL,
    ghi_chu_nhan_vien TEXT NULL,
    ngay_dat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_don_hang_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_don_hang_nhan_vien
        FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_don_hang_phieu_giam_gia
        FOREIGN KEY (id_phieu_giam_gia) REFERENCES phieu_giam_gia(id_phieu_giam_gia)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_don_hang_khach_hang_ngay (id_khach_hang, ngay_dat),
    INDEX idx_don_hang_trang_thai_ngay (trang_thai, ngay_dat),
    INDEX idx_don_hang_kenh_ban (kenh_ban)
) ENGINE=InnoDB;

CREATE TABLE chi_tiet_don_hang (
    id_chi_tiet_don_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_don_hang BIGINT UNSIGNED NOT NULL,
    id_san_pham BIGINT UNSIGNED NULL,
    ten_san_pham_luc_dat VARCHAR(200) NOT NULL,
    ma_san_pham_luc_dat VARCHAR(50) NOT NULL,
    so_luong INT UNSIGNED NOT NULL,
    don_gia DECIMAL(12,2) NOT NULL,
    gia_nhap_luc_ban DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    thanh_tien DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_chi_tiet_don_hang_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_chi_tiet_don_hang_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_chi_tiet_don_hang_san_pham (id_san_pham),
    INDEX idx_chi_tiet_don_hang_don_hang (id_don_hang)
) ENGINE=InnoDB;

CREATE TABLE giao_dich_kho (
    id_giao_dich_kho BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_san_pham BIGINT UNSIGNED NOT NULL,
    id_nhan_vien BIGINT UNSIGNED NULL,
    id_don_hang BIGINT UNSIGNED NULL,
    loai_giao_dich ENUM('nhap_hang', 'ban_hang', 'tra_hang', 'dieu_chinh') NOT NULL,
    thay_doi_so_luong INT NOT NULL,
    ghi_chu VARCHAR(500) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_giao_dich_kho_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_giao_dich_kho_nhan_vien
        FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_giao_dich_kho_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_giao_dich_kho_san_pham_ngay (id_san_pham, ngay_tao),
    INDEX idx_giao_dich_kho_don_hang (id_don_hang)
) ENGINE=InnoDB;

CREATE TABLE giao_dich_diem (
    id_giao_dich_diem BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_khach_hang BIGINT UNSIGNED NOT NULL,
    id_don_hang BIGINT UNSIGNED NULL,
    id_nhan_vien BIGINT UNSIGNED NULL,
    thay_doi_diem INT NOT NULL,
    mo_ta VARCHAR(255) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_giao_dich_diem_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_giao_dich_diem_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_giao_dich_diem_nhan_vien
        FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_giao_dich_diem_khach_hang_ngay (id_khach_hang, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE danh_gia_san_pham (
    id_danh_gia BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_khach_hang BIGINT UNSIGNED NOT NULL,
    id_san_pham BIGINT UNSIGNED NOT NULL,
    id_don_hang BIGINT UNSIGNED NOT NULL,
    so_sao TINYINT UNSIGNED NOT NULL,
    noi_dung VARCHAR(1000) NULL,
    dang_hien_thi TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_danh_gia_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_danh_gia_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_danh_gia_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_danh_gia_so_sao CHECK (so_sao BETWEEN 1 AND 5),
    UNIQUE KEY uq_danh_gia_khach_san_pham_don_hang (id_khach_hang, id_san_pham, id_don_hang),
    INDEX idx_danh_gia_san_pham_hien_thi (id_san_pham, dang_hien_thi)
) ENGINE=InnoDB;

CREATE TABLE yeu_cau_ho_tro (
    id_yeu_cau_ho_tro BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_khach_hang BIGINT UNSIGNED NOT NULL,
    id_don_hang BIGINT UNSIGNED NULL,
    id_nhan_vien_phu_trach BIGINT UNSIGNED NULL,
    tieu_de VARCHAR(200) NOT NULL,
    loai_yeu_cau ENUM('hoi_dap', 'khieu_nai', 'tra_hang', 'khac') NOT NULL DEFAULT 'hoi_dap',
    trang_thai ENUM('moi', 'dang_xu_ly', 'cho_khach_hang', 'da_giai_quyet', 'da_dong') NOT NULL DEFAULT 'moi',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_yeu_cau_ho_tro_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_yeu_cau_ho_tro_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_yeu_cau_ho_tro_nhan_vien
        FOREIGN KEY (id_nhan_vien_phu_trach) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_yeu_cau_ho_tro_trang_thai_ngay (trang_thai, ngay_tao),
    INDEX idx_yeu_cau_ho_tro_nhan_vien (id_nhan_vien_phu_trach, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE tin_nhan_ho_tro (
    id_tin_nhan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_yeu_cau_ho_tro BIGINT UNSIGNED NOT NULL,
    nguoi_gui ENUM('khach_hang', 'nhan_vien') NOT NULL,
    id_khach_hang BIGINT UNSIGNED NULL,
    id_nhan_vien BIGINT UNSIGNED NULL,
    noi_dung TEXT NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tin_nhan_yeu_cau
        FOREIGN KEY (id_yeu_cau_ho_tro) REFERENCES yeu_cau_ho_tro(id_yeu_cau_ho_tro)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tin_nhan_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_tin_nhan_nhan_vien
        FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_tin_nhan_yeu_cau_ngay (id_yeu_cau_ho_tro, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE yeu_cau_tra_hang (
    id_yeu_cau_tra_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_don_hang BIGINT UNSIGNED NOT NULL,
    id_khach_hang BIGINT UNSIGNED NOT NULL,
    id_nhan_vien_duyet BIGINT UNSIGNED NULL,
    ly_do TEXT NOT NULL,
    trang_thai ENUM('cho_duyet', 'da_duyet', 'tu_choi', 'da_nhan_hang', 'da_hoan_tien') NOT NULL DEFAULT 'cho_duyet',
    ngay_yeu_cau TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_duyet DATETIME NULL,
    CONSTRAINT fk_yeu_cau_tra_hang_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_yeu_cau_tra_hang_khach_hang
        FOREIGN KEY (id_khach_hang) REFERENCES khach_hang(id_khach_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_yeu_cau_tra_hang_nhan_vien
        FOREIGN KEY (id_nhan_vien_duyet) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_yeu_cau_tra_hang_trang_thai_ngay (trang_thai, ngay_yeu_cau)
) ENGINE=InnoDB;

CREATE TABLE chi_tiet_tra_hang (
    id_chi_tiet_tra_hang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_yeu_cau_tra_hang BIGINT UNSIGNED NOT NULL,
    id_chi_tiet_don_hang BIGINT UNSIGNED NOT NULL,
    so_luong INT UNSIGNED NOT NULL,
    CONSTRAINT fk_chi_tiet_tra_hang_yeu_cau
        FOREIGN KEY (id_yeu_cau_tra_hang) REFERENCES yeu_cau_tra_hang(id_yeu_cau_tra_hang)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_chi_tiet_tra_hang_don_hang
        FOREIGN KEY (id_chi_tiet_don_hang) REFERENCES chi_tiet_don_hang(id_chi_tiet_don_hang)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY uq_chi_tiet_tra_hang (id_yeu_cau_tra_hang, id_chi_tiet_don_hang)
) ENGINE=InnoDB;

CREATE TABLE nhat_ky_hoat_dong (
    id_nhat_ky BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_nhan_vien BIGINT UNSIGNED NULL,
    hanh_dong VARCHAR(100) NOT NULL,
    ten_bang VARCHAR(100) NOT NULL,
    id_ban_ghi BIGINT UNSIGNED NULL,
    du_lieu_cu JSON NULL,
    du_lieu_moi JSON NULL,
    dia_chi_ip VARCHAR(45) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_nhat_ky_nhan_vien
        FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_nhat_ky_bang_ban_ghi_ngay (ten_bang, id_ban_ghi, ngay_tao),
    INDEX idx_nhat_ky_nhan_vien_ngay (id_nhan_vien, ngay_tao)
) ENGINE=InnoDB;

INSERT INTO nhan_vien (ho_ten, dia_chi_email, mat_khau, vai_tro)
VALUES ('Quản trị viên', 'admin@nhasach.local', '$2y$12$1/H.TCiqeLp4Nflx0AcrCOi6lJ8mKaGK1RQY0cqus20PPPtQ8O4OC', 'quan_tri');

INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES
('Văn học', 'Tiểu thuyết, truyện ngắn và tác phẩm văn học.'),
('Kinh tế', 'Sách kinh doanh, tài chính và quản trị.'),
('Kỹ năng sống', 'Sách phát triển bản thân và kỹ năng.'),
('Thiếu nhi', 'Sách dành cho trẻ em.');

INSERT INTO san_pham
(id_danh_muc, ma_san_pham, ten_san_pham, loai_san_pham, tac_gia, nha_xuat_ban, ma_isbn, gia_ban, so_luong_ton, trang_thai)
VALUES
(1, 'BOOK-DE-MEN', 'Dế Mèn phiêu lưu ký', 'sach', 'Tô Hoài', 'Kim Đồng', '9786042000011', 65000, 25, 'con_hang'),
(2, 'BOOK-DAC-NHAN-TAM', 'Đắc nhân tâm', 'sach', 'Dale Carnegie', 'Tổng hợp TP.HCM', '9786042000028', 86000, 18, 'con_hang'),
(3, 'BOOK-NHA-GIA-KIM', 'Nhà giả kim', 'sach', 'Paulo Coelho', 'Nhã Nam', '9786042000035', 79000, 12, 'con_hang');
