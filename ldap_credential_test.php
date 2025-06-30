<?php
// LDAP 憑證測試工具 - 專門測試服務帳號憑證
session_start();

$ldapConfig = require __DIR__ . '/config/ldap.php';

function testLdapBind($server, $port, $username, $password, $description) {
    $result = [
        'description' => $description,
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        // 建立連接
        $ldapUrl = "ldap://{$server}:{$port}";
        $connection = ldap_connect($ldapUrl);
        
        if (!$connection) {
            $result['message'] = '無法連接到LDAP伺服器';
            return $result;
        }
        
        $result['details'][] = "✓ 成功連接到 {$ldapUrl}";
        
        // 設定選項
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
        
        $result['details'][] = "✓ LDAP選項設定完成";
        
        // 嘗試綁定
        $bindResult = @ldap_bind($connection, $username, $password);
        
        if ($bindResult) {
            $result['success'] = true;
            $result['message'] = '✅ 綁定成功！';
            $result['details'][] = "✓ 成功使用憑證綁定";
            
            // 嘗試搜尋測試
            $searchBase = 'dc=bookrep,dc=com,dc=tw';
            $searchFilter = '(objectClass=*)';
            
            $searchResult = @ldap_search($connection, $searchBase, $searchFilter, ['dn'], 0, 1);
            if ($searchResult) {
                $entries = ldap_get_entries($connection, $searchResult);
                $result['details'][] = "✓ 搜尋測試成功，可以讀取LDAP數據";
            } else {
                $result['details'][] = "⚠️ 綁定成功但搜尋失敗：" . ldap_error($connection);
            }
            
        } else {
            $result['success'] = false;
            $result['message'] = '❌ 綁定失敗：' . ldap_error($connection);
            $result['details'][] = "✗ 綁定失敗，錯誤：" . ldap_error($connection);
        }
        
        ldap_unbind($connection);
        
    } catch (Exception $e) {
        $result['success'] = false;
        $result['message'] = '❌ 發生錯誤：' . $e->getMessage();
    }
    
    return $result;
}

// 準備測試不同的憑證組合
$testCases = [
    [
        'description' => '當前配置的完整DN',
        'username' => $ldapConfig['admin_username'], // uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw
        'password' => $ldapConfig['admin_password']   // Bk22181417#
    ],
    [
        'description' => '只使用用戶名 (ldapuser)',
        'username' => 'ldapuser',
        'password' => $ldapConfig['admin_password']
    ],
    [
        'description' => '嘗試匿名綁定',
        'username' => '',
        'password' => ''
    ],
    [
        'description' => '嘗試簡單的uid格式',
        'username' => 'uid=ldapuser',
        'password' => $ldapConfig['admin_password']
    ],
    [
        'description' => '嘗試cn格式',
        'username' => 'cn=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw',
        'password' => $ldapConfig['admin_password']
    ]
];

// 如果有POST請求，添加自定義測試
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['custom_username'])) {
    $testCases[] = [
        'description' => '自定義測試憑證',
        'username' => $_POST['custom_username'],
        'password' => $_POST['custom_password']
    ];
}

$results = [];
foreach ($testCases as $testCase) {
    $results[] = testLdapBind(
        $ldapConfig['server'],
        $ldapConfig['port'],
        $testCase['username'],
        $testCase['password'],
        $testCase['description']
    );
}

// 嘗試匿名搜尋來檢查LDAP結構
function testAnonymousSearch($server, $port) {
    try {
        $connection = ldap_connect("ldap://{$server}:{$port}");
        if (!$connection) return false;
        
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        
        // 嘗試匿名綁定
        if (!@ldap_bind($connection)) {
            ldap_unbind($connection);
            return false;
        }
        
        // 嘗試搜尋根DN結構
        $search = @ldap_search($connection, 'dc=bookrep,dc=com,dc=tw', '(objectClass=*)', ['dn'], 0, 10);
        if ($search) {
            $entries = ldap_get_entries($connection, $search);
            ldap_unbind($connection);
            return $entries;
        }
        
        ldap_unbind($connection);
        return false;
    } catch (Exception $e) {
        return false;
    }
}

$anonymousResult = testAnonymousSearch($ldapConfig['server'], $ldapConfig['port']);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 憑證測試工具</title>
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
        .test-form {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .test-result {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        
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
        .credential-info {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            word-break: break-all;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background: #0056b3;
        }
        .summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 LDAP 憑證測試工具</h1>
        <p>專門測試LDAP服務帳號憑證，找出正確的綁定方式</p>
        
        <div class="summary">
            <h2>📊 測試摘要</h2>
            <p><strong>LDAP伺服器：</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
            <p><strong>當前配置的服務帳號：</strong></p>
            <div class="credential-info">
                用戶名: <?= htmlspecialchars($ldapConfig['admin_username']) ?><br>
                密碼: <?= htmlspecialchars($ldapConfig['admin_password']) ?>
            </div>
            
            <?php 
            $successCount = count(array_filter($results, function($r) { return $r['success']; }));
            ?>
            <p><strong>測試結果：</strong> <?= $successCount ?> / <?= count($results) ?> 個測試成功</p>
            
            <?php if ($successCount > 0): ?>
                <p style="color: #28a745; font-weight: bold;">✅ 找到可用的憑證！</p>
            <?php else: ?>
                <p style="color: #dc3545; font-weight: bold;">❌ 所有憑證測試都失敗了</p>
            <?php endif; ?>
        </div>

        <div class="test-form">
            <h3>🧪 自定義憑證測試</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="custom_username">用戶名 (DN或簡單用戶名)：</label>
                    <input type="text" id="custom_username" name="custom_username" 
                           placeholder="例如：cn=admin,dc=bookrep,dc=com,dc=tw">
                </div>
                <div class="form-group">
                    <label for="custom_password">密碼：</label>
                    <input type="password" id="custom_password" name="custom_password">
                </div>
                <button type="submit" class="btn">🚀 測試自定義憑證</button>
            </form>
        </div>

        <h2>🔍 憑證測試結果</h2>
        
        <?php foreach ($results as $result): ?>
            <div class="test-result <?= $result['success'] ? 'success' : 'error' ?>">
                <h3><?= $result['success'] ? '✅' : '❌' ?> <?= htmlspecialchars($result['description']) ?></h3>
                <p><strong>結果：</strong> <?= htmlspecialchars($result['message']) ?></p>
                
                <?php if (!empty($result['details'])): ?>
                    <div class="details">
                        <strong>詳細信息：</strong>
                        <ul>
                            <?php foreach ($result['details'] as $detail): ?>
                                <li><?= htmlspecialchars($detail) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($anonymousResult): ?>
            <div class="test-result info">
                <h3>🔍 匿名搜尋結果</h3>
                <p>LDAP允許匿名訪問，發現以下結構：</p>
                <div class="details">
                    <ul>
                        <?php for ($i = 0; $i < min(10, $anonymousResult['count']); $i++): ?>
                            <li><?= htmlspecialchars($anonymousResult[$i]['dn']) ?></li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="test-result warning">
                <h3>⚠️ 匿名搜尋失敗</h3>
                <p>LDAP伺服器不允許匿名訪問，需要有效的憑證。</p>
            </div>
        <?php endif; ?>

        <div class="test-result info">
            <h3>💡 解決建議</h3>
            
            <?php if ($successCount > 0): ?>
                <h4>✅ 找到可用憑證！</h4>
                <p>請將成功的憑證更新到 <code>config/ldap.php</code> 中：</p>
                <?php foreach ($results as $result): ?>
                    <?php if ($result['success']): ?>
                        <div class="credential-info">
                            找到可用憑證：<?= htmlspecialchars($result['description']) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <h4>❌ 沒有找到可用憑證</h4>
                <p>可能的解決方案：</p>
                <ul>
                    <li><strong>檢查密碼：</strong> 確認 ldapuser 的密碼是否正確</li>
                    <li><strong>檢查用戶存在：</strong> 確認 ldapuser 是否存在於LDAP中</li>
                    <li><strong>聯繫LDAP管理員：</strong> 獲取正確的服務帳號憑證</li>
                    <li><strong>檢查網路：</strong> 確認可以連接到LDAP伺服器</li>
                    <li><strong>檢查權限：</strong> 確認服務帳號有讀取權限</li>
                </ul>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="login_test_tool.php"><button class="btn" style="background: #28a745;">🔙 返回登入測試</button></a>
            <a href="ldap_tree_explorer.php"><button class="btn" style="background: #6c757d;">🌳 LDAP結構探索</button></a>
        </div>
    </div>
</body>
</html> 