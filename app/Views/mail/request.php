<div class="mail-request-container">
    <div class="page-header">
        <h1>寄件登記</h1>
        <p>請填寫以下寄件資訊</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

            <form method="POST" action="<?php echo $baseUrl; ?>/mail/request" class="mail-form">
        <div class="form-row">
            <div class="form-group">
                <label for="mail_type" class="form-label">寄件方式 <span class="required">*</span></label>
                <select name="mail_type" id="mail_type" class="form-input" required>
                    <option value="">請選擇</option>
                    <option value="掛號" <?php echo $formData['mail_type'] === '掛號' ? 'selected' : ''; ?>>掛號</option>
                    <option value="黑貓" <?php echo $formData['mail_type'] === '黑貓' ? 'selected' : ''; ?>>黑貓</option>
                    <option value="新竹貨運" <?php echo $formData['mail_type'] === '新竹貨運' ? 'selected' : ''; ?>>新竹貨運</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3>收件者資訊</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="receiver_name" class="form-label">收件者姓名 <span class="required">*</span></label>
                    <input type="text" name="receiver_name" id="receiver_name" class="form-input"
                           value="<?php echo htmlspecialchars($formData['receiver_name']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="receiver_address" class="form-label">收件地址 <span class="required">*</span></label>
                    <input type="text" name="receiver_address" id="receiver_address" class="form-input"
                           value="<?php echo htmlspecialchars($formData['receiver_address']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="receiver_phone" class="form-label">收件者行動電話 <span class="required">*</span></label>
                    <input type="tel" name="receiver_phone" id="receiver_phone" class="form-input"
                           value="<?php echo htmlspecialchars($formData['receiver_phone']); ?>" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>寄件者資訊</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="sender_name" class="form-label">寄件者姓名 <span class="required">*</span></label>
                    <input type="text" name="sender_name" id="sender_name" class="form-input"
                           value="<?php echo htmlspecialchars($formData['sender_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="sender_ext" class="form-label">寄件者分機 <span class="required">*</span></label>
                    <input type="text" name="sender_ext" id="sender_ext" class="form-input"
                           value="<?php echo htmlspecialchars($formData['sender_ext']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="declare_department" class="form-label">費用申報單位 <span class="required">*</span></label>
                    <input type="text" name="declare_department" id="declare_department" class="form-input"
                           value="<?php echo htmlspecialchars($formData['declare_department']); ?>" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>登記者資訊</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">登記者姓名</label>
                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($registrarName); ?>" disabled>
                    <small class="form-help">系統自動填入當前登入使用者</small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="icon">📮</i> 送出登記
            </button>
            <a href="<?php echo $baseUrl; ?>/mail/records" class="btn btn-secondary">
                <i class="icon">📋</i> 查看記錄
            </a>
        </div>
    </form>
</div>

<style>
.mail-request-container {
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

.page-header p {
    color: #666;
    font-size: 1.1rem;
}

.mail-form {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(123, 97, 255, 0.05);
    border-radius: 8px;
    border-left: 4px solid #7b61ff;
}

.form-section h3 {
    margin: 0 0 1rem 0;
    color: #7b61ff;
    font-size: 1.2rem;
    font-weight: 600;
}

.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-group {
    flex: 1;
}

.form-group.full-width {
    flex: 100%;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.required {
    color: #e74c3c;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #7b61ff;
    box-shadow: 0 0 0 3px rgba(123, 97, 255, 0.1);
}

.form-input:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
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