<?php
// 簡單的LDAP調試工具
session_start();

// 檢查是否有POST請求
$testUsername = $_POST['test_username'] ?? '';
$testPassword = $_POST['test_password'] ?? '';
$testResult = null;

if (!empty($testUsername) && !empty($testPassword)) {
    // 執行認證測試
    try {
        require_once __DIR__ . '/app/Models/User.php';
        $userModel = new User();
        $result = $userModel->authenticate($testUsername, $testPassword);
        
        if ($result) {
            $testResult = [
                'success' => true,
                'message' => '認證成功',
                'data' => $result
            ];
        } else {
            $testResult = [
                'success' => false,
                'message' => '認證失敗 - 帳號或密碼錯誤'
            ];
        }
    } catch (Exception $e) {
        $testResult = [
            'success' => false,
            'message' => '認證過程發生錯誤: ' . $e->getMessage()
        ];
    }
}

// 載入LDAP配置
$ldapConfig = require __DIR__ . '/config/ldap.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 調試工具</title>
    <style>
        body { font-family: 'Microsoft JhengHei', sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        input, button { padding: 8px; margin: 5px 0; width: 200px; }
        button { background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 LDAP 調試工具</h1>
        
        <div class="section info">
            <h3>📋 LDAP 配置檢查</h3>
            <p><strong>LDAP啟用:</strong> <?= $ldapConfig['enabled'] ? '✅ 是' : '❌ 否' ?></p>
            <p><strong>伺服器:</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
            <p><strong>基礎DN:</strong> <?= $ldapConfig['base_dn'] ?></p>
            <p><strong>使用者搜尋基礎:</strong> <?= $ldapConfig['user_search_base'] ?></p>
            <p><strong>自動建立使用者:</strong> <?= $ldapConfig['auto_create_users'] ? '是' : '否' ?></p>
            <p><strong>本地認證回歸:</strong> <?= $ldapConfig['fallback_to_local'] ? '是' : '否' ?></p>
        </div>
        
        <div class="section info">
            <h3>🖥️ 系統環境檢查</h3>
            <p><strong>PHP版本:</strong> <?= PHP_VERSION ?></p>
            <p><strong>LDAP擴充:</strong> <?= extension_loaded('ldap') ? '✅ 已載入' : '❌ 未載入' ?></p>
            <p><strong>OpenSSL擴充:</strong> <?= extension_loaded('openssl') ? '✅ 已載入' : '❌ 未載入' ?></p>
        </div>
        
        <div class="section">
            <h3>🧪 帳號認證測試</h3>
            <form method="POST">
                <div>
                    <label>使用者名稱:</label><br>
                    <input type="text" name="test_username" value="<?= htmlspecialchars($testUsername) ?>" placeholder="輸入LDAP帳號">
                </div>
                <div>
                    <label>密碼:</label><br>
                    <input type="password" name="test_password" placeholder="輸入密碼">
                </div>
                <div>
                    <button type="submit">🔍 測試認證</button>
                </div>
            </form>
            
            <?php if ($testResult): ?>
                <div class="<?= $testResult['success'] ? 'success' : 'error' ?>" style="margin-top: 15px;">
                    <h4><?= $testResult['success'] ? '✅ 測試結果: 成功' : '❌ 測試結果: 失敗' ?></h4>
                    <p><?= htmlspecialchars($testResult['message']) ?></p>
                    
                    <?php if ($testResult['success'] && isset($testResult['data'])): ?>
                        <h5>使用者資料:</h5>
                        <pre><?= htmlspecialchars(print_r($testResult['data'], true)) ?></pre>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section info">
            <h3>📚 快速診斷</h3>
            <p>如果認證失敗，可能的原因：</p>
            <ul>
                <li>🔍 <strong>帳號不存在:</strong> 確認帳號是否在正確的OU中</li>
                <li>🔑 <strong>密碼錯誤:</strong> 確認密碼是否正確</li>
                <li>🌐 <strong>連接問題:</strong> 檢查網路連線和LDAP伺服器狀態</li>
                <li>⚙️ <strong>配置錯誤:</strong> 檢查config/ldap.php中的設定</li>
                <li>🚫 <strong>權限問題:</strong> 確認帳號有登入權限</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="login" style="text-decoration: none;">
                <button type="button">🔐 返回登入頁面</button>
            </a>
        </div>
    </div>
</body>
</html> 