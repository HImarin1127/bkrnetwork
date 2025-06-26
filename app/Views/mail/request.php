<div class="page-header">
    <h1 class="page-title">📮 寄件登記系統</h1>
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

<form method="POST" action="<?php echo $baseUrl; ?>/mail/request" class="content-card">
    <div class="form-section">
        <div class="section-header">
            <h3>📦 寄件方式選擇</h3>
            <p>請選擇適合的寄件方式</p>
        </div>
        
        <div class="form-group">
            <label for="mail_type" class="form-label">寄件方式 <span class="required">*</span></label>
            <select name="mail_type" id="mail_type" class="form-select" required>
                <option value="">請選擇寄件方式</option>
                <option value="掛號" <?php echo $formData['mail_type'] === '掛號' ? 'selected' : ''; ?>>📪 掛號</option>
                <option value="黑貓" <?php echo $formData['mail_type'] === '黑貓' ? 'selected' : ''; ?>>🐱 黑貓宅急便</option>
                <option value="新竹貨運" <?php echo $formData['mail_type'] === '新竹貨運' ? 'selected' : ''; ?>>🚚 新竹貨運</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <div class="section-header">
            <h3>👤 收件者資訊</h3>
            <p>請填寫完整的收件者資料</p>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="receiver_name" class="form-label">收件者姓名 <span class="required">*</span></label>
                <input type="text" name="receiver_name" id="receiver_name" class="form-input"
                       value="<?php echo htmlspecialchars($formData['receiver_name']); ?>" 
                       placeholder="請輸入收件者姓名" required>
            </div>
            
            <div class="form-group">
                <label for="receiver_phone" class="form-label">收件者電話 <span class="required">*</span></label>
                <input type="tel" name="receiver_phone" id="receiver_phone" class="form-input"
                       value="<?php echo htmlspecialchars($formData['receiver_phone']); ?>" 
                       placeholder="例：0912345678" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="receiver_address" class="form-label">收件地址 <span class="required">*</span></label>
            <textarea name="receiver_address" id="receiver_address" class="form-textarea" rows="3"
                      placeholder="請輸入完整地址（含郵遞區號）" required><?php echo htmlspecialchars($formData['receiver_address']); ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <div class="section-header">
            <h3>📤 寄件者資訊</h3>
            <p>請確認寄件者相關資料</p>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="sender_name" class="form-label">寄件者姓名 <span class="required">*</span></label>
                <input type="text" name="sender_name" id="sender_name" class="form-input"
                       value="<?php echo htmlspecialchars($formData['sender_name']); ?>" 
                       placeholder="請輸入寄件者姓名" required>
            </div>
            
            <div class="form-group">
                <label for="sender_ext" class="form-label">寄件者分機 <span class="required">*</span></label>
                <input type="text" name="sender_ext" id="sender_ext" class="form-input"
                       value="<?php echo htmlspecialchars($formData['sender_ext']); ?>" 
                       placeholder="例：701" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="declare_department" class="form-label">費用申報單位 <span class="required">*</span></label>
            <input type="text" name="declare_department" id="declare_department" class="form-input"
                   value="<?php echo htmlspecialchars($formData['declare_department']); ?>" 
                   placeholder="請輸入申報單位名稱" required>
        </div>
    </div>

    <div class="form-section">
        <div class="section-header">
            <h3>🔐 登記者資訊</h3>
            <p>系統自動記錄登記者身分</p>
        </div>
        
        <div class="info-display">
            <div class="info-item">
                <span class="info-label">登記者姓名</span>
                <span class="info-value"><?php echo htmlspecialchars($registrarName); ?></span>
            </div>
            <div class="info-note">
                <span class="icon">ℹ️</span>
                <span>系統自動填入當前登入使用者資訊</span>
            </div>
        </div>
    </div>

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
</form>

<style>
/* 表單區塊樣式 */
.form-section {
    margin-bottom: 2rem;
    padding: 2rem;
    background: rgba(200, 16, 46, 0.02);
    border-radius: 16px;
    border-left: 4px solid #C8102E;
    border: 1px solid rgba(200, 16, 46, 0.1);
}

.section-header {
    margin-bottom: 1.5rem;
    text-align: center;
}

.section-header h3 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #C8102E;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.section-header p {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
}

/* 表單網格佈局 */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

/* 必填標記 */
.required {
    color: #C8102E;
    font-weight: bold;
}

/* 資訊顯示區塊 */
.info-display {
    background: rgba(200, 16, 46, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(200, 16, 46, 0.1);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.info-label {
    font-weight: 600;
    color: #2d3748;
}

.info-value {
    color: #C8102E;
    font-weight: 700;
    background: rgba(200, 16, 46, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.info-note {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.9rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(200, 16, 46, 0.1);
}

/* 警告和成功訊息 */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
}
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
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

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(123, 97, 255, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
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

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

@media (max-width: 768px) {
    .mail-request-container {
        padding: 1rem;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        justify-content: center;
    }
}
</style> 