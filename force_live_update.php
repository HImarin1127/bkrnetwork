<?php
// force_live_update.php
// 觸發全新的爬蟲，從網路即時抓取、解析並儲存最新的假日資料。

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "==================================================\n";
echo "開始執行【線上即時】假日資料更新作業...\n";
echo "==================================================\n";

// 自動載入器
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

try {
    echo "\n[步驟 1/2] 正在建立 HolidayCalendar 物件...\n";
    $calendar = new App\Models\HolidayCalendar();
    echo "-> 物件建立成功。\n";
    
    echo "\n[步驟 2/2] 正在呼叫 updateHolidays 方法，觸發完整的線上更新流程...\n";
    $calendar->updateHolidays();
    echo "-> 更新流程已觸發。請查看上面的訊息確認抓取和寫入狀態。\n";

} catch (Exception $e) {
    echo "\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
    echo "在作業過程中發生嚴重錯誤：\n";
    echo $e->getMessage() . "\n";
    echo "檔案：" . $e->getFile() . "，行號：" . $e->getLine() . "\n";
    echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
}

echo "\n==================================================\n";
echo "【線上即時】更新作業結束。\n";
echo "==================================================\n";
?> 