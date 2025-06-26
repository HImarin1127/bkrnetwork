<div class="mail-records-container">
    <div class="page-header">
        <h1>寄件查詢</h1>
        <p>查看和管理寄件記錄</p>
    </div>

    <!-- 搜尋功能 -->
    <div class="search-section">
        <form method="GET" class="search-form">
            <div class="search-input-group">
                <input type="text" name="search" placeholder="搜尋收件者、地址、寄件者..." 
                       value="<?php echo htmlspecialchars($keyword); ?>" class="search-input">
                <button type="submit" class="search-btn">
                    <i class="icon">🔍</i> 搜尋
                </button>
                <?php if (!empty($keyword)): ?>
                    <a href="<?php echo $baseUrl; ?>/mail/outgoing-records" class="clear-btn">
                        <i class="icon">✖</i> 清除
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 功能選單 -->
    <div class="actions-section">
        <div class="action-buttons">
            <a href="<?php echo $baseUrl; ?>/mail/request" class="btn btn-primary">
                <i class="icon">📮</i> 新增寄件
            </a>
            <a href="<?php echo $baseUrl; ?>/mail/import" class="btn btn-secondary">
                <i class="icon">📥</i> 批次匯入
            </a>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $baseUrl; ?>/mail/outgoing-records?export=1" class="btn btn-success">
                    <i class="icon">📊</i> 匯出 CSV
                </a>
            <?php endif; ?>
        </div>
        
        <div class="stats-info">
            <span class="total-count">共 <?php echo count($records); ?> 筆寄件記錄</span>
        </div>
    </div>

    <!-- 記錄列表 -->
    <div class="records-list">
        <?php if (empty($records)): ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>目前沒有寄件記錄</h3>
                <p><?php echo !empty($keyword) ? '沒有找到符合搜尋條件的記錄' : '還沒有任何寄件記錄'; ?></p>
                <a href="<?php echo $baseUrl; ?>/mail/request" class="btn btn-primary">
                    <i class="icon">📮</i> 立即新增寄件
                </a>
            </div>
        <?php else: ?>
            <div class="records-table-container">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>寄件序號</th>
                            <th>寄件方式</th>
                            <th>收件者</th>
                            <th>收件地址</th>
                            <th>寄件者</th>
                            <th>申報單位</th>
                            <th>登記時間</th>
                            <th>狀態</th>
                            <?php if ($isAdmin): ?>
                                <th>操作</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr class="record-row <?php echo $record['status'] === '已送達' ? 'delivered' : 'pending'; ?>">
                                <td class="mail-code">
                                    <strong><?php echo htmlspecialchars($record['mail_code']); ?></strong>
                                </td>
                                <td>
                                    <span class="mail-type mail-type-<?php echo strtolower(str_replace(' ', '-', $record['mail_type'])); ?>">
                                        <?php echo htmlspecialchars($record['mail_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="recipient-info">
                                        <strong><?php echo htmlspecialchars($record['receiver_name']); ?></strong>
                                        <?php if (!empty($record['receiver_phone'])): ?>
                                            <br><small><?php echo htmlspecialchars($record['receiver_phone']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="address-cell">
                                    <span class="address-text" title="<?php echo htmlspecialchars($record['receiver_address']); ?>">
                                        <?php echo mb_strlen($record['receiver_address']) > 30 ? 
                                            mb_substr(htmlspecialchars($record['receiver_address']), 0, 30) . '...' : 
                                            htmlspecialchars($record['receiver_address']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="sender-info">
                                        <strong><?php echo htmlspecialchars($record['sender_name']); ?></strong>
                                        <?php if (!empty($record['sender_ext'])): ?>
                                            <br><small>分機: <?php echo htmlspecialchars($record['sender_ext']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($record['declare_department']); ?></td>
                                <td class="datetime-cell">
                                    <div class="datetime-info">
                                        <span class="date"><?php echo date('Y/m/d', strtotime($record['created_at'])); ?></span>
                                        <span class="time"><?php echo date('H:i', strtotime($record['created_at'])); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? '處理中')); ?>">
                                        <?php echo htmlspecialchars($record['status'] ?? '處理中'); ?>
                                    </span>
                                </td>
                                <?php if ($isAdmin): ?>
                                    <td class="actions-cell">
                                        <div class="action-buttons-inline">
                                            <a href="<?php echo $baseUrl; ?>/mail/edit?id=<?php echo $record['id']; ?>" 
                                               class="btn-icon btn-edit" title="編輯">
                                                ✏️
                                            </a>
                                            <button onclick="deleteRecord(<?php echo $record['id']; ?>)" 
                                                    class="btn-icon btn-delete" title="刪除">
                                                🗑️
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

<style>
.mail-records-container {
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

.search-form {
    max-width: 600px;
    margin: 0 auto;
}

.search-input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
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

.btn-secondary {
    background: #6c757d;
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

.record-row.delivered {
    background-color: rgba(40, 167, 69, 0.05);
}

.mail-code {
    font-family: 'Courier New', monospace;
    color: #C8102E;
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

.mail-type-黑貓 {
    background: #f3e5f5;
    color: #7b1fa2;
}

.mail-type-新竹貨運 {
    background: #e8f5e8;
    color: #388e3c;
}

.recipient-info, .sender-info {
    line-height: 1.4;
}

.address-cell {
    max-width: 250px;
}

.address-text {
    display: block;
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

.status-處理中 {
    background: #fff3cd;
    color: #856404;
}

.status-已送達 {
    background: #d4edda;
    color: #155724;
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

.btn-edit:hover {
    background: rgba(40, 167, 69, 0.1);
}

.btn-delete:hover {
    background: rgba(220, 53, 69, 0.1);
}

/* 響應式設計 */
@media (max-width: 768px) {
    .mail-records-container {
        padding: 1rem;
    }
    
    .actions-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .search-input {
        margin-bottom: 0.5rem;
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
function deleteRecord(id) {
    if (confirm('確定要刪除這筆寄件記錄嗎？')) {
        window.location.href = '<?php echo $baseUrl; ?>/mail/delete?id=' + id;
    }
}
</script> 