-- 添加 LDAP 整合所需的資料庫欄位
-- 執行方式：mysql -u root -p bkrnetwork < add_ldap_fields.sql

USE bkrnetwork;

-- 檢查並添加 auth_source 欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS auth_source ENUM('local', 'ldap') DEFAULT 'local' 
AFTER status;

-- 檢查並添加 department 欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT '' 
AFTER email;

-- 檢查並添加 phone 欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT '' 
AFTER department;

-- 檢查並添加 title 欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS title VARCHAR(100) DEFAULT '' 
AFTER phone;

-- 顯示更新後的表結構
DESCRIBE users;

-- 顯示成功訊息
SELECT 'LDAP integration fields added successfully!' AS message;
SELECT 'Users table now supports LDAP authentication' AS info; 