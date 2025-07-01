<?php
/**
 * 純 LDAP 認證測試工具
 * 
 * 用於測試純 LDAP 認證功能，確保系統能夠在不建立本地用戶的情況下
 * 成功進行 LDAP 認證並設定正確的 session 資料
 */

// 啟動 session
session_start();

// 設定基本路徑
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Services/LdapService.php';

echo "<h1>純 LDAP 認證測試工具</h1>\n";
echo "<hr>\n";

// 檢查配置
echo "<h2>1. 檢查 LDAP 配置</h2>\n";
$ldapConfig = require __DIR__ . '/config/ldap.php';

echo "<p><strong>LDAP 啟用:</strong> " . ($ldapConfig['enabled'] ? '是' : '否') . "</p>\n";
echo "<p><strong>純 LDAP 模式:</strong> " . (($ldapConfig['pure_ldap_mode'] ?? false) ? '是' : '否') . "</p>\n";
echo "<p><strong>自動建立用戶:</strong> " . (($ldapConfig['auto_create_users'] ?? false) ? '是' : '否') . "</p>\n";
echo "<p><strong>同步屬性:</strong> " . (($ldapConfig['sync_attributes'] ?? false) ? '是' : '否') . "</p>\n";
echo "<p><strong>回歸本地認證:</strong> " . (($ldapConfig['fallback_to_local'] ?? false) ? '是' : '否') . "</p>\n";

// 測試 LDAP 連接
echo "<h2>2. 測試 LDAP 連接</h2>\n";
try {
    $ldapService = new LdapService();
    $connectionTest = $ldapService->testConnection();
    
    if ($connectionTest['success']) {
        echo "<p style='color: green;'>✓ LDAP 連接測試成功</p>\n";
        foreach ($connectionTest['details'] as $detail) {
            echo "<p>$detail</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ LDAP 連接測試失敗: {$connectionTest['message']}</p>\n";
        foreach ($connectionTest['details'] as $detail) {
            echo "<p>$detail</p>\n";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ LDAP 服務初始化失敗: " . $e->getMessage() . "</p>\n";
}

// 處理認證測試表單
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_auth'])) {
    $testUsername = $_POST['username'] ?? '';
    $testPassword = $_POST['password'] ?? '';
    
    if (!empty($testUsername) && !empty($testPassword)) {
        echo "<h2>3. 認證測試結果</h2>\n";
        
        try {
            $userModel = new User();
            $result = $userModel->authenticate($testUsername, $testPassword);
            
            if ($result) {
                echo "<p style='color: green;'>✓ 認證成功！</p>\n";
                echo "<h3>用戶資料：</h3>\n";
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
                foreach ($result as $key => $value) {
                    echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>\n";
                }
                echo "</table>\n";
                
                // 檢查是否為虛擬 ID
                if (strpos($result['id'], 'ldap_') === 0) {
                    echo "<p style='color: blue;'>✓ 使用虛擬 ID，確認為純 LDAP 模式</p>\n";
                } else {
                    echo "<p style='color: orange;'>⚠️ 使用數據庫 ID，可能不是純 LDAP 模式</p>\n";
                }
                
                // 檢查本地資料庫中是否存在該用戶
                if ($userModel->userExists($testUsername)) {
                    echo "<p style='color: orange;'>⚠️ 本地資料庫中存在該用戶記錄</p>\n";
                } else {
                    echo "<p style='color: green;'>✓ 本地資料庫中不存在該用戶記錄，確認為純 LDAP 模式</p>\n";
                }
                
            } else {
                echo "<p style='color: red;'>✗ 認證失敗 - 帳號或密碼錯誤</p>\n";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ 認證過程發生錯誤: " . $e->getMessage() . "</p>\n";
        }
    } else {
        echo "<p style='color: red;'>請輸入完整的帳號和密碼</p>\n";
    }
}

?>

<h2>3. 認證測試</h2>
<form method="POST">
    <table>
        <tr>
            <td><label for="username">LDAP 使用者名稱:</label></td>
            <td><input type="text" id="username" name="username" required></td>
        </tr>
        <tr>
            <td><label for="password">密碼:</label></td>
            <td><input type="password" id="password" name="password" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="test_auth" value="測試認證">
            </td>
        </tr>
    </table>
</form>

<h2>4. 當前 Session 狀態</h2>
<?php
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>已登入用戶:</strong> {$_SESSION['username']} (ID: {$_SESSION['user_id']})</p>\n";
    echo "<p><strong>認證模式:</strong> " . ($_SESSION['auth_mode'] ?? '未設定') . "</p>\n";
    echo "<p><strong>角色:</strong> " . ($_SESSION['user_role'] ?? '未設定') . "</p>\n";
    echo "<p><a href='?logout=1'>登出測試</a></p>\n";
    
    if (isset($_GET['logout'])) {
        session_destroy();
        echo "<script>window.location.href = window.location.href.split('?')[0];</script>";
    }
} else {
    echo "<p>目前未登入</p>\n";
}
?>

<hr>
<p><small>測試完成後，請刪除此測試文件以確保系統安全。</small></p> 