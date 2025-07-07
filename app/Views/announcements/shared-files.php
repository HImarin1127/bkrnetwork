<?php
// app/Views/announcements/shared-files.php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<div class="glass-card" style="padding: 2rem;">
    <h2 class="mb-4">集團公告</h2>

    <!-- 麵包屑導航 -->
    <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <?php if ($index < count($breadcrumbs) - 1): ?>
                    <li class="breadcrumb-item"><a href="?route=/group-announcements&path=<?php echo urlencode($breadcrumb['path']); ?>"><?php echo htmlspecialchars($breadcrumb['name']); ?></a></li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumb['name']); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; padding: 1rem; border-radius: 0.25rem;">
            <?php echo $error; ?>
        </div>
    <?php else: ?>
        <div class="list-group">
            <!-- 顯示資料夾 -->
            <?php if (!empty($directories)): ?>
                <?php foreach ($directories as $dir): ?>
                    <a href="?route=/group-announcements&path=<?php echo urlencode($dir['path']); ?>" class="list-group-item list-group-item-action" style="display: flex; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid #eee;" target="_blank">
                        <i class="fas fa-folder fa-fw me-3" style="color: #FFD700; font-size: 1.2rem;"></i>
                        <span><?php echo htmlspecialchars($dir['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- 顯示檔案 -->
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                    <a href="?route=/group-announcements/download&path=<?php echo urlencode($file['path']); ?>" class="list-group-item list-group-item-action" style="display: flex; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid #eee;" target="_blank">
                        <i class="fas <?php 
                            switch ($file['type']) {
                                case 'pdf': echo 'fa-file-pdf'; break;
                                case 'doc': case 'docx': echo 'fa-file-word'; break;
                                case 'xls': case 'xlsx': echo 'fa-file-excel'; break;
                                case 'ppt': case 'pptx': echo 'fa-file-powerpoint'; break;
                                case 'zip': case 'rar': echo 'fa-file-archive'; break;
                                case 'jpg': case 'jpeg': case 'png': case 'gif': echo 'fa-file-image'; break;
                                default: echo 'fa-file-alt';
                            }
                        ?> fa-fw me-3" style="font-size: 1.2rem;"></i>
                        <span style="flex-grow: 1;"><?php echo htmlspecialchars($file['name']); ?></span>
                        <span class="text-muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars(formatBytes($file['size'])); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (empty($directories) && empty($files)): ?>
            <div class="alert alert-info mt-3" style="background-color: #cce5ff; color: #004085; border-color: #b8daff; padding: 1rem; border-radius: 0.25rem;">
                這個資料夾是空的。
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div> 