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

    // 清空現有資料
    $conn->exec("TRUNCATE TABLE employee_seats");
    $conn->exec("TRUNCATE TABLE extension_numbers");
    
    // 直接插入測試資料
    $seatData = [
        // 7F
        ['張大明', 7, 'A01', 1, '101'], // 總經理
        ['王小美', 7, 'A02', 1, '102'], // 副總
        ['李志明', 7, 'A03', 1, '103'], // 特助
        
        // 6F
        ['陳編輯', 6, 'B01', 2, '201'], // 總編輯
        ['林編輯', 6, 'B02', 2, '202'],
        ['周編輯', 6, 'B03', 2, '203'],
        ['吳編輯', 6, 'B04', 2, '204'],
        
        // 5F
        ['黃業務', 5, 'C01', 3, '301'], // 業務經理
        ['趙業務', 5, 'C02', 3, '302'],
        ['孫行銷', 5, 'C03', 4, '316'], // 行銷主任
        ['鄭行銷', 5, 'C04', 4, '317'],
        
        // 4F
        ['朱製作', 4, 'D01', 5, '401'], // 製作經理
        ['馬製作', 4, 'D02', 5, '402'],
        ['方製作', 4, 'D03', 5, '403'],
        
        // 3F
        ['唐人事', 3, 'E01', 6, '501'], // 人事主任
        ['薛會計', 3, 'E02', 7, '601'], // 會計主任
        ['范資訊', 3, 'E03', 8, '701']  // IT主管
    ];

    // 插入座位資料
    $stmt = $conn->prepare("INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES (?, ?, ?, ?, ?)");
    foreach ($seatData as $seat) {
        $stmt->execute($seat);
    }
    echo "座位資料已成功匯入\n";

    // 插入分機資料
    $extData = [
        ['101', '張大明', 1, '總經理室'],
        ['102', '王小美', 1, '副總經理室'],
        ['103', '李志明', 1, '特別助理'],
        ['201', '陳編輯', 2, '總編輯室'],
        ['202', '林編輯', 2, '編輯一組'],
        ['203', '周編輯', 2, '編輯二組'],
        ['204', '吳編輯', 2, '編輯三組'],
        ['301', '黃業務', 3, '業務部經理'],
        ['302', '趙業務', 3, '業務一組'],
        ['316', '孫行銷', 4, '行銷部主任'],
        ['317', '鄭行銷', 4, '行銷企劃'],
        ['401', '朱製作', 5, '製作部經理'],
        ['402', '馬製作', 5, '製作一組'],
        ['403', '方製作', 5, '製作二組'],
        ['501', '唐人事', 6, '人事主任'],
        ['601', '薛會計', 7, '會計主任'],
        ['701', '范資訊', 8, 'IT主管']
    ];

    $stmt = $conn->prepare("INSERT INTO extension_numbers (extension_number, employee_name, department_id, description) VALUES (?, ?, ?, ?)");
    foreach ($extData as $ext) {
        $stmt->execute($ext);
    }
    echo "分機資料已成功匯入\n";

} catch (PDOException $e) {
    echo "錯誤：" . $e->getMessage() . "\n";
} 