-- ====================================
-- 讀書共和國員工服務網 完整資料庫建立指令
-- 執行方式：mysql -u root -p < create_database.sql
-- ====================================

-- 1. 建立資料庫
CREATE DATABASE IF NOT EXISTS bkrnetwork 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 2. 使用資料庫
USE bkrnetwork;

-- 3. 建立使用者表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. 建立公告表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. 建立郵件記錄表（整合原有功能）
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. 建立會議室預約表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. 建立設備借用表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. 建立各類申請表單統一表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. 建立向後相容的視圖
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

-- 10. 插入預設管理員帳號
INSERT INTO users (username, password, name, email, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '系統管理員', 'admin@republic.com.tw', 'admin', 'active')
ON DUPLICATE KEY UPDATE username = username;
-- 預設密碼是 'password'

-- 11. 插入範例公告
INSERT INTO announcements (title, content, type, status, author_id) VALUES 
('歡迎使用讀書共和國員工服務網', '新版服務網採用 MVC 架構，專為讀書共和國員工打造，提供更好的便民服務體驗。', 'general', 'published', 1),
('系統維護通知', '員工服務網將於本週日凌晨2點至4點進行例行維護，期間可能會影響系統使用。', 'general', 'published', 1),
('員工手冊更新', '讀書共和國員工手冊已更新，請查看最新版本了解公司政策變更。', 'handbook', 'published', 1),
('2025年國定假日公告', '2025年國定假日已公布，請同仁留意相關日期安排工作。', 'holiday', 'published', 1)
ON DUPLICATE KEY UPDATE title = title;

-- 12. 建立範例使用者
INSERT INTO users (username, password, name, email, role, status) VALUES 
('test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '測試使用者', 'test@republic.com.tw', 'user', 'active'),
('editor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '編輯人員', 'editor@republic.com.tw', 'user', 'active')
ON DUPLICATE KEY UPDATE username = username;
-- 所有預設密碼都是 'password'

-- 13. 顯示建立結果
SELECT 'Database setup completed successfully!' AS message;
SELECT 'Available tables:' AS info;
SHOW TABLES;

SELECT 'Default users created:' AS info;
SELECT username, name, role FROM users;

-- 14. 建立國定假日行事曆表（修改版）
CREATE TABLE IF NOT EXISTS holiday_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    url TEXT,
    fetch_date DATETIME NOT NULL,
    holiday_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_year (year),
    INDEX idx_year (year),
    INDEX idx_fetch_date (fetch_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. 插入預設假日資料（2025年實際假日）
INSERT INTO holiday_calendar (year, title, url, fetch_date, holiday_data) VALUES 
(2025, '中華民國114年（西元2025年）政府行政機關辦公日曆表', 'https://www.dgpa.gov.tw/', NOW(), 
'[
    {"month": 1, "day": 1, "name": "中華民國開國紀念日", "type": "holiday"},
    {"month": 1, "day": 27, "name": "調整放假", "type": "holiday"},
    {"month": 1, "day": 28, "name": "農曆除夕", "type": "holiday"},
    {"month": 1, "day": 29, "name": "春節", "type": "holiday"},
    {"month": 1, "day": 30, "name": "春節", "type": "holiday"},
    {"month": 1, "day": 31, "name": "春節", "type": "holiday"},
    {"month": 2, "day": 3, "name": "春節", "type": "holiday"},
    {"month": 2, "day": 28, "name": "和平紀念日", "type": "holiday"},
    {"month": 4, "day": 4, "name": "兒童節", "type": "holiday"},
    {"month": 4, "day": 5, "name": "民族掃墓節（清明節）", "type": "holiday"},
    {"month": 5, "day": 1, "name": "勞動節", "type": "holiday"},
    {"month": 5, "day": 30, "name": "調整放假", "type": "holiday"},
    {"month": 5, "day": 31, "name": "端午節", "type": "holiday"},
    {"month": 10, "day": 6, "name": "中秋節", "type": "holiday"},
    {"month": 10, "day": 10, "name": "國慶日", "type": "holiday"}
]')
ON DUPLICATE KEY UPDATE 
title = VALUES(title), 
url = VALUES(url), 
fetch_date = VALUES(fetch_date), 
holiday_data = VALUES(holiday_data); 