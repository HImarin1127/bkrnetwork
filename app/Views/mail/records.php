<div class="mail-records-container">
    <div class="page-header">
        <h1>寄件記錄</h1>
        <p>查詢與管理寄件資料</p>
    </div>

    <div class="records-toolbar">
        <!-- 搜尋表單 -->
        <form method="GET" action="<?php echo $baseUrl; ?>index.php" class="search-form">
            <input type="hidden" name="route" value="/mail/records">
            <div class="search-group">
                <input type="text" name="search" placeholder="搜尋寄件編號、寄件者、收件者..." 
                       value="<?php echo htmlspecialchars($keyword); ?>" class="search-input">
                <button type="submit" class="btn btn-primary">搜尋</button>
                <?php if (!empty($keyword)): ?>
                    <a href="<?php echo $baseUrl; ?>mail/records" class="btn btn-secondary">清除</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- 操作按鈕 -->
        <div class="action-buttons">
            <a href="<?php echo $baseUrl; ?>mail/request" class="btn btn-success">
                <i class="icon">➕</i> 新增寄件
            </a>
            <a href="<?php echo $baseUrl; ?>mail/import" class="btn btn-info">
                <i class="icon">📥</i> 批次匯入
            </a>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $baseUrl; ?>mail/records&export=1" class="btn btn-warning">
                    <i class="icon">📤</i> 匯出 CSV
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($records)): ?>
        <div class="empty-state">
            <div class="empty-icon">📮</div>
            <h3><?php echo !empty($keyword) ? '找不到符合條件的記錄' : '目前沒有寄件記錄'; ?></h3>
            <p><?php echo !empty($keyword) ? '請嘗試調整搜尋條件' : '開始登記您的第一個寄件吧！'; ?></p>
            <a href="<?php echo $baseUrl; ?>mail/request" class="btn btn-primary">
                <i class="icon">➕</i> 新增寄件登記
            </a>
        </div>
    <?php else: ?>
        <div class="records-table-container">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>寄件編號</th>
                        <th>寄件方式</th>
                        <th>寄件者</th>
                        <th>收件者</th>
                        <th>申報部門</th>
                        <th>狀態</th>
                        <th>登記時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td>
                            <span class="mail-code"><?php echo htmlspecialchars($record['mail_code'] ?? '尚未產生'); ?></span>
                        </td>
                        <td>
                            <span class="mail-type"><?php echo htmlspecialchars($record['mail_type']); ?></span>
                        </td>
                        <td>
                            <div class="sender-info">
                                <span class="name"><?php echo htmlspecialchars($record['sender_name']); ?></span>
                                <?php if ($record['sender_ext']): ?>
                                    <small class="ext">分機: <?php echo htmlspecialchars($record['sender_ext']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="receiver-info">
                                <span class="name"><?php echo htmlspecialchars($record['receiver_name']); ?></span>
                                <?php if ($record['receiver_phone']): ?>
                                    <small class="phone"><?php echo htmlspecialchars($record['receiver_phone']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($record['declare_department']); ?></td>
                        <td>
                            <span class="status status-<?php echo str_replace(' ', '', strtolower($record['status'])); ?>">
                                <?php echo htmlspecialchars($record['status']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="date"><?php echo date('Y-m-d H:i', strtotime($record['created_at'])); ?></span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <?php if ($record['status'] === '草稿' || $isAdmin): ?>
                                    <a href="<?php echo $baseUrl; ?>mail/edit&id=<?php echo $record['id']; ?>" 
                                       class="btn btn-sm btn-warning">編輯</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
.mail-records-container {
    max-width: 1200px;
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

.records-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-form {
    flex: 1;
    max-width: 400px;
}

.search-group {
    display: flex;
    gap: 0.5rem;
}

.search-input {
    flex: 1;
    padding: 0.5rem;
    border: 2px solid #e1e5e9;
    border-radius: 6px;
    font-size: 0.9rem;
}

.search-input:focus {
    outline: none;
    border-color: #7b61ff;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.btn-primary { background: #7b61ff; color: white; }
.btn-secondary { background: #6c757d; color: white; }
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