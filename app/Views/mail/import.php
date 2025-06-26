<div class="mail-import-container">
    <div class="page-header">
        <h1>寄件資料批次匯入</h1>
        <p>上傳 CSV 檔案來批次匯入寄件資料</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="import-form-container">
        <form method="POST" action="<?php echo $baseUrl; ?>mail/import" 
              enctype="multipart/form-data" class="import-form">
            
            <div class="form-section">
                <h3>選擇檔案</h3>
                <div class="file-upload-area">
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="file-input">
                    <label for="csv_file" class="file-label">
                        <div class="upload-icon">📄</div>
                        <div class="upload-text">
                            <strong>點擊選擇 CSV 檔案</strong>
                            <span>或拖拽檔案到此區域</span>
                        </div>
                    </label>
                    <div class="file-info" id="fileInfo" style="display: none;"></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="icon">📥</i> 開始匯入
                </button>
                <a href="<?php echo $baseUrl; ?>mail/records" class="btn btn-secondary">
                    <i class="icon">📋</i> 查看記錄
                </a>
            </div>
        </form>
    </div>

    <div class="import-instructions">
        <h3>📋 匯入說明</h3>
        <div class="instructions-content">
            <div class="instruction-item">
                <h4>檔案格式要求</h4>
                <ul>
                    <li>檔案格式：CSV (逗號分隔值)</li>
                    <li>編碼：UTF-8</li>
                    <li>第一行必須是欄位標題</li>
                </ul>
            </div>

            <div class="instruction-item">
                <h4>欄位順序</h4>
                <div class="csv-format">
                    <code>寄件方式,收件者姓名,收件地址,收件者行動電話,費用申報單位,寄件者姓名,寄件者分機</code>
                </div>
            </div>

            <div class="instruction-item">
                <h4>注意事項</h4>
                <ul>
                    <li>「寄件方式」必須是：掛號、黑貓、新竹貨運</li>
                    <li>「收件者姓名」和「寄件方式」為必填欄位</li>
                    <li>系統會自動產生寄件序號</li>
                    <li>登記者會設定為當前登入使用者</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.mail-import-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.import-form-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.file-upload-area {
    border: 2px dashed #7b61ff;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
}

.upload-icon {
    font-size: 3rem;
    color: #7b61ff;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding-top: 2rem;
    border-top: 1px solid #e1e5e9;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #7b61ff, #4caaff);
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.import-instructions {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.instruction-item {
    margin-bottom: 1.5rem;
}

.instruction-item h4 {
    margin: 0 0 1rem 0;
    color: #7b61ff;
    font-size: 1.1rem;
    font-weight: 600;
}

.csv-format {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.csv-format code {
    color: #333;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}
</style>

<script>
// 檔案選擇處理
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    
    if (file) {
        fileInfo.innerHTML = `
            <strong>已選擇檔案：</strong> ${file.name}<br>
            <strong>檔案大小：</strong> ${(file.size / 1024).toFixed(2)} KB<br>
            <strong>檔案類型：</strong> ${file.type || '未知'}
        `;
        fileInfo.style.display = 'block';
        
        // 更新上傳區域樣式
        const uploadArea = document.querySelector('.file-upload-area');
        uploadArea.style.borderColor = '#28a745';
        uploadArea.style.backgroundColor = 'rgba(40, 167, 69, 0.05)';
    } else {
        fileInfo.style.display = 'none';
    }
});

// 拖拽上傳
const uploadArea = document.querySelector('.file-upload-area');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#4caaff';
    this.style.backgroundColor = 'rgba(123, 97, 255, 0.1)';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = '#7b61ff';
    this.style.backgroundColor = 'transparent';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#7b61ff';
    this.style.backgroundColor = 'transparent';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('csv_file').files = files;
        // 觸發 change 事件
        const event = new Event('change', { bubbles: true });
        document.getElementById('csv_file').dispatchEvent(event);
    }
});

// 下載範本檔案
function downloadTemplate() {
    const csvContent = "寄件方式,收件者姓名,收件地址,收件者行動電話,費用申報單位,寄件者姓名,寄件者分機\n" +
                      "掛號,王小明,台北市信義區信義路五段7號,0912345678,總務部,李大華,1234\n" +
                      "黑貓,陳美麗,新北市板橋區中山路一段158號,0987654321,行政部,張志明,5678";
    
    const blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    
    link.setAttribute("href", url);
    link.setAttribute("download", "寄件匯入範本.csv");
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script> 