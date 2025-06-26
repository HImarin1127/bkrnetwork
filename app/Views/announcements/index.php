<div class="announcements-container">
    <div class="page-header">
        <h1>讀書共和國最新公告</h1>
        <p>查看讀書共和國最新資訊與重要通知</p>
    </div>

    <?php if (!empty($announcements)): ?>
        <div class="announcements-list">
            <?php foreach ($announcements as $announcement): ?>
            <div class="announcement-item">
                <div class="announcement-header">
                    <h2 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h2>
                    <span class="announcement-date"><?php echo date('Y-m-d', strtotime($announcement['created_at'])); ?></span>
                </div>
                <div class="announcement-content">
                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                </div>
                <?php if ($announcement['type'] === 'general'): ?>
                    <div class="announcement-type">
                        <span class="type-badge type-general">一般公告</span>
                    </div>
                <?php elseif ($announcement['type'] === 'holiday'): ?>
                    <div class="announcement-type">
                        <span class="type-badge type-holiday">假日公告</span>
                    </div>
                <?php elseif ($announcement['type'] === 'handbook'): ?>
                    <div class="announcement-type">
                        <span class="type-badge type-handbook">員工手冊</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-announcements">
            <div class="empty-state">
                <div class="empty-icon">📢</div>
                <h3>目前暫無公告</h3>
                <p>請稍後再來查看最新資訊</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="announcement-nav">
        <div class="nav-links">
            <a href="<?php echo $baseUrl; ?>announcements/holidays" class="nav-btn">
                📅 假日資訊
            </a>
            <a href="<?php echo $baseUrl; ?>announcements/handbook" class="nav-btn">
                📖 員工手冊
            </a>
        </div>
    </div>
</div>

<style>
.announcements-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-header h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #7b61ff, #4caaff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-header p {
    color: #666;
    font-size: 1.1rem;
}

.announcement-type {
    margin-top: 1rem;
}

.type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.type-general {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

.type-holiday {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.type-handbook {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
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
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #666;
}

.announcement-nav {
    margin-top: 3rem;
    text-align: center;
}

.nav-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.nav-btn {
    display: inline-block;
    padding: 1rem 2rem;
    background: rgba(123, 97, 255, 0.1);
    color: #7b61ff;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid rgba(123, 97, 255, 0.2);
}

.nav-btn:hover {
    background: rgba(123, 97, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(123, 97, 255, 0.2);
}
</style> 