<?php
// check_db_structure.php - 檢查資料庫結構

require_once __DIR__ . '/app/Models/Database.php';

use App\Models\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "=== 資料庫結構檢查 ===\n\n";
    
    // 檢查資料表是否存在
    $tables = ['floor_info', 'department_contacts', 'employee_seats', 'extension_numbers'];
    
    foreach ($tables as $table) {
        echo "--- $table 表結構 ---\n";
        
        try {
            // 檢查表結構
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($columns as $column) {
                echo "欄位: {$column['Field']} | 類型: {$column['Type']} | 允許NULL: {$column['Null']} | 預設值: {$column['Default']}\n";
            }
            
            // 檢查資料筆數
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "資料筆數: {$count['count']}\n";
            
            // 顯示前 3 筆資料樣本
            if ($count['count'] > 0) {
                echo "資料樣本:\n";
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 3");
                $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($samples as $sample) {
                    echo "  " . json_encode($sample, JSON_UNESCAPED_UNICODE) . "\n";
                }
            }
            
        } catch (Exception $e) {
            echo "錯誤: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "資料庫連接錯誤: " . $e->getMessage() . "\n";
}
?> 