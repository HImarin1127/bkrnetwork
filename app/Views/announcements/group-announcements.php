<div class="shared-files-container">
    <div class="page-header">
        <h1>集團公告</h1>
        <p class="page-subtitle">公司內部共用檔案、教學文件與資源</p>
    </div>

    <div class="file-browser-card glass-card">
        <nav class="breadcrumbs">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php if ($index < count($breadcrumbs) - 1): ?>
                    <a href="<?php echo $baseUrl . '/group-announcements?path=' . urlencode($crumb['path']); ?>"><?php echo htmlspecialchars($crumb['name']); ?></a>
                    <span class="separator">/</span>
                <?php else: ?>
                    <span class="current"><?php echo htmlspecialchars($crumb['name']); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="file-list">
            <?php if (isset($error)): ?>
                <p class="empty-folder" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif (empty($items)): ?>
                <p class="empty-folder">此資料夾是空的。</p>
            <?php else: ?>
                <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <a href="<?php echo $item['url']; ?>" <?php if ($item['type'] === 'file') echo 'target="_blank"'; ?>>
                            <span class="icon"><?php echo ($item['type'] === 'dir' ? '📁' : '📄'); ?></span>
                            <span class="name"><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.shared-files-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    color: #6b46c1;
    margin-bottom: 10px;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
}

/* New Styles for File Browser */
.breadcrumbs {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 1.1rem;
}
.breadcrumbs a {
    color: #6b46c1;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}
.breadcrumbs a:hover {
    color: #C8102E;
}
.breadcrumbs .separator {
    margin: 0 8px;
    color: #9ca3af;
}
.breadcrumbs .current {
    color: #374151;
    font-weight: 500;
}

.file-list ul {
    list-style-type: none;
    padding-left: 0;
}
.file-list li {
    margin-bottom: 8px;
}
.file-list a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-radius: 8px;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.3s ease;
}
.file-list a:hover {
    background-color: rgba(107, 70, 193, 0.1);
}
.file-list .icon {
    font-size: 1.5rem;
    margin-right: 15px;
    width: 25px; /* for alignment */
    text-align: center;
}
.file-list .name {
    font-size: 1rem;
    font-weight: 500;
}
.empty-folder {
    text-align: center;
    color: #6b7280;
    padding: 40px 0;
    font-size: 1.1rem;
}
</style> 