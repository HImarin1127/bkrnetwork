<?php
session_start();

require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';

echo "<h1>使用者Session與權限Debug工具</h1>";
echo "<hr>";

echo "<h2>1. Session 資料檢查</h2>";
if (isset($_SESSION['username'])) {
    echo "<p><strong>✅ 已登入</strong></p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Session Key</th><th>Value</th></tr>";
    
    $sessionKeys = ['username', 'user_role', 'user_name', 'auth_source'];
    foreach ($sessionKeys as $key) {
        $value = $_SESSION[$key] ?? '未設定';
        echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h2>2. 資料庫使用者資料檢查</h2>";
    try {
        $userModel = new User();
        $user = $userModel->find($_SESSION['username']);
        
        if ($user) {
            echo "<p><strong>✅ 資料庫中找到使用者 (Username: {$_SESSION['username']})</strong></p>";
            echo "<h3>從資料庫取得的資料:</h3>";
            echo "<pre>" . print_r($user, true) . "</pre>";
            
            echo "<h2>3. 權限檢查測試</h2>";
            $canManage = $userModel->canManageAnnouncements($_SESSION['username']);
            echo "<p><strong>公告管理權限:</strong> " . ($canManage ? '✅ 是' : '❌ 否') . "</p>";
            
            // 詳細權限檢查邏輯
            echo "<h3>權限檢查詳細過程:</h3>";
            $userCheck = $userModel->find($_SESSION['username']);
            if ($userCheck) {
                echo "<p><strong>✅ 第二次 find() 方法驗證成功</strong></p>";
            } else {
                echo "<p><strong>❌ 第二次 find() 方法驗證失敗</strong></p>";
            }
        } else {
            echo "<p><strong>❌ 資料庫中找不到使用者 (Username: {$_SESSION['username']})</strong></p>";
        }
        
    } catch (Exception $e) {
        echo "<p><strong>資料庫錯誤:</strong> " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p><strong>使用者未登入。</strong></p>";
}

echo "<hr>";
echo "<p><a href='.'>[返回首頁]</a> | <a href='?refresh=1'>[重新整理]</a></p>";
?> 