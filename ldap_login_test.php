<?php
// ldap_login_test.php
// 網頁版 LDAP 登入測試工具

session_start();

// 引入必要的類別
require_once 'app/Models/User.php';
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
            $userModel = new User();
            $user = $userModel->authenticate($username, $password);
            
            if ($user) {
                $loginSuccess = true;
                $userInfo = $user;
                $message = "登入成功！歡迎 {$user['name']} ({$user['username']})";
                $messageType = 'success';
            } else {
                $message = '帳號或密碼錯誤';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = '登入測試失敗：' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// 快速統計 LDAP 帳號數量
$ldapStats = '';
try {
    $ldapService = new LdapService();
    $allUsers = $ldapService->getAllUsers();
    if (count($allUsers) > 0) {
        $ldapStats = "✅ LDAP 伺服器中共有 " . count($allUsers) . " 個帳號可以登入";
    } else {
        $ldapStats = "❌ 無法取得 LDAP 帳號信息";
    }
} catch (Exception $e) {
    $ldapStats = "❌ LDAP 連接錯誤：" . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 登入測試工具 - 讀書共和國內部網站</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 15px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #718096;
            font-size: 16px;
        }
        
        .stats {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 30px;
            text-align: center;
            color: #4a5568;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message.success {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        
        .message.error {
            background: #fed7d7;
            color: #822727;
            border: 1px solid #feb2b2;
        }
        
        .user-info {
            background: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .user-info h3 {
            color: #22543d;
            margin-bottom: 15px;
        }
        
        .user-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #c6f6d5;
        }
        
        .user-detail:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .user-detail strong {
            color: #22543d;
        }
        
        .test-accounts {
            background: #fff8f1;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .test-accounts h3 {
            color: #9c4221;
            margin-bottom: 15px;
        }
        
        .account-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .account-item {
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            font-family: monospace;
            font-size: 14px;
            color: #744210;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .account-item:hover {
            background: #fef5e7;
            transform: scale(1.05);
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #718096;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                讀
            </div>
            <h1>LDAP 登入測試</h1>
            <p class="subtitle">測試內部網站帳號登入功能</p>
        </div>
        
        <div class="stats">
            <strong><?php echo $ldapStats; ?></strong>
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
                       placeholder="輸入您的密碼" required>
            </div>
            
            <button type="submit" class="btn">測試登入</button>
        </form>
        
        <?php if ($loginSuccess && $userInfo): ?>
            <div class="user-info">
                <h3>✅ 登入成功！使用者資訊：</h3>
                <div class="user-detail">
                    <span>帳號：</span>
                    <strong><?php echo htmlspecialchars($userInfo['username']); ?></strong>
                </div>
                <div class="user-detail">
                    <span>姓名：</span>
                    <strong><?php echo htmlspecialchars($userInfo['name']); ?></strong>
                </div>
                <div class="user-detail">
                    <span>郵件：</span>
                    <strong><?php echo htmlspecialchars($userInfo['email'] ?: '未設定'); ?></strong>
                </div>
                <div class="user-detail">
                    <span>部門：</span>
                    <strong><?php echo htmlspecialchars($userInfo['department'] ?: '未設定'); ?></strong>
                </div>
                <div class="user-detail">
                    <span>角色：</span>
                    <strong><?php echo htmlspecialchars($userInfo['role']); ?></strong>
                </div>
                <div class="user-detail">
                    <span>認證方式：</span>
                    <strong><?php echo htmlspecialchars($userInfo['auth_source']); ?></strong>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="test-accounts">
            <h3>🧪 常見測試帳號</h3>
            <p style="color: #9c4221; margin-bottom: 10px;">點擊帳號可快速填入表單：</p>
            <div class="account-list">
                <div class="account-item" onclick="fillAccount('admin')">admin</div>
                <div class="account-item" onclick="fillAccount('ldapuser')">ldapuser</div>
                <div class="account-item" onclick="fillAccount('audrey')">audrey</div>
                <div class="account-item" onclick="fillAccount('austin')">austin</div>
                <div class="account-item" onclick="fillAccount('angelicawei')">angelicawei</div>
                <div class="account-item" onclick="fillAccount('eric')">eric</div>
                <div class="account-item" onclick="fillAccount('kate')">kate</div>
                <div class="account-item" onclick="fillAccount('steve')">steve</div>
            </div>
            <p style="color: #9c4221; font-size: 12px; margin-top: 10px;">
                <strong>提示：</strong>使用您在公司 NAS、郵件系統中的相同帳號密碼
            </p>
        </div>
        
        <div class="footer">
            <p>© 2024 讀書共和國 | 內部網站 LDAP 整合測試</p>
            <p style="margin-top: 5px;">測試時間：<?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
    
    <script>
        function fillAccount(username) {
            document.getElementById('username').value = username;
            document.getElementById('password').focus();
        }
        
        // 自動聚焦到帳號欄位
        document.getElementById('username').focus();
    </script>
</body>
</html> 