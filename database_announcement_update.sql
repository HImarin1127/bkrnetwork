-- 讀書共和國公告系統更新 SQL
-- 添加公告日期、附件功能和用戶部門信息

-- 1. 更新users表，添加部門信息
ALTER TABLE users 
ADD COLUMN department ENUM('總務人資', '資訊', '財務', '其他') DEFAULT '其他' AFTER role,
ADD COLUMN created_by INT NULL AFTER department,
ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- 2. 更新announcements表，添加公告日期和附件功能
ALTER TABLE announcements 
ADD COLUMN announcement_date DATE NOT NULL AFTER date,
ADD COLUMN attachment_url VARCHAR(500) NULL AFTER sort_order,
ADD COLUMN attachment_name VARCHAR(255) NULL AFTER attachment_url,
ADD COLUMN can_attach_pdf BOOLEAN DEFAULT FALSE AFTER attachment_name,
ADD COLUMN published_at TIMESTAMP NULL AFTER status,
ADD COLUMN published_by INT NULL AFTER published_at,
ADD FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL;

-- 3. 創建公告操作日誌表
CREATE TABLE IF NOT EXISTS announcement_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    action ENUM('created', 'updated', 'published', 'unpublished', 'deleted') NOT NULL,
    action_by INT NOT NULL,
    action_details JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (action_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_announcement_id (announcement_id),
    INDEX idx_action_by (action_by),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. 更新現有管理員用戶，設定部門
UPDATE users SET department = '資訊' WHERE role = 'admin' AND username = 'admin';

-- 5. 創建範例部門用戶（測試用）
INSERT INTO users (username, password, name, email, role, department, status) VALUES 
('hr_manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '總務人資主管', 'hr@republic.com.tw', 'user', '總務人資', 'active'),
('finance_manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '財務主管', 'finance@republic.com.tw', 'user', '財務', 'active'),
('it_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '資訊人員', 'it@republic.com.tw', 'user', '資訊', 'active')
ON DUPLICATE KEY UPDATE username = username;

-- 6. 刪除舊的範例公告
DELETE FROM announcements WHERE title IN (
    '歡迎使用讀書共和國員工服務網',
    '系統維護通知', 
    '員工手冊更新',
    '2025年國定假日公告'
);

-- 7. 創建新的範例公告（使用新結構）
INSERT INTO announcements (title, content, type, status, announcement_date, author_id, created_at, updated_at) VALUES 
('讀書共和國員工服務網正式啟用', '親愛的同仁們，全新的員工服務網已正式啟用，提供更便利的數位化服務。', 'general', 'published', CURDATE(), 1, NOW(), NOW()),
('2025年度教育訓練計畫', '2025年度員工教育訓練計畫已規劃完成，請各部門主管協助安排同仁參與。', 'general', 'published', CURDATE(), 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE title = title;

SELECT 'Database update completed successfully!' AS message; 