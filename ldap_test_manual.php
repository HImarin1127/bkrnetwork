<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 手動測試 - 讀書共和國</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
        .content {
            padding: 30px;
        }
        .test-step {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #C8102E;
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 LDAP 手動連接測試</h1>
            <p>測試讀書共和國 LDAP 整合設定</p>
        </div>
        
        <div class="content">
            <?php
            $test_start_time = microtime(true);
            
            // LDAP 設定
            $ldap_server = '192.168.2.16';
            $ldap_port = 389;
            $base_dn = 'dc=bookrep,dc=com,dc=tw';
            $service_user_dn = 'uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw';
            $service_password = 'Bk22181417#';
            
            echo '<div class="test-step">';
            echo '<h3>📋 測試配置</h3>';
            echo '<div class="info">';
            echo "測試時間: " . date('Y-m-d H:i:s') . "<br>";
            echo "LDAP 伺服器: {$ldap_server}:{$ldap_port}<br>";
            echo "基礎 DN: {$base_dn}<br>";
            echo "服務帳號: {$service_user_dn}<br>";
            echo "協定版本: LDAP v3";
            echo '</div>';
            echo '</div>';
            
            $all_success = true;
            
            // 步驟 1: 檢查 PHP LDAP 擴充
            echo '<div class="test-step">';
            echo '<h3>1️⃣ 檢查 PHP LDAP 擴充</h3>';
            if (!extension_loaded('ldap')) {
                echo '<div class="error">❌ PHP LDAP 擴充套件未安裝</div>';
                echo '<p>請在 XAMPP 控制台啟用 php_ldap 擴充</p>';
                $all_success = false;
            } else {
                echo '<div class="success">✅ PHP LDAP 擴充套件已載入</div>';
            }
            echo '</div>';
            
            if ($all_success) {
                // 步驟 2: 建立 LDAP 連接
                echo '<div class="test-step">';
                echo '<h3>2️⃣ 建立 LDAP 連接</h3>';
                $ldap_url = "ldap://{$ldap_server}:{$ldap_port}";
                
                $connection = ldap_connect($ldap_url);
                if (!$connection) {
                    echo '<div class="error">❌ 無法建立 LDAP 連接</div>';
                    $all_success = false;
                } else {
                    echo '<div class="success">✅ LDAP 連接建立成功</div>';
                    echo '<div class="info">連接 URL: ' . $ldap_url . '</div>';
                    
                    // 設定選項
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
                    ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
                    echo '<div class="info">LDAP 選項設定完成</div>';
                }
                echo '</div>';
            }
            
            if ($all_success) {
                // 步驟 3: 服務帳號認證
                echo '<div class="test-step">';
                echo '<h3>3️⃣ 服務帳號認證</h3>';
                
                if (!@ldap_bind($connection, $service_user_dn, $service_password)) {
                    $error = ldap_error($connection);
                    echo '<div class="error">❌ 服務帳號認證失敗</div>';
                    echo '<div class="error">錯誤訊息: ' . $error . '</div>';
                    echo '<div class="info">可能的解決方案:</div>';
                    echo '<ul>';
                    echo '<li>檢查帳號密碼是否正確</li>';
                    echo '<li>檢查 DN 格式是否正確</li>';
                    echo '<li>檢查帳號是否被鎖定或停用</li>';
                    echo '</ul>';
                    $all_success = false;
                } else {
                    echo '<div class="success">✅ 服務帳號認證成功</div>';
                    echo '<div class="info">成功綁定: ' . $service_user_dn . '</div>';
                }
                echo '</div>';
            }
            
            if ($all_success) {
                // 步驟 4: 基礎搜尋測試
                echo '<div class="test-step">';
                echo '<h3>4️⃣ 基礎搜尋測試</h3>';
                
                $search = @ldap_search($connection, $base_dn, '(objectClass=*)', ['dn'], 0, 5);
                if (!$search) {
                    echo '<div class="error">❌ 無法進行 LDAP 搜尋</div>';
                    echo '<div class="error">錯誤訊息: ' . ldap_error($connection) . '</div>';
                    $all_success = false;
                } else {
                    $entries = ldap_get_entries($connection, $search);
                    echo '<div class="success">✅ 基礎搜尋成功，找到 ' . $entries['count'] . ' 個項目</div>';
                    
                    if ($entries['count'] > 0) {
                        echo '<div class="info">LDAP 結構預覽:</div>';
                        echo '<pre>';
                        for ($i = 0; $i < min(3, $entries['count']); $i++) {
                            echo htmlspecialchars($entries[$i]['dn']) . "\n";
                        }
                        echo '</pre>';
                    }
                }
                echo '</div>';
            }
            
            if ($all_success) {
                // 步驟 5: 使用者搜尋測試
                echo '<div class="test-step">';
                echo '<h3>5️⃣ 使用者搜尋測試</h3>';
                
                $user_search_base = 'cn=users,dc=bookrep,dc=com,dc=tw';
                $user_search = @ldap_search($connection, $user_search_base, '(objectClass=*)', ['dn', 'uid', 'cn'], 0, 10);
                
                if (!$user_search) {
                    echo '<div class="error">❌ 無法搜尋使用者</div>';
                    echo '<div class="error">錯誤訊息: ' . ldap_error($connection) . '</div>';
                } else {
                    $user_entries = ldap_get_entries($connection, $user_search);
                    echo '<div class="success">✅ 使用者搜尋成功，找到 ' . $user_entries['count'] . ' 個使用者</div>';
                    
                    if ($user_entries['count'] > 0) {
                        echo '<div class="info">找到的使用者:</div>';
                        echo '<pre>';
                        for ($i = 0; $i < min(5, $user_entries['count']); $i++) {
                            $uid = isset($user_entries[$i]['uid'][0]) ? $user_entries[$i]['uid'][0] : '未知';
                            $cn = isset($user_entries[$i]['cn'][0]) ? $user_entries[$i]['cn'][0] : '未知';
                            echo "帳號: {$uid}, 名稱: {$cn}\n";
                            echo "DN: " . htmlspecialchars($user_entries[$i]['dn']) . "\n\n";
                        }
                        echo '</pre>';
                    }
                }
                echo '</div>';
            }
            
            if ($all_success) {
                // 步驟 6: 使用者認證測試
                echo '<div class="test-step">';
                echo '<h3>6️⃣ 使用者認證功能測試</h3>';
                
                $test_username = 'ldapuser';
                $test_password = 'Bk22181417#';
                
                $user_filter = "(uid={$test_username})";
                $auth_search = @ldap_search($connection, $user_search_base, $user_filter, ['dn']);
                
                if (!$auth_search) {
                    echo '<div class="error">❌ 無法搜尋測試使用者</div>';
                } else {
                    $auth_entries = ldap_get_entries($connection, $auth_search);
                    if ($auth_entries['count'] === 0) {
                        echo '<div class="error">❌ 找不到測試使用者 "' . $test_username . '"</div>';
                    } else {
                        $user_dn = $auth_entries[0]['dn'];
                        echo '<div class="info">找到使用者 DN: ' . htmlspecialchars($user_dn) . '</div>';
                        
                        // 測試使用者認證
                        $user_connection = ldap_connect($ldap_url);
                        ldap_set_option($user_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                        
                        if (@ldap_bind($user_connection, $user_dn, $test_password)) {
                            echo '<div class="success">✅ 使用者認證測試成功</div>';
                        } else {
                            echo '<div class="error">❌ 使用者認證測試失敗: ' . ldap_error($user_connection) . '</div>';
                        }
                        
                        ldap_unbind($user_connection);
                    }
                }
                echo '</div>';
            }
            
            // 關閉連接
            if (isset($connection)) {
                ldap_unbind($connection);
            }
            
            // 測試結果摘要
            $test_end_time = microtime(true);
            $test_duration = round(($test_end_time - $test_start_time) * 1000, 2);
            
            echo '<div class="test-step">';
            echo '<h3>📊 測試結果摘要</h3>';
            if ($all_success) {
                echo '<div class="success">🎉 所有測試通過！LDAP 整合設定正確</div>';
                echo '<div class="info">系統已準備好使用 LDAP 認證</div>';
                echo '<div class="info">測試耗時: ' . $test_duration . ' 毫秒</div>';
            } else {
                echo '<div class="error">⚠️ 測試過程中發現問題</div>';
                echo '<div class="info">請根據上述錯誤訊息進行調整</div>';
            }
            echo '</div>';
            ?>
            
            <div style="text-align: center; margin-top: 30px;">
                <button onclick="location.reload()" style="background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%); color: white; border: none; padding: 15px 30px; border-radius: 8px; font-size: 16px; cursor: pointer;">
                    🔄 重新測試
                </button>
            </div>
        </div>
    </div>
</body>
</html> 