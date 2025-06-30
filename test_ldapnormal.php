<?php
// 測試 ldapnormal 帳號
session_start();

$ldapConfig = require __DIR__ . '/config/ldap.php';

// 測試參數
$testUsername = 'ldapnormal';
$testPassword = 'Bk1597531417#';

function testLdapNormalAccount($server, $port, $username, $password) {
    $results = [];
    
    // 測試1：不同的DN格式
    $dnFormats = [
        "uid={$username},cn=users,dc=bookrep,dc=com,dc=tw",
        "cn={$username},cn=users,dc=bookrep,dc=com,dc=tw", 
        $username, // 簡單用戶名
        "uid={$username}",
        "cn={$username},dc=bookrep,dc=com,dc=tw"
    ];
    
    foreach ($dnFormats as $index => $dn) {
        $result = [
            'test' => "測試 " . ($index + 1),
            'dn' => $dn,
            'success' => false,
            'message' => '',
            'details' => [],
            'can_search' => false,
            'can_auth_users' => false
        ];
        
        try {
            $connection = ldap_connect("ldap://{$server}:{$port}");
            if (!$connection) {
                $result['message'] = '無法連接到LDAP伺服器';
                $results[] = $result;
                continue;
            }
            
            $result['details'][] = "✓ 成功連接到 ldap://{$server}:{$port}";
            
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
            
            $result['details'][] = "✓ LDAP選項設定完成";
            
            // 嘗試綁定
            $bindResult = @ldap_bind($connection, $dn, $password);
            
            if ($bindResult) {
                $result['success'] = true;
                $result['message'] = '✅ 綁定成功！';
                $result['details'][] = "✓ 成功使用DN綁定";
                
                // 測試搜尋權限
                $searchResult = @ldap_search($connection, 'dc=bookrep,dc=com,dc=tw', '(objectClass=*)', ['dn'], 0, 5);
                if ($searchResult) {
                    $entries = ldap_get_entries($connection, $searchResult);
                    $result['can_search'] = true;
                    $result['details'][] = "✓ 搜尋測試成功，找到 {$entries['count']} 個項目";
                } else {
                    $result['details'][] = "⚠️ 綁定成功但搜尋失敗：" . ldap_error($connection);
                }
                
                // 測試是否能認證其他用戶（模擬用戶登入）
                $testSearchFilter = "(&(objectClass=inetOrgPerson)(uid=ldapuser))";
                $userSearchResult = @ldap_search($connection, 'cn=users,dc=bookrep,dc=com,dc=tw', $testSearchFilter, ['dn', 'uid', 'cn']);
                if ($userSearchResult) {
                    $userEntries = ldap_get_entries($connection, $userSearchResult);
                    if ($userEntries['count'] > 0) {
                        $result['can_auth_users'] = true;
                        $result['details'][] = "✓ 可以搜尋其他用戶，適合作為服務帳號";
                    }
                }
                
            } else {
                $result['success'] = false;
                $error = ldap_error($connection);
                $result['message'] = "❌ 綁定失敗：{$error}";
                $result['details'][] = "✗ 綁定失敗，錯誤：{$error}";
            }
            
            ldap_unbind($connection);
            
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = '❌ 錯誤：' . $e->getMessage();
        }
        
        $results[] = $result;
    }
    
    return $results;
}

$testResults = testLdapNormalAccount($ldapConfig['server'], $ldapConfig['port'], $testUsername, $testPassword);
$successfulTests = array_filter($testResults, function($r) { return $r['success']; });
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>測試 ldapnormal 帳號</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            margin: 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .test-result {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        
        .credential-display {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            word-break: break-all;
        }
        .details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 0.9rem;
        }
        .details ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .recommended {
            background: #28a745 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 測試 ldapnormal 帳號結果</h1>
        <p>測試新的LDAP服務帳號憑證</p>
        
        <div class="summary">
            <h2>📊 測試摘要</h2>
            <p><strong>LDAP伺服器：</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
            <p><strong>測試帳號：</strong></p>
            <div class="credential-display">
                帳號: <?= htmlspecialchars($testUsername) ?><br>
                密碼: <?= htmlspecialchars($testPassword) ?>
            </div>
            
            <p><strong>測試結果：</strong> <?= count($successfulTests) ?> / <?= count($testResults) ?> 個DN格式成功</p>
            
            <?php if (count($successfulTests) > 0): ?>
                <p style="color: #28a745; font-weight: bold;">🎉 成功！ldapnormal 帳號可以使用！</p>
                
                <?php
                $bestOption = null;
                foreach ($successfulTests as $test) {
                    if ($test['can_search'] && $test['can_auth_users']) {
                        $bestOption = $test;
                        break;
                    }
                }
                if (!$bestOption) {
                    foreach ($successfulTests as $test) {
                        if ($test['can_search']) {
                            $bestOption = $test;
                            break;
                        }
                    }
                }
                if (!$bestOption) {
                    $bestOption = $successfulTests[0];
                }
                ?>
                
                <h3>💎 建議使用的配置：</h3>
                <div class="credential-display">
                    'admin_username' => '<?= htmlspecialchars($bestOption['dn']) ?>',<br>
                    'admin_password' => '<?= htmlspecialchars($testPassword) ?>',
                </div>
                
            <?php else: ?>
                <p style="color: #dc3545; font-weight: bold;">❌ ldapnormal 帳號測試失敗</p>
            <?php endif; ?>
        </div>

        <h2>🔍 詳細測試結果</h2>
        
        <?php foreach ($testResults as $test): ?>
            <div class="test-result <?= $test['success'] ? 'success' : 'error' ?>">
                <h3><?= $test['success'] ? '✅' : '❌' ?> <?= htmlspecialchars($test['test']) ?></h3>
                <p><strong>DN格式：</strong> <code><?= htmlspecialchars($test['dn']) ?></code></p>
                <p><strong>結果：</strong> <?= htmlspecialchars($test['message']) ?></p>
                
                <?php if ($test['success']): ?>
                    <p>
                        <strong>搜尋權限：</strong> <?= $test['can_search'] ? '✅ 有' : '❌ 無' ?> |
                        <strong>用戶認證能力：</strong> <?= $test['can_auth_users'] ? '✅ 有' : '❌ 無' ?>
                    </p>
                    
                    <?php if ($test['can_search'] && $test['can_auth_users']): ?>
                        <p style="color: #28a745; font-weight: bold;">🌟 推薦：這個DN格式最適合作為服務帳號！</p>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!empty($test['details'])): ?>
                    <div class="details">
                        <strong>詳細信息：</strong>
                        <ul>
                            <?php foreach ($test['details'] as $detail): ?>
                                <li><?= htmlspecialchars($detail) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (count($successfulTests) > 0): ?>
            <div class="test-result info">
                <h3>🚀 下一步：更新配置</h3>
                <p>ldapnormal 帳號測試成功！現在可以更新LDAP配置：</p>
                
                <h4>📝 需要更新 config/ldap.php：</h4>
                <?php $recommended = $bestOption ?? $successfulTests[0]; ?>
                <div class="credential-display">
                    'admin_username' => '<?= htmlspecialchars($recommended['dn']) ?>',<br>
                    'admin_password' => '<?= htmlspecialchars($testPassword) ?>',
                </div>
                
                <p>更新後請重新測試登入功能。</p>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <?php if (count($successfulTests) > 0): ?>
                <button onclick="updateConfig()" class="btn recommended">🔧 自動更新配置</button>
            <?php endif; ?>
            <a href="login_test_tool.php" class="btn">🧪 登入測試</a>
            <a href="ldap_credential_test.php" class="btn">🔙 其他測試工具</a>
        </div>
    </div>

    <script>
        function updateConfig() {
            if (confirm('確定要更新LDAP配置為 ldapnormal 帳號嗎？\n\n這會修改 config/ldap.php 文件。')) {
                // 這裡可以添加AJAX請求來更新配置
                alert('請手動更新配置文件，或聯繫管理員協助更新。');
            }
        }
    </script>
</body>
</html> 