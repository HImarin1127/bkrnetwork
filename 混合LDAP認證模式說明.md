# 混合 LDAP 認證模式實現說明

## 概述
本系統實現了創新的混合 LDAP 認證模式，結合 LDAP 認證安全性和本地資料庫便利性。

### 核心理念
- **認證**：完全依賴 LDAP 伺服器
- **資料儲存**：本地資料庫儲存用戶資料（不含密碼）
- **欄位映射**：username=中文名字，id=LDAP帳號

## 資料庫結構調整

### Users 表新增欄位
```sql
ALTER TABLE users 
ADD COLUMN ldap_uid VARCHAR(100) COMMENT 'LDAP 使用者帳號',
ADD COLUMN department VARCHAR(100) COMMENT '部門',
ADD COLUMN phone VARCHAR(50) COMMENT '電話',
ADD COLUMN title VARCHAR(100) COMMENT '職稱',
ADD COLUMN auth_source ENUM('local', 'ldap') DEFAULT 'local';
```

### 欄位映射策略
- `id`: 資料庫主鍵
- `ldap_uid`: LDAP 帳號（來自 LDAP uid，例如：ldapnormal）
- `username`: 中文顯示名稱（來自 LDAP description，例如：測試使用者）

### LDAP 屬性映射
- LDAP `uid` → 系統 `ldap_uid`（帳號）
- LDAP `description` → 系統 `username`（中文顯示名稱）
- 如果 `description` 為空，則使用 LDAP `cn` 作為顯示名稱

## 配置設定

### LDAP 配置調整
```php
'auto_create_users' => true,
'sync_attributes' => true,
'pure_ldap_mode' => false,
'hybrid_mode' => true,
```

## 認證流程

1. 使用者輸入 LDAP 帳號密碼
2. 系統向 LDAP 伺服器驗證
3. 認證成功後同步用戶資料到本地
4. 設定 session（使用本地資料庫 ID）

## 優勢

### 安全性
- 不儲存密碼
- LDAP 集中認證

### 便利性
- 本地資料庫查詢效率
- 中文名稱顯示
- 支援複雜關聯查詢

### 靈活性
- 群組到部門映射
- 配置化規則
- 平滑遷移 