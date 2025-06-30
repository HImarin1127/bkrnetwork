-- 資料庫更新腳本：新增 LDAP 支援欄位
-- 執行日期：2024-01-01
-- 說明：為 users 表新增 LDAP 認證所需的欄位

-- 為 users 表新增 LDAP 相關欄位
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS auth_source VARCHAR(20) DEFAULT 'local' COMMENT '認證來源：local=本地認證, ldap=LDAP認證',
ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT '' COMMENT '部門',
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT '' COMMENT '電話號碼',
ADD COLUMN IF NOT EXISTS title VARCHAR(100) DEFAULT '' COMMENT '職稱',
ADD COLUMN IF NOT EXISTS last_ldap_sync TIMESTAMP NULL COMMENT '最後 LDAP 同步時間';

-- 新增索引以提升查詢效能
CREATE INDEX IF NOT EXISTS idx_users_auth_source ON users(auth_source);
CREATE INDEX IF NOT EXISTS idx_users_department ON users(department);

-- 顯示更新後的表結構
DESCRIBE users; 