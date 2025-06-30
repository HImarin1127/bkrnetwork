<?php
// LDAP 連接測試工具
// 用於測試 LDAP 設定是否正確

// 啟動 session（如果需要）
session_start();

// 包含必要的文件
require_once __DIR__ . '/app/Services/LdapService.php';

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 連接測試 - 讀書共和國</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
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
        
        .test-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            background: #f9f9f9;
        }
        
        .test-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .details {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #C8102E;
        }
        
        .details ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .details li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }
        
        .config-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .config-table th,
        .config-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .config-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .btn {
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 1rem;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(200,16,46,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 LDAP 連接測試工具</h1>
            <p>測試讀書共和國 LDAP 認證設定</p>
        </div>
        
        <div class="content">
            <?php
            try {
                // 載入 LDAP 配置
                $ldapConfig = require __DIR__ . '/config/ldap.php';
                
                echo '<div class="test-section">';
                echo '<h3 class="test-title">📋 LDAP 配置檢查</h3>';
                
                if ($ldapConfig['enabled']) {
                    echo '<div class="status success">✅ LDAP 認證已啟用</div>';
                } else {
                    echo '<div class="status error">❌ LDAP 認證已停用</div>';
                }
                
                echo '<div class="config-info">';
                echo '<h4>🔧 目前 LDAP 設定：</h4>';
                echo '<table class="config-table">';
                echo '<tr><th>設定項目</th><th>值</th></tr>';
                echo '<tr><td>LDAP 伺服器</td><td>' . $ldapConfig['server'] . ':' . $ldapConfig['port'] . '</td></tr>';
                echo '<tr><td>基礎 DN</td><td>' . $ldapConfig['base_dn'] . '</td></tr>';
                echo '<tr><td>使用者搜尋基礎</td><td>' . $ldapConfig['user_search_base'] . '</td></tr>';
                echo '<tr><td>使用 SSL</td><td>' . ($ldapConfig['use_ssl'] ? '是' : '否') . '</td></tr>';
                echo '<tr><td>使用 TLS</td><td>' . ($ldapConfig['use_tls'] ? '是' : '否') . '</td></tr>';
                echo '<tr><td>自動建立使用者</td><td>' . ($ldapConfig['auto_create_users'] ? '是' : '否') . '</td></tr>';
                echo '<tr><td>本地認證回歸</td><td>' . ($ldapConfig['fallback_to_local'] ? '是' : '否') . '</td></tr>';
                echo '</table>';
                echo '</div>';
                echo '</div>';
                
                // 測試 LDAP 連接
                echo '<div class="test-section">';
                echo '<h3 class="test-title">🔗 LDAP 連接測試</h3>';
                
                if ($ldapConfig['enabled']) {
                    $ldapService = new LdapService();
                    $connectionTest = $ldapService->testConnection();
                    
                    if ($connectionTest['success']) {
                        echo '<div class="status success">✅ ' . $connectionTest['message'] . '</div>';
                    } else {
                        echo '<div class="status error">❌ ' . $connectionTest['message'] . '</div>';
                    }
                    
                    echo '<div class="details">';
                    echo '<h4>測試詳情：</h4>';
                    echo '<ul>';
                    foreach ($connectionTest['details'] as $detail) {
                        echo '<li>' . htmlspecialchars($detail) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<div class="status info">ℹ️ LDAP 認證已停用，無法進行連接測試</div>';
                }
                echo '</div>';
                
                // 系統環境檢查
                echo '<div class="test-section">';
                echo '<h3 class="test-title">🖥️ 系統環境檢查</h3>';
                
                $checks = [
                    'PHP LDAP 擴充套件' => extension_loaded('ldap'),
                    'PHP 版本 >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                    'OpenSSL 支援' => extension_loaded('openssl'),
                ];
                
                echo '<div class="details">';
                echo '<ul>';
                foreach ($checks as $check => $result) {
                    if ($result) {
                        echo '<li>✅ ' . $check . ' - 正常</li>';
                    } else {
                        echo '<li>❌ ' . $check . ' - 需要安裝/啟用</li>';
                    }
                }
                echo '<li>ℹ️ PHP 版本: ' . PHP_VERSION . '</li>';
                echo '</ul>';
                echo '</div>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="test-section">';
                echo '<div class="status error">❌ 測試過程發生錯誤</div>';
                echo '<div class="details">';
                echo '<p><strong>錯誤訊息：</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
            
            <div class="test-section">
                <h3 class="test-title">📚 使用說明</h3>
                <div class="details">
                    <h4>✅ 如果測試成功：</h4>
                    <ul>
                        <li>您現在可以在登入頁面使用 LDAP 帳號登入</li>
                        <li>系統會優先嘗試 LDAP 認證，失敗時會回歸本地認證（如果啟用）</li>
                        <li>LDAP 使用者的資料會自動同步到本地資料庫（如果啟用）</li>
                    </ul>
                    
                    <h4>❌ 如果測試失敗：</h4>
                    <ul>
                        <li>檢查 <code>config/ldap.php</code> 中的伺服器設定</li>
                        <li>確認 LDAP 伺服器位址和連接埠是否正確</li>
                        <li>驗證管理員帳號密碼是否正確</li>
                        <li>檢查網路連線和防火牆設定</li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/login" class="btn">🔐 前往登入頁面</a>
                <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/" class="btn">🏠 返回首頁</a>
            </div>
        </div>
    </div>
</body>
</html> 