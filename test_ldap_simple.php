<?php
// 簡單的LDAP測試檔案 - 獨立運行，不依賴路由
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
                'message' => '認證成功！',
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

// 獲取LDAP使用者清單
function getLdapUsers($ldapConfig) {
    try {
        if (!$ldapConfig['enabled']) {
            return [];
        }
        
        // 建立LDAP連接
        $server = $ldapConfig['use_ssl'] 
            ? "ldaps://{$ldapConfig['server']}:{$ldapConfig['port']}"
            : "ldap://{$ldapConfig['server']}:{$ldapConfig['port']}";
            
        $connection = ldap_connect($server);
        
        if (!$connection) {
            return [];
        }
        
        // 設定LDAP選項
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $ldapConfig['timeout']);
        
        // 啟用TLS（如果配置）
        if ($ldapConfig['use_tls']) {
            ldap_start_tls($connection);
        }
        
        // 嘗試綁定
        if (!ldap_bind($connection, $ldapConfig['admin_username'], $ldapConfig['admin_password'])) {
            ldap_unbind($connection);
            return [];
        }
        
        // 搜尋使用者
        $searchFilter = '(objectClass=inetOrgPerson)';
        $attributes = ['uid', 'cn', 'mail', 'telephoneNumber', 'department', 'title'];
        
        $search = ldap_search(
            $connection,
            $ldapConfig['user_search_base'],
            $searchFilter,
            $attributes
        );
        
        if (!$search) {
            ldap_unbind($connection);
            return [];
        }
        
        $entries = ldap_get_entries($connection, $search);
        $users = [];
        
        for ($i = 0; $i < $entries['count']; $i++) {
            $entry = $entries[$i];
            $users[] = [
                'uid' => isset($entry['uid'][0]) ? $entry['uid'][0] : '未設定',
                'cn' => isset($entry['cn'][0]) ? $entry['cn'][0] : '未設定',
                'mail' => isset($entry['mail'][0]) ? $entry['mail'][0] : '未設定',
                'department' => isset($entry['department'][0]) ? $entry['department'][0] : '未設定',
                'title' => isset($entry['title'][0]) ? $entry['title'][0] : '未設定',
                'phone' => isset($entry['telephonenumber'][0]) ? $entry['telephonenumber'][0] : '未設定'
            ];
        }
        
        ldap_unbind($connection);
        return $users;
        
    } catch (Exception $e) {
        return [];
    }
}

$ldapUsers = getLdapUsers($ldapConfig);

// 測試LDAP連接
function testLdapConnection($ldapConfig) {
    try {
        require_once __DIR__ . '/app/Services/LdapService.php';
        $ldapService = new LdapService();
        return $ldapService->testConnection();
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'LDAP服務載入失敗: ' . $e->getMessage(),
            'details' => []
        ];
    }
}

$connectionTest = testLdapConnection($ldapConfig);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 簡易測試工具</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .content {
            padding: 2rem;
        }
        
        .section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            background: #f9f9f9;
        }
        
        .section.success {
            background: rgba(212, 237, 218, 0.3);
            border-color: #c3e6cb;
        }
        
        .section.error {
            background: rgba(248, 215, 218, 0.3);
            border-color: #f5c6cb;
        }
        
        .section.info {
            background: rgba(209, 236, 241, 0.3);
            border-color: #bee5eb;
        }
        
        .section h3 {
            margin: 0 0 1rem 0;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            margin-right: 1rem;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 LDAP 簡易測試工具</h1>
            <p>直接測試LDAP連接和認證功能</p>
        </div>
        
        <div class="content">
            <!-- LDAP配置 -->
            <div class="section info">
                <h3>📋 LDAP 配置狀態</h3>
                <p><strong>LDAP啟用:</strong> <?= $ldapConfig['enabled'] ? '✅ 是' : '❌ 否' ?></p>
                <p><strong>伺服器:</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
                <p><strong>搜尋基礎:</strong> <?= $ldapConfig['user_search_base'] ?></p>
            </div>

            <!-- 連接測試 -->
            <?php if ($connectionTest): ?>
                <div class="section <?= $connectionTest['success'] ? 'success' : 'error' ?>">
                    <h3>🔗 LDAP 連接測試</h3>
                    <p><strong><?= $connectionTest['success'] ? '✅' : '❌' ?> <?= htmlspecialchars($connectionTest['message']) ?></strong></p>
                    <?php if (!empty($connectionTest['details'])): ?>
                        <ul>
                            <?php foreach ($connectionTest['details'] as $detail): ?>
                                <li><?= htmlspecialchars($detail) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- 使用者清單 -->
            <?php if (!empty($ldapUsers)): ?>
                <div class="section success">
                    <h3>👥 LDAP 使用者清單 (找到 <?= count($ldapUsers) ?> 個帳號)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>帳號 (uid)</th>
                                <th>姓名 (cn)</th>
                                <th>郵件</th>
                                <th>部門</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ldapUsers as $user): ?>
                                <tr>
                                    <td><strong style="color: #C8102E;"><?= htmlspecialchars($user['uid']) ?></strong></td>
                                    <td><?= htmlspecialchars($user['cn']) ?></td>
                                    <td><?= htmlspecialchars($user['mail']) ?></td>
                                    <td><?= htmlspecialchars($user['department']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;"
                                                onclick="useAccount('<?= htmlspecialchars($user['uid']) ?>')">
                                            使用此帳號
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- 認證測試 -->
            <div class="section">
                <h3>🧪 帳號認證測試</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="test_username">LDAP 帳號:</label>
                        <input type="text" id="test_username" name="test_username" 
                               value="<?= htmlspecialchars($testUsername) ?>"
                               placeholder="請輸入您的LDAP帳號" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_password">真實LDAP密碼:</label>
                        <input type="password" id="test_password" name="test_password" 
                               placeholder="請輸入您的真實LDAP密碼" required>
                    </div>
                    
                    <button type="submit" class="btn">🔍 測試認證</button>
                </form>

                <!-- 測試結果 -->
                <?php if ($testResult): ?>
                    <div class="alert <?= $testResult['success'] ? 'alert-success' : 'alert-error' ?>">
                        <h4><?= $testResult['success'] ? '✅ 認證成功' : '❌ 認證失敗' ?></h4>
                        <p><?= htmlspecialchars($testResult['message']) ?></p>
                        
                        <?php if ($testResult['success'] && isset($testResult['data'])): ?>
                            <h5>使用者資料:</h5>
                            <ul>
                                <li><strong>ID:</strong> <?= htmlspecialchars($testResult['data']['id'] ?? '未設定') ?></li>
                                <li><strong>帳號:</strong> <?= htmlspecialchars($testResult['data']['username'] ?? '未設定') ?></li>
                                <li><strong>姓名:</strong> <?= htmlspecialchars($testResult['data']['name'] ?? '未設定') ?></li>
                                <li><strong>郵件:</strong> <?= htmlspecialchars($testResult['data']['email'] ?? '未設定') ?></li>
                                <li><strong>角色:</strong> <?= htmlspecialchars($testResult['data']['role'] ?? '未設定') ?></li>
                                <li><strong>認證來源:</strong> <?= htmlspecialchars($testResult['data']['auth_source'] ?? '未設定') ?></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="login" class="btn">🔐 前往登入頁面</a>
                <a href="." class="btn">🏠 返回首頁</a>
            </div>
        </div>
    </div>

    <script>
    function useAccount(username) {
        document.getElementById('test_username').value = username;
        document.getElementById('test_password').focus();
        alert('已填入帳號：' + username + '，請輸入真實LDAP密碼');
    }
    </script>
</body>
</html> 