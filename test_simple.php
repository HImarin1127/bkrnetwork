<?php
// test_simple.php - 簡單的測試腳本

echo "<h2>PHP 測試</h2>";
echo "<p>PHP 版本：" . phpversion() . "</p>";
echo "<p>當前時間：" . date('Y-m-d H:i:s') . "</p>";

// 檢查擴充套件
echo "<h3>擴充套件檢查</h3>";
echo "<p>LDAP 擴充：" . (extension_loaded('ldap') ? '✅ 已載入' : '❌ 未載入') . "</p>";
echo "<p>PDO 擴充：" . (extension_loaded('pdo') ? '✅ 已載入' : '❌ 未載入') . "</p>";

// 檢查檔案
echo "<h3>檔案檢查</h3>";
$files = [
    'app/Models/User.php',
    'app/Services/LdapService.php',
    'config/ldap.php'
];

foreach ($files as $file) {
    echo "<p>{$file}：" . (file_exists($file) ? '✅ 存在' : '❌ 不存在') . "</p>";
}

// 嘗試載入類別
echo "<h3>類別載入測試</h3>";
try {
    require_once 'app/Models/Model.php';
    echo "<p>Model 基類：✅ 載入成功</p>";
    
    require_once 'app/Models/Database.php';
    echo "<p>Database 類：✅ 載入成功</p>";
    
    require_once 'app/Models/User.php';
    echo "<p>User 類：✅ 載入成功</p>";
    
    require_once 'app/Services/LdapService.php';
    echo "<p>LdapService 類：✅ 載入成功</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤：" . $e->getMessage() . "</p>";
}

echo "<h3>LDAP 連接測試</h3>";
try {
    $ldapService = new LdapService();
    echo "<p>LdapService 實例化：✅ 成功</p>";
    
    // 簡單連接測試
    $connection = ldap_connect('192.168.2.16', 389);
    if ($connection) {
        echo "<p>LDAP 連接：✅ 成功</p>";
        ldap_close($connection);
    } else {
        echo "<p>LDAP 連接：❌ 失敗</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>LDAP 錯誤：" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='ldap_login_test.php'>測試主要登入頁面</a></p>";
?> 