<?php
// config/ldap.php
// LDAP 認證配置檔案

/**
 * LDAP 伺服器連接與認證設定
 * 
 * 配置 LDAP 伺服器連接參數、使用者搜尋設定和屬性對應
 * 支援 Active Directory 和標準 LDAP 伺服器
 */

return [
    // ========================================
    // LDAP 伺服器基本連接設定
    // ========================================
    
    'enabled' => true,
    // 啟用 LDAP 認證，設為 false 將回歸傳統認證方式
    
    'server' => '192.168.2.16',
    // LDAP 伺服器 IP 位址或主機名稱，請替換為您的 LDAP 伺服器位址
    
    'port' => 389,
    // LDAP 連接埠，標準 LDAP 使用 389，LDAPS (SSL) 使用 636
    
    'use_ssl' => false,
    // 是否使用 SSL 加密連接，生產環境建議設為 true
    
    'use_tls' => false,
    // 是否使用 TLS 加密，與 use_ssl 擇一使用
    
    // ========================================
    // LDAP 認證設定
    // ========================================
    
    'base_dn' => 'dc=bookrep,dc=com,dc=tw',
    // 基礎搜尋 DN (Distinguished Name)，請依據您的 LDAP 結構調整
    
    'admin_username' => 'uid=ldapnormal,cn=users,dc=bookrep,dc=com,dc=tw',
    // 服務帳號 DN，用於連接 LDAP 進行使用者搜尋（可使用一般用戶帳號，只需讀取權限）
    
    'admin_password' => 'Bk1597531417#',
    // 服務帳號密碼，請替換為您的 LDAP 服務帳號密碼
    
    'user_search_base' => 'cn=users,dc=bookrep,dc=com,dc=tw',
    // 使用者搜尋基礎 DN，通常為使用者所在的組織單位
    
    'user_filter' => '(&(objectClass=inetOrgPerson)(uid={username}))',
    // 使用者搜尋過濾器，{username} 會被實際的使用者名稱替換
    // 常見過濾器：
    // - OpenLDAP: '(&(objectClass=inetOrgPerson)(uid={username}))'
    // - Active Directory: '(&(objectClass=user)(sAMAccountName={username}))'
    
    // ========================================
    // 使用者屬性對應
    // ========================================
    
    'attributes' => [
        // LDAP 屬性與系統欄位的對應關係
        'username' => 'uid',           // 使用者名稱屬性 (uid 或 sAMAccountName)
        'name' => 'cn',               // 顯示名稱屬性 (cn 或 displayName)
        'email' => 'mail',            // 電子郵件屬性
        'department' => 'department',  // 部門屬性 (department 或 ou)
        'phone' => 'telephoneNumber', // 電話號碼屬性
        'title' => 'title',           // 職稱屬性
    ],
    
    // ========================================
    // 權限控制設定
    // ========================================
    
    'admin_groups' => [
        // 管理員群組 DN 列表，屬於這些群組的使用者將擁有管理員權限
        'cn=administrators,ou=groups,dc=bookrep,dc=com,dc=tw',
        'cn=it_admins,ou=groups,dc=bookrep,dc=com,dc=tw',
    ],
    
    'allowed_groups' => [
        // 允許登入的群組列表，空陣列表示允許所有認證成功的使用者
        // 'cn=employees,ou=groups,dc=bookrep,dc=com,dc=tw',
    ],
    
    // ========================================
    // 進階設定
    // ========================================
    
    'timeout' => 10,
    // LDAP 連接逾時時間（秒）
    
        'auto_create_users' => true,
    // 啟用自動建立本地使用者帳號 - LDAP認證成功後自動在本地創建用戶

    'sync_attributes' => true,
    // 啟用同步使用者屬性到本地資料庫 - 保持資料同步

    'fallback_to_local' => false,
    // 停用本地認證回歸，純 LDAP 認證模式
    
    'debug' => false,
    // 除錯模式，開啟後會記錄詳細的 LDAP 操作日誌
    
    'direct_bind_mode' => true,
    // 直接綁定模式：當服務帳號搜尋權限有限時，使用構造的 DN 進行直接認證
]; 