<?php
// 檢查公告新增問題的專用診斷工具
session_start();

echo "<h1>🔍 公告系統問題診斷工具</h1>\n";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
.info { color: blue; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>\n";

// 檢查基本配置
echo "<h2>1. 檢查基本配置</h2>\n";

// 檢查是否已登入
echo "<h2>1. Session 狀態檢查</h2>\n";
echo "<ul>\n";

// 檢查 Session 是否已啟動
if (session_status() == PHP_SESSION_ACTIVE) {
    echo "<span class='success'>✅ Session 已啟動</span><br>\n";
} else {
    echo "<span class='error'>❌ Session 未啟動</span><br>\n";
}

// 檢查使用者是否登入
if (isset($_SESSION['username'])) {
    echo "<span class='success'>✅ 使用者已登入，Username: " . htmlspecialchars($_SESSION['username']) . "</span><br>\n";
} else {
    echo "<span class='error'>❌ 使用者未登入</span><br>\n";
}

// 檢查權限
echo "<h2>2. 使用者權限檢查</h2>\n";

if (isset($_SESSION['username'])) {
    try {
        require_once 'app/Models/User.php';
        $userModel = new User();
        
        $username = $_SESSION['username'];
        $user = $userModel->find($username);

        if ($user) {
            echo "<span class='success'>✅ 在資料庫中找到使用者: " . htmlspecialchars($user['name']) . "</span><br>\n";

            $isAdmin = $userModel->isAdmin($username);
            $canManage = $userModel->canManageAnnouncements($username);

            echo "管理員權限 (isAdmin): " . ($isAdmin ? "<span class='success'>是</span>" : "<span class='error'>否</span>") . "<br>\n";
            echo "公告管理權限 (canManageAnnouncements): " . ($canManage ? "<span class='success'>是</span>" : "<span class='error'>否</span>") . "<br>\n";
        } else {
            echo "<span class='error'>❌ 在資料庫中找不到使用者: " . htmlspecialchars($username) . "</span><br>\n";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ 資料庫錯誤: " . $e->getMessage() . "</span><br>\n";
    }
}

// 檢查資料庫連接和公告資料
echo "<h2>3. 公告資料檢查</h2>\n";

try {
    require_once __DIR__ . '/app/Models/Database.php';
    require_once __DIR__ . '/app/Models/Announcement.php';
    
    echo "<span class='success'>✅ 資料庫連接成功</span><br>\n";
    
    $announcement = new Announcement();
    $allAnnouncements = $announcement->getAllAnnouncementsWithAuthor();
    echo "<span class='info'>📊 目前總公告數量: " . count($allAnnouncements) . "</span><br>\n";
    
    echo "<h3>最近的公告</h3>\n";
    $recentAnnouncements = array_slice($allAnnouncements, 0, 5);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr><th>ID</th><th>標題</th><th>類型</th><th>狀態</th><th>作者</th><th>建立時間</th></tr>\n";
    foreach ($recentAnnouncements as $ann) {
        echo "<tr>";
        echo "<td>" . $ann['id'] . "</td>";
        echo "<td>" . htmlspecialchars($ann['title']) . "</td>";
        echo "<td>" . $ann['type'] . "</td>";
        echo "<td>" . $ann['status'] . "</td>";
        echo "<td>" . htmlspecialchars($ann['author_name'] ?? '未知') . "</td>";
        echo "<td>" . $ann['created_at'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ 資料庫錯誤: " . $e->getMessage() . "</span><br>\n";
}

// 檢查路由配置
echo "<h2>4. 全域變數和設定檢查</h2>\n";
echo "<ul>\n";

// 檢查 BASE_URL
if (defined('BASE_URL')) {
    echo "<span class='success'>✅ BASE_URL 已定義</span><br>\n";
} else {
    echo "<span class='error'>❌ BASE_URL 未定義</span><br>\n";
}

echo "</ul>\n";

// 建議的解決步驟
echo "<h2>5. 測試 AuthMiddleware::getCurrentUser()</h2>\n";

if (isset($_SESSION['username'])) {
    try {
        require_once 'app/Middleware/AuthMiddleware.php';
        $currentUser = AuthMiddleware::getCurrentUser();
        echo "<span class='success'>✅ 測試 AuthMiddleware::getCurrentUser() 成功</span><br>\n";
    } catch (Exception $e) {
        echo "<span class='error'>❌ 測試 AuthMiddleware::getCurrentUser() 失敗: " . $e->getMessage() . "</span><br>\n";
    }
}

// 快速測試按鈕
if (isset($_SESSION['username'])) {
    echo "<h2>6. 快速測試</h2>\n";
    echo "<div style='margin: 20px 0;'>\n";
    echo "<a href='" . (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/bkrnetwork/admin/announcements' style='padding: 10px 20px; background: #007cba; color: white; text-decoration: none; border-radius: 5px;'>🔗 測試公告管理頁面</a>\n";
    echo "</div>\n";
}

echo "<h2>7. 診斷完成</h2>\n";
echo "<p>如果問題仍然存在，請提供上述檢查結果給技術人員進一步協助。</p>\n";
?> 