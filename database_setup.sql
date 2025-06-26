-- database_setup.sql
-- 讀書共和國員工服務網 MVC 架構資料庫設定

-- 使用者表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 公告表
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('general', 'holiday', 'handbook') DEFAULT 'general',
    status ENUM('draft', 'published') DEFAULT 'draft',
    sort_order INT DEFAULT 0,
    date DATE NULL,
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 郵件記錄表（整合原有功能）
CREATE TABLE IF NOT EXISTS mail_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail_code VARCHAR(50) UNIQUE,
    mail_type VARCHAR(50) NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_ext VARCHAR(20),
    receiver_name VARCHAR(100) NOT NULL,
    receiver_address TEXT NOT NULL,
    receiver_phone VARCHAR(20),
    declare_department VARCHAR(100),
    item_count INT DEFAULT 1,
    postage DECIMAL(10,2) DEFAULT 0,
    tracking_number VARCHAR(100),
    status ENUM('草稿', '已送出', '已寄達') DEFAULT '已送出',
    notes TEXT,
    registrar_id INT,
    sender_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registrar_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_mail_code (mail_code),
    INDEX idx_date (created_at),
    INDEX idx_registrar (registrar_id)
);

-- 為了向後相容，建立 mail_items 視圖
CREATE VIEW IF NOT EXISTS mail_items AS
SELECT 
    id,
    mail_code,
    mail_type,
    sender_name,
    sender_ext,
    receiver_name,
    receiver_address,
    receiver_phone,
    declare_department,
    item_count,
    postage,
    tracking_number,
    status,
    notes,
    registrar_id,
    sender_id,
    created_at,
    updated_at
FROM mail_records;

-- 會議室預約表
CREATE TABLE IF NOT EXISTS meeting_room_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    purpose TEXT NOT NULL,
    attendees_count INT DEFAULT 1,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 設備借用表
CREATE TABLE IF NOT EXISTS equipment_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_name VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    return_date DATE NOT NULL,
    purpose TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'returned') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 各類申請表單統一表
CREATE TABLE IF NOT EXISTS form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    form_type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    form_data JSON NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    processed_by INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 插入預設管理員帳號
INSERT INTO users (username, password, name, email, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '系統管理員', 'admin@republic.com.tw', 'admin', 'active')
ON DUPLICATE KEY UPDATE username = username;
-- 密碼是 'password'

-- 插入範例公告
INSERT INTO announcements (title, content, type, status, author_id) VALUES 
('歡迎使用讀書共和國員工服務網', '新版服務網採用 MVC 架構，專為讀書共和國員工打造，提供更好的便民服務體驗。', 'general', 'published', 1),
('系統維護通知', '員工服務網將於本週日凌晨2點至4點進行例行維護，期間可能會影響系統使用。', 'general', 'published', 1),
('員工手冊更新', '讀書共和國員工手冊已更新，請查看最新版本了解公司政策變更。', 'handbook', 'published', 1)
ON DUPLICATE KEY UPDATE title = title; 