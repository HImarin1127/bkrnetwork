<div class="admin-header">
    <div class="admin-breadcrumb">
        <span class="breadcrumb-item">
            <a href="<?php echo $baseUrl; ?>/admin/dashboard">管理後台</a>
        </span>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">公告管理</span>
    </div>
    <h1 class="admin-title">📢 公告管理系統</h1>
    <p class="admin-subtitle">管理企業內部公告、假日資訊與員工手冊</p>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <div class="alert-icon">✅</div>
    <div class="alert-content">
        <strong>操作成功</strong>
        <p><?php echo htmlspecialchars($_GET['success']); ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-error">
    <div class="alert-icon">❌</div>
    <div class="alert-content">
        <strong>操作失敗</strong>
        <p><?php echo htmlspecialchars($_GET['error']); ?></p>
    </div>
</div>
<?php endif; ?>

<div class="admin-actions">
    <div class="action-buttons">
        <a href="<?php echo $baseUrl; ?>/admin/announcements/create" class="btn btn-primary">
            <span class="btn-icon">➕</span>
            <span class="btn-text">新增公告</span>
        </a>
        <button class="btn btn-outline" onclick="refreshAnnouncements()">
            <span class="btn-icon">🔄</span>
            <span class="btn-text">重新載入</span>
        </button>
    </div>
    
    <div class="filter-controls">
        <select id="typeFilter" class="form-select" onchange="filterAnnouncements()">
            <option value="">所有類型</option>
            <option value="general">一般公告</option>
            <option value="holiday">假日資訊</option>
            <option value="handbook">員工手冊</option>
        </select>
        
        <select id="statusFilter" class="form-select" onchange="filterAnnouncements()">
            <option value="">所有狀態</option>
            <option value="draft">草稿</option>
            <option value="published">已發布</option>
        </select>
    </div>
</div>

<div class="announcements-container">
    <?php if (!empty($announcements)): ?>
        <div class="announcements-table-wrapper">
            <table class="announcements-table">
                <thead>
                    <tr>
                        <th class="col-title">標題</th>
                        <th class="col-type">類型</th>
                        <th class="col-status">狀態</th>
                        <th class="col-dates">日期資訊</th>
                        <th class="col-author">作者</th>
                        <th class="col-attachment">附件</th>
                        <th class="col-actions">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                    <tr class="announcement-row" 
                        data-type="<?php echo $announcement['type']; ?>" 
                        data-status="<?php echo $announcement['status']; ?>">
                        
                        <td class="col-title">
                            <div class="announcement-title">
                                <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                                <p class="announcement-preview">
                                    <?php echo htmlspecialchars(mb_strimwidth($announcement['content'], 0, 80, '...')); ?>
                                </p>
                            </div>
                        </td>
                        
                        <td class="col-type">
                            <?php 
                            $typeConfig = [
                                'general' => ['icon' => '📢', 'label' => '一般公告', 'class' => 'type-general'],
                                'holiday' => ['icon' => '🎉', 'label' => '假日資訊', 'class' => 'type-holiday'],
                                'handbook' => ['icon' => '📚', 'label' => '員工手冊', 'class' => 'type-handbook']
                            ];
                            $config = $typeConfig[$announcement['type']] ?? $typeConfig['general'];
                            ?>
                            <span class="type-badge <?php echo $config['class']; ?>">
                                <?php echo $config['icon']; ?> <?php echo $config['label']; ?>
                            </span>
                        </td>
                        
                        <td class="col-status">
                            <span class="status-badge <?php echo $announcement['status'] === 'published' ? 'status-published' : 'status-draft'; ?>">
                                <?php echo $announcement['status'] === 'published' ? '✅ 已發布' : '📝 草稿'; ?>
                            </span>
                        </td>
                        
                        <td class="col-dates">
                            <div class="date-info">
                                <div class="date-item">
                                    <span class="date-label">新增時間：</span>
                                    <span class="date-value"><?php echo date('Y/m/d H:i', strtotime($announcement['created_at'])); ?></span>
                                </div>
                                <div class="date-item">
                                    <span class="date-label">公告日期：</span>
                                    <span class="date-value"><?php echo $announcement['date'] ? date('Y/m/d', strtotime($announcement['date'])) : '未設定'; ?></span>
                                </div>
                                <?php if ($announcement['published_at']): ?>
                                <div class="date-item">
                                    <span class="date-label">發布時間：</span>
                                    <span class="date-value"><?php echo date('Y/m/d H:i', strtotime($announcement['published_at'])); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="col-author">
                            <div class="author-info">
                                <div class="author-name"><?php echo htmlspecialchars($announcement['author_name']); ?></div>
                                <?php if ($announcement['author_username']): ?>
                                <div class="author-dept">帳號：<?php echo htmlspecialchars($announcement['author_username']); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="col-attachment">
                            <?php if ($announcement['attachment_url']): ?>
                            <div class="attachment-info">
                                <a href="<?php echo $baseUrl; ?>/<?php echo $announcement['attachment_url']; ?>" 
                                   target="_blank" class="attachment-link">
                                    📎 <?php echo htmlspecialchars($announcement['attachment_name']); ?>
                                </a>
                            </div>
                            <?php else: ?>
                            <span class="no-attachment">無附件</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="col-actions">
                            <div class="action-buttons-group">
                                <a href="<?php echo $baseUrl; ?>/admin/announcements/edit?id=<?php echo $announcement['id']; ?>" 
                                   class="btn btn-sm btn-secondary" title="編輯">
                                    ✏️
                                </a>
                                
                                <?php if ($announcement['status'] === 'draft'): ?>
                                <button onclick="publishAnnouncement(<?php echo $announcement['id']; ?>)" 
                                        class="btn btn-sm btn-success" title="發布">
                                    🚀
                                </button>
                                <?php else: ?>
                                <button onclick="unpublishAnnouncement(<?php echo $announcement['id']; ?>)" 
                                        class="btn btn-sm btn-warning" title="取消發布">
                                    📤
                                </button>
                                <?php endif; ?>
                                
                                <button onclick="deleteAnnouncement(<?php echo $announcement['id']; ?>)" 
                                        class="btn btn-sm btn-danger" title="刪除">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3 class="empty-title">目前沒有任何公告</h3>
            <p class="empty-message">點擊上方「新增公告」按鈕開始建立您的第一個公告</p>
            <a href="<?php echo $baseUrl; ?>/admin/announcements/create" class="btn btn-primary">
                <span>➕</span> 建立第一個公告
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #C8102E;
}

.admin-breadcrumb {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.breadcrumb-item a {
    color: #C8102E;
    text-decoration: none;
}

.breadcrumb-separator {
    margin: 0 0.5rem;
}

.breadcrumb-current {
    font-weight: 600;
}

.admin-title {
    font-size: 2rem;
    color: #C8102E;
    margin: 0.5rem 0;
}

.admin-subtitle {
    color: #666;
    margin: 0;
}

.alert {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: rgba(76, 175, 80, 0.1);
    border: 1px solid #4CAF50;
    color: #2E7D32;
}

.alert-error {
    background: rgba(244, 67, 54, 0.1);
    border: 1px solid #F44336;
    color: #C62828;
}

.alert-icon {
    font-size: 1.5rem;
}

.alert-content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.admin-actions {
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
}

.filter-controls {
    display: flex;
    gap: 1rem;
}

.form-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    font-size: 0.9rem;
    min-width: 120px;
}

.announcements-table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.announcements-table {
    width: 100%;
    border-collapse: collapse;
}

.announcements-table th {
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.announcements-table td {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    vertical-align: top;
}

.announcement-row:hover {
    background: rgba(200, 16, 46, 0.02);
}

.announcement-title h4 {
    margin: 0 0 0.25rem 0;
    color: #2d3748;
    font-size: 1rem;
    font-weight: 600;
}

.announcement-preview {
    margin: 0;
    color: #666;
    font-size: 0.85rem;
    line-height: 1.4;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.type-general {
    background: rgba(33, 150, 243, 0.1);
    color: #1976D2;
    border: 1px solid #2196F3;
}

.type-holiday {
    background: rgba(255, 152, 0, 0.1);
    color: #F57C00;
    border: 1px solid #FF9800;
}

.type-handbook {
    background: rgba(76, 175, 80, 0.1);
    color: #388E3C;
    border: 1px solid #4CAF50;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.3rem 0.7rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-published {
    background: rgba(76, 175, 80, 0.1);
    color: #2E7D32;
    border: 1px solid #4CAF50;
}

.status-draft {
    background: rgba(158, 158, 158, 0.1);
    color: #424242;
    border: 1px solid #9E9E9E;
}

.date-info {
    font-size: 0.85rem;
}

.date-item {
    margin-bottom: 0.25rem;
}

.date-label {
    color: #666;
    font-weight: 500;
}

.date-value {
    color: #2d3748;
    margin-left: 0.25rem;
}

.author-info {
    font-size: 0.9rem;
}

.author-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.author-dept {
    color: #666;
    font-size: 0.8rem;
}

.attachment-link {
    color: #C8102E;
    text-decoration: none;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.attachment-link:hover {
    text-decoration: underline;
}

.no-attachment {
    color: #999;
    font-size: 0.85rem;
    font-style: italic;
}

.action-buttons-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.9rem;
    min-width: 2rem;
    border-radius: 4px;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-title {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-message {
    color: #666;
    margin-bottom: 2rem;
}

/* 響應式設計 */
@media (max-width: 1024px) {
    .announcements-table-wrapper {
        overflow-x: auto;
    }
    
    .announcements-table {
        min-width: 1000px;
    }
}

@media (max-width: 768px) {
    .admin-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .filter-controls {
        justify-content: center;
    }
}
</style>

<script>
function filterAnnouncements() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.announcement-row');
    
    rows.forEach(row => {
        const type = row.dataset.type;
        const status = row.dataset.status;
        
        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        if (typeMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function refreshAnnouncements() {
    window.location.href = '<?php echo $baseUrl; ?>/admin/announcements';
}

function handleApiResponse(response) {
    if (response.success) {
        alert(response.message || '操作成功！');
        window.location.reload();
    } else {
        alert('操作失敗：' + (response.message || '未知錯誤'));
    }
}

function sendPostRequest(url, id) {
    const formData = new FormData();
    formData.append('id', id);

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(handleApiResponse)
    .catch(error => {
        console.error('請求錯誤:', error);
        alert('請求發送失敗，請查看主控台以獲取更多資訊。');
    });
}

function deleteAnnouncement(id) {
    if (confirm('您確定要永久刪除此公告嗎？此操作無法復原。')) {
        sendPostRequest('<?php echo $baseUrl; ?>/admin/announcements/delete', id);
    }
}

function publishAnnouncement(id) {
    if (confirm('您確定要發布此公告嗎？')) {
        sendPostRequest('<?php echo $baseUrl; ?>/admin/announcements/publish', id);
    }
}

function unpublishAnnouncement(id) {
    if (confirm('您確定要將此公告取消發布（轉為草稿）嗎？')) {
        sendPostRequest('<?php echo $baseUrl; ?>/admin/announcements/unpublish', id);
    }
}
</script> 