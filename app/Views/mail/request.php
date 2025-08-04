<div class="page-header">
    <h2 class="page-title">📮 寄件登記系統</h2>
    <p class="page-subtitle">快速登記寄件資訊，專業郵務管理</p>
</div>

<?php if ($success): ?>
    <div class="content-card">
        <div class="alert alert-success">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">✅</span>
                <span><?php echo $success; ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="content-card">
        <div class="alert alert-error">
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">❌</span>
                <div>
                    <strong>請修正以下錯誤：</strong>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1rem;">
                        <?php foreach($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="main-content">
    <div class="content-card">
        <h2 class="form-title">寄件登記</h2>
        
        <div class="guideline-container">
            <h3 class="guideline-title">一般使用者寄件流程</h3>
            <div class="mermaid">
                graph LR
                    A[Step 1: 登記/匯入資料] --> B[Step 2: 將物品送至8F]
                    B --> C[Step 3: 至寄件紀錄<br>查看是否成功]
                    C --> D[Step 4: 至郵資查詢<br>查看郵資]
            </div>
        </div>

        <form id="mail-request-form" action="<?php echo $baseUrl; ?>/mail/request" method="post">
    <!-- 緊湊型表單佈局 - 一次顯示所有欄位 -->
    <div class="form-container">
        <!-- 第一行：寄件方式 -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="mail_type" class="form-label">📦 寄件方式 <span class="required">*</span></label>
                <select name="mail_type" id="mail_type" class="form-select" required>
                    <option value="">請選擇寄件方式</option>
                    <option value="掛號" <?php echo $formData['mail_type'] === '掛號' ? 'selected' : ''; ?>>📪 掛號</option>
                    <option value="黑貓" <?php echo $formData['mail_type'] === '黑貓' ? 'selected' : ''; ?>>🐱 黑貓宅急便</option>
                    <option value="新竹貨運" <?php echo $formData['mail_type'] === '新竹貨運' ? 'selected' : ''; ?>>🚚 新竹貨運</option>
                </select>
            </div>
        </div>

        <!-- 第二行：收件者基本資訊 -->
        <div class="form-row">
            <div class="form-group">
                <label for="receiver_name" class="form-label">👤 收件者姓名 <span class="required">*</span></label>
                <input type="text" name="receiver_name" id="receiver_name" class="form-input"
                       value="<?php echo htmlspecialchars($formData['receiver_name']); ?>" 
                       placeholder="請輸入收件者姓名" required>
            </div>
            
            <div class="form-group">
                <label for="receiver_phone" class="form-label">📱 收件者電話 <span class="required">*</span></label>
                <input type="tel" name="receiver_phone" id="receiver_phone" class="form-input"
                       value="<?php echo htmlspecialchars($formData['receiver_phone']); ?>" 
                       placeholder="例：0912345678" required>
            </div>
        </div>

        <!-- 第三行：收件地址 -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="receiver_address" class="form-label">📍 收件地址 <span class="required">*</span></label>
                <input type="text" name="receiver_address" id="receiver_address" class="form-input"
                       value="<?php echo htmlspecialchars($formData['receiver_address']); ?>" 
                       placeholder="請輸入完整地址（含郵遞區號）" required>
            </div>
        </div>

        <!-- 第四行：寄件者資訊 -->
        <div class="form-row">
            <div class="form-group">
                <label for="sender_name" class="form-label">📤 寄件者姓名 <span class="required">*</span></label>
                <input type="text" name="sender_name" id="sender_name" class="form-input"
                       value="<?php echo htmlspecialchars($formData['sender_name']); ?>" 
                       placeholder="請輸入寄件者姓名" required>
            </div>
            
            <div class="form-group">
                <label for="sender_ext" class="form-label">☎️ 寄件者分機 <span class="required">*</span></label>
                <input type="text" name="sender_ext" id="sender_ext" class="form-input"
                       value="<?php echo htmlspecialchars($formData['sender_ext']); ?>" 
                       placeholder="例：701" required>
            </div>
        </div>

        <!-- 第五行：費用申報單位和登記者 -->
        <div class="form-row">
            <div class="form-group">
                <label for="declare_department" class="form-label">💰 郵資掛帳單位 <span class="required">*</span></label>
                <input type="text" name="declare_department" id="declare_department" class="form-input"
                       value="<?php echo htmlspecialchars($formData['declare_department']); ?>" 
                       placeholder="請輸入申報單位名稱" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">📝 登記者</label>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($registrarName); ?>" disabled>
            </div>
        </div>

        <!-- 按鈕區 -->
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <span>📮</span> 送出登記
            </button>
            <a href="<?php echo $baseUrl; ?>/mail/records" class="btn btn-secondary">
                <span>📋</span> 查看記錄
            </a>
            <a href="<?php echo $baseUrl; ?>/mail/import" class="btn btn-outline">
                <span>📁</span> 批次匯入
            </a>
        </div>
    </div>
</form>
    </div>
</div>

<style>
/* 緊湊型表單樣式 */
.compact-form {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.form-container {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: end;
}

.form-row .full-width {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.required {
    color: #C8102E;
    font-weight: bold;
}

.form-input, .form-select {
    padding: 0.8rem 1rem;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
}

.form-input:disabled {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

/* 按鈕樣式 */
.btn-group {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid rgba(200, 16, 46, 0.1);
}

.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-primary {
    background: #C8102E;
    color: white;
}

.btn-primary:hover {
    background: #a00d25;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(200, 16, 46, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #565e64;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: #C8102E;
    border: 2px solid #C8102E;
}

.btn-outline:hover {
    background: #C8102E;
    color: white;
    transform: translateY(-2px);
}

/* 響應式設計 */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .compact-form {
        padding: 1.5rem;
    }
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
</style>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ startOnLoad: true });
</script> 