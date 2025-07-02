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
echo "<h3>登入狀態檢查</h3>\n";
if (isset($_SESSION['user_id'])) {
    echo "<span class='success'>✅ 使用者已登入，ID: " . $_SESSION['user_id'] . "</span><br>\n";
    if (isset($_SESSION['username'])) {
        echo "<span class='info'>👤 使用者名稱: " . $_SESSION['username'] . "</span><br>\n";
    }
    if (isset($_SESSION['name'])) {
        echo "<span class='info'>👤 使用者姓名: " . $_SESSION['name'] . "</span><br>\n";
    }
    if (isset($_SESSION['role'])) {
        echo "<span class='info'>🏷️ 使用者角色: " . $_SESSION['role'] . "</span><br>\n";
    }
} else {
    echo "<span class='error'>❌ 使用者未登入</span><br>\n";
}

// 檢查權限
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/app/Models/User.php';
    $userModel = new User();
    
    echo "<h3>權限檢查</h3>\n";
    $isAdmin = $userModel->isAdmin($_SESSION['user_id']);
    $canManage = $userModel->canManageAnnouncements($_SESSION['user_id']);
    
    echo "<span class='" . ($isAdmin ? 'success' : 'warning') . "'>" . 
         ($isAdmin ? '✅' : '⚠️') . " 管理員權限: " . ($isAdmin ? '是' : '否') . "</span><br>\n";
    echo "<span class='" . ($canManage ? 'success' : 'warning') . "'>" . 
         ($canManage ? '✅' : '⚠️') . " 公告管理權限: " . ($canManage ? '是' : '否') . "</span><br>\n";
    
    if (!$isAdmin && !$canManage) {
        echo "<div class='error'>❌ <strong>問題發現：</strong>您沒有公告管理權限！</div>\n";
        echo "<div class='info'>💡 <strong>解決方案：</strong><br>\n";
        echo "1. 請聯絡管理員提升您的權限<br>\n";
        echo "2. 或在您的姓名中包含以下關鍵字之一：資訊、人資、總務、財務<br>\n";
        echo "3. 或將您的角色改為 admin</div>\n";
    }
}

// 檢查資料庫連接和公告資料
try {
    require_once __DIR__ . '/app/Models/Database.php';
    require_once __DIR__ . '/app/Models/Announcement.php';
    
    echo "<h2>2. 資料庫檢查</h2>\n";
    
    $db = Database::getInstance();
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
echo "<h2>3. 路由配置檢查</h2>\n";
if (file_exists(__DIR__ . '/routes/web.php')) {
    $routes = require __DIR__ . '/routes/web.php';
    $adminRoutes = [];
    foreach ($routes as $path => $handler) {
        if (strpos($path, '/admin/announcements') === 0) {
            $adminRoutes[$path] = $handler;
        }
    }
    
    echo "<span class='success'>✅ 找到 " . count($adminRoutes) . " 個公告管理相關路由</span><br>\n";
    echo "<pre>\n";
    foreach ($adminRoutes as $path => $handler) {
        echo sprintf("%-30s => [%s, %s]\n", $path, $handler[0], $handler[1]);
    }
    echo "</pre>\n";
} else {
    echo "<span class='error'>❌ 路由配置檔案不存在</span><br>\n";
}

// 建議的解決步驟
echo "<h2>4. 建議的解決步驟</h2>\n";
echo "<ol>\n";
echo "<li><strong>檢查權限問題：</strong>確認您有公告管理權限</li>\n";
echo "<li><strong>清除瀏覽器快取：</strong>按 Ctrl+F5 強制重新載入頁面</li>\n";
echo "<li><strong>檢查 URL：</strong>確認新增公告後跳轉到正確的 URL</li>\n";
echo "<li><strong>檢查 Session：</strong>嘗試重新登入</li>\n";
echo "<li><strong>檢查資料庫：</strong>確認公告確實已新增到資料庫</li>\n";
echo "</ol>\n";

// 快速測試按鈕
if (isset($_SESSION['user_id'])) {
    echo "<h2>5. 快速測試</h2>\n";
    echo "<div style='margin: 20px 0;'>\n";
    echo "<a href='" . (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/bkrnetwork/admin/announcements' style='padding: 10px 20px; background: #007cba; color: white; text-decoration: none; border-radius: 5px;'>🔗 測試公告管理頁面</a>\n";
    echo "</div>\n";
}

echo "<h2>6. 診斷完成</h2>\n";
echo "<p>如果問題仍然存在，請提供上述檢查結果給技術人員進一步協助。</p>\n";
?> 