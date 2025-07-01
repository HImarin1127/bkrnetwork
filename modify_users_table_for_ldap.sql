-- ====================================
-- 修改 users 表以支援混合 LDAP 認證模式
-- 
-- 概念：
-- - 移除 password 欄位（認證完全依賴 LDAP）
-- - 保留用戶資料在本地資料庫（方便查詢和關聯）
-- - 欄位映射調整：
--   * username = 中文名字（顯示名稱）
--   * id = 自增主鍵（保持不變）
--   * 新增 ldap_uid 欄位存放 LDAP 帳號
-- ====================================

USE bkrnetwork;

-- 1. 備份現有資料
CREATE TABLE IF NOT EXISTS users_backup_before_ldap_migration AS SELECT * FROM users;

-- 2. 添加 LDAP 相關欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS ldap_uid VARCHAR(100) COMMENT 'LDAP 使用者帳號',
ADD COLUMN IF NOT EXISTS department VARCHAR(100) COMMENT '部門',
ADD COLUMN IF NOT EXISTS phone VARCHAR(50) COMMENT '電話',
ADD COLUMN IF NOT EXISTS title VARCHAR(100) COMMENT '職稱',
ADD COLUMN IF NOT EXISTS auth_source ENUM('local', 'ldap') DEFAULT 'local' COMMENT '認證來源';

-- 3. 為 LDAP 欄位建立索引
ALTER TABLE users 
ADD INDEX IF NOT EXISTS idx_ldap_uid (ldap_uid),
ADD INDEX IF NOT EXISTS idx_auth_source (auth_source);

-- 4. 修改現有欄位註解以明確用途
ALTER TABLE users 
MODIFY COLUMN username VARCHAR(100) COMMENT '顯示名稱（中文名字）',
MODIFY COLUMN email VARCHAR(150) COMMENT '電子郵件',
MODIFY COLUMN name VARCHAR(100) COMMENT '全名（可能重複username）';

-- 5. 移除 password 欄位（注意：這會刪除所有本地密碼）
-- 警告：執行此步驟前請確保 LDAP 認證正常運作
-- ALTER TABLE users DROP COLUMN IF EXISTS password;

-- 6. 創建視圖以維持向後相容性
CREATE OR REPLACE VIEW user_display AS
SELECT 
    id,
    ldap_uid as uid,
    username as display_name,
    username as name,  -- 為了相容性
    email,
    department,
    phone,
    title,
    role,
    status,
    auth_source,
    created_at,
    updated_at
FROM users;

-- 7. 顯示修改結果
SELECT 'Users table modification completed!' AS message;
SELECT 'Current table structure:' AS info;
DESCRIBE users;

-- 8. 檢查現有資料
SELECT 'Current user data:' AS info;
SELECT id, username, ldap_uid, email, role, auth_source FROM users;

-- 9. 使用指南
SELECT '
混合 LDAP 認證模式使用說明：
1. 認證：完全依賴 LDAP，不使用本地密碼
2. 欄位映射：
   - id: 資料庫主鍵（自增）
   - ldap_uid: LDAP 帳號（例如: ldapnormal）
   - username: 中文顯示名稱（例如: 測試使用者）
   - 其他欄位從 LDAP 同步
3. 查詢時使用 ldap_uid 而非 username
4. 顯示時使用 username（中文名稱）
' AS usage_guide; 