<div class="admin-header">
    <div class="admin-breadcrumb">
        <span class="breadcrumb-item">
            <a href="<?php echo $baseUrl; ?>/admin/dashboard">管理後台</a>
        </span>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item">
            <a href="<?php echo $baseUrl; ?>/admin/announcements">公告管理</a>
        </span>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">新增公告</span>
    </div>
    <h1 class="admin-title">➕ 新增公告</h1>
    <p class="admin-subtitle">建立新的公司公告、假日資訊或員工手冊內容</p>
</div>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-error">
    <div class="alert-icon">❌</div>
    <div class="alert-content">
        <strong>建立失敗</strong>
        <p><?php echo htmlspecialchars($_GET['error']); ?></p>
    </div>
</div>
<?php endif; ?>

<div class="announcement-form-container">
    <form id="announcementForm" method="POST" enctype="multipart/form-data" class="announcement-form">
        
        <!-- 基本資訊區塊 -->
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">📝 基本資訊</h3>
                <p class="section-subtitle">設定公告的基本內容和類型</p>
            </div>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title" class="form-label required">公告標題</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           placeholder="請輸入公告標題..." required maxlength="255">
                    <small class="form-help">簡潔明確的標題有助於同仁快速理解公告內容</small>
                </div>
                
                <div class="form-group">
                    <label for="type" class="form-label required">公告類型</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="general">📢 一般公告</option>
                        <option value="holiday">🎉 假日資訊</option>
                        <option value="handbook">📚 員工手冊</option>
                    </select>
                    <small class="form-help">選擇適當的類型有助於分類管理</small>
                </div>
                
                <div class="form-group">
                    <label for="announcement_date" class="form-label required">公告日期</label>
                    <input type="date" id="announcement_date" name="announcement_date" 
                           class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                    <small class="form-help">公告的正式生效日期</small>
                </div>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label">排序權重</label>
                    <input type="number" id="sort_order" name="sort_order" 
                           class="form-input" value="0" min="0" max="999">
                    <small class="form-help">數字越大顯示越前面（0為預設）</small>
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label required">發布狀態</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="draft">📝 儲存草稿</option>
                        <option value="published" selected>🚀 立即發布</option>
                    </select>
                    <small class="form-help">可稍後在公告管理中調整狀態</small>
                </div>
            </div>
        </div>
        
        <!-- 內容編輯區塊 -->
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">📄 公告內容</h3>
                <p class="section-subtitle">撰寫詳細的公告內容</p>
            </div>
            
            <div class="form-group full-width">
                <label for="content" class="form-label required">公告內容</label>
                <textarea id="content" name="content" class="form-textarea simple-editor" 
                          placeholder="請輸入公告內容...&#10;&#10;您可以使用以下HTML標籤：&#10;• <strong>粗體文字</strong>&#10;• <em>斜體文字</em>&#10;• <u>底線文字</u>&#10;• <ul><li>項目符號</li></ul>&#10;• <ol><li>數字編號</li></ol>" 
                          required rows="15"></textarea>
                <small class="form-help">
                    <strong>格式化提示：</strong><br>
                    • 粗體：&lt;strong&gt;文字&lt;/strong&gt;<br>
                    • 斜體：&lt;em&gt;文字&lt;/em&gt;<br>
                    • 底線：&lt;u&gt;文字&lt;/u&gt;<br>
                    • 項目符號：&lt;ul&gt;&lt;li&gt;項目&lt;/li&gt;&lt;/ul&gt;<br>
                    • 數字編號：&lt;ol&gt;&lt;li&gt;項目&lt;/li&gt;&lt;/ol&gt;
                </small>
            </div>
        </div>
        
        <!-- 附件上傳區塊 -->
        <?php if ($canUploadPDF): ?>
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">📎 附件上傳</h3>
                <p class="section-subtitle">僅限總務人資、資訊、財務部門可上傳PDF附件</p>
            </div>
            
            <div class="form-group full-width">
                <label for="pdf_attachment" class="form-label">PDF附件</label>
                <div class="file-upload-area" id="fileUploadArea">
                    <input type="file" id="pdf_attachment" name="pdf_attachment" 
                           class="file-input" accept=".pdf" onchange="handleFileSelect(this)">
                    <div class="upload-placeholder">
                        <div class="upload-icon">📄</div>
                        <p class="upload-text">點擊選擇PDF檔案或拖曳檔案到此處</p>
                        <p class="upload-limit">檔案大小限制：10MB</p>
                    </div>
                    <div class="file-preview" id="filePreview" style="display: none;">
                        <div class="preview-icon">📄</div>
                        <div class="preview-info">
                            <span class="preview-name" id="previewName"></span>
                            <span class="preview-size" id="previewSize"></span>
                        </div>
                        <button type="button" class="remove-file-btn" onclick="removeFile()">✕</button>
                    </div>
                </div>
                <small class="form-help">僅支援PDF格式，檔案將供全公司同仁下載查看</small>
            </div>
        </div>
        <?php else: ?>
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">📎 附件權限</h3>
                <p class="section-subtitle">您目前沒有上傳附件的權限</p>
            </div>
            <div class="permission-notice">
                <div class="notice-icon">🔒</div>
                <div class="notice-content">
                    <h4>附件上傳權限說明</h4>
                    <p>僅有以下部門可上傳PDF附件：</p>
                    <ul>
                        <li>總務人資部</li>
                        <li>資訊部</li>
                        <li>財務部</li>
                    </ul>
                    <p>如需上傳附件，請聯絡上述部門同仁協助。</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 操作按鈕 -->
        <div class="form-actions">
            <div class="action-buttons">
                <a href="<?php echo $baseUrl; ?>/admin/announcements" class="btn btn-secondary">
                    <span class="btn-icon">↩️</span>
                    <span class="btn-text">取消返回</span>
                </a>
                
                <button type="button" class="btn btn-outline" onclick="previewAnnouncement()">
                    <span class="btn-icon">👁️</span>
                    <span class="btn-text">預覽效果</span>
                </button>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-icon">💾</span>
                    <span class="btn-text">儲存公告</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- 預覽模態框 -->
<div id="previewModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📋 公告預覽</h3>
            <button type="button" class="modal-close" onclick="closePreview()">✕</button>
        </div>
        <div class="modal-body">
            <div id="previewContent"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closePreview()">關閉預覽</button>
        </div>
    </div>
</div>

<style>
.admin-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #C8102E;
}

.admin-breadcrumb {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.breadcrumb-item a {
    color: #C8102E;
    text-decoration: none;
}

.breadcrumb-separator {
    margin: 0 0.5rem;
}

.breadcrumb-current {
    font-weight: 600;
}

.admin-title {
    font-size: 2rem;
    color: #C8102E;
    margin: 0.5rem 0;
}

.admin-subtitle {
    color: #666;
    margin: 0;
}

.alert {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: rgba(244, 67, 54, 0.1);
    border: 1px solid #F44336;
    color: #C62828;
}

.alert-icon {
    font-size: 1.5rem;
}

.alert-content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.announcement-form-container {
    max-width: 1000px;
    margin: 0 auto;
}

.announcement-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-section {
    padding: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.3rem;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.section-subtitle {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: #C8102E;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    font-family: inherit;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
}

.form-textarea {
    resize: vertical;
    line-height: 1.6;
}

.simple-editor {
    min-height: 400px;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 0.95rem;
    line-height: 1.8;
    padding: 1rem;
    background: #fafafa;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    resize: vertical;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.simple-editor:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
    background: white;
}

.form-help {
    margin-top: 0.75rem;
    color: #666;
    font-size: 0.85rem;
    line-height: 1.6;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border-left: 4px solid #C8102E;
}

.file-upload-area {
    position: relative;
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #C8102E;
    background: rgba(200, 16, 46, 0.02);
}

.file-upload-area.drag-over {
    border-color: #C8102E;
    background: rgba(200, 16, 46, 0.05);
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.upload-text {
    font-size: 1.1rem;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
    font-weight: 500;
}

.upload-limit {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.file-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(200, 16, 46, 0.05);
    border-radius: 6px;
    margin-top: 1rem;
}

.preview-icon {
    font-size: 2rem;
}

.preview-info {
    flex: 1;
    text-align: left;
}

.preview-name {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.preview-size {
    display: block;
    color: #666;
    font-size: 0.9rem;
}

.remove-file-btn {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.remove-file-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

.permission-notice {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid #FFC107;
    border-radius: 8px;
    color: #856404;
}

.notice-icon {
    font-size: 2rem;
}

.notice-content h4 {
    margin: 0 0 0.5rem 0;
    color: #856404;
}

.notice-content p {
    margin: 0 0 0.5rem 0;
    line-height: 1.5;
}

.notice-content ul {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.form-actions {
    padding: 2rem;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    flex-wrap: wrap;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 800px;
    max-height: 80vh;
    width: 90%;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
}

.modal-header h3 {
    margin: 0;
    color: #2d3748;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0.25rem;
    border-radius: 3px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #e9ecef;
    color: #2d3748;
}

.modal-body {
    padding: 1.5rem;
    max-height: 60vh;
    overflow-y: auto;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #eee;
    background: #f8f9fa;
    text-align: right;
}

/* 響應式設計 */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        justify-content: center;
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// 簡化的表單提交處理
document.getElementById('announcementForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    
    // 基本驗證
    if (!title || !content) {
        e.preventDefault();
        alert('請填寫完整的標題和內容！');
        return false;
    }
    
    // 發布確認
    const status = document.getElementById('status').value;
    if (status === 'published') {
        const confirmText = `確定要發布這個公告嗎？\n\n⚠️ 發布後全公司都看得到！\n\n公告標題：${title}`;
        if (!confirm(confirmText)) {
            e.preventDefault();
            return false;
        }
    }
    
    // 允許正常提交
    return true;
});

// 檔案處理
function handleFileSelect(input) {
    const file = input.files[0];
    
    if (file) {
        // 檢查檔案類型
        if (file.type !== 'application/pdf') {
            alert('只能上傳PDF檔案！');
            input.value = '';
            return;
        }
        
        // 檢查檔案大小
        if (file.size > 10 * 1024 * 1024) {
            alert('檔案大小不能超過10MB！');
            input.value = '';
            return;
        }
        
        showFilePreview(file);
    }
}

function showFilePreview(file) {
    const preview = document.getElementById('filePreview');
    const placeholder = document.querySelector('.upload-placeholder');
    const previewName = document.getElementById('previewName');
    const previewSize = document.getElementById('previewSize');
    
    previewName.textContent = file.name;
    previewSize.textContent = formatFileSize(file.size);
    
    placeholder.style.display = 'none';
    preview.style.display = 'flex';
}

function removeFile() {
    const input = document.getElementById('pdf_attachment');
    const preview = document.getElementById('filePreview');
    const placeholder = document.querySelector('.upload-placeholder');
    
    input.value = '';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// 拖拽上傳
const uploadArea = document.getElementById('fileUploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('pdf_attachment');
        input.files = files;
        handleFileSelect(input);
    }
});

// 移除所有可能干擾的編輯器功能，保持簡潔

// 簡化的預覽功能
function previewAnnouncement() {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    
    if (!title || !content) {
        alert('請先填寫標題和內容！');
        return;
    }
    
    const type = document.getElementById('type').value;
    const date = document.getElementById('announcement_date').value;
    const typeLabels = {
        'general': '📢 一般公告',
        'holiday': '🎉 假日資訊',
        'handbook': '📚 員工手冊'
    };
    
    const previewHTML = `
        <div style="font-family: inherit;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                <span style="background: rgba(200, 16, 46, 0.1); color: #C8102E; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.9rem;">${typeLabels[type]}</span>
                <span style="color: #666; font-size: 0.9rem;">${date}</span>
            </div>
            <h2 style="color: #2d3748; margin: 0 0 1rem 0; font-size: 1.5rem;">${title}</h2>
            <div style="line-height: 1.6; color: #4a5568;">${content.replace(/\n/g, '<br>')}</div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHTML;
    document.getElementById('previewModal').style.display = 'flex';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
}

// 狀態改變時更新按鈕文字
document.getElementById('status').addEventListener('change', function() {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    
    if (this.value === 'published') {
        btnText.textContent = '發布公告';
        submitBtn.className = 'btn btn-success';
        submitBtn.querySelector('.btn-icon').textContent = '🚀';
    } else {
        btnText.textContent = '儲存草稿';
        submitBtn.className = 'btn btn-primary';
        submitBtn.querySelector('.btn-icon').textContent = '💾';
    }
});

// 簡潔的初始化，不添加任何可能干擾的事件監聽器
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    
    // 只確保textarea正常工作，不添加任何額外的事件監聽器
    if (contentTextarea) {
        console.log('公告內容編輯器已準備就緒');
    }
});
</script> 