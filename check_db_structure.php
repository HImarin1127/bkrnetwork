<?php
// 檢查資料庫結構

require_once 'app/Models/Database.php';

echo "<h1>資料庫結構檢查</h1>";

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo "<h2>users 資料表結構</h2>";
    
    $result = $connection->query("DESCRIBE users");
    
    if ($result) {
        echo "<table border='1' style='border-collapse:collapse'>";
        echo "<tr><th>欄位</th><th>類型</th><th>Null</th><th>Key</th><th>預設值</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // 檢查 id 欄位是否為 AUTO_INCREMENT
        $result = $connection->query("DESCRIBE users");
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] === 'id') {
                if (strpos($row['Extra'], 'auto_increment') !== false) {
                    echo "<div style='background-color:#ffe8e8; padding:15px; border-left:4px solid #f44336; margin:10px 0'>";
                    echo "<h3>⚠️ 發現問題</h3>";
                    echo "<p><code>id</code> 欄位設定為 <strong>AUTO_INCREMENT</strong>，這會導致無法手動指定 id 值。</p>";
                    echo "<p>需要修改資料庫結構或調整程式邏輯。</p>";
                    echo "</div>";
                    
                    echo "<h3>解決方案建議</h3>";
                    echo "<div style='background-color:#e8f5e8; padding:15px; border-left:4px solid #4CAF50'>";
                    echo "<h4>方案 1：修改資料庫結構（建議）</h4>";
                    echo "<p>執行以下 SQL 命令來移除 AUTO_INCREMENT：</p>";
                    echo "<pre style='background-color:#f0f0f0; padding:10px; border:1px solid #ddd'>ALTER TABLE users MODIFY COLUMN id VARCHAR(50) NOT NULL PRIMARY KEY;</pre>";
                    
                    echo "<h4>方案 2：程式邏輯調整</h4>";
                    echo "<p>在 User Model 中特別處理 LDAP 用戶的 id 分配。</p>";
                    echo "</div>";
                    
                } else {
                    echo "<div style='background-color:#e8f5e8; padding:15px; border-left:4px solid #4CAF50; margin:10px 0'>";
                    echo "<h3>✅ 結構正常</h3>";
                    echo "<p><code>id</code> 欄位沒有 AUTO_INCREMENT，可以手動指定值。</p>";
                    echo "</div>";
                }
                break;
            }
        }
        
    } else {
        echo "<p style='color:red'>無法查詢資料表結構</p>";
    }
    
    echo "<h2>現有 users 記錄</h2>";
    
    $result = $connection->query("SELECT id, username, name, auth_source FROM users ORDER BY id");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse:collapse'>";
        echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Auth Source</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['auth_source']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>資料表為空或查詢失敗</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>錯誤：" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 