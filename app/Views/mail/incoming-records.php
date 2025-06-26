<div class="mail-incoming-records-container">
    <div class="page-header">
        <h1>收件查詢</h1>
        <p>查看和管理收件記錄</p>
    </div>

    <!-- 搜尋與篩選功能 -->
    <div class="search-section">
        <form method="GET" class="search-form">
            <div class="search-row">
                <div class="search-group">
                    <input type="text" name="keyword" placeholder="搜尋寄件者、收件者、內容..." 
                           value="<?php echo htmlspecialchars($filters['keyword']); ?>" class="search-input">
                </div>
                
                <div class="search-group">
                    <input type="date" name="date_from" placeholder="開始日期" 
                           value="<?php echo htmlspecialchars($filters['dateFrom']); ?>" class="search-input">
                </div>
                
                <div class="search-group">
                    <input type="date" name="date_to" placeholder="結束日期" 
                           value="<?php echo htmlspecialchars($filters['dateTo']); ?>" class="search-input">
                </div>
                
                <div class="search-group">
                    <select name="status" class="search-input">
                        <option value="">所有狀態</option>
                        <option value="已收件" <?php echo $filters['status'] === '已收件' ? 'selected' : ''; ?>>已收件</option>
                        <option value="已轉交" <?php echo $filters['status'] === '已轉交' ? 'selected' : ''; ?>>已轉交</option>
                        <option value="已領取" <?php echo $filters['status'] === '已領取' ? 'selected' : ''; ?>>已領取</option>
                        <option value="等待處理" <?php echo $filters['status'] === '等待處理' ? 'selected' : ''; ?>>等待處理</option>
                    </select>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-btn">
                        <i class="icon">🔍</i> 搜尋
                    </button>
                    <?php if (!empty(array_filter($filters))): ?>
                        <a href="<?php echo $baseUrl; ?>/mail/incoming-records" class="clear-btn">
                            <i class="icon">✖</i> 清除
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- 功能選單 -->
    <div class="actions-section">
        <div class="action-buttons">
            <a href="<?php echo $baseUrl; ?>/mail/incoming-register" class="btn btn-primary">
                <i class="icon">📬</i> 新增收件
            </a>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $baseUrl; ?>/mail/incoming-records?export=1" class="btn btn-success">
                    <i class="icon">📊</i> 匯出 CSV
                </a>
            <?php endif; ?>
        </div>
        
        <div class="stats-info">
            <span class="total-count">共 <?php echo count($records); ?> 筆收件記錄</span>
        </div>
    </div>

    <!-- 記錄列表 -->
    <div class="records-list">
        <?php if (empty($records)): ?>
            <div class="empty-state">
                <div class="empty-icon">📬</div>
                <h3>目前沒有收件記錄</h3>
                <p><?php echo !empty(array_filter($filters)) ? '沒有找到符合搜尋條件的記錄' : '還沒有任何收件記錄'; ?></p>
                <a href="<?php echo $baseUrl; ?>/mail/incoming-register" class="btn btn-primary">
                    <i class="icon">📬</i> 立即新增收件
                </a>
            </div>
        <?php else: ?>
            <div class="records-table-container">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>登記編號</th>
                            <th>物流單號</th>
                            <th>郵件類型</th>
                            <th>寄件者</th>
                            <th>收件者</th>
                            <th>收件日期</th>
                            <th>狀態</th>
                            <th>緊急</th>
                            <?php if ($isAdmin): ?>
                                <th>操作</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr class="record-row <?php echo $record['urgent'] ? 'urgent' : ''; ?>">
                                <td class="record-id">
                                    <strong>IN-<?php echo htmlspecialchars($record['id']); ?></strong>
                                </td>
                                <td class="tracking-number">
                                    <?php if (!empty($record['tracking_number'])): ?>
                                        <span class="tracking-code"><?php echo htmlspecialchars($record['tracking_number']); ?></span>
                                    <?php else: ?>
                                        <span class="no-tracking">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="mail-type mail-type-<?php echo strtolower(str_replace(' ', '-', $record['mail_type'])); ?>">
                                        <?php echo htmlspecialchars($record['mail_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="sender-info">
                                        <strong><?php echo htmlspecialchars($record['sender_name']); ?></strong>
                                        <?php if (!empty($record['sender_company'])): ?>
                                            <br><small><?php echo htmlspecialchars($record['sender_company']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="recipient-info">
                                        <strong><?php echo htmlspecialchars($record['recipient_name']); ?></strong>
                                        <?php if (!empty($record['recipient_department'])): ?>
                                            <br><small><?php echo htmlspecialchars($record['recipient_department']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="datetime-cell">
                                    <div class="datetime-info">
                                        <span class="date"><?php echo date('Y/m/d', strtotime($record['received_date'])); ?></span>
                                        <?php if (!empty($record['received_time'])): ?>
                                            <span class="time"><?php echo $record['received_time']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? '已收件')); ?>">
                                        <?php echo htmlspecialchars($record['status'] ?? '已收件'); ?>
                                    </span>
                                </td>
                                <td class="urgent-cell">
                                    <?php if ($record['urgent']): ?>
                                        <span class="urgent-badge">🚨 緊急</span>
                                    <?php else: ?>
                                        <span class="normal-badge">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($isAdmin): ?>
                                    <td class="actions-cell">
                                        <div class="action-buttons-inline">
                                            <button onclick="viewDetails(<?php echo $record['id']; ?>)" 
                                                    class="btn-icon btn-view" title="查看詳情">
                                                👁️
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 詳情模態框 -->
<div id="detailModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>收件詳情</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- 詳情內容將在這裡動態載入 -->
        </div>
    </div>
</div>

<style>
.mail-incoming-records-container {
    max-width: 1400px;
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

.search-section {
    background: rgba(255, 255, 255, 0.95);
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(200, 16, 46, 0.1);
    margin-bottom: 2rem;
}

.search-row {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.search-group {
    flex: 1;
    min-width: 150px;
}

.search-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.search-input:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
}

.search-actions {
    display: flex;
    gap: 0.5rem;
}

.search-btn, .clear-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.search-btn {
    background: linear-gradient(135deg, #C8102E, #8B0000);
    color: white;
}

.clear-btn {
    background: #6c757d;
    color: white;
}

.search-btn:hover, .clear-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.actions-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
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

.btn-success {
    background: #28a745;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.stats-info {
    color: #666;
    font-weight: 500;
}

.records-list {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(200, 16, 46, 0.1);
    overflow: hidden;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #333;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #666;
    margin-bottom: 2rem;
}

.records-table-container {
    overflow-x: auto;
}

.records-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.records-table th {
    background: linear-gradient(135deg, #C8102E, #8B0000);
    color: white;
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
}

.records-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #e9ecef;
    vertical-align: top;
}

.record-row:hover {
    background-color: #f8f9fa;
}

.record-row.urgent {
    background-color: rgba(255, 193, 7, 0.1);
    border-left: 4px solid #ffc107;
}

.record-id {
    font-family: 'Courier New', monospace;
    color: #C8102E;
    font-weight: bold;
}

.tracking-number {
    font-family: 'Courier New', monospace;
}

.tracking-code {
    color: #0066cc;
    font-weight: 500;
}

.no-tracking {
    color: #999;
}

.mail-type {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
}

.mail-type-掛號 {
    background: #e3f2fd;
    color: #1976d2;
}

.mail-type-包裹 {
    background: #f3e5f5;
    color: #7b1fa2;
}

.mail-type-快遞 {
    background: #e8f5e8;
    color: #388e3c;
}

.mail-type-一般信件 {
    background: #fff3e0;
    color: #f57c00;
}

.mail-type-公文 {
    background: #fce4ec;
    color: #c2185b;
}

.mail-type-其他 {
    background: #f5f5f5;
    color: #757575;
}

.sender-info, .recipient-info {
    line-height: 1.4;
}

.datetime-cell {
    min-width: 100px;
}

.datetime-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-weight: 500;
}

.time {
    font-size: 0.8rem;
    color: #666;
}

.status {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
}

.status-已收件 {
    background: #e3f2fd;
    color: #1976d2;
}

.status-已轉交 {
    background: #fff3cd;
    color: #856404;
}

.status-已領取 {
    background: #d4edda;
    color: #155724;
}

.status-等待處理 {
    background: #f8d7da;
    color: #721c24;
}

.urgent-cell {
    text-align: center;
}

.urgent-badge {
    color: #dc3545;
    font-weight: bold;
    font-size: 0.8rem;
}

.normal-badge {
    color: #999;
}

.actions-cell {
    white-space: nowrap;
}

.action-buttons-inline {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    padding: 0.5rem;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-view:hover {
    background: rgba(0, 123, 255, 0.1);
}

/* 模態框樣式 */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    padding: 0;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #C8102E, #8B0000);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-body {
    padding: 1.5rem;
}

.close {
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    color: white;
}

.close:hover {
    opacity: 0.7;
}

/* 響應式設計 */
@media (max-width: 768px) {
    .mail-incoming-records-container {
        padding: 1rem;
    }
    
    .search-row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .search-group {
        min-width: auto;
    }
    
    .search-actions {
        flex-direction: column;
    }
    
    .actions-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .records-table {
        font-size: 0.8rem;
    }
    
    .records-table th,
    .records-table td {
        padding: 0.5rem;
    }
}
</style>

<script>
function viewDetails(id) {
    // 顯示收件詳情模態框
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    
    modalBody.innerHTML = '<div class="loading">載入中...</div>';
    modal.style.display = 'flex';
    
    // 這裡可以用 AJAX 載入詳細資料
    // 暫時顯示佔位內容
    setTimeout(() => {
        modalBody.innerHTML = `
            <div class="detail-content">
                <h4>收件詳情 - IN-${id}</h4>
                <p>詳細資訊載入中，請稍候...</p>
            </div>
        `;
    }, 500);
}

function editRecord(id) {
    if (confirm('確定要編輯這筆收件記錄嗎？')) {
        window.location.href = '<?php echo $baseUrl; ?>/mail/incoming-edit?id=' + id;
    }
}

function deleteRecord(id) {
    if (confirm('確定要刪除這筆收件記錄嗎？')) {
        window.location.href = '<?php echo $baseUrl; ?>/mail/incoming-delete?id=' + id;
    }
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

// 點擊模態框外部關閉
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script> 