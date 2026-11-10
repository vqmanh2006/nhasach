ALTER TABLE sales
    ADD COLUMN payment_method ENUM('offline', 'online') NOT NULL DEFAULT 'offline'
    AFTER delivery_address;
