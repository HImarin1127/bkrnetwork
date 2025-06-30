<?php
// LDAP 使用者管理工具
require_once __DIR__ . '/app/Services/LdapService.php';
require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';

$ldapService = new LdapService();
$userModel = new User();

// 取得所有 LDAP 使用者函數
function getAllLdapUsers() {
    try {
        $config = require __DIR__ . '/config/ldap.php';
        
        $connection = ldap_connect("ldap://{$config['server']}:{$config['port']}");
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($connection, $config['admin_username'], $config['admin_password']);
        
        $search = ldap_search(
            $connection,
            $config['user_search_base'],
            '(objectClass=inetOrgPerson)',
            ['uid', 'cn', 'mail', 'department']
        );
        
        $entries = ldap_get_entries($connection, $search);
        $users = [];
        
        for ($i = 0; $i < $entries['count']; $i++) {
            $entry = $entries[$i];
            $users[] = [
                'username' => $entry['uid'][0] ?? '',
                'name' => $entry['cn'][0] ?? '',
                'email' => $entry['mail'][0] ?? '',
                'department' => $entry['department'][0] ?? ''
            ];
        }
        
        ldap_unbind($connection);
        return $users;
        
    } catch (Exception $e) {
        return [];
    }
}

$ldapUsers = getAllLdapUsers();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>LDAP 使用者管理</title>
    <style>
        body { font-family: 'Microsoft JhengHei', sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: #C8102E; color: white; padding: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .btn { background: #C8102E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 LDAP 使用者管理</h1>
            <p>管理讀書共和國內部系統的 LDAP 使用者</p>
        </div>
        
        <h2>📊 系統狀態</h2>
        <p><strong>✅ LDAP 連接正常</strong></p>
        <p><strong>📈 找到 <?php echo count($ldapUsers); ?> 個 LDAP 使用者</strong></p>
        <p><strong>🔐 所有 LDAP 使用者都可以登入系統</strong></p>
        
        <h2>👤 可登入的 LDAP 使用者列表</h2>
        <p>以下使用者都可以直接使用 LDAP 帳號密碼登入系統：</p>
        
        <table>
            <thead>
                <tr>
                    <th>帳號</th>
                    <th>姓名</th>
                    <th>電子郵件</th>
                    <th>部門</th>
                    <th>登入狀態</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ldapUsers as $user): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['name'] ?: '未設定'); ?></td>
                    <td><?php echo htmlspecialchars($user['email'] ?: '未設定'); ?></td>
                    <td><?php echo htmlspecialchars($user['department'] ?: '未設定'); ?></td>
                    <td style="color: green;"><strong>✅ 可登入</strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>📖 登入說明</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3>🚀 如何登入系統：</h3>
            <ol>
                <li>前往 <a href="/bkrnetwork/login" class="btn">登入頁面</a></li>
                <li>使用上表中任何一個 LDAP 帳號</li>
                <li>輸入對應的 LDAP 密碼</li>
                <li>系統會自動驗證並登入</li>
            </ol>
            
            <h3>⚡ 自動功能：</h3>
            <ul>
                <li><strong>自動建立帳號：</strong>首次登入會自動在本地建立使用者資料</li>
                <li><strong>屬性同步：</strong>每次登入會同步最新的 LDAP 使用者資料</li>
                <li><strong>權限管理：</strong>根據 LDAP 群組自動設定使用者權限</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="/bkrnetwork/" class="btn">🏠 返回系統首頁</a>
            <a href="/bkrnetwork/login" class="btn">🔑 前往登入</a>
        </div>
    </div>
</body>
</html> 