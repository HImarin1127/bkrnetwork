<?php
/**
 * LDAP 認證與欄位映射測試
 * 通過實際登入來測試欄位是否正確映射
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/Services/LdapService.php';
require_once 'app/Models/User.php';

echo "=== LDAP 認證與欄位映射測試 ===\n\n";

// 測試帳號資訊
$username = 'ldapnormal';
$password = 'Bk1597531417#';

try {
    // 測試 LDAP 服務
    $ldapService = new LdapService();
    
    echo "1. 測試 LDAP 連接...\n";
    if (!$ldapService->connect()) {
        throw new Exception("LDAP 連接失敗");
    }
    echo "✓ LDAP 連接成功\n\n";
    
    echo "2. 測試 LDAP 認證...\n";
    $authResult = $ldapService->authenticate($username, $password);
    
    if ($authResult) {
        echo "✓ LDAP 認證成功\n\n";
        
        echo "3. 取得使用者詳細資料...\n";
        $userData = $ldapService->getUserData($username);
        
        if ($userData) {
            echo "✓ 成功取得使用者資料\n\n";
            echo "=== LDAP Service 回傳的資料 ===\n";
            foreach ($userData as $key => $value) {
                echo "{$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
            echo "\n";
            
            echo "=== 重點欄位檢查 ===\n";
            echo "✓ ldap_uid: " . ($userData['ldap_uid'] ?? '未設定') . "\n";
            echo "✓ id: " . ($userData['id'] ?? '未設定') . "\n";
            echo "✓ username: " . ($userData['username'] ?? '未設定') . "\n";
            echo "✓ name: " . ($userData['name'] ?? '未設定') . "\n";
            echo "✓ display_name: " . ($userData['display_name'] ?? '未設定') . "\n";
            echo "✓ department: " . ($userData['department'] ?? '未設定') . "\n";
            echo "✓ email: " . ($userData['email'] ?? '未設定') . "\n";
            
            echo "\n=== 預期結果檢查 ===\n";
            echo "id 是否等於 username: " . (($userData['id'] ?? '') === ($userData['username'] ?? '') ? '✓ 是' : '✗ 否') . "\n";
            echo "name 是否為 '陳延勳': " . (($userData['name'] ?? '') === '陳延勳' ? '✓ 是' : '✗ 否 (實際: ' . ($userData['name'] ?? '空') . ')') . "\n";
            echo "department 是否為 '資訊部': " . (($userData['department'] ?? '') === '資訊部' ? '✓ 是' : '✗ 否 (實際: ' . ($userData['department'] ?? '空') . ')') . "\n";
            
        } else {
            echo "✗ 無法取得使用者資料\n";
        }
        
    } else {
        echo "✗ LDAP 認證失敗\n";
    }
    
    echo "\n4. 測試完整的使用者認證流程...\n";
    $userModel = new User();
    $authenticatedUser = $userModel->authenticate($username, $password);
    
    if ($authenticatedUser) {
        echo "✓ 完整認證流程成功\n\n";
        echo "=== 資料庫中的最終使用者資料 ===\n";
        foreach ($authenticatedUser as $key => $value) {
            echo "{$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
        
        echo "\n=== 最終結果檢查 ===\n";
        echo "id 是否等於 username: " . (($authenticatedUser['id'] ?? '') === ($authenticatedUser['username'] ?? '') ? '✓ 是' : '✗ 否') . "\n";
        echo "name 是否為 '陳延勳': " . (($authenticatedUser['name'] ?? '') === '陳延勳' ? '✓ 是' : '✗ 否 (實際: ' . ($authenticatedUser['name'] ?? '空') . ')') . "\n";
        echo "department 是否為 '資訊部': " . (($authenticatedUser['department'] ?? '') === '資訊部' ? '✓ 是' : '✗ 否 (實際: ' . ($authenticatedUser['department'] ?? '空') . ')') . "\n";
        echo "password 是否為 LDAP 標記: " . (($authenticatedUser['password'] ?? '') === 'LDAP_AUTH_ONLY' ? '✓ 是' : '✗ 否') . "\n";
        
    } else {
        echo "✗ 完整認證流程失敗\n";
    }
    
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

echo "\n=== 測試結束 ===\n";
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 認證與欄位映射測試</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin: 10px 0; }
        input[type="text"], input[type="password"] { padding: 8px; width: 200px; }
        button { padding: 8px 16px; background-color: #007cba; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #005a87; }
        .info-box { background-color: #e7f3ff; padding: 15px; border: 1px solid #b3d9ff; margin: 10px 0; }
    </style>
</head>
<body>
    
    <div class="info-box">
        <h2>測試說明</h2>
        <p>此工具通過實際LDAP認證來測試欄位映射是否正確：</p>
        <ul>
            <li><strong>uid</strong> → <strong>id</strong> 和 <strong>username</strong> (都應該是登入帳號)</li>
            <li><strong>gecos</strong> → <strong>name</strong> (應該是：陳延勳)</li>
            <li><strong>departmentNumber</strong> → <strong>department</strong> (應該是：資訊部)</li>
        </ul>
    </div>
    
    <form method="POST">
        <h2>請輸入 LDAP 認證資訊</h2>
        <div class="form-group">
            <label for="username">帳號：</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="例如：ldapnormal">
        </div>
        <div class="form-group">
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" placeholder="輸入密碼">
        </div>
        <div class="form-group">
            <button type="submit">測試認證與欄位映射</button>
        </div>
    </form>
    
</body>
</html> 