<div class="page-header">
    <h2 class="page-title">✏️ 編輯寄件記錄</h2>
    <p class="page-subtitle">修改寄件資訊，並儲存變更</p>
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

<form method="POST" action="<?php echo $baseUrl; ?>/mail/edit?mail_code=<?php echo urlencode($record['mail_code']); ?>" class="content-card compact-form">
    <div class="form-container">
        <div class="form-row">
            <div class="form-group full-width">
                <label for="mail_type" class="form-label">📦 寄件方式 <span class="required">*</span></label>
                <select name="mail_type" id="mail_type" class="form-select" required>
                    <option value="">請選擇寄件方式</option>
                    <option value="掛號" <?php echo $record['mail_type'] === '掛號' ? 'selected' : ''; ?>>📪 掛號</option>
                    <option value="黑貓" <?php echo $record['mail_type'] === '黑貓' ? 'selected' : ''; ?>>🐱 黑貓宅急便</option>
                    <option value="新竹貨運" <?php echo $record['mail_type'] === '新竹貨運' ? 'selected' : ''; ?>>🚚 新竹貨運</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="receiver_name" class="form-label">👤 收件者姓名 <span class="required">*</span></label>
                <input type="text" name="receiver_name" id="receiver_name" class="form-input"
                       value="<?php echo htmlspecialchars($record['receiver_name']); ?>" 
                       placeholder="請輸入收件者姓名" required>
            </div>
            <div class="form-group">
                <label for="receiver_phone" class="form-label">📱 收件者電話 <span class="required">*</span></label>
                <input type="tel" name="receiver_phone" id="receiver_phone" class="form-input"
                       value="<?php echo htmlspecialchars($record['receiver_phone']); ?>" 
                       placeholder="例：0912345678" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <label for="receiver_address" class="form-label">📍 收件地址 <span class="required">*</span></label>
                <input type="text" name="receiver_address" id="receiver_address" class="form-input"
                       value="<?php echo htmlspecialchars($record['receiver_address']); ?>" 
                       placeholder="請輸入完整地址（含郵遞區號）" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="sender_name" class="form-label">📤 寄件者姓名 <span class="required">*</span></label>
                <input type="text" name="sender_name" id="sender_name" class="form-input"
                       value="<?php echo htmlspecialchars($record['sender_name']); ?>" 
                       placeholder="請輸入寄件者姓名" required>
            </div>
            <div class="form-group">
                <label for="sender_ext" class="form-label">☎️ 寄件者分機 <span class="required">*</span></label>
                <input type="text" name="sender_ext" id="sender_ext" class="form-input"
                       value="<?php echo htmlspecialchars($record['sender_ext']); ?>" 
                       placeholder="例：701" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="declare_department" class="form-label">💰 費用申報單位 <span class="required">*</span></label>
                <input type="text" name="declare_department" id="declare_department" class="form-input"
                       value="<?php echo htmlspecialchars($record['declare_department']); ?>" 
                       placeholder="請輸入申報單位名稱" required>
            </div>
            <div class="form-group">
                <label class="form-label">📝 登記者</label>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($record['registrar_username'] ?? ''); ?>" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="item_count" class="form-label">📦 件數</label>
                <input type="number" name="item_count" id="item_count" class="form-input"
                       value="<?php echo htmlspecialchars($record['item_count'] ?? 1); ?>" min="1">
            </div>
            <div class="form-group">
                <label for="postage" class="form-label">💵 郵資</label>
                <input type="number" name="postage" id="postage" class="form-input"
                       value="<?php echo htmlspecialchars($record['postage'] ?? 0); ?>" min="0" step="0.01">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="tracking_number" class="form-label">🔎 追蹤號碼</label>
                <input type="text" name="tracking_number" id="tracking_number" class="form-input"
                       value="<?php echo htmlspecialchars($record['tracking_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="status" class="form-label">📊 狀態</label>
                <select name="status" id="status" class="form-select">
                    <option value="草稿" <?php echo ($record['status'] ?? '') === '草稿' ? 'selected' : ''; ?>>草稿</option>
                    <option value="已送出" <?php echo ($record['status'] ?? '') === '已送出' ? 'selected' : ''; ?>>已送出</option>
                    <option value="已寄達" <?php echo ($record['status'] ?? '') === '已寄達' ? 'selected' : ''; ?>>已寄達</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <label for="notes" class="form-label">📝 備註</label>
                <textarea name="notes" id="notes" class="form-input" rows="2" placeholder="可填寫特殊說明或備註"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <span>💾</span> 儲存變更
            </button>
            <a href="<?php echo $baseUrl; ?>/mail/outgoing-records" class="btn btn-secondary">
                <span>📋</span> 返回列表
            </a>
        </div>
    </div>
</form>

<style>
<?php include __DIR__ . '/request.php'; // 共用樣式 ?>
</style> 