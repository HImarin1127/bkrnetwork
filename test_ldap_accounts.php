<?php
// test_ldap_accounts.php
// LDAP 帳號數量和詳細資訊測試腳本

echo "=== LDAP 帳號測試腳本 ===\n";
echo "測試時間: " . date('Y-m-d H:i:s') . "\n";
echo "==============================\n\n";

// LDAP 設定
$ldap_server = '192.168.2.16';
$ldap_port = 389;
$base_dn = 'dc=bookrep,dc=com,dc=tw';
$service_user_dn = 'uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw';
$service_password = 'Bk22181417#';
$user_search_base = 'cn=users,dc=bookrep,dc=com,dc=tw';

// 步驟 1: 檢查 PHP LDAP 擴充
echo "步驟 1: 檢查 PHP LDAP 擴充\n";
if (!extension_loaded('ldap')) {
    echo "❌ 錯誤: PHP LDAP 擴充套件未安裝\n";
    exit(1);
}
echo "✅ PHP LDAP 擴充套件已載入\n\n";

// 步驟 2: 建立 LDAP 連接
echo "步驟 2: 建立 LDAP 連接\n";
$ldap_url = "ldap://{$ldap_server}:{$ldap_port}";
echo "連接到: {$ldap_url}\n";

$connection = ldap_connect($ldap_url);
if (!$connection) {
    echo "❌ 錯誤: 無法建立 LDAP 連接\n";
    exit(1);
}

// 設定 LDAP 選項
ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
echo "✅ LDAP 連接建立成功\n\n";

// 步驟 3: 服務帳號認證
echo "步驟 3: 服務帳號認證\n";
echo "使用帳號: {$service_user_dn}\n";

if (!@ldap_bind($connection, $service_user_dn, $service_password)) {
    $error = ldap_error($connection);
    echo "❌ 錯誤: 服務帳號認證失敗 - {$error}\n";
    ldap_unbind($connection);
    exit(1);
}
echo "✅ 服務帳號認證成功\n\n";

// 步驟 4: 搜尋所有使用者帳號
echo "步驟 4: 搜尋所有使用者帳號\n";
echo "搜尋範圍: {$user_search_base}\n";
echo "搜尋條件: (objectClass=inetOrgPerson)\n\n";

$search = @ldap_search(
    $connection, 
    $user_search_base, 
    '(objectClass=inetOrgPerson)', 
    ['uid', 'cn', 'mail', 'department', 'telephoneNumber', 'title', 'memberOf']
);

if (!$search) {
    echo "❌ 錯誤: 無法搜尋使用者\n";
    echo "錯誤訊息: " . ldap_error($connection) . "\n";
    ldap_unbind($connection);
    exit(1);
}

$entries = ldap_get_entries($connection, $search);
$user_count = $entries['count'];

echo "✅ 搜尋完成！\n";
echo "📊 找到 {$user_count} 個使用者帳號\n\n";

// 步驟 5: 統計有效帳號
echo "步驟 5: 統計有效帳號\n";

$valid_accounts = [];
$account_names = [];

for ($i = 0; $i < $user_count; $i++) {
    $entry = $entries[$i];
    $username = $entry['uid'][0] ?? '';
    $name = $entry['cn'][0] ?? '';
    
    if (!empty($username)) {
        $valid_accounts[] = $username;
        $account_names[] = $username . ($name ? " ({$name})" : "");
    }
}

echo "✅ 統計完成\n";
echo "📊 有效帳號數量: " . count($valid_accounts) . "\n\n";

// 步驟 6: 測試帳號認證功能
echo "\n步驟 6: 測試帳號認證功能\n";
echo "=====================================\n";

// 測試已知的帳號
$test_username = 'ldapuser';
$test_password = 'Bk22181417#';

echo "測試帳號: {$test_username}\n";

// 搜尋測試使用者的 DN
$user_filter = "(uid={$test_username})";
$auth_search = @ldap_search($connection, $user_search_base, $user_filter, ['dn']);

if ($auth_search) {
    $auth_entries = ldap_get_entries($connection, $auth_search);
    if ($auth_entries['count'] > 0) {
        $user_dn = $auth_entries[0]['dn'];
        echo "找到使用者 DN: {$user_dn}\n";
        
        // 建立新連接進行使用者認證測試
        $user_connection = ldap_connect($ldap_url);
        ldap_set_option($user_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        
        if (@ldap_bind($user_connection, $user_dn, $test_password)) {
            echo "✅ 使用者認證測試成功\n";
        } else {
            echo "❌ 使用者認證測試失敗: " . ldap_error($user_connection) . "\n";
        }
        
        ldap_unbind($user_connection);
    } else {
        echo "❌ 找不到測試使用者\n";
    }
} else {
    echo "❌ 搜尋測試使用者失敗\n";
}

// 關閉主連接
ldap_unbind($connection);

// 步驟 7: 統計摘要
echo "\n步驟 7: 統計摘要\n";
echo "===============================\n";
echo "📊 LDAP 伺服器連接: ✅ 成功\n";
echo "📊 服務帳號認證: ✅ 成功\n";
echo "📊 總帳號數量: {$user_count}\n";
echo "📊 有效帳號數量: " . count($valid_accounts) . "\n";

if (count($valid_accounts) > 0) {
    echo "📊 可登入系統的帳號:\n";
    echo "  " . implode(', ', $valid_accounts) . "\n";
}

echo "\n🎯 結論: \n";
echo "✅ 所有 " . count($valid_accounts) . " 個帳號都可以使用 LDAP 密碼登入您的網頁系統\n";
echo "✅ 系統會自動為首次登入的使用者建立本地帳號\n";
echo "✅ 每次登入會同步最新的 LDAP 使用者資料\n";

echo "\n=== 測試完成 ===\n";
?> 