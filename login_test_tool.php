<?php
// 登入流程詳細測試工具
session_start();

require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Services/LdapService.php';

$testUsername = 'ldapuser';
$testPassword = 'Bk22181417#';

// 如果有POST請求，使用提交的數據
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testUsername = $_POST['username'] ?? 'ldapuser';
    $testPassword = $_POST['password'] ?? 'Bk22181417#';
}

$results = [];

// 測試步驟1：LDAP配置檢查
$results['config'] = [];
$ldapConfig = require __DIR__ . '/config/ldap.php';
$results['config']['enabled'] = $ldapConfig['enabled'];
$results['config']['auto_create_users'] = $ldapConfig['auto_create_users'];
$results['config']['sync_attributes'] = $ldapConfig['sync_attributes'];
$results['config']['fallback_to_local'] = $ldapConfig['fallback_to_local'];

// 測試步驟2：LDAP服務連接測試
$results['ldap_connection'] = [];
try {
    $ldapService = new LdapService();
    $connectionResult = $ldapService->testConnection();
    $results['ldap_connection']['success'] = $connectionResult['success'];
    $results['ldap_connection']['message'] = $connectionResult['message'];
} catch (Exception $e) {
    $results['ldap_connection']['success'] = false;
    $results['ldap_connection']['message'] = 'LDAP服務錯誤：' . $e->getMessage();
}

// 測試步驟3：LDAP認證測試
$results['ldap_auth'] = [];
try {
    $ldapService = new LdapService();
    $ldapUser = $ldapService->authenticate($testUsername, $testPassword);
    if ($ldapUser) {
        $results['ldap_auth']['success'] = true;
        $results['ldap_auth']['message'] = 'LDAP認證成功';
        $results['ldap_auth']['user_data'] = $ldapUser;
    } else {
        $results['ldap_auth']['success'] = false;
        $results['ldap_auth']['message'] = 'LDAP認證失敗';
    }
} catch (Exception $e) {
    $results['ldap_auth']['success'] = false;
    $results['ldap_auth']['message'] = 'LDAP認證錯誤：' . $e->getMessage();
}

// 測試步驟4：本地用戶檢查
$results['local_user'] = [];
try {
    $userModel = new User();
    $localUser = $userModel->findBy('username', $testUsername);
    if ($localUser) {
        $results['local_user']['exists'] = true;
        $results['local_user']['message'] = '本地用戶已存在';
        $results['local_user']['user_data'] = $localUser;
    } else {
        $results['local_user']['exists'] = false;
        $results['local_user']['message'] = '本地用戶不存在，需要自動創建';
    }
} catch (Exception $e) {
    $results['local_user']['exists'] = false;
    $results['local_user']['message'] = '本地用戶檢查錯誤：' . $e->getMessage();
}

// 測試步驟5：完整認證流程測試
$results['full_auth'] = [];
try {
    $userModel = new User();
    $authenticatedUser = $userModel->authenticate($testUsername, $testPassword);
    if ($authenticatedUser) {
        $results['full_auth']['success'] = true;
        $results['full_auth']['message'] = '完整認證流程成功！';
        $results['full_auth']['user_data'] = $authenticatedUser;
        
        // 模擬登入流程設定session
        $_SESSION['test_user_id'] = $authenticatedUser['id'];
        $_SESSION['test_username'] = $authenticatedUser['username'];
        $_SESSION['test_user_role'] = $authenticatedUser['role'];
        $_SESSION['test_user_name'] = $authenticatedUser['name'];
        $_SESSION['test_auth_source'] = $authenticatedUser['auth_source'] ?? 'local';
        
    } else {
        $results['full_auth']['success'] = false;
        $results['full_auth']['message'] = '完整認證流程失敗';
    }
} catch (Exception $e) {
    $results['full_auth']['success'] = false;
    $results['full_auth']['message'] = '完整認證流程錯誤：' . $e->getMessage();
}

// 測試步驟6：重新檢查本地用戶（看是否自動創建了）
$results['local_user_after'] = [];
try {
    $userModel = new User();
    $localUserAfter = $userModel->findBy('username', $testUsername);
    if ($localUserAfter) {
        $results['local_user_after']['exists'] = true;
        $results['local_user_after']['message'] = '本地用戶現在已存在';
        $results['local_user_after']['user_data'] = $localUserAfter;
    } else {
        $results['local_user_after']['exists'] = false;
        $results['local_user_after']['message'] = '本地用戶仍然不存在';
    }
} catch (Exception $e) {
    $results['local_user_after']['exists'] = false;
    $results['local_user_after']['message'] = '認證後本地用戶檢查錯誤：' . $e->getMessage();
}

function getStatusIcon($success) {
    return $success ? '✅' : '❌';
}

function getStatusClass($success) {
    return $success ? 'success' : 'error';
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入流程詳細測試工具</title>
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
        .step {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .step h3 {
            margin: 0 0 15px 0;
            font-size: 1.2rem;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .data-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        .data-box pre {
            margin: 0;
            font-size: 0.9rem;
            white-space: pre-wrap;
            word-wrap: break-word;
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
        .summary h2 {
            color: #856404;
            margin: 0 0 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 登入流程詳細測試工具</h1>
        <p>逐步測試LDAP登入的每個環節，找出具體問題所在</p>
        
        <div class="test-form">
            <h3>🔧 測試參數設定</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="username">測試帳號：</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($testUsername) ?>">
                </div>
                <div class="form-group">
                    <label for="password">測試密碼：</label>
                    <input type="password" id="password" name="password" value="<?= htmlspecialchars($testPassword) ?>">
                </div>
                <button type="submit" class="btn">🚀 開始測試</button>
            </form>
        </div>

        <div class="summary">
            <h2>📊 測試總結</h2>
            <p><strong>測試帳號：</strong> <?= htmlspecialchars($testUsername) ?></p>
            <p><strong>最終結果：</strong> <?= $results['full_auth']['success'] ? '✅ 登入成功！' : '❌ 登入失敗' ?></p>
            <?php if ($results['full_auth']['success']): ?>
                <p style="color: #28a745; font-weight: bold;">🎉 恭喜！您的LDAP登入已經正常工作了！</p>
                <p>現在可以前往正式登入頁面使用這個帳號密碼登入。</p>
            <?php else: ?>
                <p style="color: #dc3545; font-weight: bold;">⚠️ 登入仍有問題，請查看下面詳細步驟找出原因。</p>
            <?php endif; ?>
        </div>

        <!-- 步驟1：LDAP配置檢查 -->
        <div class="step info">
            <h3>📋 步驟1：LDAP配置檢查</h3>
            <p><strong>LDAP啟用：</strong> <?= $results['config']['enabled'] ? '✅ 是' : '❌ 否' ?></p>
            <p><strong>自動創建用戶：</strong> <?= $results['config']['auto_create_users'] ? '✅ 是' : '❌ 否' ?></p>
            <p><strong>同步屬性：</strong> <?= $results['config']['sync_attributes'] ? '✅ 是' : '❌ 否' ?></p>
            <p><strong>回歸本地認證：</strong> <?= $results['config']['fallback_to_local'] ? '✅ 是' : '❌ 否' ?></p>
        </div>

        <!-- 步驟2：LDAP連接測試 -->
        <div class="step <?= getStatusClass($results['ldap_connection']['success']) ?>">
            <h3><?= getStatusIcon($results['ldap_connection']['success']) ?> 步驟2：LDAP伺服器連接測試</h3>
            <p><strong>結果：</strong> <?= htmlspecialchars($results['ldap_connection']['message']) ?></p>
        </div>

        <!-- 步驟3：LDAP認證測試 -->
        <div class="step <?= getStatusClass($results['ldap_auth']['success']) ?>">
            <h3><?= getStatusIcon($results['ldap_auth']['success']) ?> 步驟3：LDAP認證測試</h3>
            <p><strong>結果：</strong> <?= htmlspecialchars($results['ldap_auth']['message']) ?></p>
            <?php if (isset($results['ldap_auth']['user_data'])): ?>
                <div class="data-box">
                    <strong>LDAP用戶資料：</strong>
                    <pre><?= json_encode($results['ldap_auth']['user_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <!-- 步驟4：本地用戶檢查（認證前） -->
        <div class="step <?= getStatusClass($results['local_user']['exists']) ?>">
            <h3><?= getStatusIcon($results['local_user']['exists']) ?> 步驟4：本地用戶檢查（認證前）</h3>
            <p><strong>結果：</strong> <?= htmlspecialchars($results['local_user']['message']) ?></p>
            <?php if (isset($results['local_user']['user_data'])): ?>
                <div class="data-box">
                    <strong>本地用戶資料：</strong>
                    <pre><?= json_encode($results['local_user']['user_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <!-- 步驟5：完整認證流程測試 -->
        <div class="step <?= getStatusClass($results['full_auth']['success']) ?>">
            <h3><?= getStatusIcon($results['full_auth']['success']) ?> 步驟5：完整認證流程測試</h3>
            <p><strong>結果：</strong> <?= htmlspecialchars($results['full_auth']['message']) ?></p>
            <?php if (isset($results['full_auth']['user_data'])): ?>
                <div class="data-box">
                    <strong>認證成功用戶資料：</strong>
                    <pre><?= json_encode($results['full_auth']['user_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <!-- 步驟6：本地用戶檢查（認證後） -->
        <div class="step <?= getStatusClass($results['local_user_after']['exists']) ?>">
            <h3><?= getStatusIcon($results['local_user_after']['exists']) ?> 步驟6：本地用戶檢查（認證後）</h3>
            <p><strong>結果：</strong> <?= htmlspecialchars($results['local_user_after']['message']) ?></p>
            <?php if (isset($results['local_user_after']['user_data'])): ?>
                <div class="data-box">
                    <strong>認證後本地用戶資料：</strong>
                    <pre><?= json_encode($results['local_user_after']['user_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="login"><button class="btn" style="background: #28a745;">🔐 前往正式登入頁面</button></a>
            <a href="ldap_debug.php"><button class="btn" style="background: #6c757d;">🧪 其他測試工具</button></a>
        </div>
    </div>
</body>
</html> 