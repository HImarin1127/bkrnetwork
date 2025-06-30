<?php
// dual_login_clean.php
// 乾淨的雙模式登入系統

// 完全關閉所有錯誤顯示
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

session_start();

$message = '';
$messageType = '';
$loginSuccess = false;
$userInfo = null;
$activeTab = $_POST['login_mode'] ?? 'ldap';

// 處理登入請求
if ($_POST['action'] ?? '' === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $loginMode = $_POST['login_mode'] ?? 'ldap';
    
    if (empty($username) || empty($password)) {
        $message = '請輸入帳號和密碼';
        $messageType = 'error';
    } else {
        if ($loginMode === 'ldap') {
            // LDAP 模式測試
            try {
                require_once 'app/Services/LdapService.php';
                $ldapService = new LdapService();
                $user = $ldapService->authenticate($username, $password);
                
                if ($user) {
                    $loginSuccess = true;
                    $userInfo = $user;
                    $userInfo['auth_mode'] = 'LDAP';
                    $message = "LDAP 認證成功！歡迎 {$user['name']}";
                    $messageType = 'success';
                    
                    $_SESSION['user_id'] = $user['username'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['auth_mode'] = 'ldap';
                } else {
                    $message = 'LDAP 認證失敗：帳號或密碼錯誤';
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = 'LDAP 服務暫時無法使用';
                $messageType = 'error';
            }
            
        } else {
            // 本地模式測試
            try {
                require_once 'app/Models/User.php';
                $userModel = new User();
                $user = $userModel->authenticateLocal($username, $password);
                
                if ($user) {
                    $loginSuccess = true;
                    $userInfo = $user;
                    $userInfo['auth_mode'] = '本地資料庫';
                    $message = "本地認證成功！歡迎 {$user['name']}";
                    $messageType = 'success';
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['auth_mode'] = 'local';
                } else {
                    $message = '本地認證失敗：帳號或密碼錯誤';
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = '本地認證服務暫時無法使用';
                $messageType = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>雙模式登入 - 讀書共和國內部網站</title>
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
            border-radius: 25px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
            padding: 0;
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        .header {
            text-align: center;
            padding: 40px 40px 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 25px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        }
        
        h1 {
            color: #1e293b;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 16px;
        }
        
        .status-bar {
            display: flex;
            justify-content: space-between;
            padding: 15px 40px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }
        
        .tabs {
            display: flex;
            background: #f1f5f9;
            margin: 0;
        }
        
        .tab {
            flex: 1;
            padding: 20px;
            text-align: center;
            background: #e2e8f0;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            border: none;
            font-size: 16px;
        }
        
        .tab.active {
            background: white;
            color: #1e293b;
            box-shadow: inset 0 -3px 0 #667eea;
        }
        
        .tab:hover:not(.active) {
            background: #cbd5e1;
            color: #475569;
        }
        
        .tab-content {
            padding: 40px;
        }
        
        .tab-panel {
            display: none;
        }
        
        .tab-panel.active {
            display: block;
        }
        
        .mode-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }
        
        .ldap-badge {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        
        .local-badge {
            background: linear-gradient(45deg, #f59e0b, #d97706);
            color: white;
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
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-ldap {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .btn-ldap:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .btn-local {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .btn-local:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .auth-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            color: white;
        }
        
        .auth-ldap {
            background: #059669;
        }
        
        .auth-local {
            background: #d97706;
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
        
        .help-text {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
            line-height: 1.5;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">讀</div>
            <h1>員工登入系統</h1>
            <p class="subtitle">選擇您的登入方式</p>
        </div>
        
        <div class="status-bar">
            <span>🏢 LDAP 雙模式系統</span>
            <span>🔐 本地資料庫系統</span>
        </div>
        
        <?php if ($message): ?>
            <div style="padding: 20px 40px 0;">
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button type="button" class="tab <?php echo $activeTab === 'ldap' ? 'active' : ''; ?>" onclick="switchTab('ldap')">
                🏢 公司帳號 (LDAP)
            </button>
            <button type="button" class="tab <?php echo $activeTab === 'local' ? 'active' : ''; ?>" onclick="switchTab('local')">
                🔐 本地帳號
            </button>
        </div>
        
        <div class="tab-content">
            <!-- LDAP 登入模式 -->
            <div id="ldap-panel" class="tab-panel <?php echo $activeTab === 'ldap' ? 'active' : ''; ?>">
                <div class="mode-badge ldap-badge">LDAP 認證模式</div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="login_mode" value="ldap">
                    
                    <div class="form-group">
                        <label for="ldap_username">公司帳號</label>
                        <input type="text" id="ldap_username" name="username" 
                               value="<?php echo $activeTab === 'ldap' ? htmlspecialchars($_POST['username'] ?? '') : ''; ?>"
                               placeholder="輸入您的 LDAP 帳號" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ldap_password">密碼</label>
                        <input type="password" id="ldap_password" name="password" 
                               placeholder="輸入您的 LDAP 密碼" required>
                    </div>
                    
                    <button type="submit" class="btn btn-ldap">🏢 LDAP 登入</button>
                </form>
                
                <div class="help-text">
                    <strong>📝 說明：</strong>使用您在公司郵件系統、NAS 中的相同帳號密碼。<br>
                    此模式直接向 LDAP 伺服器認證，不涉及本地資料庫。
                </div>
            </div>
            
            <!-- 本地登入模式 -->
            <div id="local-panel" class="tab-panel <?php echo $activeTab === 'local' ? 'active' : ''; ?>">
                <div class="mode-badge local-badge">本地認證模式</div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="login_mode" value="local">
                    
                    <div class="form-group">
                        <label for="local_username">本地帳號</label>
                        <input type="text" id="local_username" name="username" 
                               value="<?php echo $activeTab === 'local' ? htmlspecialchars($_POST['username'] ?? '') : ''; ?>"
                               placeholder="輸入本地帳號" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="local_password">密碼</label>
                        <input type="password" id="local_password" name="password" 
                               placeholder="輸入本地密碼" required>
                    </div>
                    
                    <button type="submit" class="btn btn-local">🔐 本地登入</button>
                </form>
                
                <div class="help-text">
                    <strong>📝 說明：</strong>使用預先建立的本地帳號密碼。<br>
                    此模式使用資料庫認證，適合外部使用者或特殊權限帳號。<br>
                    <strong>測試帳號：</strong>admin / password
                </div>
            </div>
        </div>
        
        <?php if ($loginSuccess && $userInfo): ?>
            <div style="padding: 0 40px 20px;">
                <div class="user-info">
                    <h3>
                        🎉 登入成功！
                        <span class="auth-badge auth-<?php echo $activeTab; ?>">
                            <?php echo htmlspecialchars($userInfo['auth_mode']); ?>
                        </span>
                    </h3>
                    <div class="user-detail">
                        <span class="user-detail-label">帳號：</span>
                        <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['username']); ?></span>
                    </div>
                    <div class="user-detail">
                        <span class="user-detail-label">姓名：</span>
                        <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['name']); ?></span>
                    </div>
                    <?php if (isset($userInfo['email'])): ?>
                    <div class="user-detail">
                        <span class="user-detail-label">郵件：</span>
                        <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['email'] ?: '未設定'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="user-detail">
                        <span class="user-detail-label">認證方式：</span>
                        <span class="user-detail-value"><?php echo htmlspecialchars($userInfo['auth_mode']); ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>© 2024 讀書共和國 | 雙模式登入系統</p>
            <p>測試時間：<?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
    
    <script>
        function switchTab(mode) {
            // 更新標籤狀態
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // 更新面板顯示
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            document.getElementById(mode + '-panel').classList.add('active');
            
            // 清空表單
            document.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
                input.value = '';
            });
            
            // 聚焦到對應的帳號欄位
            if (mode === 'ldap') {
                document.getElementById('ldap_username').focus();
            } else {
                document.getElementById('local_username').focus();
            }
        }
        
        // 初始聚焦
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($activeTab === 'ldap'): ?>
                document.getElementById('ldap_username').focus();
            <?php else: ?>
                document.getElementById('local_username').focus();
            <?php endif; ?>
        });
    </script>
</body>
</html> 