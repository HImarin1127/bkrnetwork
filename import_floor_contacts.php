<?php
// import_floor_contacts.php

require 'vendor/autoload.php';
require_once __DIR__ . '/app/Models/Database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// 資料庫連線
$db = new Database();
$pdo = $db->getConnection();

try {
    // 讀取平面座位圖
    echo "開始讀取座位圖...\n";
    $floorPlanReader = IOFactory::createReader('Xlsx');
    $floorPlan = $floorPlanReader->load('簡版平面座位圖(250401).xlsx');
    $floorPlanSheet = $floorPlan->getActiveSheet();

    // 讀取分機表
    echo "開始讀取分機表...\n";
    $extensionReader = IOFactory::createReader('Xlsx');
    $extension = $extensionReader->load('1. 讀書共和國社內分機表(241226).xlsx');
    $extensionSheet = $extension->getActiveSheet();

    // 開始交易
    $pdo->beginTransaction();

    // 清空現有資料
    echo "清空現有資料...\n";
    $pdo->exec("DELETE FROM employee_seats");
    $pdo->exec("DELETE FROM extension_numbers");

    // 處理座位資訊
    echo "處理座位資訊...\n";
    $highestRow = $floorPlanSheet->getHighestRow();
    $currentFloor = null;
    
    for ($row = 1; $row <= $highestRow; $row++) {
        $floorValue = $floorPlanSheet->getCell('A' . $row)->getValue();
        $nameValue = $floorPlanSheet->getCell('B' . $row)->getValue();
        $seatValue = $floorPlanSheet->getCell('C' . $row)->getValue();
        
        // 如果是樓層標記
        if (is_numeric($floorValue) && empty($nameValue)) {
            $currentFloor = $floorValue;
            continue;
        }
        
        // 如果有員工姓名
        if (!empty($nameValue) && $currentFloor) {
            $stmt = $pdo->prepare("
                INSERT INTO employee_seats 
                (employee_name, floor_number, seat_number, department_id)
                SELECT ?, ?, ?, id 
                FROM department_contacts 
                WHERE floor_number = ?
                LIMIT 1
            ");
            
            $stmt->execute([
                trim($nameValue),
                $currentFloor,
                $seatValue ?? '',
                $currentFloor
            ]);
        }
    }

    // 處理分機資訊
    echo "處理分機資訊...\n";
    $highestRow = $extensionSheet->getHighestRow();
    
    for ($row = 2; $row <= $highestRow; $row++) {
        $name = $extensionSheet->getCell('A' . $row)->getValue();
        $extension = $extensionSheet->getCell('B' . $row)->getValue();
        $department = $extensionSheet->getCell('C' . $row)->getValue();
        
        if (!empty($name) && !empty($extension)) {
            // 更新員工座位表的分機號碼
            $stmt = $pdo->prepare("
                UPDATE employee_seats 
                SET extension_number = ? 
                WHERE employee_name = ?
            ");
            $stmt->execute([trim($extension), trim($name)]);

            // 插入分機資訊
            $stmt = $pdo->prepare("
                INSERT INTO extension_numbers 
                (extension_number, employee_name, department_id, description)
                SELECT ?, ?, id, ?
                FROM department_contacts
                WHERE department_name LIKE ?
                LIMIT 1
            ");
            $stmt->execute([
                trim($extension),
                trim($name),
                $department ?? null,
                '%' . ($department ?? '') . '%'
            ]);
        }
    }

    // 提交交易
    $pdo->commit();
    echo "資料匯入成功！\n";

} catch (Exception $e) {
    // 發生錯誤時回滾交易
    $pdo->rollBack();
    echo "錯誤：" . $e->getMessage() . "\n";
    echo "錯誤發生於第 " . $e->getLine() . " 行\n";
    echo "堆疊追蹤：\n" . $e->getTraceAsString() . "\n";
} 