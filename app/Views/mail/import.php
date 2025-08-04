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

    <div class="main-content">
        <div class="content-card">
            <h2 class="form-title">寄件匯入</h2>

            <div class="guideline-container">
                <h3 class="guideline-title">一般使用者寄件流程</h3>
                <div class="mermaid">
                    graph LR
                        A[Step 1: 登記/匯入資料] --> B[Step 2: 將物品送至8F]
                        B --> C[Step 3: 至寄件紀錄<br>查看是否成功]
                        C --> D[Step 4: 至郵資查詢<br>查看郵資]
                </div>
            </div>

            <div class="import-section">
                <p class="section-description">
                    <strong>注意事項：</strong>
                    1. 請確保您提供的 CSV 檔案格式正確，包含所有必要的欄位。
                    2. 系統會自動跳過有錯誤的行，並顯示詳細的錯誤訊息。
                    3. 請根據錯誤訊息修正後重新匯入。
                    4. 匯入成功後，您可以在「寄件紀錄」頁面查看所有匯入的資料。
                </p>
                <div class="import-form-container">
                    <form method="POST" action="<?php echo $baseUrl; ?>/mail/import" 
                          enctype="multipart/form-data" class="import-form" id="importForm">
                        
                        <div class="form-section">
                            <h3>選擇檔案</h3>
                            <div class="file-upload-area" id="fileUploadArea">
                                <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="file-input">
                                <label for="csv_file" class="file-label">
                                    <div class="upload-icon">📄</div>
                                    <div class="upload-text">
                                        <strong>點擊選擇 CSV 檔案</strong>
                                        <span>或拖拽檔案到此區域</span>
                                        <small>支援編碼：UTF-8、Big5、GB2312</small>
                                    </div>
                                </label>
                                <div class="file-info" id="fileInfo" style="display: none;"></div>
                            </div>
                            
                            <div class="file-validation" id="fileValidation" style="display: none;">
                                <div class="validation-item">
                                    <span class="check">✓</span> 檔案格式正確
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="importBtn">
                                <i class="icon">📥</i> 開始匯入
                            </button>
                            <button type="button" onclick="downloadTemplate()" class="btn btn-secondary">
                                <i class="icon">📄</i> 下載範本
                            </button>
                            <a href="<?php echo $baseUrl; ?>/mail/records" class="btn btn-secondary">
                                <i class="icon">📋</i> 查看記錄
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="import-instructions">
                <h3>📋 匯入說明</h3>
                <div class="instructions-content">
                    <div class="instruction-item">
                        <h4>檔案格式要求</h4>
                        <ul>
                            <li><strong>檔案格式</strong>：CSV (逗號分隔值)</li>
                            <li><strong>編碼支援</strong>：UTF-8、Big5、CP950、GB2312</li>
                            <li><strong>檔案大小</strong>：建議不超過 10MB</li>
                            <li><strong>標題行</strong>：第一行必須是欄位標題</li>
                        </ul>
                    </div>

                    <div class="instruction-item">
                        <h4>欄位順序與格式</h4>
                        <div class="csv-format">
                            <code>寄件方式,收件者姓名,收件地址,收件者電話,申報部門,寄件者姓名,寄件者分機</code>
                        </div>
                        <div class="field-details">
                            <table class="field-table">
                                <thead>
                                    <tr>
                                        <th>欄位名稱</th>
                                        <th>是否必填</th>
                                        <th>格式說明</th>
                                        <th>範例</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>寄件方式</td>
                                        <td class="required">必填</td>
                                        <td>掛號、黑貓、新竹貨運、郵局掛號、宅急便</td>
                                        <td>掛號</td>
                                    </tr>
                                    <tr>
                                        <td>收件者姓名</td>
                                        <td class="required">必填</td>
                                        <td>收件人完整姓名</td>
                                        <td>王小明</td>
                                    </tr>
                                    <tr>
                                        <td>收件地址</td>
                                        <td class="required">必填</td>
                                        <td>完整地址，不超過500字元</td>
                                        <td>台北市大安區信義路四段1號</td>
                                    </tr>
                                    <tr>
                                        <td>收件者電話</td>
                                        <td class="required">必填</td>
                                        <td>手機或市話</td>
                                        <td>0912-345-678</td>
                                    </tr>
                                    <tr>
                                        <td>申報部門</td>
                                        <td class="required">必填</td>
                                        <td>費用申報的部門</td>
                                        <td>總務部</td>
                                    </tr>
                                    <tr>
                                        <td>寄件者姓名</td>
                                        <td class="required">必填</td>
                                        <td>實際寄件人姓名</td>
                                        <td>李小華</td>
                                    </tr>
                                    <tr>
                                        <td>寄件者分機</td>
                                        <td class="required">必填</td>
                                        <td>寄件人分機號碼</td>
                                        <td>1234</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="instruction-item">
                        <h4>⚠️ 重要注意事項</h4>
                        <ul>
                            <li><strong>編碼問題</strong>：如果是從 Excel 匯出，請選擇 UTF-8 編碼</li>
                            <li><strong>特殊字元</strong>：地址中的逗號請用中文逗號「，」代替</li>
                            <li><strong>空行處理</strong>：系統會自動跳過空行</li>
                            <li><strong>錯誤處理</strong>：有錯誤的行會跳過，其他正確的行會繼續匯入</li>
                            <li><strong>自動編號</strong>：系統會自動產生寄件序號</li>
                            <li><strong>登記者</strong>：會設定為當前登入使用者</li>
                        </ul>
                    </div>

                    <div class="instruction-item">
                        <h4>常見問題排除</h4>
                        <div class="troubleshooting">
                            <details>
                                <summary>中文顯示亂碼怎麼辦？</summary>
                                <p>系統支援多種 CSV 編碼格式，包括：</p>
                        <ul>
                            <li><strong>UTF-8：</strong>新版 Office 或 Google 試算表</li>
                            <li><strong>Big5：</strong>繁體中文舊版 Excel</li>
                            <li><strong>GB2312/GBK：</strong>簡體中文 Excel</li>
                        </ul>
                        <p>系統會自動偵測並轉換檔案編碼，您可以直接上傳從任何版本 Excel 匯出的 CSV 檔案。</p>
                            </details>
                            <details>
                                <summary>匯入失敗，顯示欄位數量不足？</summary>
                                <p>請檢查 CSV 檔案是否有 7 個欄位，並確認沒有多餘的逗號或缺少欄位。</p>
                            </details>
                            <details>
                                <summary>部分資料匯入失敗？</summary>
                                <p>系統會跳過有錯誤的行，並顯示詳細的錯誤訊息。請根據錯誤訊息修正後重新匯入。</p>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 檔案選擇處理
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    const fileValidation = document.getElementById('fileValidation');
    const uploadArea = document.getElementById('fileUploadArea');
    
    if (file) {
        // 顯示檔案資訊
        fileInfo.style.display = 'block';
        fileInfo.innerHTML = `
            <div class="file-details">
                <strong>檔案名稱：</strong>${file.name}<br>
                <strong>檔案大小：</strong>${(file.size / 1024).toFixed(2)} KB<br>
                <strong>檔案類型：</strong>${file.type || 'CSV'}
            </div>
        `;
        
        // 檔案驗證
        if (file.name.toLowerCase().endsWith('.csv')) {
            uploadArea.classList.add('file-selected');
            fileValidation.style.display = 'block';
        } else {
            uploadArea.classList.add('file-error');
            fileInfo.innerHTML += '<div class="error">⚠️ 請選擇 CSV 檔案</div>';
        }
    }
});

// 拖拽上傳功能
const uploadArea = document.getElementById('fileUploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('csv_file').files = files;
        document.getElementById('csv_file').dispatchEvent(new Event('change'));
    }
});

// 下載範本功能
function downloadTemplate() {
    // 【關鍵修正】根據使用者提供的正確格式，重寫 CSV 範本內容
    const headers = "寄件方式,寄件者姓名,寄件者分機,收件者姓名,收件地址,收件者行動電話,費用申報部門\n";
    const example1 = "掛號,張經理,1234,王小明,台北市信義區信義路一段1號,0912345678,行政部\n";
    const example2 = "黑貓,林主任,2345,陳小美,新北市板橋區文化路二段2號,0923456789,財務部\n";
    const example3 = "新竹貨運,周協理,3456,李大華,台中市西屯區市政路三段3號,0935678901,資訊部\n";
    
    const csvContent = headers + example1 + example2 + example3;
    
    // 加上 BOM 來確保 Excel 能正確讀取 UTF-8 中文
    const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
    const blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });
    
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    
    link.setAttribute("href", url);
    link.setAttribute("download", "mail_import_template.csv");
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// 表單提交前的最後檢查
document.getElementById('importForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('csv_file');
    const importBtn = document.getElementById('importBtn');
    
    if (!fileInput.files[0]) {
        e.preventDefault();
        alert('請選擇要匯入的 CSV 檔案');
        return;
    }
    
    // 顯示載入狀態
    importBtn.disabled = true;
    importBtn.innerHTML = '<i class="icon">⏳</i> 匯入中...';
});
</script>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ startOnLoad: true });
</script>

<style>
.mail-import-container {
    max-width: 1200px;
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

.main-content {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.form-title {
    font-size: 1.8rem;
    color: #C8102E;
    margin-bottom: 1.5rem;
    text-align: center;
}

.guideline-container {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}
.guideline-title {
    font-size: 1.5rem;
    color: #C8102E;
    margin-top: 0;
    margin-bottom: 1rem;
    text-align: center;
}

.mermaid {
    text-align: center;
    background-color: #fff;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.import-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e1e5e9;
}

.section-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1.5rem;
    padding-left: 1rem;
}

.import-form-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.file-upload-area {
    border: 2px dashed #C8102E;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
}

.file-upload-area:hover {
    border-color: #a00e26;
    background-color: rgba(200, 16, 46, 0.05);
}

.file-upload-area.drag-over {
    border-color: #a00e26;
    background-color: rgba(200, 16, 46, 0.1);
}

.file-upload-area.file-selected {
    border-color: #28a745;
    background-color: rgba(40, 167, 69, 0.05);
}

.file-upload-area.file-error {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
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
    color: #C8102E;
}

.upload-text small {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.file-info {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: left;
}

.file-validation {
    margin-top: 1rem;
    padding: 0.5rem;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 6px;
    color: #155724;
}

.validation-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.check {
    color: #28a745;
    font-weight: bold;
}

.field-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.field-table th,
.field-table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.field-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.required {
    color: #dc3545;
    font-weight: 600;
}

.optional {
    color: #6c757d;
}

.troubleshooting details {
    margin: 0.5rem 0;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.troubleshooting summary {
    font-weight: 600;
    cursor: pointer;
    color: #C8102E;
}

.troubleshooting p {
    margin: 0.5rem 0 0 0;
    color: #666;
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

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: #C8102E;
    color: white;
}

.btn-secondary {
    background: #C8102E;
    color: white;
}

.btn:hover:not(:disabled) {
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
    margin-bottom: 2rem;
}

.instruction-item:last-child {
    margin-bottom: 0;
}

.instruction-item h4 {
    margin: 0 0 1rem 0;
    color: #C8102E;
    font-size: 1.1rem;
    font-weight: 600;
}

.csv-format {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.csv-format code {
    color: #333;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    word-break: break-all;
}

.alert {
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 8px;
    border: 1px solid transparent;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.error {
    color: #dc3545;
    font-weight: 600;
    margin-top: 0.5rem;
}
</style> 