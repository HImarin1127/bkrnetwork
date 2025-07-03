<?php
// 資料庫連線設定
$host = 'localhost';
$dbname = 'bkrnetwork';
$username = 'root';
$password = '';

try {
    // 建立資料庫連線
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 預設的員工座位資料
    $employeeSeats = [
        ['張文書', 7, 'A01', 1, '101'],
        ['李編輯', 6, 'B03', 2, '201'],
        ['王業務', 5, 'C02', 3, '301'],
        ['陳行銷', 5, 'C05', 4, '316'],
        ['林製作', 4, 'D01', 5, '401'],
        ['周人事', 3, 'E02', 6, '501'],
        ['吳會計', 3, 'E05', 7, '601'],
        ['黃資訊', 3, 'E08', 8, '701']
    ];

    // 預設的分機號碼資料
    $extensionNumbers = [
        ['101', '張文書', 1, '總經理室分機'],
        ['201', '李編輯', 2, '編輯部主任分機'],
        ['301', '王業務', 3, '業務部經理分機'],
        ['316', '陳行銷', 4, '行銷部主任分機'],
        ['401', '林製作', 5, '製作部經理分機'],
        ['501', '周人事', 6, '人事主任分機'],
        ['601', '吳會計', 7, '會計主任分機'],
        ['701', '黃資訊', 8, 'IT主管分機']
    ];

    // 插入員工座位資料
    $stmt = $conn->prepare("INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($employeeSeats as $seat) {
        $stmt->execute($seat);
    }
    
    echo "員工座位資料已成功匯入\n";
    
    // 插入分機號碼資料
    $stmt = $conn->prepare("INSERT INTO extension_numbers (extension_number, employee_name, department_id, description) VALUES (?, ?, ?, ?)");
    
    foreach ($extensionNumbers as $extension) {
        $stmt->execute($extension);
    }
    
    echo "分機號碼資料已成功匯入\n";
    
} catch (PDOException $e) {
    echo "錯誤：" . $e->getMessage() . "\n";
} 