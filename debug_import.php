<?php
// debug_import.php - 寄件匯入功能調試頁面
require_once 'app/Models/Database.php';
require_once 'app/Models/Model.php';
require_once 'app/Models/MailRecord.php';

session_start();

// 模擬一個登入使用者
$mockUserId = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    echo "<h2>🔍 寄件匯入調試結果</h2>";
    
    // 1. 檢查檔案上傳狀態
    echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h3>📁 檔案上傳狀態</h3>";
    echo "<strong>檔案名稱:</strong> " . ($_FILES['csv_file']['name'] ?? 'N/A') . "<br>";
    echo "<strong>檔案大小:</strong> " . ($_FILES['csv_file']['size'] ?? 0) . " bytes<br>";
    echo "<strong>檔案類型:</strong> " . ($_FILES['csv_file']['type'] ?? 'N/A') . "<br>";
    echo "<strong>上傳錯誤碼:</strong> " . ($_FILES['csv_file']['error'] ?? 'N/A') . "<br>";
    echo "<strong>暫存檔案:</strong> " . ($_FILES['csv_file']['tmp_name'] ?? 'N/A') . "<br>";
    echo "</div>";
    
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK && $_FILES['csv_file']['tmp_name']) {
        $tmpFile = $_FILES['csv_file']['tmp_name'];
        
        // 2. 檢查檔案內容
        echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h3>📄 檔案內容預覽</h3>";
        if (($handle = fopen($tmpFile, 'r')) !== false) {
            echo "<pre style='background: white; padding: 10px; border: 1px solid #ddd; overflow-x: auto;'>";
            $lineCount = 0;
            while (($line = fgets($handle)) !== false && $lineCount < 10) {
                echo htmlspecialchars($line);
                $lineCount++;
            }
            fclose($handle);
            echo "</pre>";
        }
        echo "</div>";
        
        // 3. 測試 CSV 解析
        echo "<div style='background: #fff3e0; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h3>🔧 CSV 解析測試</h3>";
        if (($handle = fopen($tmpFile, 'r')) !== false) {
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            $rowCount = 0;
            while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < 5) {
                echo "<tr style='border: 1px solid #ddd;'>";
                foreach ($row as $index => $cell) {
                    $style = $rowCount === 0 ? 'background: #f5f5f5; font-weight: bold;' : '';
                    echo "<td style='border: 1px solid #ddd; padding: 8px; {$style}'>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
                $rowCount++;
            }
            echo "</table>";
            fclose($handle);
        }
        echo "</div>";
        
        // 4. 測試資料庫連接
        echo "<div style='background: #f3e5f5; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h3>🔌 資料庫連接測試</h3>";
        try {
            $mailModel = new MailRecord();
            echo "✅ 資料庫連接成功<br>";
            
            // 測試表是否存在
            $db = Database::getInstance();
            $conn = $db->getConnection();
            $stmt = $conn->query("SHOW TABLES LIKE 'mail_records'");
            if ($stmt->rowCount() > 0) {
                echo "✅ mail_records 表存在<br>";
                
                // 檢查表結構
                $stmt = $conn->query("DESCRIBE mail_records");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "📋 表欄位: " . implode(', ', $columns) . "<br>";
            } else {
                echo "❌ mail_records 表不存在<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ 資料庫連接失敗: " . $e->getMessage() . "<br>";
        }
        echo "</div>";
        
        // 5. 執行實際匯入測試
        echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h3>⚙️ 匯入功能測試</h3>";
        try {
            $mailModel = new MailRecord();
            $result = $mailModel->batchImport($tmpFile, $mockUserId);
            
            echo "<strong>匯入結果:</strong><br>";
            echo "✅ 成功匯入: {$result['imported']} 筆<br>";
            
            if (!empty($result['errors'])) {
                echo "<br><strong>❌ 錯誤列表:</strong><br>";
                foreach ($result['errors'] as $error) {
                    echo "• " . htmlspecialchars($error) . "<br>";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ 匯入過程發生錯誤: " . $e->getMessage() . "<br>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";
        
    } else {
        echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border-radius: 5px; color: #c62828;'>";
        echo "<h3>❌ 檔案上傳失敗</h3>";
        $errors = [
            UPLOAD_ERR_INI_SIZE => '檔案大小超過 upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => '檔案大小超過 MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => '檔案只有部分被上傳',
            UPLOAD_ERR_NO_FILE => '沒有檔案被上傳',
            UPLOAD_ERR_NO_TMP_DIR => '找不到暫存目錄',
            UPLOAD_ERR_CANT_WRITE => '檔案寫入失敗',
            UPLOAD_ERR_EXTENSION => 'PHP 擴充功能停止了檔案上傳'
        ];
        echo $errors[$_FILES['csv_file']['error']] ?? '未知錯誤';
        echo "</div>";
    }
    
    echo "<br><a href='debug_import.php' style='background: #C8102E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 重新測試</a>";
    
} else {
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 寄件匯入調試工具</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        input[type="file"] { 
            width: 100%; 
            padding: 12px; 
            border: 2px dashed #C8102E; 
            border-radius: 8px; 
            background: #fff; 
        }
        button { 
            background: #C8102E; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600; 
        }
        button:hover { background: #a00d26; }
        .example { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 6px; 
            margin: 15px 0; 
            border-left: 4px solid #C8102E; 
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 寄件匯入調試工具</h1>
        <p>這個工具將幫助診斷寄件匯入功能的問題，提供詳細的調試資訊。</p>
        
        <div class="warning">
            <strong>⚠️ 注意：</strong> 這是一個調試工具，實際資料將會被寫入資料庫。測試完成後請記得清理測試資料。
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">📁 選擇 CSV 檔案進行測試:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
            </div>
            <button type="submit">🚀 開始調試測試</button>
        </form>
        
        <div class="example">
            <h3>📋 測試用 CSV 格式範例</h3>
            <p>請準備一個包含以下內容的 CSV 檔案：</p>
            <pre>寄件方式,收件者姓名,收件地址,收件者行動電話,費用申報單位,寄件者姓名,寄件者分機
掛號,測試收件者,台北市信義區信義路五段7號,0912345678,測試部門,測試寄件者,1234
黑貓,張三,新北市板橋區中山路一段158號,0987654321,行政部,李四,5678</pre>
        </div>
        
        <div class="example">
            <h3>✅ 檢查項目</h3>
            <ul>
                <li>📁 檔案上傳狀態檢查</li>
                <li>📄 CSV 檔案內容解析</li>
                <li>🔌 資料庫連接測試</li>
                <li>📋 資料表結構檢查</li>
                <li>⚙️ 實際匯入功能測試</li>
                <li>🐛 詳細錯誤報告</li>
            </ul>
        </div>
    </div>
</body>
</html>
<?php
}
?> 