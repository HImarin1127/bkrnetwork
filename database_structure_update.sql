-- ====================================
-- 讀書共和國員工服務網 資料庫結構更新
-- 執行方式：mysql -u root -p bkrnetwork < database_structure_update.sql
-- ====================================

USE bkrnetwork;

-- 1. 備份現有資料
CREATE TABLE IF NOT EXISTS users_backup AS SELECT * FROM users;
CREATE TABLE IF NOT EXISTS mail_records_backup AS SELECT * FROM mail_records;

-- 2. 建立新的 users 資料表
DROP TABLE IF EXISTS users_new;
CREATE TABLE users_new (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL COMMENT '格式：部門-名字',
    email VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. 建立新的 mail_records 資料表
DROP TABLE IF EXISTS mail_records_new;
CREATE TABLE mail_records_new (
    mail_code VARCHAR(50) PRIMARY KEY COMMENT '格式：POSTYYYYMMDD + 流水號',
    mail_type VARCHAR(50) NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_ext VARCHAR(20),
    receiver_name VARCHAR(100) NOT NULL,
    receiver_address TEXT NOT NULL,
    receiver_phone VARCHAR(20),
    declare_department VARCHAR(100),
    registrar_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (registrar_id) REFERENCES users_new(username) ON DELETE CASCADE,
    INDEX idx_mail_code (mail_code),
    INDEX idx_registrar (registrar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. 遷移 users 資料
INSERT INTO users_new (username, password, name, email)
SELECT 
    username,
    password,
    CASE 
        WHEN role = 'admin' THEN '最高系統管理員-系統管理員'
        ELSE CONCAT('一般-', name)
    END as name,
    email
FROM users_backup;

-- 5. 遷移 mail_records 資料
INSERT INTO mail_records_new (
    mail_code, mail_type, sender_name, sender_ext,
    receiver_name, receiver_address, receiver_phone,
    declare_department, registrar_id
)
SELECT 
    COALESCE(mail_code, CONCAT('POST', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 4, '0'))) as mail_code,
    mail_type,
    sender_name,
    sender_ext,
    receiver_name,
    receiver_address,
    receiver_phone,
    declare_department,
    (SELECT username FROM users_backup WHERE id = mail_records_backup.registrar_id) as registrar_id
FROM mail_records_backup
WHERE registrar_id IS NOT NULL;

-- 6. 刪除舊資料表並重新命名
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS mail_records;
RENAME TABLE users_new TO users;
RENAME TABLE mail_records_new TO mail_records;

-- 7. 插入預設最高系統管理員帳號
INSERT INTO users (username, password, name, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '最高系統管理員-系統管理員', 'admin@republic.com.tw')
ON DUPLICATE KEY UPDATE username = username;

-- 8. 插入範例部門使用者
INSERT INTO users (username, password, name, email) VALUES 
('general_affairs', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '總務-王小明', 'ga@republic.com.tw'),
('it_dept', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '資訊-李小華', 'it@republic.com.tw'),
('hr_dept', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '人資-陳小美', 'hr@republic.com.tw'),
('finance_dept', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '財務-張小強', 'finance@republic.com.tw')
ON DUPLICATE KEY UPDATE username = username;

-- 9. 刪除其他不需要的資料表視圖
DROP VIEW IF EXISTS mail_items;

-- 樓層資訊表
CREATE TABLE IF NOT EXISTS floor_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_number INT NOT NULL,
    floor_name VARCHAR(100) NOT NULL,
    floor_description TEXT,
    floor_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 部門聯絡資訊表
CREATE TABLE IF NOT EXISTS department_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    building VARCHAR(50),
    floor_number INT NOT NULL,
    extension_range VARCHAR(50),
    email VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_number) REFERENCES floor_info(floor_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 員工座位表
CREATE TABLE IF NOT EXISTS employee_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(50) NOT NULL,
    floor_number INT NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    department_id INT,
    extension_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_number) REFERENCES floor_info(floor_number),
    FOREIGN KEY (department_id) REFERENCES department_contacts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 分機號碼表
CREATE TABLE IF NOT EXISTS extension_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension_number VARCHAR(20) NOT NULL,
    employee_name VARCHAR(50) NOT NULL,
    department_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES department_contacts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入基本樓層資訊
INSERT INTO floor_info (floor_number, floor_name, floor_description, floor_type) VALUES
(7, '主管辦公區', '總經理室、副總經理室、高階主管會議室、秘書處', 'executive'),
(6, '編輯部門', '總編輯室、編輯部辦公區、編輯會議室、資料室', 'editorial'),
(5, '業務行銷部門', '業務部、行銷部、業務會議室、客服中心', 'sales'),
(4, '製作發行部門', '製作部、發行部、製作會議室、樣書室', 'production'),
(3, '行政支援部門', '人事行政部、財務會計部、資訊部、員工休息室', 'admin');

-- 插入基本部門聯絡資訊
INSERT INTO department_contacts (department_name, building, floor_number, extension_range, email, description) VALUES
('總裁辦公室', '108-2', 9, '101-105', 'ceo@bookrep.com.tw', '總裁室及秘書處'),
('編輯部', '108-3', 6, '201-210', 'editorial@bookrep.com.tw', '負責圖書編輯與出版'),
('業務部', '108-4', 5, '301-315', 'sales@bookrep.com.tw', '負責圖書銷售與通路管理'),
('行銷部', '108-4', 5, '316-325', 'marketing@bookrep.com.tw', '負責圖書行銷與推廣'),
('製作部', '108-3', 8, '401-410', 'production@bookrep.com.tw', '負責圖書製作與印刷'),
('人事行政部', '108-3', 3, '501-505', 'hr@bookrep.com.tw', '負責人事管理與行政事務'),
('財務會計部', '108-3', 3, '601-608', 'finance@bookrep.com.tw', '負責財務與會計作業'),
('資訊部', '108-3', 3, '701-705', 'it@bookrep.com.tw', '負責資訊系統維護與開發'),
('物流部', 'nankan', 1, '801-810', 'logistics@bookrep.com.tw', '南崁物流中心');

-- 更新部門聯絡資訊表，添加大樓欄位
ALTER TABLE department_contacts ADD COLUMN IF NOT EXISTS building VARCHAR(50) AFTER department_name;

-- 更新現有資料的大樓資訊
UPDATE department_contacts SET building = '108-3' WHERE floor_number IN (3, 6, 8) AND department_name NOT IN ('業務部', '行銷部');
UPDATE department_contacts SET building = '108-4' WHERE floor_number IN (5, 8) AND department_name IN ('業務部', '行銷部');
UPDATE department_contacts SET building = '108-2' WHERE floor_number = 9;
UPDATE department_contacts SET building = 'nankan' WHERE department_name LIKE '%物流%';

-- 更新部門聯絡資訊
DELETE FROM department_contacts;

INSERT INTO department_contacts (department_name, building, floor_number, extension_range, email, description) VALUES
('總裁辦公室', '108-2', 9, '101-105', 'ceo@bookrep.com.tw', '總裁室及秘書處'),
('編輯部', '108-3', 6, '201-210', 'editorial@bookrep.com.tw', '負責圖書編輯與出版'),
('業務部', '108-4', 5, '301-315', 'sales@bookrep.com.tw', '負責圖書銷售與通路管理'),
('行銷部', '108-4', 5, '316-325', 'marketing@bookrep.com.tw', '負責圖書行銷與推廣'),
('製作部', '108-3', 8, '401-410', 'production@bookrep.com.tw', '負責圖書製作與印刷'),
('人事行政部', '108-3', 3, '501-505', 'hr@bookrep.com.tw', '負責人事管理與行政事務'),
('財務會計部', '108-3', 3, '601-608', 'finance@bookrep.com.tw', '負責財務與會計作業'),
('資訊部', '108-3', 3, '701-705', 'it@bookrep.com.tw', '負責資訊系統維護與開發'),
('物流部', 'nankan', 1, '801-810', 'logistics@bookrep.com.tw', '南崁物流中心');

COMMIT; 