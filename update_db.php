<?php
try {
    // 建立資料庫連接
    $pdo = new PDO(
        "mysql:host=localhost;dbname=bkrnetwork;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // 設定字符集
    $pdo->exec("SET NAMES utf8mb4");
    
    // 添加大樓欄位
    $pdo->exec("ALTER TABLE department_contacts ADD COLUMN IF NOT EXISTS building VARCHAR(50) AFTER department_name");
    
    // 清空現有資料
    $pdo->exec("DELETE FROM department_contacts");
    
    // 準備插入資料的 SQL 語句
    $sql = "INSERT INTO department_contacts (department_name, building, floor_number, extension_range, email, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // 部門資料
    $departments = [
        ['總裁辦公室', '108-2', 9, '101-105', 'ceo@bookrep.com.tw', '總裁室及秘書處'],
        ['編輯部', '108-3', 6, '201-210', 'editorial@bookrep.com.tw', '負責圖書編輯與出版'],
        ['業務部', '108-4', 5, '301-315', 'sales@bookrep.com.tw', '負責圖書銷售與通路管理'],
        ['行銷部', '108-4', 5, '316-325', 'marketing@bookrep.com.tw', '負責圖書行銷與推廣'],
        ['製作部', '108-3', 8, '401-410', 'production@bookrep.com.tw', '負責圖書製作與印刷'],
        ['人事行政部', '108-3', 3, '501-505', 'hr@bookrep.com.tw', '負責人事管理與行政事務'],
        ['財務會計部', '108-3', 3, '601-608', 'finance@bookrep.com.tw', '負責財務與會計作業'],
        ['資訊部', '108-3', 3, '701-705', 'it@bookrep.com.tw', '負責資訊系統維護與開發'],
        ['物流部', 'nankan', 1, '801-810', 'logistics@bookrep.com.tw', '南崁物流中心']
    ];
    
    // 執行插入
    foreach ($departments as $dept) {
        $stmt->execute($dept);
    }
    
    echo "資料庫更新成功！\n";
    
} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "發生錯誤：" . $e->getMessage() . "\n";
} 