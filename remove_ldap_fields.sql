-- 移除 LDAP 整合欄位腳本
-- 執行前請確認已備份資料庫
-- 只有在確定永遠使用純LDAP認證時才執行此腳本

USE bkrnetwork;

-- 移除 LDAP 相關欄位和索引
ALTER TABLE users 
DROP INDEX IF EXISTS idx_users_auth_source,
DROP INDEX IF EXISTS idx_users_department,
DROP COLUMN IF EXISTS auth_source,
DROP COLUMN IF EXISTS department,
DROP COLUMN IF EXISTS phone,
DROP COLUMN IF EXISTS title,
DROP COLUMN IF EXISTS last_ldap_sync;

-- 顯示更新後的表結構
DESCRIBE users;

SELECT 'LDAP 欄位已移除，users 表已恢復為純本地認證結構' AS message; 