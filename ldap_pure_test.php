<?php
// ldap_pure_test.php
// 純 LDAP 登入測試工具（不涉及資料庫）

session_start();

// 引入 LDAP 服務
require_once 'app/Services/LdapService.php';

$message = '';
$messageType = '';
$loginSuccess = false;
$userInfo = null;

// 處理登入測試
if ($_POST['action'] ?? '' === 'test_login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = '請輸入帳號和密碼';
        $messageType = 'error';
    } else {
        try {
            // 直接使用 LdapService 進行純 LDAP 認證
            $ldapService = new LdapService();
            $user = $ldapService->authenticate($username, $password);
            
            if ($user) {
                $loginSuccess = true;
                $userInfo = $user;
                $message = "LDAP 認證成功！歡迎 {$user['name']} ({$user['username']})";
                $messageType = 'success';
            } else {
                $message = 'LDAP 認證失敗：帳號或密碼錯誤';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'LDAP 連接失敗：' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// 測試 LDAP 連接狀態
$ldapStatus = '';
try {
    $ldapService = new LdapService();
    $connectionTest = $ldapService->testConnection();
    if ($connectionTest['success']) {
        $ldapStatus = "✅ LDAP 伺服器連接正常";
    } else {
        $ldapStatus = "❌ LDAP 伺服器連接失敗：" . $connectionTest['message'];
    }
} catch (Exception $e) {
    $ldapStatus = "❌ LDAP 服務錯誤：" . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>純 LDAP 登入測試 - 讀書共和國內部網站</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(45deg, #4f46e5, #7c3aed);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        
        h1 {
            color: #1e293b;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .pure-badge {
            display: inline-block;
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #475569;
        }
        
        .status.success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }
        
        .status.error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }
        
        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message.success {
            background: #f0fdf4;
            color: #166534;
            border: 2px solid #bbf7d0;
        }
        
        .message.error {
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #fecaca;
        }
        
        .user-info {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 2px solid #bbf7d0;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .user-info h3 {
            color: #166534;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 700;
        }
        
        .user-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #d1fae5;
        }
        
        .user-detail:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .user-detail-label {
            color: #166534;
            font-weight: 600;
        }
        
        .user-detail-value {
            color: #059669;
            font-weight: 700;
            font-family: monospace;
        }
        
        .features {
            background: #fffbeb;
            border: 2px solid #fed7aa;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .features h3 {
            color: #9a3412;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 700;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            color: #a16207;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            font-size: 14px;
        }
        
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #059669;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 14px;
        }
        
        .footer p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                讀
            </div>
            <h1>純 LDAP 認證測試</h1>
            <p class="subtitle">不涉及資料庫的 LDAP 登入測試</p>
            <span class="pure-badge">Pure LDAP</span>
        </div>
        
        <div class="status <?php echo strpos($ldapStatus, '✅') === 0 ? 'success' : 'error'; ?>">
            <strong><?php echo $ldapStatus; ?></strong>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="test_login">
            
            <div class="form-group">
                <label for="username">使用者帳號</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       placeholder="輸入您的 LDAP 帳號" required>
            </div>
            
            <div class="form-group">
                <label for="password">密碼</label>
                <input type="password" id="password" name="password" 
                       placeholder="輸入您的 LDAP 密碼" required>
            </div>
            
            <button type="submit" class="btn">純 LDAP 認證測試</button>
        </form>
        
        <?php if ($loginSuccess && $userInfo): ?>
            <div class="user-info">
                <h3>🎉 LDAP 認證成功！使用者資訊：</h3>
                <div class="user-detail">
                    <span class="user-detail-label">帳號：</span>
                    <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['username']); ?></span>
                </div>
                <div class="user-detail">
                    <span class="user-detail-label">姓名：</span>
                    <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['name']); ?></span>
                </div>
                <div class="user-detail">
                    <span class="user-detail-label">郵件：</span>
                    <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['email'] ?: '未設定'); ?></span>
                </div>
                <div class="user-detail">
                    <span class="user-detail-label">部門：</span>
                    <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['department'] ?: '未設定'); ?></span>
                </div>
                <div class="user-detail">
                    <span class="user-detail-label">認證方式：</span>
                    <span class="user-detail-value">純 LDAP 認證</span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="features">
            <h3>🚀 純 LDAP 認證特點</h3>
            <ul class="feature-list">
                <li>直接與 LDAP 伺服器認證，不涉及資料庫</li>
                <li>即時獲取 LDAP 中的使用者資訊</li>
                <li>無需本地帳號同步或建立</li>
                <li>快速、輕量的認證流程</li>
                <li>適合測試 LDAP 連接和帳號狀態</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>© 2024 讀書共和國 | 純 LDAP 認證測試工具</p>
            <p>測試時間：<?php echo date('Y-m-d H:i:s'); ?></p>
            <p style="color: #059669; font-weight: 600;">✅ 此工具不會操作任何資料庫</p>
        </div>
    </div>
    
    <script>
        // 自動聚焦到帳號欄位
        document.getElementById('username').focus();
    </script>
</body>
</html> 