<?php
// setup_floor_contacts.php

require_once __DIR__ . '/app/Models/Database.php';

// 資料庫連線
$db = new Database();
$pdo = $db->getConnection();

try {
    // 開始交易
    $pdo->beginTransaction();

    // 建立資料表
    $sql = [
        // 樓層資訊表
        "CREATE TABLE IF NOT EXISTS floor_info (
            id INT AUTO_INCREMENT PRIMARY KEY,
            floor_number INT NOT NULL,
            floor_name VARCHAR(100) NOT NULL,
            floor_description TEXT,
            floor_type VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        // 部門聯絡資訊表
        "CREATE TABLE IF NOT EXISTS department_contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            department_name VARCHAR(100) NOT NULL,
            floor_number INT NOT NULL,
            extension_range VARCHAR(50),
            email VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        // 員工座位表
        "CREATE TABLE IF NOT EXISTS employee_seats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_name VARCHAR(50) NOT NULL,
            floor_number INT NOT NULL,
            seat_number VARCHAR(20) NOT NULL,
            department_id INT,
            extension_number VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        // 分機號碼表
        "CREATE TABLE IF NOT EXISTS extension_numbers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            extension_number VARCHAR(20) NOT NULL,
            employee_name VARCHAR(50) NOT NULL,
            department_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];

    // 執行建立資料表的 SQL
    foreach ($sql as $query) {
        $pdo->exec($query);
    }

    // 插入基本樓層資訊
    $floorInfo = [
        [7, '主管辦公區', '總經理室、副總經理室、高階主管會議室、秘書處', 'executive'],
        [6, '編輯部門', '總編輯室、編輯部辦公區、編輯會議室、資料室', 'editorial'],
        [5, '業務行銷部門', '業務部、行銷部、業務會議室、客服中心', 'sales'],
        [4, '製作發行部門', '製作部、發行部、製作會議室、樣書室', 'production'],
        [3, '行政支援部門', '人事行政部、財務會計部、資訊部、員工休息室', 'admin']
    ];

    $stmt = $pdo->prepare("INSERT INTO floor_info (floor_number, floor_name, floor_description, floor_type) VALUES (?, ?, ?, ?)");
    foreach ($floorInfo as $floor) {
        $stmt->execute($floor);
    }

    // 插入基本部門聯絡資訊
    $departmentInfo = [
        ['總經理辦公室', 7, '101', 'ceo@bookrep.com.tw'],
        ['編輯部', 6, '201-210', 'editorial@bookrep.com.tw'],
        ['業務部', 5, '301-315', 'sales@bookrep.com.tw'],
        ['行銷部', 5, '316-325', 'marketing@bookrep.com.tw'],
        ['製作部', 4, '401-410', 'production@bookrep.com.tw'],
        ['人事行政部', 3, '501-505', 'hr@bookrep.com.tw'],
        ['財務會計部', 3, '601-608', 'finance@bookrep.com.tw'],
        ['資訊部', 3, '701-705', 'it@bookrep.com.tw']
    ];

    $stmt = $pdo->prepare("INSERT INTO department_contacts (department_name, floor_number, extension_range, email) VALUES (?, ?, ?, ?)");
    foreach ($departmentInfo as $dept) {
        $stmt->execute($dept);
    }

    // 提交交易
    $pdo->commit();
    echo "資料表建立成功！基本資料已匯入！\n";

} catch (Exception $e) {
    // 發生錯誤時回滾交易
    $pdo->rollBack();
    echo "錯誤：" . $e->getMessage() . "\n";
}

// 安裝 PhpSpreadsheet（如果尚未安裝）
if (!file_exists('vendor/autoload.php')) {
    echo "\n正在安裝必要的套件...\n";
    exec('composer require phpoffice/phpspreadsheet', $output, $returnVar);
    if ($returnVar === 0) {
        echo "套件安裝成功！\n";
    } else {
        echo "套件安裝失敗，請手動執行：composer require phpoffice/phpspreadsheet\n";
    }
}

// 提示下一步
echo "\n接下來您可以執行：php import_floor_contacts.php 來匯入 Excel 檔案的資料\n"; 