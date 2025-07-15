<?php
// load_dataset.php - 載入最新測試資料集

require_once __DIR__ . '/app/Models/Database.php';

use App\Models\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // 讀取 SQL 檔案
    $sqlFile = __DIR__ . '/latest_company_dataset.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("找不到 SQL 檔案: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("無法讀取 SQL 檔案");
    }
    
    // 分割 SQL 語句（以分號分割）
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    echo "開始載入測試資料集...\n";
    
    // 關閉外鍵檢查
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $executed = 0;
    foreach ($statements as $statement) {
        if (trim($statement)) {
            try {
                $pdo->exec($statement);
                $executed++;
                if ($executed % 10 == 0) {
                    echo "已執行 $executed 個語句...\n";
                }
            } catch (Exception $e) {
                echo "執行語句時發生錯誤: " . $e->getMessage() . "\n";
                echo "語句: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    // 重新啟用外鍵檢查
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n=== 載入完成！總共執行 $executed 個語句 ===\n\n";
    
    // 驗證載入結果
    echo "=== 資料載入驗證 ===\n";
    
    $tables = [
        'floor_info' => '樓層資訊',
        'department_contacts' => '部門聯絡', 
        'employee_seats' => '員工座位',
        'extension_numbers' => '分機號碼'
    ];
    
    foreach ($tables as $table => $name) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "$name: {$count['count']} 筆\n";
    }
    
    echo "\n=== 資料樣本 ===\n";
    
    // 顯示部門聯絡資訊樣本
    echo "部門聯絡資訊:\n";
    $stmt = $pdo->query("SELECT department_name, building, floor_number, extension_range FROM department_contacts LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['department_name']} ({$row['building']}) {$row['floor_number']}F 分機:{$row['extension_range']}\n";
    }
    
    echo "\n員工座位資訊:\n";
    $stmt = $pdo->query("SELECT employee_name, floor_number, seat_number, extension_number FROM employee_seats LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['employee_name']} {$row['floor_number']}F-{$row['seat_number']} 分機:{$row['extension_number']}\n";
    }
    
    echo "\n✅ 資料集載入成功！\n";
    
} catch (Exception $e) {
    echo "❌ 載入失敗: " . $e->getMessage() . "\n";
    echo "詳細錯誤: " . $e->getFile() . " 第 " . $e->getLine() . " 行\n";
}
?> 