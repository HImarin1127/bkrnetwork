<?php
// test_ldap_connection.php
// LDAP 連接測試工具

require_once __DIR__ . '/app/Services/LdapService.php';

/**
 * LDAP 連接測試工具
 * 
 * 這個工具可以幫助您：
 * 1. 測試 LDAP 伺服器連接
 * 2. 驗證配置參數
 * 3. 測試使用者認證
 * 4. 檢查 LDAP 結構
 */

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 連接測試工具 - 讀書共和國內部系統</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .content {
            padding: 30px;
        }
        
        .test-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #C8102E;
        }
        
        .test-section h2 {
            margin: 0 0 20px 0;
            color: #C8102E;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #C8102E;
        }
        
        .btn {
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .result {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border-left: 5px solid #28a745;
        }
        
        .result.error {
            border-left-color: #dc3545;
        }
        
        .result.warning {
            border-left-color: #ffc107;
        }
        
        .result h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        
        .result-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        
        .config-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            border: 1px solid #e9ecef;
        }
        
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 LDAP 連接測試工具</h1>
            <p>測試和調試您的 LDAP 伺服器連接配置</p>
        </div>
        
        <div class="content">
            <!-- 基本連接測試 -->
            <div class="test-section">
                <h2>🌐 基本連接測試</h2>
                <p>測試 LDAP 伺服器的基本連接功能</p>
                
                <button class="btn" onclick="testBasicConnection()">測試基本連接</button>
                <button class="btn btn-secondary" onclick="showCurrentConfig()">顯示目前配置</button>
                
                <div id="basic-test-result"></div>
            </div>
            
            <!-- 使用者認證測試 -->
            <div class="test-section">
                <h2>👤 使用者認證測試</h2>
                <p>測試特定使用者的 LDAP 認證功能</p>
                
                <div class="grid">
                    <div class="form-group">
                        <label for="test_username">測試使用者名稱：</label>
                        <input type="text" id="test_username" placeholder="請輸入要測試的使用者名稱">
                    </div>
                    <div class="form-group">
                        <label for="test_password">測試密碼：</label>
                        <input type="password" id="test_password" placeholder="請輸入使用者密碼">
                    </div>
                </div>
                
                <button class="btn" onclick="testUserAuthentication()">測試使用者認證</button>
                
                <div id="auth-test-result"></div>
            </div>
            
            <!-- 配置檢查 -->
            <div class="test-section">
                <h2>⚙️ 配置檢查</h2>
                <p>檢查 LDAP 配置的完整性和正確性</p>
                
                <button class="btn" onclick="validateConfig()">檢查配置</button>
                <button class="btn btn-secondary" onclick="showConfigHelp()">配置說明</button>
                
                <div id="config-check-result"></div>
            </div>
        </div>
    </div>

    <script>
        // 測試基本連接
        function testBasicConnection() {
            const resultDiv = document.getElementById('basic-test-result');
            resultDiv.innerHTML = '<div class="result"><h3>🔄 正在測試連接...</h3></div>';
            
            fetch('?action=test_connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                displayResult('basic-test-result', data);
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="result error"><h3>❌ 測試失敗</h3><p>無法連接到測試服務</p></div>';
            });
        }
        
        // 測試使用者認證
        function testUserAuthentication() {
            const username = document.getElementById('test_username').value;
            const password = document.getElementById('test_password').value;
            const resultDiv = document.getElementById('auth-test-result');
            
            if (!username || !password) {
                resultDiv.innerHTML = '<div class="result error"><h3>❌ 輸入錯誤</h3><p>請輸入使用者名稱和密碼</p></div>';
                return;
            }
            
            resultDiv.innerHTML = '<div class="result"><h3>🔄 正在測試認證...</h3></div>';
            
            fetch('?action=test_auth', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                displayResult('auth-test-result', data);
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="result error"><h3>❌ 測試失敗</h3><p>無法連接到測試服務</p></div>';
            });
        }
        
        // 驗證配置
        function validateConfig() {
            const resultDiv = document.getElementById('config-check-result');
            resultDiv.innerHTML = '<div class="result"><h3>🔄 正在檢查配置...</h3></div>';
            
            fetch('?action=validate_config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                displayResult('config-check-result', data);
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="result error"><h3>❌ 檢查失敗</h3><p>無法讀取配置資訊</p></div>';
            });
        }
        
        // 顯示目前配置
        function showCurrentConfig() {
            const resultDiv = document.getElementById('basic-test-result');
            resultDiv.innerHTML = '<div class="result"><h3>🔄 正在載入配置...</h3></div>';
            
            fetch('?action=show_config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                displayResult('basic-test-result', data);
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="result error"><h3>❌ 載入失敗</h3><p>無法讀取配置資訊</p></div>';
            });
        }
        
        // 顯示配置說明
        function showConfigHelp() {
            const helpText = `
LDAP 配置說明：

🔧 基本設定：
• server: LDAP 伺服器 IP 或主機名稱
• port: 連接埠 (標準 389, SSL 636)
• base_dn: 基礎搜尋 DN
• admin_username: 管理員帳號 DN
• admin_password: 管理員密碼

👥 使用者設定：
• user_search_base: 使用者搜尋基礎
• user_filter: 使用者搜尋過濾器
• attributes: 屬性對應設定

🔒 安全設定：
• use_ssl: 使用 SSL 加密
• use_tls: 使用 TLS 加密
• admin_groups: 管理員群組列表

⚙️ 進階設定：
• auto_create_users: 自動建立使用者
• sync_attributes: 同步使用者屬性
• fallback_to_local: 回歸本地認證
• debug: 除錯模式
            `;
            
            document.getElementById('config-check-result').innerHTML = 
                '<div class="result"><h3>📖 配置說明</h3><div class="config-display">' + helpText + '</div></div>';
        }
        
        // 顯示測試結果
        function displayResult(elementId, data) {
            const resultDiv = document.getElementById(elementId);
            let html = '';
            
            if (data.success) {
                html = '<div class="result"><h3>✅ ' + data.message + '</h3>';
            } else {
                html = '<div class="result error"><h3>❌ ' + data.message + '</h3>';
            }
            
            if (data.details && data.details.length > 0) {
                data.details.forEach(detail => {
                    html += '<div class="result-item">' + detail + '</div>';
                });
            }
            
            if (data.config) {
                html += '<h4>配置資訊：</h4>';
                html += '<div class="config-display">' + JSON.stringify(data.config, null, 2) + '</div>';
            }
            
            if (data.user_data) {
                html += '<h4>使用者資料：</h4>';
                html += '<div class="config-display">' + JSON.stringify(data.user_data, null, 2) + '</div>';
            }
            
            html += '</div>';
            resultDiv.innerHTML = html;
        }
    </script>
</body>
</html>

<?php
// 處理 AJAX 請求
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $ldapService = new LdapService();
        
        switch ($_GET['action']) {
            case 'test_connection':
                $result = $ldapService->testConnection();
                echo json_encode($result);
                break;
                
            case 'test_auth':
                $input = json_decode(file_get_contents('php://input'), true);
                $username = $input['username'] ?? '';
                $password = $input['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    echo json_encode([
                        'success' => false,
                        'message' => '請提供使用者名稱和密碼'
                    ]);
                    break;
                }
                
                $userData = $ldapService->authenticate($username, $password);
                
                if ($userData) {
                    echo json_encode([
                        'success' => true,
                        'message' => '使用者認證成功',
                        'user_data' => $userData
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => '使用者認證失敗'
                    ]);
                }
                break;
                
            case 'validate_config':
                $config = require __DIR__ . '/config/ldap.php';
                $issues = [];
                
                // 檢查必要配置
                if (empty($config['server'])) $issues[] = '❌ 未設定 LDAP 伺服器地址';
                if (empty($config['base_dn'])) $issues[] = '❌ 未設定基礎 DN';
                if (empty($config['admin_username'])) $issues[] = '❌ 未設定管理員帳號';
                if (empty($config['admin_password'])) $issues[] = '❌ 未設定管理員密碼';
                if (empty($config['user_search_base'])) $issues[] = '❌ 未設定使用者搜尋基礎';
                if (empty($config['user_filter'])) $issues[] = '❌ 未設定使用者搜尋過濾器';
                
                // 檢查 PHP 擴展
                if (!extension_loaded('ldap')) $issues[] = '❌ PHP LDAP 擴展未安裝';
                
                if (empty($issues)) {
                    $issues[] = '✅ 所有必要配置都已設定';
                    $issues[] = '✅ PHP LDAP 擴展已安裝';
                    
                    echo json_encode([
                        'success' => true,
                        'message' => '配置檢查通過',
                        'details' => $issues
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => '配置檢查發現問題',
                        'details' => $issues
                    ]);
                }
                break;
                
            case 'show_config':
                $config = require __DIR__ . '/config/ldap.php';
                
                // 隱藏敏感資訊
                $safeConfig = $config;
                $safeConfig['admin_password'] = '***隱藏***';
                
                echo json_encode([
                    'success' => true,
                    'message' => '目前 LDAP 配置',
                    'config' => $safeConfig
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => '未知的測試動作'
                ]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => '測試過程發生錯誤：' . $e->getMessage()
        ]);
    }
    
    exit;
}
?> 