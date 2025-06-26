<div class="page-header">
    <h1 class="page-title">📋 寄件記錄管理</h1>
    <p class="page-subtitle">查詢與管理所有寄件資料，追蹤郵件狀態</p>
</div>

<div class="content-card">
    <div class="records-toolbar">
        <!-- 搜尋表單 -->
        <form method="GET" action="<?php echo $baseUrl; ?>index.php" class="search-form">
            <input type="hidden" name="route" value="/mail/records">
            <div class="search-section">
                <div class="search-group">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" placeholder="搜尋寄件編號、寄件者、收件者..." 
                               value="<?php echo htmlspecialchars($keyword); ?>" class="search-input">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span>🔍</span> 搜尋
                    </button>
                    <?php if (!empty($keyword)): ?>
                        <a href="<?php echo $baseUrl; ?>mail/records" class="btn btn-outline">
                            <span>🗑️</span> 清除
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- 操作按鈕 -->
        <div class="action-buttons">
            <a href="<?php echo $baseUrl; ?>mail/request" class="btn btn-primary">
                <span>📮</span> 新增寄件
            </a>
            <a href="<?php echo $baseUrl; ?>mail/import" class="btn btn-secondary">
                <span>📁</span> 批次匯入
            </a>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $baseUrl; ?>mail/records&export=1" class="btn btn-success">
                    <span>📊</span> 匯出 CSV
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (empty($records)): ?>
    <div class="content-card">
        <div class="empty-state">
            <div class="empty-illustration">
                <div class="empty-icon">📮</div>
                <div class="empty-pattern"></div>
            </div>
            <h3 class="empty-title">
                <?php echo !empty($keyword) ? '🔍 找不到符合條件的記錄' : '📭 目前沒有寄件記錄'; ?>
            </h3>
            <p class="empty-message">
                <?php echo !empty($keyword) ? '請嘗試調整搜尋條件或關鍵字' : '開始登記您的第一個寄件，建立完整的郵務系統！'; ?>
            </p>
            <div class="empty-actions">
                <a href="<?php echo $baseUrl; ?>mail/request" class="btn btn-primary">
                    <span>📮</span> 新增寄件登記
                </a>
                <?php if (!empty($keyword)): ?>
                    <a href="<?php echo $baseUrl; ?>mail/records" class="btn btn-outline">
                        <span>🗑️</span> 清除搜尋
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="content-card">
        <div class="table-header">
            <h3>📊 寄件記錄總覽</h3>
            <div class="table-stats">
                <span class="stat-item">
                    <span class="stat-number"><?php echo count($records); ?></span>
                    <span class="stat-label">筆記錄</span>
                </span>
            </div>
        </div>
        
        <div class="records-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>📦 寄件編號</th>
                        <th>🚚 寄件方式</th>
                        <th>👤 寄件者</th>
                        <th>📍 收件者</th>
                        <th>🏢 申報部門</th>
                        <th>📊 狀態</th>
                        <th>📅 登記時間</th>
                        <th>⚙️ 操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr class="record-row">
                        <td>
                            <div class="mail-code-wrapper">
                                <span class="mail-code"><?php echo htmlspecialchars($record['mail_code'] ?? '待產生'); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="mail-type-tag mail-type-<?php echo strtolower(str_replace(['貨運', '宅急便'], ['freight', 'express'], $record['mail_type'])); ?>">
                                <?php 
                                $typeIcons = [
                                    '掛號' => '📪',
                                    '黑貓' => '🐱',
                                    '新竹貨運' => '🚚'
                                ];
                                echo ($typeIcons[$record['mail_type']] ?? '📦') . ' ' . htmlspecialchars($record['mail_type']); 
                                ?>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <span class="name"><?php echo htmlspecialchars($record['sender_name']); ?></span>
                                <?php if ($record['sender_ext']): ?>
                                    <small class="ext">📞 分機: <?php echo htmlspecialchars($record['sender_ext']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <span class="name"><?php echo htmlspecialchars($record['receiver_name']); ?></span>
                                <?php if ($record['receiver_phone']): ?>
                                    <small class="phone">📱 <?php echo htmlspecialchars($record['receiver_phone']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="department"><?php echo htmlspecialchars($record['declare_department']); ?></span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo str_replace(' ', '', strtolower($record['status'])); ?>">
                                <?php 
                                $statusIcons = [
                                    '草稿' => '📝',
                                    '已寄出' => '✅',
                                    '已送達' => '🏆',
                                    '退回' => '↩️'
                                ];
                                echo ($statusIcons[$record['status']] ?? '❓') . ' ' . htmlspecialchars($record['status']); 
                                ?>
                            </span>
                        </td>
                        <td>
                            <div class="date-info">
                                <span class="date"><?php echo date('Y-m-d', strtotime($record['created_at'])); ?></span>
                                <small class="time"><?php echo date('H:i', strtotime($record['created_at'])); ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons-cell">
                                <?php if ($record['status'] === '草稿' || $isAdmin): ?>
                                    <a href="<?php echo $baseUrl; ?>mail/edit&id=<?php echo $record['id']; ?>" 
                                       class="btn btn-sm btn-outline" title="編輯記錄">
                                        <span>✏️</span> 編輯
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-secondary" onclick="viewDetail(<?php echo $record['id']; ?>)" title="查看詳情">
                                    <span>👁️</span> 查看
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
</div>

<!-- 詳情彈窗 -->
<div id="detailModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>寄件詳情</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="detailContent">
            <!-- 詳情內容會通過 JavaScript 動態載入 -->
        </div>
    </div>
</div>

<style>
/* 工具列樣式 */
.records-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    margin-bottom: 0;
    flex-wrap: wrap;
}

.search-form {
    flex: 1;
    min-width: 300px;
}

.search-section {
    margin-bottom: 1rem;
}

.search-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #666;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 2.5rem;
    border: 2px solid rgba(200,16,46,0.15);
    border-radius: 12px;
    font-size: 0.95rem;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #C8102E;
    box-shadow: 0 0 0 3px rgba(200,16,46,0.1);
    background: rgba(255,255,255,0.95);
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
}

/* 空狀態樣式 */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-illustration {
    position: relative;
    margin-bottom: 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-pattern {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    border: 2px dashed rgba(200,16,46,0.2);
    border-radius: 50%;
    z-index: -1;
}

.empty-title {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 1rem;
    font-weight: 600;
}

.empty-message {
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.6;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* 表格標題區塊 */
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(200,16,46,0.1);
}

.table-header h3 {
    font-size: 1.3rem;
    color: #C8102E;
    margin: 0;
    font-weight: 700;
}

.table-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #C8102E;
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
}

/* 表格容器 */
.records-table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

/* 記錄行樣式 */
.record-row {
    transition: all 0.2s ease;
}

.record-row:hover {
    background-color: rgba(200,16,46,0.02);
    transform: scale(1.002);
}

/* 郵件編號 */
.mail-code-wrapper {
    font-family: 'Courier New', monospace;
}

.mail-code {
    background: linear-gradient(135deg, rgba(200,16,46,0.1) 0%, rgba(200,16,46,0.05) 100%);
    color: #C8102E;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px solid rgba(200,16,46,0.2);
}

/* 郵件類型標籤 */
.mail-type-tag {
    display: inline-block;
    padding: 0.5rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
    min-width: 80px;
}

.mail-type-掛號 {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1565c0;
    border: 1px solid #90caf9;
}

.mail-type-黑貓 {
    background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
    color: #7b1fa2;
    border: 1px solid #ce93d8;
}

.mail-type-freight {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

/* 聯絡資訊 */
.contact-info .name {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.contact-info .ext,
.contact-info .phone {
    display: block;
    font-size: 0.8rem;
    color: #666;
}

/* 部門 */
.department {
    color: #4a5568;
    font-weight: 500;
}

/* 狀態徽章 */
.status-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    min-width: 70px;
}

.status-草稿 {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-已寄出 {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-已送達 {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-退回 {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* 日期資訊 */
.date-info .date {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.date-info .time {
    font-size: 0.8rem;
    color: #666;
}

/* 操作按鈕 */
.action-buttons-cell {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.btn-success { background: #28a745; color: white; }
.btn-info { background: #17a2b8; color: white; }
.btn-warning { background: #ffc107; color: #212529; }
.btn-danger { background: #dc3545; color: white; }

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.records-table-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.records-table {
    width: 100%;
    border-collapse: collapse;
}

.records-table th,
.records-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e1e5e9;
}

.records-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.records-table tr:hover {
    background: rgba(123, 97, 255, 0.05);
}

.mail-code {
    font-family: monospace;
    background: rgba(123, 97, 255, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.mail-type {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.sender-info, .receiver-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.name {
    font-weight: 500;
}

.ext, .phone {
    color: #6c757d;
    font-size: 0.8rem;
}

.status {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-草稿 { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
.status-已送出 { background: rgba(40, 167, 69, 0.1); color: #28a745; }
.status-已寄達 { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }

.date {
    color: #6c757d;
    font-size: 0.9rem;
}

.btn-group {
    display: flex;
    gap: 0.25rem;
}

@media (max-width: 768px) {
    .mail-records-container {
        padding: 1rem;
    }
    
    .records-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form {
        max-width: none;
    }
    
    .records-table-container {
        overflow-x: auto;
    }
    
    .records-table {
        min-width: 800px;
    }
    
    .action-buttons {
        justify-content: center;
    }
}
</style>

<script>
// 查看詳情
function viewDetails(id) {
    fetch(`<?php echo $baseUrl; ?>api/mail/detail&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const record = data.record;
                document.getElementById('detailContent').innerHTML = `
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>寄件編號:</label>
                            <span>${record.mail_code || '尚未產生'}</span>
                        </div>
                        <div class="detail-item">
                            <label>寄件方式:</label>
                            <span>${record.mail_type}</span>
                        </div>
                        <div class="detail-item">
                            <label>寄件者:</label>
                            <span>${record.sender_name}${record.sender_ext ? ' (分機: ' + record.sender_ext + ')' : ''}</span>
                        </div>
                        <div class="detail-item">
                            <label>收件者:</label>
                            <span>${record.receiver_name}</span>
                        </div>
                        <div class="detail-item">
                            <label>收件地址:</label>
                            <span>${record.receiver_address}</span>
                        </div>
                        <div class="detail-item">
                            <label>收件者電話:</label>
                            <span>${record.receiver_phone || '未提供'}</span>
                        </div>
                        <div class="detail-item">
                            <label>申報部門:</label>
                            <span>${record.declare_department}</span>
                        </div>
                        <div class="detail-item">
                            <label>狀態:</label>
                            <span class="status status-${record.status.toLowerCase()}">${record.status}</span>
                        </div>
                        <div class="detail-item">
                            <label>登記時間:</label>
                            <span>${new Date(record.created_at).toLocaleString()}</span>
                        </div>
                        ${record.notes ? `
                        <div class="detail-item full-width">
                            <label>備註:</label>
                            <span>${record.notes}</span>
                        </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('detailModal').style.display = 'flex';
            } else {
                alert('無法載入詳情：' + data.message);
            }
        })
        .catch(error => {
            alert('載入失敗：' + error.message);
        });
}

// 關閉彈窗
function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

// 刪除記錄
function deleteRecord(id) {
    if (confirm('確定要刪除這筆記錄嗎？')) {
        fetch(`<?php echo $baseUrl; ?>mail/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('刪除失敗：' + data.message);
            }
        })
        .catch(error => {
            alert('刪除失敗：' + error.message);
        });
    }
}

// 點擊彈窗外部關閉
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script> 