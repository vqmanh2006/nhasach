USE `nhasach`;

ALTER TABLE users
    MODIFY role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
    ADD COLUMN phone VARCHAR(30) NULL AFTER role,
    ADD COLUMN address VARCHAR(255) NULL AFTER phone;
