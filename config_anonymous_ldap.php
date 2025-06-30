<?php
// config/ldap_anonymous.php - 匿名LDAP配置
// 複製自原始config/ldap.php，但使用匿名綁定

return [
    // ========================================
    // LDAP 伺服器基本連接設定
    // ========================================
    
    'enabled' => true,
    'server' => '192.168.2.16',
    'port' => 389,
    'use_ssl' => false,
    'use_tls' => false,
    
    // ========================================
    // LDAP 認證設定 - 使用匿名綁定
    // ========================================
    
    'base_dn' => 'dc=bookrep,dc=com,dc=tw',
    
    // 匿名綁定 - 空用戶名和密碼
    'admin_username' => '',
    'admin_password' => '',
    
    'user_search_base' => 'cn=users,dc=bookrep,dc=com,dc=tw',
    'user_filter' => '(&(objectClass=inetOrgPerson)(uid={username}))',
    
    // ========================================
    // 使用者屬性對應
    // ========================================
    
    'attributes' => [
        'username' => 'uid',
        'name' => 'cn',
        'email' => 'mail',
        'department' => 'department',
        'phone' => 'telephoneNumber',
        'title' => 'title',
    ],
    
    // ========================================
    // 權限控制設定
    // ========================================
    
    'admin_groups' => [
        'cn=administrators,ou=groups,dc=bookrep,dc=com,dc=tw',
        'cn=it_admins,ou=groups,dc=bookrep,dc=com,dc=tw',
    ],
    
    'allowed_groups' => [],
    
    // ========================================
    // 進階設定
    // ========================================
    
    'timeout' => 10,
    'auto_create_users' => true,
    'sync_attributes' => true,
    'fallback_to_local' => false,
    'debug' => false,
]; 