<?php
// test_upload.php - 檔案上傳測試頁面

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h2>檔案上傳測試結果</h2>";
    
    // 檢查 PHP 設定
    echo "<h3>PHP 檔案上傳設定:</h3>";
    echo "file_uploads: " . (ini_get('file_uploads') ? '啟用' : '停用') . "<br>";
    echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
    echo "post_max_size: " . ini_get('post_max_size') . "<br>";
    echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
    echo "memory_limit: " . ini_get('memory_limit') . "<br>";
    
    // 檢查上傳檔案
    echo "<h3>上傳檔案資訊:</h3>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    // 檢查檔案內容（如果是 CSV）
    if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK && $_FILES['test_file']['tmp_name']) {
        $tmpFile = $_FILES['test_file']['tmp_name'];
        
        echo "<h3>檔案內容預覽:</h3>";
        if (($handle = fopen($tmpFile, 'r')) !== false) {
            echo "<pre>";
            $lineCount = 0;
            while (($line = fgets($handle)) !== false && $lineCount < 10) {
                echo htmlspecialchars($line);
                $lineCount++;
            }
            if ($lineCount >= 10) {
                echo "...(顯示前10行)\n";
            }
            fclose($handle);
            echo "</pre>";
            
            // 測試 CSV 解析
            echo "<h3>CSV 解析測試:</h3>";
            if (($handle = fopen($tmpFile, 'r')) !== false) {
                echo "<table border='1'>";
                $rowCount = 0;
                while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < 5) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                    $rowCount++;
                }
                echo "</table>";
                fclose($handle);
            }
        } else {
            echo "無法讀取檔案";
        }
    } else {
        echo "<h3>檔案上傳錯誤:</h3>";
        $errors = [
            UPLOAD_ERR_OK => '沒有錯誤',
            UPLOAD_ERR_INI_SIZE => '檔案大小超過 upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => '檔案大小超過 MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => '檔案只有部分被上傳',
            UPLOAD_ERR_NO_FILE => '沒有檔案被上傳',
            UPLOAD_ERR_NO_TMP_DIR => '找不到暫存目錄',
            UPLOAD_ERR_CANT_WRITE => '檔案寫入失敗',
            UPLOAD_ERR_EXTENSION => 'PHP 擴充功能停止了檔案上傳'
        ];
        echo $errors[$_FILES['test_file']['error']] ?? '未知錯誤';
    }
    
    echo "<br><br><a href='test_upload.php'>返回測試頁面</a>";
} else {
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>檔案上傳測試</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { padding: 5px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>檔案上傳測試</h1>
    <p>測試 CSV 檔案上傳功能，用於診斷寄件匯入問題。</p>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="test_file">選擇測試檔案（建議使用 CSV 檔案）:</label>
            <input type="file" name="test_file" id="test_file" accept=".csv,.txt" required>
        </div>
        <button type="submit">上傳測試</button>
    </form>
    
    <h3>建議測試用 CSV 內容:</h3>
    <pre>寄件方式,收件者姓名,收件地址,收件者行動電話,費用申報單位,寄件者姓名,寄件者分機
掛號,測試姓名,測試地址,0912345678,測試部門,測試寄件者,1234</pre>
</body>
</html>
<?php
}
?> 