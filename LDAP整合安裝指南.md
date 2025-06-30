# 🔗 LDAP 整合安裝指南

讀書共和國內部系統 LDAP 認證整合完全指南

## 📋 目錄

1. [系統需求](#系統需求)
2. [安裝步驟](#安裝步驟)
3. [配置設定](#配置設定)
4. [測試連接](#測試連接)
5. [進階設定](#進階設定)
6. [常見問題](#常見問題)
7. [維護管理](#維護管理)

---

## 🛠️ 系統需求

### PHP 擴展需求
```bash
# 檢查 PHP LDAP 擴展是否已安裝
php -m | grep ldap

# 如果未安裝，請執行以下命令（根據您的系統）：
# Ubuntu/Debian:
sudo apt-get install php-ldap

# CentOS/RHEL:
sudo yum install php-ldap

# Windows (XAMPP):
# 編輯 php.ini，取消註解：extension=ldap
```

### 權限要求
- 網頁伺服器對 `config/` 目錄的讀取權限
- 對 `logs/` 目錄的寫入權限（除錯模式）
- 資料庫修改權限

---

## 🚀 安裝步驟

### 步驟 1：更新資料庫結構

執行以下 SQL 腳本來新增 LDAP 支援欄位：

```sql
-- 在 phpMyAdmin 或 MySQL 命令列中執行
SOURCE database_ldap_update.sql;

-- 或手動執行：
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS auth_source VARCHAR(20) DEFAULT 'local' COMMENT '認證來源：local=本地認證, ldap=LDAP認證',
ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT '' COMMENT '部門',
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT '' COMMENT '電話號碼',
ADD COLUMN IF NOT EXISTS title VARCHAR(100) DEFAULT '' COMMENT '職稱',
ADD COLUMN IF NOT EXISTS last_ldap_sync TIMESTAMP NULL COMMENT '最後 LDAP 同步時間';

CREATE INDEX IF NOT EXISTS idx_users_auth_source ON users(auth_source);
CREATE INDEX IF NOT EXISTS idx_users_department ON users(department);
```

### 步驟 2：配置 LDAP 設定

編輯 `config/ldap.php` 檔案，更新您的 LDAP 伺服器資訊：

```php
return [
    // 🔧 基本連接設定
    'enabled' => true,                          // 啟用 LDAP 認證
    'server' => '您的LDAP伺服器IP',               // 例如：'192.168.1.100'
    'port' => 389,                              // 標準 LDAP 埠號
    'use_ssl' => false,                         // 生產環境建議設為 true
    
    // 🎯 認證設定
    'base_dn' => 'dc=您的公司,dc=local',           // 例如：'dc=bkr,dc=local'
    'admin_username' => 'cn=admin,dc=您的公司,dc=local',
    'admin_password' => '您的管理員密碼',
    'user_search_base' => 'ou=users,dc=您的公司,dc=local',
    'user_filter' => '(&(objectClass=inetOrgPerson)(uid={username}))',
    
    // 👥 屬性對應
    'attributes' => [
        'username' => 'uid',                    // 或 'sAMAccountName' (AD)
        'name' => 'cn',                         // 或 'displayName' (AD)
        'email' => 'mail',
        'department' => 'department',
        'phone' => 'telephoneNumber',
        'title' => 'title',
    ],
    
    // 🔒 權限控制
    'admin_groups' => [
        'cn=administrators,ou=groups,dc=您的公司,dc=local',
    ],
    
    // ⚙️ 進階設定
    'auto_create_users' => true,                // 自動建立 LDAP 使用者
    'sync_attributes' => true,                  // 同步使用者屬性
    'fallback_to_local' => true,                // 允許本地認證備援
    'debug' => false,                           // 開啟以記錄詳細日誌
];
```

### 步驟 3：測試 LDAP 連接

1. 在瀏覽器中開啟測試工具：
   ```
   http://您的網站域名/test_ldap_connection.php
   ```

2. 執行以下測試：
   - **基本連接測試**：確保能連接到 LDAP 伺服器
   - **配置檢查**：驗證所有必要設定都已配置
   - **使用者認證測試**：測試實際使用者登入

### 步驟 4：驗證整合

1. 嘗試使用 LDAP 帳號登入系統
2. 檢查使用者資料是否正確同步
3. 確認權限分配是否正常

---

## ⚙️ 配置設定

### 🔧 Active Directory 配置範例

如果您使用的是 Microsoft Active Directory：

```php
'user_filter' => '(&(objectClass=user)(sAMAccountName={username}))',
'attributes' => [
    'username' => 'sAMAccountName',
    'name' => 'displayName',
    'email' => 'mail',
    'department' => 'department',
    'phone' => 'telephoneNumber',
    'title' => 'title',
],
```

### 🔧 OpenLDAP 配置範例

如果您使用的是 OpenLDAP：

```php
'user_filter' => '(&(objectClass=inetOrgPerson)(uid={username}))',
'attributes' => [
    'username' => 'uid',
    'name' => 'cn',
    'email' => 'mail',
    'department' => 'departmentNumber',
    'phone' => 'telephoneNumber',
    'title' => 'title',
],
```

### 🔒 SSL/TLS 設定

生產環境建議啟用加密連接：

```php
'use_ssl' => true,      // 使用 LDAPS (Port 636)
// 或
'use_tls' => true,      // 使用 StartTLS (Port 389)
```

---

## 🧪 測試連接

### 使用測試工具

1. **基本連接測試**
   - 檢查 LDAP 伺服器連通性
   - 驗證管理員帳號認證
   - 測試搜尋功能

2. **使用者認證測試**
   - 輸入測試使用者帳號密碼
   - 檢查認證結果和使用者資料
   - 驗證群組權限

3. **配置檢查**
   - 檢查所有必要配置項目
   - 驗證 PHP 擴展安裝
   - 顯示目前配置資訊

### 手動測試指令

```bash
# 測試 LDAP 連接（Linux/Mac）
ldapsearch -x -H ldap://您的LDAP伺服器 -D "cn=admin,dc=公司,dc=local" -W -b "dc=公司,dc=local"

# 測試使用者搜尋
ldapsearch -x -H ldap://您的LDAP伺服器 -D "cn=admin,dc=公司,dc=local" -W -b "ou=users,dc=公司,dc=local" "(uid=測試使用者)"
```

---

## 🔧 進階設定

### 自動使用者同步

啟用自動同步功能可在每次登入時更新使用者資料：

```php
'sync_attributes' => true,
'auto_create_users' => true,
```

### 權限群組管理

設定管理員群組和允許登入的群組：

```php
'admin_groups' => [
    'cn=IT部門,ou=groups,dc=公司,dc=local',
    'cn=系統管理員,ou=groups,dc=公司,dc=local',
],
'allowed_groups' => [
    'cn=員工,ou=groups,dc=公司,dc=local',
    // 空陣列表示允許所有認證成功的使用者
],
```

### 除錯模式

開啟除錯模式可記錄詳細的 LDAP 操作日誌：

```php
'debug' => true,
```

日誌檔案位置：`logs/ldap.log`

### 回歸認證設定

允許 LDAP 認證失敗時使用本地認證：

```php
'fallback_to_local' => true,
```

這對於緊急情況下的管理員存取很重要。

---

## ❓ 常見問題

### Q: PHP LDAP 擴展安裝失敗？

**A:** 檢查您的 PHP 版本和套件管理員：

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install php-ldap php-dev

# CentOS/RHEL
sudo yum install php-ldap php-devel

# 重啟網頁伺服器
sudo systemctl restart apache2  # 或 nginx
```

### Q: 連接測試失敗？

**A:** 檢查以下項目：

1. **網路連通性**：確保伺服器能連接到 LDAP 伺服器
2. **防火牆設定**：開啟 389 (LDAP) 或 636 (LDAPS) 埠號
3. **憑證設定**：檢查管理員帳號和密碼
4. **DN 格式**：確認 DN 格式正確

### Q: 使用者認證成功但無法登入？

**A:** 可能的原因：

1. **群組權限**：檢查 `allowed_groups` 設定
2. **屬性對應**：確認屬性對應設定正確
3. **自動建立**：檢查 `auto_create_users` 是否啟用

### Q: 如何測試特定使用者？

**A:** 使用測試工具或手動測試：

```php
// 在測試檔案中
require_once 'app/Services/LdapService.php';
$ldap = new LdapService();
$result = $ldap->authenticate('測試使用者', '測試密碼');
var_dump($result);
```

### Q: 如何停用 LDAP 認證？

**A:** 修改配置檔案：

```php
'enabled' => false,
```

系統將自動回歸本地認證。

---

## 🔧 維護管理

### 定期檢查項目

1. **連接測試**：定期執行測試工具確保連接正常
2. **日誌監控**：檢查 `logs/ldap.log` 的錯誤訊息
3. **使用者同步**：確認使用者資料正確同步
4. **權限檢查**：驗證群組權限設定

### 安全建議

1. **啟用加密**：生產環境務必使用 SSL/TLS
2. **權限最小化**：LDAP 管理員帳號只給予必要權限
3. **定期更新**：保持 PHP 和 LDAP 擴展最新版本
4. **備援計畫**：保留本地管理員帳號作為備援

### 效能優化

1. **連接池**：考慮實作 LDAP 連接池
2. **快取機制**：對使用者資料實作適當快取
3. **索引優化**：確保資料庫索引正確建立

### 監控指標

- LDAP 連接成功率
- 認證回應時間
- 使用者同步錯誤數量
- 回歸認證使用次數

---

## 📞 技術支援

如果在整合過程中遇到問題，請：

1. 查看 `logs/ldap.log` 詳細錯誤訊息
2. 使用測試工具診斷問題
3. 檢查伺服器 PHP 錯誤日誌
4. 聯繫系統管理員或開發團隊

---

## 🎉 完成！

恭喜！您已成功整合 LDAP 認證到讀書共和國內部系統。現在員工可以使用其域帳號登入，享受統一身分認證的便利性。

**年薪 300 萬的目標又更近一步了！** 💰✨ 