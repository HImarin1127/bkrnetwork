<?php
// 調試公告系統的腳本
require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/Announcement.php';

echo "<h1>🔍 公告系統調試工具</h1>\n";

try {
    // 測試資料庫連接
    echo "<h2>1. 測試資料庫連接</h2>\n";
    $db = Database::getInstance();
    echo "✅ 資料庫連接成功\n";
    
    // 檢查公告表結構
    echo "<h2>2. 檢查公告表結構</h2>\n";
    $result = $db->fetchAll("DESCRIBE announcements");
    echo "<pre>\n";
    foreach ($result as $row) {
        echo sprintf("%-20s %-20s %-10s %-10s %-20s %-20s\n", 
            $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
    }
    echo "</pre>\n";
    
    // 檢查現有公告數量
    echo "<h2>3. 檢查現有公告</h2>\n";
    $announcement = new Announcement();
    $announcements = $announcement->getAllAnnouncementsWithAuthor();
    echo "總公告數量：" . count($announcements) . "\n";
    echo "<pre>\n";
    foreach ($announcements as $ann) {
        echo sprintf("ID: %d | 標題: %s | 類型: %s | 狀態: %s | 建立時間: %s\n", 
            $ann['id'], $ann['title'], $ann['type'], $ann['status'], $ann['created_at']);
    }
    echo "</pre>\n";
    
    // 測試新增公告
    echo "<h2>4. 測試新增公告</h2>\n";
    $testData = [
        'title' => '測試公告 - ' . date('Y-m-d H:i:s'),
        'content' => '這是一個測試公告，用於調試系統',
        'type' => 'general',
        'status' => 'published',
        'date' => date('Y-m-d'),
        'sort_order' => 0
    ];
    
    $newId = $announcement->createAnnouncementWithDetails($testData, 1);
    if ($newId) {
        echo "✅ 成功新增測試公告，ID: $newId\n";
        
        // 立即查詢剛新增的公告
        $newAnnouncement = $announcement->find($newId);
        if ($newAnnouncement) {
            echo "✅ 成功查詢到新增的公告\n";
            echo "<pre>\n";
            print_r($newAnnouncement);
            echo "</pre>\n";
        } else {
            echo "❌ 無法查詢到剛新增的公告\n";
        }
        
        // 重新檢查所有公告
        $allAnnouncements = $announcement->getAllAnnouncementsWithAuthor();
        echo "更新後總公告數量：" . count($allAnnouncements) . "\n";
        
    } else {
        echo "❌ 新增測試公告失敗\n";
    }
    
    // 檢查使用者表
    echo "<h2>5. 檢查使用者表</h2>\n";
    $users = $db->fetchAll("SELECT id, username, name, role FROM users LIMIT 5");
    echo "<pre>\n";
    foreach ($users as $user) {
        echo sprintf("ID: %d | 帳號: %s | 姓名: %s | 角色: %s\n", 
            $user['id'], $user['username'], $user['name'], $user['role']);
    }
    echo "</pre>\n";
    
} catch (Exception $e) {
    echo "❌ 錯誤：" . $e->getMessage() . "\n";
    echo "檔案：" . $e->getFile() . "\n";
    echo "行號：" . $e->getLine() . "\n";
}

echo "<h2>6. 調試完成</h2>\n";
echo "如果上述測試都正常，請檢查：\n";
echo "1. 瀏覽器是否有快取問題\n";
echo "2. Session 是否正常\n";
echo "3. 權限檢查是否正確\n";
echo "4. URL 重導向是否正確\n";
?> 