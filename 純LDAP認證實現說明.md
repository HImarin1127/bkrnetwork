# 純 LDAP 認證實現說明

## 🎯 功能概述

實現了真正意義上的純 LDAP 認證，系統不再在本地資料庫中儲存任何用戶帳號資料，完全依賴 LDAP 伺服器進行認證，就像 Google 登入那樣。

## 🔧 主要修改

### 1. LDAP 配置 (`config/ldap.php`)

```php
'auto_create_users' => false,     // 停用自動建立本地使用者帳號
'sync_attributes' => false,       // 停用同步使用者屬性到本地資料庫
'fallback_to_local' => false,     // 停用本地認證回歸
'pure_ldap_mode' => true,         // 啟用純 LDAP 認證模式
```

### 2. 用戶模型 (`app/Models/User.php`)

**主要變更：**
- `authenticate()` 方法：檢測到純 LDAP 模式時，直接返回 LDAP 用戶資料，不進行本地同步
- 為 LDAP 用戶創建虛擬 ID：`ldap_username` 格式
- `isAdmin()` 方法：支援虛擬 ID，從 session 取得角色資訊

### 3. 認證控制器 (`app/Controllers/AuthController.php`)

**主要變更：**
- `login()` 方法：檢測到純 LDAP 用戶時，設定 `auth_mode = 'ldap'`
- 將 LDAP 用戶的詳細資訊儲存到 session 中

### 4. 認證中介軟體 (`app/Middleware/AuthMiddleware.php`)

**主要變更：**
- `getCurrentUser()` 方法：支援 LDAP 模式，從 session 建構用戶資料而非查詢資料庫
- 修正角色欄位使用 `user_role` 保持一致性

### 5. 郵件記錄模型 (`app/Models/MailRecord.php`)

**主要變更：**
- `getByUserId()`: 支援虛擬 ID，使用用戶名進行查詢
- `checkPermission()`: 支援虛擬 ID 權限檢查
- `search()`: 支援虛擬 ID 的搜尋權限控制
- `getIncomingRecords()`: 支援虛擬 ID 的收件記錄查詢
- `searchIncomingRecords()`: 支援虛擬 ID 的收件記錄搜尋
- `getUserName()`: 支援虛擬 ID，從 session 取得用戶名稱

## 🔄 認證流程

### 純 LDAP 模式認證流程：

1. **用戶登入** → LDAP 伺服器驗證
2. **認證成功** → 取得 LDAP 用戶資料
3. **創建虛擬 ID** → `ldap_username` 格式
4. **設定 Session** → 
   - `user_id`: `ldap_username`
   - `auth_mode`: `ldap`
   - 其他 LDAP 屬性資料
5. **權限控制** → 完全基於 session 資料，不查詢本地資料庫

### 虛擬 ID 系統：

- **格式**: `ldap_{username}`
- **用途**: 用於 session 管理和權限控制
- **優點**: 不依賴本地資料庫，但保持系統兼容性

## 🔍 權限控制邏輯

### 管理員權限檢查：
```php
// 虛擬 ID 檢測
if (strpos($userId, 'ldap_') === 0) {
    // 從 session 取得角色
    return $_SESSION['user_role'] === 'admin';
} else {
    // 本地用戶從資料庫查詢
    return $user['role'] === 'admin';
}
```

### 郵件記錄權限控制：
```php
// 虛擬 ID 使用用戶名查詢
if (strpos($userId, 'ldap_') === 0) {
    $username = $_SESSION['username'];
    // 使用 LIKE 查詢相關記錄
    WHERE sender_name LIKE '%{$username}%'
} else {
    // 本地用戶使用 ID 查詢
    WHERE registrar_id = {$userId}
}
```

## 🧪 測試工具

創建了 `test_pure_ldap_auth.php` 測試工具，提供：

1. **配置檢查** - 驗證純 LDAP 模式設定
2. **連接測試** - 測試 LDAP 伺服器連接
3. **認證測試** - 測試用戶認證流程
4. **虛擬 ID 驗證** - 確認使用虛擬 ID
5. **本地資料庫檢查** - 確認不創建本地用戶

## ✅ 使用說明

### 啟用純 LDAP 模式：

1. 確保 LDAP 伺服器正常運作
2. 確認 `config/ldap.php` 中 `pure_ldap_mode = true`
3. 使用測試工具驗證功能
4. 正常登入系統

### 兼容性說明：

- **混合模式支援** - 系統同時支援純 LDAP 和本地用戶
- **現有資料保護** - 不影響現有的本地用戶資料
- **權限系統** - 完全兼容現有的權限控制邏輯

## ⚠️ 注意事項

1. **LDAP 依賴性** - 純 LDAP 用戶完全依賴 LDAP 伺服器，伺服器離線將無法登入
2. **Session 管理** - 用戶資料儲存在 session 中，建議設定適當的 session 過期時間
3. **資料一致性** - LDAP 用戶的角色和權限需要在 LDAP 伺服器端正確配置
4. **備份機制** - 建議保留至少一個本地管理員帳號作為緊急存取方式

## 🔄 回滾說明

如需回到原有模式，只需修改 `config/ldap.php`：

```php
'pure_ldap_mode' => false,
'auto_create_users' => true,
'sync_attributes' => true,
```

系統將自動回到原有的 LDAP + 本地同步模式。

## 📝 測試建議

1. 使用測試工具驗證配置
2. 測試不同角色的 LDAP 用戶登入
3. 驗證權限控制是否正常
4. 測試郵件記錄等功能的權限邏輯
5. 確認登出和重新登入流程

---

**完成日期**: 2024年12月
**版本**: v2.0 純 LDAP 認證模式 