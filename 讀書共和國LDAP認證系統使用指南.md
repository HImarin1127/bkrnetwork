# 📚 讀書共和國 LDAP 認證系統使用指南

> **版本**: v3.0  
> **更新日期**: 2024年  
> **系統狀態**: ✅ 生產環境就緒  

---

## 🌟 系統概述

讀書共和國員工服務網採用 **混合 LDAP 認證模式**，整合企業 LDAP 伺服器與本地資料庫，提供安全、便利的單一登入體驗。

### ✨ 核心特色
- 🔐 **純 LDAP 認證**: 使用者密碼完全由 LDAP 伺服器管理
- 📊 **資料同步**: 自動同步 LDAP 使用者資料到本地資料庫
- 🛡️ **安全防護**: 防止 LDAP 使用者進行本地密碼認證
- 🔄 **混合模式**: 支援 LDAP 使用者與本地使用者並存

---

## 🔧 系統架構

```
LDAP 伺服器 (192.168.2.16)
    ↓ 認證
Web 應用程式 (混合認證控制器)
    ↓ 資料同步
本地資料庫 (使用者資料存儲)
```

### 🏗️ 認證流程
1. **使用者登入** → 嘗試 LDAP 認證
2. **LDAP 認證成功** → 同步使用者資料到本地
3. **建立 Session** → 使用本地使用者 ID 管理狀態
4. **權限檢查** → 基於本地角色進行權限控制

---

## ⚙️ LDAP 設定

### 📝 主要配置檔案
**位置**: `config/ldap.php`

```php
return [
    'enabled' => true,                    // 啟用 LDAP 認證
    'server' => '192.168.2.16',         // LDAP 伺服器位址
    'port' => 389,                       // LDAP 連接埠
    'base_dn' => 'dc=bookrep,dc=com,dc=tw',
    'user_search_base' => 'cn=users,dc=bookrep,dc=com,dc=tw',
    'admin_username' => 'uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw',
    'admin_password' => 'Bk22181417#',
    
    // 混合模式設定
    'hybrid_mode' => true,               // 啟用混合模式
    'auto_create_users' => true,         // 自動建立使用者
    'sync_attributes' => true,           // 同步使用者屬性
    'fallback_to_local' => false,       // 停用本地認證回歸
    
    // 直接綁定模式（權限受限時使用）
    'direct_bind_mode' => true,
    'allow_self_query' => true,
];
```

### 🔐 安全特色
- **密碼不存儲**: LDAP 使用者密碼欄位設為 `LDAP_AUTH_ONLY`
- **認證分離**: LDAP 使用者無法使用本地密碼認證
- **權限隔離**: 清楚區分 LDAP 與本地使用者

---

## 🛠️ 系統工具

### 📋 保留的測試工具

#### 1. **資料庫結構檢查**
```bash
http://yourdomain.com/check_db_structure.php
```
- 檢查資料庫結構完整性
- 驗證 LDAP 相關欄位是否正確設置

#### 2. **完整認證測試**
```bash
http://yourdomain.com/test_complete_auth.php
```
- 測試完整的 LDAP 認證流程
- 驗證使用者資料同步功能
- 檢查資料庫記錄正確性

#### 3. **LDAP 認證測試**
```bash
http://yourdomain.com/test_ldap_auth.php
```
- 測試 LDAP 伺服器連接
- 驗證使用者認證功能
- 檢查屬性對應設定

#### 4. **純 LDAP 認證測試**
```bash
http://yourdomain.com/test_pure_ldap_auth.php
```
- 測試純 LDAP 認證模式
- 驗證無本地密碼存儲
- 確認安全性設定

---

## 🏥 故障排除

### ❌ 常見問題與解決方案

#### 1. **無法連接 LDAP 伺服器**
```
錯誤: 無法連接到 LDAP 伺服器
解決: 
- 檢查網路連通性: ping 192.168.2.16
- 確認 LDAP 服務運行狀態
- 檢查防火牆設定（埠 389）
```

#### 2. **認證失敗但帳號密碼正確**
```
錯誤: LDAP 認證失敗
解決:
- 檢查服務帳號權限（ldapuser）
- 確認使用者搜尋路徑設定
- 啟用除錯模式查看詳細日誌
```

#### 3. **使用者資料未同步**
```
錯誤: 登入成功但資料不完整
解決:
- 檢查屬性對應設定（config/ldap.php）
- 確認 auto_create_users 和 sync_attributes 已啟用
- 檢查資料庫 users 表結構
```

#### 4. **本地使用者無法登入**
```
錯誤: 本地使用者登入失敗
解決:
- 確認 fallback_to_local 設定
- 檢查使用者 auth_source 欄位值
- 驗證密碼雜湊格式
```

### 🔍 除錯模式
```php
// 在 config/ldap.php 中啟用
'debug' => true,

// 查看日誌檔案
tail -f logs/ldap.log
```

---

## 📊 資料庫結構

### 👥 users 表（重要欄位）
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),              -- 顯示名稱（中文名字）
    name VARCHAR(100),                  -- 全名
    email VARCHAR(150),                 -- 電子郵件
    department VARCHAR(100),            -- 部門
    phone VARCHAR(50),                  -- 電話
    title VARCHAR(100),                 -- 職稱
    role ENUM('admin', 'user'),         -- 系統角色
    status ENUM('active', 'inactive'),  -- 帳號狀態
    auth_source ENUM('local', 'ldap'),  -- 認證來源
    password VARCHAR(255),              -- 密碼（LDAP用戶為'LDAP_AUTH_ONLY'）
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 🔑 密碼欄位說明
- **LDAP 使用者**: `password = 'LDAP_AUTH_ONLY'`
- **本地使用者**: `password = bcrypt_hash`

---

## 🚀 部署檢查清單

### ✅ 系統就緒檢查
- [ ] LDAP 伺服器連通性測試
- [ ] 服務帳號權限驗證
- [ ] 資料庫結構檢查
- [ ] 屬性對應設定確認
- [ ] 安全性設定驗證
- [ ] 測試使用者登入驗證

### ✅ 效能優化
- [ ] LDAP 連接逾時設定
- [ ] 資料庫索引優化
- [ ] Session 管理設定
- [ ] 錯誤日誌監控

---

## 🔧 維護作業

### 📅 定期維護
1. **每週**: 檢查 LDAP 連接狀態
2. **每月**: 清理過期 Session 和日誌
3. **每季**: 檢查使用者資料同步狀況
4. **每年**: 更新服務帳號密碼

### 📝 資料同步
```php
// 手動同步特定使用者
$user = new User();
$ldapData = $ldapService->getUserData($username);
$user->syncLdapUser($ldapData);
```

### 🔄 批量操作
```sql
-- 清理 LDAP 使用者密碼欄位
UPDATE users 
SET password = 'LDAP_AUTH_ONLY' 
WHERE auth_source = 'ldap' AND password != 'LDAP_AUTH_ONLY';
```

---

## 📞 技術支援

### 🆘 緊急聯絡
- **資訊部門**: 分機 XXX
- **系統管理員**: admin@bookrep.com.tw

### 📚 相關文件
- `LDAP整合安裝指南.md` - 詳細安裝步驟
- `混合LDAP認證模式說明.md` - 認證模式說明
- `純LDAP認證實現說明.md` - 技術實現細節
- `管理者操作指南.md` - 管理功能說明

---

## 📈 版本歷史

### v3.0 (當前版本)
- ✅ 修正 LDAP 使用者密碼儲存問題
- ✅ 實現純 LDAP 認證模式
- ✅ 強化安全性設定
- ✅ 優化使用者資料同步
- ✅ 清理測試和除錯檔案

### v2.0
- 🔧 基礎 LDAP 認證整合
- 🔧 混合認證模式實現
- 🔧 使用者資料同步機制

### v1.0
- 🎯 本地認證系統
- 🎯 基礎 MVC 架構
- 🎯 使用者管理功能

---

**🎉 系統已就緒，請聯繫技術團隊進行最終驗收！** 