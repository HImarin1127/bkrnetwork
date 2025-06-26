<div class="postage-container">
    <div class="page-header">
        <h1>郵資查詢</h1>
        <p>查詢各種寄件方式的郵資費用</p>
    </div>

    <div class="postage-form-container">
        <form method="POST" action="<?php echo $baseUrl; ?>mail/postage" class="postage-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="mail_type" class="form-label">寄件方式</label>
                    <select name="mail_type" id="mail_type" class="form-input" required>
                        <option value="">請選擇</option>
                        <option value="掛號" <?php echo ($_POST['mail_type'] ?? '') === '掛號' ? 'selected' : ''; ?>>掛號</option>
                        <option value="黑貓" <?php echo ($_POST['mail_type'] ?? '') === '黑貓' ? 'selected' : ''; ?>>黑貓</option>
                        <option value="新竹貨運" <?php echo ($_POST['mail_type'] ?? '') === '新竹貨運' ? 'selected' : ''; ?>>新竹貨運</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="destination" class="form-label">目的地/服務類型</label>
                    <select name="destination" id="destination" class="form-input" required>
                        <option value="">請選擇</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="weight" class="form-label">重量 (公斤)</label>
                    <input type="number" name="weight" id="weight" class="form-input" 
                           min="0" step="0.1" value="<?php echo $_POST['weight'] ?? '1'; ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="icon">🔍</i> 查詢郵資
                </button>
            </div>
        </form>
    </div>

    <?php if ($result): ?>
        <div class="result-container">
            <h3>📋 查詢結果</h3>
            <div class="result-card">
                <div class="result-header">
                    <span class="mail-type"><?php echo htmlspecialchars($result['mail_type']); ?></span>
                    <span class="destination"><?php echo htmlspecialchars($result['destination']); ?></span>
                </div>
                
                <div class="result-details">
                    <div class="detail-item">
                        <span class="label">重量:</span>
                        <span class="value"><?php echo $result['weight']; ?> 公斤</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">基本費率:</span>
                        <span class="value">NT$ <?php echo $result['base_rate']; ?></span>
                    </div>
                    <?php if ($result['weight_fee'] > 0): ?>
                        <div class="detail-item">
                            <span class="label">超重費用:</span>
                            <span class="value">NT$ <?php echo $result['weight_fee']; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-item total">
                        <span class="label">總計費用:</span>
                        <span class="value">NT$ <?php echo $result['total_fee']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="rate-table-container">
        <h3>📊 郵資費率表</h3>
        
        <div class="rate-tables">
            <?php foreach ($postageRates as $mailType => $rates): ?>
                <div class="rate-table">
                    <h4><?php echo htmlspecialchars($mailType); ?></h4>
                    <table>
                        <thead>
                            <tr>
                                <th>類型/地區</th>
                                <th>費用 (NT$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rates as $type => $price): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($type); ?></td>
                                    <td>NT$ <?php echo $price; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if ($mailType !== '掛號'): ?>
                        <small class="rate-note">* 超過1公斤每公斤加收 NT$ 10</small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="usage-tips">
        <h3>💡 使用說明</h3>
        <ul>
            <li><strong>掛號：</strong>中華郵政掛號信件，安全可靠，適用於重要文件</li>
            <li><strong>黑貓：</strong>黑貓宅急便，快速便利，提供常溫、冷藏、冷凍服務</li>
            <li><strong>新竹貨運：</strong>新竹物流，適合大型物品，提供一般及快遞服務</li>
            <li><strong>費用計算：</strong>基本費率 + 超重費用（超過1公斤部分）</li>
        </ul>
    </div>
</div>

<style>
.postage-container {
    max-width: 1000px;
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

.postage-form-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-input {
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #7b61ff;
    box-shadow: 0 0 0 3px rgba(123, 97, 255, 0.1);
}

.form-actions {
    text-align: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
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

.result-container {
    margin-bottom: 2rem;
}

.result-container h3 {
    margin-bottom: 1rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.result-card {
    background: linear-gradient(135deg, #7b61ff, #4caaff);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(123, 97, 255, 0.3);
}

.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.mail-type {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.destination {
    font-size: 1.1rem;
    font-weight: 500;
}

.result-details {
    display: grid;
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-item.total {
    font-size: 1.2rem;
    font-weight: 600;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.rate-table-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.rate-table-container h3 {
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rate-tables {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.rate-table h4 {
    margin-bottom: 1rem;
    color: #7b61ff;
    font-size: 1.1rem;
    font-weight: 600;
}

.rate-table table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.rate-table th,
.rate-table td {
    padding: 0.75rem;
    text-align: left;
}

.rate-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.rate-table td {
    border-bottom: 1px solid #e1e5e9;
}

.rate-table tr:last-child td {
    border-bottom: none;
}

.rate-note {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-style: italic;
}

.usage-tips {
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.usage-tips h3 {
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.usage-tips ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.usage-tips li {
    margin-bottom: 1rem;
    padding: 1rem;
    background: rgba(123, 97, 255, 0.05);
    border-radius: 8px;
    border-left: 4px solid #7b61ff;
}

.usage-tips strong {
    color: #7b61ff;
}

@media (max-width: 768px) {
    .postage-container {
        padding: 1rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .rate-tables {
        grid-template-columns: 1fr;
    }
    
    .result-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<script>
// 動態更新目的地選項
document.getElementById('mail_type').addEventListener('change', function() {
    const mailType = this.value;
    const destinationSelect = document.getElementById('destination');
    
    // 清空目的地選項
    destinationSelect.innerHTML = '<option value="">請選擇</option>';
    
    if (mailType === '掛號') {
        destinationSelect.innerHTML += '<option value="本島">本島</option>';
        destinationSelect.innerHTML += '<option value="離島">離島</option>';
    } else if (mailType === '黑貓') {
        destinationSelect.innerHTML += '<option value="常溫">常溫</option>';
        destinationSelect.innerHTML += '<option value="冷藏">冷藏</option>';
        destinationSelect.innerHTML += '<option value="冷凍">冷凍</option>';
    } else if (mailType === '新竹貨運') {
        destinationSelect.innerHTML += '<option value="一般">一般</option>';
        destinationSelect.innerHTML += '<option value="快遞">快遞</option>';
    }
    
    // 恢復之前選擇的值
    const savedDestination = '<?php echo $_POST['destination'] ?? ''; ?>';
    if (savedDestination) {
        destinationSelect.value = savedDestination;
    }
});

// 頁面載入時觸發一次
document.addEventListener('DOMContentLoaded', function() {
    const mailTypeSelect = document.getElementById('mail_type');
    if (mailTypeSelect.value) {
        mailTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script> 