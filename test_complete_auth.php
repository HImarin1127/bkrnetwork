<?php
// 測試完整的 LDAP 認證與欄位映射

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/Services/LdapService.php';
require_once 'app/Models/User.php';

echo "<h1>完整 LDAP 認證測試</h1>";

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h2>測試結果</h2>";
    echo "<p>測試帳號：<strong>{$username}</strong></p>";
    
    try {
        // 測試 LDAP 服務
        echo "<h3>1. LDAP 服務測試</h3>";
        $ldapService = new LdapService();
        
        // 測試連接
        echo "<p>正在測試 LDAP 連接...</p>";
        if ($ldapService->testConnection()) {
            echo "<p style='color:green'>✓ LDAP 連接成功</p>";
        } else {
            echo "<p style='color:red'>✗ LDAP 連接失敗</p>";
            throw new Exception("LDAP 連接失敗");
        }
        
        // 測試認證
        echo "<p>正在進行 LDAP 認證...</p>";
        $ldapData = $ldapService->authenticate($username, $password);
        
        if ($ldapData) {
            echo "<p style='color:green'>✓ LDAP 認證成功</p>";
            
            echo "<h4>LDAP 回傳的資料：</h4>";
            echo "<table border='1' style='border-collapse:collapse; margin:10px 0'>";
            echo "<tr style='background-color:#f0f0f0'><th>欄位</th><th>值</th></tr>";
            foreach ($ldapData as $key => $value) {
                echo "<tr><td><strong>{$key}</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:red'>✗ LDAP 認證失敗</p>";
            throw new Exception("LDAP 認證失敗");
        }
        
        // 測試 User Model
        echo "<h3>2. User Model 測試</h3>";
        
        $userModel = new User();
        
        // 嘗試透過 User::authenticate 進行認證
        echo "<p>正在透過 User Model 進行認證...</p>";
        $authenticatedUser = $userModel->authenticate($username, $password);
        
        if ($authenticatedUser) {
            echo "<p style='color:green'>✓ User Model 認證成功</p>";
            
            echo "<h4>User Model 處理後的資料：</h4>";
            echo "<table border='1' style='border-collapse:collapse; margin:10px 0'>";
            echo "<tr style='background-color:#f0f0f0'><th>欄位</th><th>值</th><th>說明</th></tr>";
            echo "<tr><td><strong>id</strong></td><td>" . htmlspecialchars($authenticatedUser['id']) . "</td><td>應該是 uid</td></tr>";
            echo "<tr><td><strong>username</strong></td><td>" . htmlspecialchars($authenticatedUser['username']) . "</td><td>應該是 uid</td></tr>";
            echo "<tr><td><strong>name</strong></td><td>" . htmlspecialchars($authenticatedUser['name']) . "</td><td>應該是 gecos（中文姓名）</td></tr>";
            echo "<tr><td><strong>department</strong></td><td>" . htmlspecialchars($authenticatedUser['department'] ?? '未設定') . "</td><td>應該是 departmentNumber</td></tr>";
            echo "<tr><td><strong>email</strong></td><td>" . htmlspecialchars($authenticatedUser['email'] ?? '未設定') . "</td><td>應該是 mail</td></tr>";
            echo "<tr><td><strong>role</strong></td><td>" . htmlspecialchars($authenticatedUser['role'] ?? '未設定') . "</td><td>系統角色</td></tr>";
            echo "</table>";
            
            // 檢查資料庫中的記錄
            echo "<h3>3. 資料庫記錄檢查</h3>";
            
                         $storedUser = $userModel->findBy('username', $username);
            if ($storedUser) {
                echo "<p style='color:green'>✓ 資料庫中找到使用者記錄</p>";
                
                echo "<h4>資料庫中的記錄：</h4>";
                echo "<table border='1' style='border-collapse:collapse; margin:10px 0'>";
                echo "<tr style='background-color:#f0f0f0'><th>欄位</th><th>值</th><th>檢查</th></tr>";
                
                $checks = [
                    'id' => ['value' => $storedUser['id'], 'expected' => $username, 'description' => '應該與 username 相同'],
                    'username' => ['value' => $storedUser['username'], 'expected' => $username, 'description' => '登入帳號'],
                    'name' => ['value' => $storedUser['name'], 'expected' => $ldapData['name'] ?? null, 'description' => '中文姓名（來自 gecos）'],
                    'department' => ['value' => $storedUser['department'] ?? '', 'expected' => $ldapData['department'] ?? '', 'description' => '部門（來自 departmentNumber）'],
                    'email' => ['value' => $storedUser['email'] ?? '', 'expected' => $ldapData['email'] ?? '', 'description' => '電子郵件'],
                    'password' => ['value' => $storedUser['password'], 'expected' => 'LDAP_AUTH_ONLY', 'description' => '不應儲存真實密碼']
                ];
                
                foreach ($checks as $field => $check) {
                    $value = htmlspecialchars($check['value']);
                    $expected = htmlspecialchars($check['expected']);
                    $match = ($check['value'] == $check['expected']) ? '✓' : '✗';
                    $color = ($check['value'] == $check['expected']) ? 'green' : 'red';
                    
                    echo "<tr>";
                    echo "<td><strong>{$field}</strong></td>";
                    echo "<td>{$value}</td>";
                    echo "<td style='color:{$color}'>{$match} {$check['description']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // 特別檢查密碼是否正確設定
                if ($storedUser['password'] === 'LDAP_AUTH_ONLY') {
                    echo "<p style='color:green'>✓ 密碼正確設定為 LDAP_AUTH_ONLY（不儲存真實密碼）</p>";
                } else {
                    echo "<p style='color:red'>✗ 密碼未正確設定，應為 LDAP_AUTH_ONLY</p>";
                }
            } else {
                echo "<p style='color:red'>✗ 資料庫中找不到使用者記錄</p>";
            }
            
        } else {
            echo "<p style='color:red'>✗ User Model 認證失敗</p>";
        }
        
        echo "<hr>";
        echo "<h3>🎉 測試總結</h3>";
        
        if ($ldapData && $authenticatedUser) {
            echo "<div style='background-color:#e8f5e8; padding:15px; border-left:4px solid #4CAF50'>";
            echo "<h4>成功！欄位映射驗證：</h4>";
            echo "<ul>";
            echo "<li><strong>id & username</strong>: {$authenticatedUser['username']} (來自 LDAP uid)</li>";
            echo "<li><strong>name</strong>: {$authenticatedUser['name']} (來自 LDAP gecos)</li>";
            echo "<li><strong>department</strong>: " . ($authenticatedUser['department'] ?? '未設定') . " (來自 LDAP departmentNumber)</li>";
            echo "<li><strong>password</strong>: 不儲存真實密碼，使用 LDAP_AUTH_ONLY 標記</li>";
            echo "</ul>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background-color:#ffe8e8; padding:15px; border-left:4px solid #f44336'>";
        echo "<h4>錯誤：</h4>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
} else {
    // 顯示輸入表單
    echo "<p>請輸入 LDAP 使用者帳號密碼來測試完整的認證流程：</p>";
    echo "<form method='POST'>";
    echo "<table>";
    echo "<tr><td>帳號：</td><td><input type='text' name='username' value='ldapnormal' required style='padding:5px; width:200px'></td></tr>";
    echo "<tr><td>密碼：</td><td><input type='password' name='password' required style='padding:5px; width:200px'></td></tr>";
    echo "<tr><td></td><td><input type='submit' value='測試完整認證' style='padding:10px 20px; background-color:#4CAF50; color:white; border:none; cursor:pointer'></td></tr>";
    echo "</table>";
    echo "</form>";
    
    echo "<h3>測試說明</h3>";
    echo "<ul>";
    echo "<li>會測試 LDAP 服務連接與認證</li>";
    echo "<li>驗證欄位映射是否正確：uid → username/id，gecos → name，departmentNumber → department</li>";
    echo "<li>檢查 User Model 是否正確處理 LDAP 資料</li>";
    echo "<li>確認資料庫中的記錄是否正確（包含密碼設定）</li>";
    echo "<li>建議先用 ldapnormal 測試</li>";
    echo "</ul>";
}
?> 