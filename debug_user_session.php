<?php
session_start();

require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';

echo "<h1>使用者Session與權限Debug工具</h1>";
echo "<hr>";

echo "<h2>1. Session 資料檢查</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>✅ 已登入</strong></p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Session Key</th><th>Value</th></tr>";
    
    $sessionKeys = ['user_id', 'username', 'user_role', 'user_name', 'auth_source'];
    foreach ($sessionKeys as $key) {
        $value = $_SESSION[$key] ?? '未設定';
        echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h2>2. 資料庫使用者資料檢查</h2>";
    try {
        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);
        
        if ($user) {
            echo "<p><strong>✅ 資料庫中找到使用者</strong></p>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>欄位</th><th>值</th></tr>";
            foreach ($user as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p><strong>❌ 資料庫中找不到使用者 (ID: {$_SESSION['user_id']})</strong></p>";
        }
        
        echo "<h2>3. 權限檢查測試</h2>";
        $canManage = $userModel->canManageAnnouncements($_SESSION['user_id']);
        echo "<p><strong>公告管理權限:</strong> " . ($canManage ? '✅ 有權限' : '❌ 無權限') . "</p>";
        
        // 詳細權限檢查邏輯
        echo "<h3>權限檢查詳細過程:</h3>";
        $userCheck = $userModel->find($_SESSION['user_id']);
        if ($userCheck) {
            echo "<p>1. 使用者角色: <strong>" . htmlspecialchars($userCheck['role']) . "</strong></p>";
            echo "<p>2. 使用者姓名: <strong>" . htmlspecialchars($userCheck['name']) . "</strong></p>";
            
            // 檢查管理員權限
            if ($userCheck['role'] === 'admin') {
                echo "<p>3. ✅ 管理員權限 - 有權限</p>";
            } else {
                echo "<p>3. ❌ 非管理員</p>";
                
                // 檢查部門關鍵字
                $name = $userCheck['name'];
                $allowedKeywords = ['資訊', '人資', '總務', '財務'];
                echo "<p>4. 檢查姓名中的部門關鍵字:</p>";
                echo "<ul>";
                
                $hasKeyword = false;
                foreach ($allowedKeywords as $keyword) {
                    $found = strpos($name, $keyword) !== false;
                    $hasKeyword = $hasKeyword || $found;
                    echo "<li><strong>$keyword</strong>: " . ($found ? '✅ 找到' : '❌ 未找到') . "</li>";
                }
                echo "</ul>";
                
                echo "<p>5. 最終結果: " . ($hasKeyword ? '✅ 有權限' : '❌ 無權限') . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p><strong>❌ 錯誤:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p><strong>❌ 未登入</strong></p>";
}

echo "<hr>";
echo "<p><a href='.'>[返回首頁]</a> | <a href='?refresh=1'>[重新整理]</a></p>";
?> 