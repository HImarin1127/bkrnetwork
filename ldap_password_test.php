<?php
// LDAP 密碼測試工具 - 使用正確的DN測試不同密碼
session_start();

$ldapConfig = require __DIR__ . '/config/ldap.php';

// 使用已知正確的DN格式
$correctDN = 'uid=ldapuser,cn=users,dc=bookrep,dc=com,dc=tw';

// 準備常見的密碼變體進行測試
$passwordsToTest = [
    'Bk22181417#',        // 當前配置的密碼
    'ldapuser',           // 簡單密碼
    'password',           // 預設密碼
    'admin',              // 管理員密碼
    '123456',             // 簡單數字
    'Bk221814',           // 去掉符號
    'bk22181417#',        // 小寫版本
    'BK22181417#',        // 大寫版本
    '22181417',           // 只有數字部分
    'Bk22181417',         // 去掉#號
    '',                   // 空密碼
];

// 如果有POST請求，添加自定義密碼
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['custom_password'])) {
    $passwordsToTest[] = $_POST['custom_password'];
}

function testPassword($server, $port, $username, $password, $description) {
    $result = [
        'description' => $description,
        'password' => $password,
        'success' => false,
        'message' => '',
        'can_search' => false
    ];
    
    try {
        $connection = ldap_connect("ldap://{$server}:{$port}");
        if (!$connection) {
            $result['message'] = '無法連接到LDAP伺服器';
            return $result;
        }
        
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
        
        // 嘗試綁定
        $bindResult = @ldap_bind($connection, $username, $password);
        
        if ($bindResult) {
            $result['success'] = true;
            $result['message'] = '✅ 密碼正確！綁定成功';
            
            // 測試搜尋權限
            $searchResult = @ldap_search($connection, 'dc=bookrep,dc=com,dc=tw', '(objectClass=*)', ['dn'], 0, 1);
            if ($searchResult) {
                $result['can_search'] = true;
                $result['message'] .= ' 且有搜尋權限';
            } else {
                $result['message'] .= ' 但無搜尋權限';
            }
        } else {
            $result['success'] = false;
            $error = ldap_error($connection);
            $result['message'] = "❌ 密碼錯誤：{$error}";
        }
        
        ldap_unbind($connection);
        
    } catch (Exception $e) {
        $result['success'] = false;
        $result['message'] = '❌ 錯誤：' . $e->getMessage();
    }
    
    return $result;
}

$results = [];
foreach ($passwordsToTest as $index => $password) {
    $description = ($index === 0) ? '當前配置密碼' : "測試密碼 #" . ($index + 1);
    if (empty($password)) {
        $description = '空密碼測試';
    }
    
    $results[] = testPassword(
        $ldapConfig['server'],
        $ldapConfig['port'],
        $correctDN,
        $password,
        $description
    );
}

$successfulPasswords = array_filter($results, function($r) { return $r['success']; });
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 密碼測試工具</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            margin: 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1000px;
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
        .result {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        
        .password-display {
            background: #333;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: monospace;
            margin: 5px 0;
            display: inline-block;
            min-width: 100px;
        }
        .summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔑 LDAP 密碼測試工具</h1>
        <p>使用正確的DN格式測試不同的密碼組合</p>
        
        <div class="summary">
            <h2>📊 測試摘要</h2>
            <p><strong>LDAP伺服器：</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
            <p><strong>使用的DN：</strong></p>
            <div class="password-display"><?= htmlspecialchars($correctDN) ?></div>
            
            <p><strong>測試結果：</strong> <?= count($successfulPasswords) ?> / <?= count($results) ?> 個密碼成功</p>
            
            <?php if (count($successfulPasswords) > 0): ?>
                <p style="color: #28a745; font-weight: bold;">✅ 找到正確密碼！</p>
                <?php foreach ($successfulPasswords as $success): ?>
                    <p><strong>正確密碼：</strong> 
                        <span class="password-display"><?= htmlspecialchars($success['password'] ?: '(空密碼)') ?></span>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #dc3545; font-weight: bold;">❌ 沒有找到正確密碼</p>
            <?php endif; ?>
        </div>

        <div class="test-form">
            <h3>🧪 測試自定義密碼</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="custom_password">輸入要測試的密碼：</label>
                    <input type="password" id="custom_password" name="custom_password" 
                           placeholder="輸入可能的密碼...">
                </div>
                <button type="submit" class="btn">🚀 測試密碼</button>
            </form>
        </div>

        <h2>🔍 密碼測試結果</h2>
        
        <?php foreach ($results as $result): ?>
            <div class="result <?= $result['success'] ? 'success' : 'error' ?>">
                <h4><?= $result['success'] ? '✅' : '❌' ?> <?= htmlspecialchars($result['description']) ?></h4>
                <p><strong>密碼：</strong> 
                    <span class="password-display"><?= htmlspecialchars($result['password'] ?: '(空密碼)') ?></span>
                </p>
                <p><strong>結果：</strong> <?= htmlspecialchars($result['message']) ?></p>
                <?php if ($result['success'] && $result['can_search']): ?>
                    <p style="color: #28a745;">🎉 這個密碼可以用於LDAP服務！</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="result info">
            <h3>💡 下一步建議</h3>
            
            <?php if (count($successfulPasswords) > 0): ?>
                <h4>✅ 找到正確密碼！</h4>
                <p>請更新 <code>config/ldap.php</code> 中的密碼設定：</p>
                <?php foreach ($successfulPasswords as $success): ?>
                    <div class="password-display">
                        'admin_password' => '<?= htmlspecialchars($success['password']) ?>',
                    </div>
                <?php endforeach; ?>
                <p>然後重新測試登入功能。</p>
            <?php else: ?>
                <h4>❌ 沒找到正確密碼</h4>
                <p>建議的解決方案：</p>
                <ul>
                    <li><strong>聯繫LDAP管理員：</strong> 獲取 ldapuser 的正確密碼</li>
                    <li><strong>重設密碼：</strong> 請管理員重設 ldapuser 的密碼</li>
                    <li><strong>使用其他帳號：</strong> 獲取其他有權限的服務帳號</li>
                    <li><strong>檢查帳號狀態：</strong> 確認 ldapuser 帳號是否被鎖定或停用</li>
                </ul>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="ldap_credential_test.php"><button class="btn" style="background: #6c757d;">🔙 返回憑證測試</button></a>
            <a href="login_test_tool.php"><button class="btn" style="background: #28a745;">🧪 登入測試</button></a>
        </div>
    </div>
</body>
</html> 