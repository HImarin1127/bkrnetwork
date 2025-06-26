<?php
// 測試假日爬蟲功能 - 新版
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 載入必要檔案
require_once 'app/Models/Database.php';
require_once 'app/Models/Model.php';
require_once 'app/Models/HolidayCalendar.php';
require_once 'config/database.php';

echo "<h1>假日行事曆測試</h1>";

try {
    $holidayCalendar = new HolidayCalendar();
    
    echo "<h2>1. 測試爬蟲功能</h2>";
    $holidays = $holidayCalendar->fetchGovernmentHolidays();
    
    if (empty($holidays)) {
        echo "<p style='color: orange;'>⚠️ 沒有爬取到資料，使用預設假日資料</p>";
    } else {
        echo "<p style='color: green;'>✅ 成功取得 " . count($holidays) . " 筆假日資料</p>";
    }
    
    echo "<h3>假日資料:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>月份</th><th>日期</th><th>假日名稱</th></tr>";
    
    foreach ($holidays as $holiday) {
        echo "<tr>";
        echo "<td>" . $holiday['month'] . "月</td>";
        echo "<td>" . $holiday['day'] . "日</td>";
        echo "<td>" . htmlspecialchars($holiday['name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>2. 測試資料庫儲存</h2>";
    $saved = $holidayCalendar->saveHolidays($holidays);
    
    if ($saved) {
        echo "<p style='color: green;'>✅ 資料成功儲存到資料庫</p>";
    } else {
        echo "<p style='color: red;'>❌ 資料儲存失敗</p>";
    }
    
    echo "<h2>3. 測試從資料庫讀取</h2>";
    $savedHolidays = $holidayCalendar->getHolidayCalendar(2025);
    
    if (!empty($savedHolidays)) {
        echo "<p style='color: green;'>✅ 成功從資料庫讀取 " . count($savedHolidays) . " 筆假日資料</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ 資料庫中沒有假日資料</p>";
    }
    
    echo "<h2>4. 測試生成行事曆HTML</h2>";
    try {
        $calendarHTML = $holidayCalendar->generateCalendarHTML(2025);
        echo "<p style='color: green;'>✅ 成功生成行事曆HTML (長度: " . strlen($calendarHTML) . " 字元)</p>";
        
        // 顯示部分HTML預覽
        echo "<h3>行事曆預覽:</h3>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo "<pre>" . htmlspecialchars(substr($calendarHTML, 0, 500)) . "...</pre>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ 生成行事曆HTML失敗: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>5. 測試更新功能</h2>";
    $updateResult = $holidayCalendar->updateHolidays();
    
    echo "<p>更新狀態: <strong>" . $updateResult['status'] . "</strong></p>";
    echo "<p>訊息: " . htmlspecialchars($updateResult['message']) . "</p>";
    if (isset($updateResult['count'])) {
        echo "<p>資料筆數: " . $updateResult['count'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ 測試過程發生錯誤: " . $e->getMessage() . "</p>";
    echo "<p>錯誤詳情: " . $e->getFile() . " 第 " . $e->getLine() . " 行</p>";
    echo "<p>錯誤追蹤:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>6. 系統連結</h2>";
echo "<p><a href='/bkrnetwork/announcements/holidays'>📅 查看假日公告頁面</a></p>";
echo "<p><a href='/bkrnetwork/admin_holiday_update.php'>🔧 管理假日資料</a></p>";
echo "<p><a href='#' onclick='window.location.reload()'>🔄 重新測試</a></p>";

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 15px 0; }
th, td { padding: 8px; text-align: left; }
th { background: #f0f0f0; }
h1 { color: #C8102E; }
h2 { color: #333; border-bottom: 2px solid #C8102E; padding-bottom: 5px; }
a { color: #C8102E; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>";
?> 