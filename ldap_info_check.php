<?php
// LDAP 資訊檢查工具
// 用來查看LDAP伺服器中的實際資訊
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 載入LDAP配置
$ldapConfig = require __DIR__ . '/config/ldap.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 資訊檢查工具</title>
    <style>
        body { 
            font-family: 'Microsoft JhengHei', sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
            line-height: 1.6;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .section { 
            margin-bottom: 30px; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
            font-size: 0.9rem;
        }
        th, td { 
            padding: 8px 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        th { 
            background: #f8f9fa; 
            font-weight: 600; 
        }
        tr:hover { background: #f8f9fa; }
        
        pre { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 4px; 
            overflow-x: auto; 
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .attribute-value {
            max-width: 300px;
            word-break: break-all;
        }
        
        .user-count {
            font-size: 1.2rem;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 LDAP 伺服器資訊檢查工具</h1>
        <p>檢查LDAP伺服器連接狀態和使用者資訊</p>
        
        <?php
        try {
            // 檢查LDAP擴充
            if (!extension_loaded('ldap')) {
                echo '<div class="section error">';
                echo '<h3>❌ 系統檢查失敗</h3>';
                echo '<p>PHP LDAP 擴充套件未安裝或未啟用</p>';
                echo '</div>';
                exit;
            }

            echo '<div class="section success">';
            echo '<h3>✅ 系統環境正常</h3>';
            echo '<p>PHP LDAP 擴充套件已載入</p>';
            echo '</div>';
            
            // 顯示LDAP配置
            echo '<div class="section info">';
            echo '<h3>📋 LDAP 配置資訊</h3>';
            echo '<table>';
            echo '<tr><th>配置項目</th><th>設定值</th></tr>';
            echo '<tr><td>LDAP 啟用</td><td>' . ($ldapConfig['enabled'] ? '✅ 是' : '❌ 否') . '</td></tr>';
            echo '<tr><td>LDAP 伺服器</td><td>' . $ldapConfig['server'] . ':' . $ldapConfig['port'] . '</td></tr>';
            echo '<tr><td>使用 SSL</td><td>' . ($ldapConfig['use_ssl'] ? '是' : '否') . '</td></tr>';
            echo '<tr><td>使用 TLS</td><td>' . ($ldapConfig['use_tls'] ? '是' : '否') . '</td></tr>';
            echo '<tr><td>基礎 DN</td><td>' . $ldapConfig['base_dn'] . '</td></tr>';
            echo '<tr><td>使用者搜尋基礎</td><td>' . $ldapConfig['user_search_base'] . '</td></tr>';
            echo '<tr><td>使用者過濾器</td><td>' . $ldapConfig['user_filter'] . '</td></tr>';
            echo '<tr><td>管理員帳號</td><td>' . $ldapConfig['admin_username'] . '</td></tr>';
            echo '</table>';
            echo '</div>';
            
            if (!$ldapConfig['enabled']) {
                echo '<div class="section warning">';
                echo '<h3>⚠️ LDAP 已停用</h3>';
                echo '<p>LDAP 認證功能已停用，無法進行連接測試</p>';
                echo '</div>';
                exit;
            }
            
            // 嘗試連接LDAP
            echo '<div class="section">';
            echo '<h3>🔗 LDAP 連接測試</h3>';
            
            $server = $ldapConfig['use_ssl'] 
                ? "ldaps://{$ldapConfig['server']}:{$ldapConfig['port']}"
                : "ldap://{$ldapConfig['server']}:{$ldapConfig['port']}";
                
            $connection = ldap_connect($server);
            
            if (!$connection) {
                echo '<div class="error">❌ 無法建立 LDAP 連接</div>';
                exit;
            }
            
            // 設定LDAP選項
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $ldapConfig['timeout']);
            
            // 啟用TLS（如果配置）
            if ($ldapConfig['use_tls']) {
                if (!ldap_start_tls($connection)) {
                    echo '<div class="error">❌ 無法啟動 TLS：' . ldap_error($connection) . '</div>';
                    exit;
                }
            }
            
            // 嘗試綁定
            if (!ldap_bind($connection, $ldapConfig['admin_username'], $ldapConfig['admin_password'])) {
                echo '<div class="error">❌ LDAP 綁定失敗：' . ldap_error($connection) . '</div>';
                echo '<p>請檢查管理員帳號和密碼是否正確</p>';
                exit;
            }
            
            echo '<div class="success">✅ LDAP 連接成功</div>';
            echo '<p>伺服器：' . $server . '</p>';
            echo '<p>綁定帳號：' . $ldapConfig['admin_username'] . '</p>';
            echo '</div>';
            
            // 搜尋使用者
            echo '<div class="section">';
            echo '<h3>👥 LDAP 使用者清單</h3>';
            
            $searchFilter = '(objectClass=inetOrgPerson)';
            $attributes = ['uid', 'cn', 'mail', 'telephoneNumber', 'department', 'title', 'memberOf'];
            
            $search = ldap_search(
                $connection,
                $ldapConfig['user_search_base'],
                $searchFilter,
                $attributes
            );
            
            if (!$search) {
                echo '<div class="error">❌ 使用者搜尋失敗：' . ldap_error($connection) . '</div>';
            } else {
                $entries = ldap_get_entries($connection, $search);
                $userCount = $entries['count'];
                
                echo '<div class="user-count">找到 ' . $userCount . ' 個使用者帳號</div>';
                
                if ($userCount > 0) {
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>帳號 (uid)</th>';
                    echo '<th>姓名 (cn)</th>';
                    echo '<th>郵件 (mail)</th>';
                    echo '<th>部門 (department)</th>';
                    echo '<th>職稱 (title)</th>';
                    echo '<th>電話</th>';
                    echo '</tr>';
                    
                    for ($i = 0; $i < $userCount; $i++) {
                        $entry = $entries[$i];
                        echo '<tr>';
                        echo '<td><strong>' . (isset($entry['uid'][0]) ? htmlspecialchars($entry['uid'][0]) : '未設定') . '</strong></td>';
                        echo '<td>' . (isset($entry['cn'][0]) ? htmlspecialchars($entry['cn'][0]) : '未設定') . '</td>';
                        echo '<td>' . (isset($entry['mail'][0]) ? htmlspecialchars($entry['mail'][0]) : '未設定') . '</td>';
                        echo '<td>' . (isset($entry['department'][0]) ? htmlspecialchars($entry['department'][0]) : '未設定') . '</td>';
                        echo '<td>' . (isset($entry['title'][0]) ? htmlspecialchars($entry['title'][0]) : '未設定') . '</td>';
                        echo '<td>' . (isset($entry['telephonenumber'][0]) ? htmlspecialchars($entry['telephonenumber'][0]) : '未設定') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<div class="warning">⚠️ 在指定的搜尋基礎下沒有找到任何使用者</div>';
                }
            }
            echo '</div>';
            
            // 顯示第一個使用者的詳細屬性（如果有的話）
            if (isset($entries) && $entries['count'] > 0) {
                echo '<div class="section">';
                echo '<h3>🔍 第一個使用者的詳細屬性</h3>';
                echo '<p>帳號：<strong>' . (isset($entries[0]['uid'][0]) ? $entries[0]['uid'][0] : '未知') . '</strong></p>';
                
                echo '<table>';
                echo '<tr><th>屬性名稱</th><th>屬性值</th></tr>';
                
                foreach ($entries[0] as $attr => $values) {
                    if (is_numeric($attr) || $attr === 'count') continue;
                    
                    echo '<tr>';
                    echo '<td><strong>' . htmlspecialchars($attr) . '</strong></td>';
                    echo '<td class="attribute-value">';
                    
                    if (is_array($values)) {
                        for ($j = 0; $j < $values['count']; $j++) {
                            echo htmlspecialchars($values[$j]);
                            if ($j < $values['count'] - 1) echo '<br>';
                        }
                    } else {
                        echo htmlspecialchars($values);
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
            }
            
            // 測試使用者過濾器
            echo '<div class="section">';
            echo '<h3>🧪 使用者過濾器測試</h3>';
            echo '<p>測試配置的使用者過濾器：<code>' . $ldapConfig['user_filter'] . '</code></p>';
            
            // 如果有使用者，測試第一個使用者的過濾器
            if (isset($entries) && $entries['count'] > 0 && isset($entries[0]['uid'][0])) {
                $testUsername = $entries[0]['uid'][0];
                $testFilter = str_replace('{username}', ldap_escape($testUsername, '', LDAP_ESCAPE_FILTER), $ldapConfig['user_filter']);
                
                echo '<p>測試使用者：<strong>' . htmlspecialchars($testUsername) . '</strong></p>';
                echo '<p>實際過濾器：<code>' . htmlspecialchars($testFilter) . '</code></p>';
                
                $testSearch = ldap_search($connection, $ldapConfig['user_search_base'], $testFilter, ['dn']);
                if ($testSearch) {
                    $testEntries = ldap_get_entries($connection, $testSearch);
                    if ($testEntries['count'] > 0) {
                        echo '<div class="success">✅ 使用者過濾器測試成功</div>';
                        echo '<p>找到的DN：' . htmlspecialchars($testEntries[0]['dn']) . '</p>';
                    } else {
                        echo '<div class="error">❌ 使用者過濾器測試失敗：找不到使用者</div>';
                    }
                } else {
                    echo '<div class="error">❌ 使用者過濾器測試失敗：' . ldap_error($connection) . '</div>';
                }
            }
            echo '</div>';
            
            // 清理連接
            ldap_unbind($connection);
            
        } catch (Exception $e) {
            echo '<div class="section error">';
            echo '<h3>❌ 發生錯誤</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="section info">
            <h3>📝 診斷建議</h3>
            <ul>
                <li><strong>如果看到使用者清單：</strong> 您可以使用列表中的任何帳號進行登入測試</li>
                <li><strong>如果沒有看到使用者：</strong> 檢查搜尋基礎和過濾器設定</li>
                <li><strong>如果連接失敗：</strong> 檢查伺服器位址、埠號和認證資訊</li>
                <li><strong>注意帳號格式：</strong> 使用表格中顯示的確切帳號名稱進行登入</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="ldap_debug.php" style="text-decoration: none; margin-right: 10px;">
                <button type="button" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">🧪 認證測試工具</button>
            </a>
            <a href="login" style="text-decoration: none;">
                <button type="button" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">🔐 返回登入頁面</button>
            </a>
        </div>
    </div>
</body>
</html> 