<div class="mail-incoming-container">
    <div class="page-header">
        <h1>收件登記</h1>
        <p>請填寫以下收件資訊</p>
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

    <form method="POST" action="<?php echo $baseUrl; ?>/mail/incoming-register" class="mail-form">
        <div class="form-row">
            <div class="form-group">
                <label for="tracking_number" class="form-label">物流單號/掛號號碼</label>
                <input type="text" name="tracking_number" id="tracking_number" class="form-input"
                       value="<?php echo htmlspecialchars($formData['tracking_number']); ?>" 
                       placeholder="請輸入物流單號或掛號號碼">
            </div>
            
            <div class="form-group">
                <label for="mail_type" class="form-label">郵件類型 <span class="required">*</span></label>
                <select name="mail_type" id="mail_type" class="form-input" required>
                    <option value="">請選擇</option>
                    <option value="掛號" <?php echo $formData['mail_type'] === '掛號' ? 'selected' : ''; ?>>掛號</option>
                    <option value="包裹" <?php echo $formData['mail_type'] === '包裹' ? 'selected' : ''; ?>>包裹</option>
                    <option value="快遞" <?php echo $formData['mail_type'] === '快遞' ? 'selected' : ''; ?>>快遞</option>
                    <option value="一般信件" <?php echo $formData['mail_type'] === '一般信件' ? 'selected' : ''; ?>>一般信件</option>
                    <option value="公文" <?php echo $formData['mail_type'] === '公文' ? 'selected' : ''; ?>>公文</option>
                    <option value="其他" <?php echo $formData['mail_type'] === '其他' ? 'selected' : ''; ?>>其他</option>
                </select>
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
                    <label for="sender_company" class="form-label">寄件者公司/機關</label>
                    <input type="text" name="sender_company" id="sender_company" class="form-input"
                           value="<?php echo htmlspecialchars($formData['sender_company']); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>收件者資訊</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="recipient_name" class="form-label">收件者姓名 <span class="required">*</span></label>
                    <input type="text" name="recipient_name" id="recipient_name" class="form-input"
                           value="<?php echo htmlspecialchars($formData['recipient_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="recipient_department" class="form-label">收件部門</label>
                    <select name="recipient_department" id="recipient_department" class="form-input">
                        <option value="">請選擇</option>
                        <option value="資訊部" <?php echo $formData['recipient_department'] === '資訊部' ? 'selected' : ''; ?>>資訊部</option>
                        <option value="人資部" <?php echo $formData['recipient_department'] === '人資部' ? 'selected' : ''; ?>>人資部</option>
                        <option value="財務部" <?php echo $formData['recipient_department'] === '財務部' ? 'selected' : ''; ?>>財務部</option>
                        <option value="編輯部" <?php echo $formData['recipient_department'] === '編輯部' ? 'selected' : ''; ?>>編輯部</option>
                        <option value="行銷部" <?php echo $formData['recipient_department'] === '行銷部' ? 'selected' : ''; ?>>行銷部</option>
                        <option value="業務部" <?php echo $formData['recipient_department'] === '業務部' ? 'selected' : ''; ?>>業務部</option>
                        <option value="總經理室" <?php echo $formData['recipient_department'] === '總經理室' ? 'selected' : ''; ?>>總經理室</option>
                        <option value="其他" <?php echo $formData['recipient_department'] === '其他' ? 'selected' : ''; ?>>其他</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>收件資訊</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="received_date" class="form-label">收件日期 <span class="required">*</span></label>
                    <input type="date" name="received_date" id="received_date" class="form-input"
                           value="<?php echo htmlspecialchars($formData['received_date']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="received_time" class="form-label">收件時間</label>
                    <input type="time" name="received_time" id="received_time" class="form-input"
                           value="<?php echo htmlspecialchars($formData['received_time']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="content_description" class="form-label">內容描述</label>
                    <textarea name="content_description" id="content_description" class="form-input form-textarea" 
                              rows="3" placeholder="簡述郵件內容或用途"><?php echo htmlspecialchars($formData['content_description']); ?></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-checkbox-label">
                        <input type="checkbox" name="urgent" value="1" class="form-checkbox" 
                               <?php echo $formData['urgent'] ? 'checked' : ''; ?>>
                        <span class="checkmark"></span>
                        緊急件
                    </label>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="notes" class="form-label">備註</label>
                    <textarea name="notes" id="notes" class="form-input form-textarea" 
                              rows="2" placeholder="其他注意事項或備註"><?php echo htmlspecialchars($formData['notes']); ?></textarea>
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
                <i class="icon">📬</i> 確認收件
            </button>
            <a href="<?php echo $baseUrl; ?>/mail/incoming-records" class="btn btn-secondary">
                <i class="icon">📋</i> 查看記錄
            </a>
        </div>
    </form>
</div>

<style>
.mail-incoming-container {
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
    color: #C8102E;
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
    box-shadow: 0 5px 20px rgba(200, 16, 46, 0.1);
}

.form-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(200, 16, 46, 0.05);
    border-radius: 8px;
    border-left: 4px solid #C8102E;
}

.form-section h3 {
    margin: 0 0 1rem 0;
    color: #C8102E;
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
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
}

.form-input:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.form-checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 500;
    color: #333;
}

.form-checkbox {
    width: 18px;
    height: 18px;
    accent-color: #C8102E;
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
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
    background: linear-gradient(135deg, #C8102E, #8B0000);
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

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    border-left: 4px solid;
}

.alert-success {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* 響應式設計 */
@media (max-width: 768px) {
    .mail-incoming-container {
        padding: 1rem;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<script>
// 自動設定當前時間
document.addEventListener('DOMContentLoaded', function() {
    const timeInput = document.getElementById('received_time');
    if (timeInput && !timeInput.value) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeInput.value = `${hours}:${minutes}`;
    }
});
</script> 