<?php
// manual_ldap_test.php
// 手動 LDAP 測試腳本

echo "=== 手動 LDAP 連接測試 ===\n";
echo "測試時間: " . date('Y-m-d H:i:s') . "\n\n";

// LDAP 設定 (根據您的截圖)
$ldap_server = '192.168.2.16';
$ldap_port = 389;
$base_dn = 'dc=bookrep,dc=com,dc=tw';
$service_user_dn = 'uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw';
$service_password = 'Bk22181417#';

echo "LDAP 設定:\n";
echo "伺服器: {$ldap_server}:{$ldap_port}\n";
echo "基礎 DN: {$base_dn}\n";
echo "服務帳號: {$service_user_dn}\n";
echo "協定: LDAP v3\n\n";

// 步驟 1: 檢查 PHP LDAP 擴充
echo "步驟 1: 檢查 PHP LDAP 擴充\n";
if (!extension_loaded('ldap')) {
    echo "❌ 錯誤: PHP LDAP 擴充套件未安裝\n";
    echo "請在 XAMPP 控制台啟用 php_ldap 擴充\n";
    exit(1);
}
echo "✅ PHP LDAP 擴充套件已載入\n\n";

// 步驟 2: 建立 LDAP 連接
echo "步驟 2: 建立 LDAP 連接\n";
$ldap_url = "ldap://{$ldap_server}:{$ldap_port}";
echo "嘗試連接: {$ldap_url}\n";

$connection = ldap_connect($ldap_url);
if (!$connection) {
    echo "❌ 錯誤: 無法建立 LDAP 連接\n";
    exit(1);
}
echo "✅ LDAP 連接建立成功\n\n";

// 步驟 3: 設定 LDAP 選項
echo "步驟 3: 設定 LDAP 選項\n";
ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
echo "✅ LDAP 選項設定完成 (協定版本 3)\n\n";

// 步驟 4: 服務帳號認證
echo "步驟 4: 服務帳號認證\n";
echo "嘗試綁定: {$service_user_dn}\n";

if (!@ldap_bind($connection, $service_user_dn, $service_password)) {
    $error = ldap_error($connection);
    echo "❌ 錯誤: 服務帳號認證失敗\n";
    echo "錯誤訊息: {$error}\n";
    echo "\n可能的解決方案:\n";
    echo "1. 檢查帳號密碼是否正確\n";
    echo "2. 檢查 DN 格式是否正確\n";
    echo "3. 檢查帳號是否被鎖定或停用\n";
    ldap_unbind($connection);
    exit(1);
}
echo "✅ 服務帳號認證成功\n\n";

// 步驟 5: 測試基礎搜尋
echo "步驟 5: 測試基礎搜尋\n";
echo "搜尋範圍: {$base_dn}\n";

$search = @ldap_search($connection, $base_dn, '(objectClass=*)', ['dn'], 0, 5);
if (!$search) {
    echo "❌ 錯誤: 無法進行 LDAP 搜尋\n";
    echo "錯誤訊息: " . ldap_error($connection) . "\n";
} else {
    $entries = ldap_get_entries($connection, $search);
    echo "✅ 基礎搜尋成功，找到 {$entries['count']} 個項目\n";
    
    // 顯示前幾個 DN
    if ($entries['count'] > 0) {
        echo "前幾個項目的 DN:\n";
        for ($i = 0; $i < min(3, $entries['count']); $i++) {
            echo "  - {$entries[$i]['dn']}\n";
        }
    }
}
echo "\n";

// 步驟 6: 搜尋使用者
echo "步驟 6: 搜尋使用者\n";
$user_search_base = 'cn=users,dc=bookrep,dc=com,dc=tw';
echo "使用者搜尋範圍: {$user_search_base}\n";

$user_search = @ldap_search($connection, $user_search_base, '(objectClass=*)', ['dn', 'uid', 'cn'], 0, 10);
if (!$user_search) {
    echo "❌ 錯誤: 無法搜尋使用者\n";
    echo "錯誤訊息: " . ldap_error($connection) . "\n";
} else {
    $user_entries = ldap_get_entries($connection, $user_search);
    echo "✅ 使用者搜尋成功，找到 {$user_entries['count']} 個使用者\n";
    
    if ($user_entries['count'] > 0) {
        echo "找到的使用者:\n";
        for ($i = 0; $i < min(5, $user_entries['count']); $i++) {
            $uid = isset($user_entries[$i]['uid'][0]) ? $user_entries[$i]['uid'][0] : '未知';
            $cn = isset($user_entries[$i]['cn'][0]) ? $user_entries[$i]['cn'][0] : '未知';
            echo "  - 帳號: {$uid}, 名稱: {$cn}, DN: {$user_entries[$i]['dn']}\n";
        }
    }
}
echo "\n";

// 步驟 7: 測試特定使用者認證
echo "步驟 7: 測試使用者認證功能\n";
$test_username = 'ldapuser';
$test_password = 'Bk22181417#';

echo "測試使用者: {$test_username}\n";

// 搜尋使用者 DN
$user_filter = "(uid={$test_username})";
$auth_search = @ldap_search($connection, $user_search_base, $user_filter, ['dn']);

if (!$auth_search) {
    echo "❌ 錯誤: 無法搜尋測試使用者\n";
} else {
    $auth_entries = ldap_get_entries($connection, $auth_search);
    if ($auth_entries['count'] === 0) {
        echo "❌ 錯誤: 找不到測試使用者 '{$test_username}'\n";
    } else {
        $user_dn = $auth_entries[0]['dn'];
        echo "找到使用者 DN: {$user_dn}\n";
        
        // 測試使用者認證
        $user_connection = ldap_connect($ldap_url);
        ldap_set_option($user_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        
        if (@ldap_bind($user_connection, $user_dn, $test_password)) {
            echo "✅ 使用者認證測試成功\n";
        } else {
            echo "❌ 使用者認證測試失敗: " . ldap_error($user_connection) . "\n";
        }
        
        ldap_unbind($user_connection);
    }
}

// 關閉連接
ldap_unbind($connection);

echo "\n=== 測試完成 ===\n";
echo "如果所有步驟都顯示 ✅，表示 LDAP 整合設定正確\n";
echo "如果有任何 ❌，請根據錯誤訊息進行調整\n";
?> 