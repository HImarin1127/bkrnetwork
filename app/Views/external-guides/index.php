<?php
// app/Views/external-guides/index.php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function getIconForFileType($type) {
    switch ($type) {
        case 'pdf': return 'fa-file-pdf';
        case 'doc': case 'docx': return 'fa-file-word';
        case 'xls': case 'xlsx': return 'fa-file-excel';
        case 'ppt': case 'pptx': return 'fa-file-powerpoint';
        case 'zip': case 'rar': return 'fa-file-archive';
        case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image';
        default: return 'fa-file-alt';
    }
}

function renderFileList($files, $directories) {
    ob_start();
    if (!empty($directories)) {
        foreach ($directories as $dir) {
            echo '<a href="?route=/external-guides&path=' . urlencode($dir['path']) . '" class="list-group-item list-group-item-action file-item">';
            echo '<i class="fas fa-folder fa-fw me-3 folder-icon"></i>';
            echo '<span>' . htmlspecialchars($dir['name']) . '</span>';
            echo '</a>';
        }
    }
    if (!empty($files)) {
        foreach ($files as $file) {
            $icon = getIconForFileType($file['type']);
            
            // 根據檔案類型決定連結方式
            if (isset($file['is_web_content']) && $file['is_web_content']) {
                // HTML 檔案：直接在同一視窗開啟
                $link_url = '?route=/external-guides/view&path=' . urlencode($file['path']);
                $target = '';
                $icon = 'fa-globe'; // 網頁圖示
            } else {
                // 一般檔案：下載或在新視窗開啟
                $link_url = '?route=/external-guides/download&path=' . urlencode($file['path']);
                $target = ' target="_blank"';
            }
            
            echo '<a href="' . $link_url . '"' . $target . ' class="list-group-item list-group-item-action file-item">';
            echo '<i class="fas ' . $icon . ' fa-fw me-3 file-icon"></i>';
            echo '<span class="file-name">' . htmlspecialchars($file['name']) . '</span>';
            echo '<span class="file-size">' . htmlspecialchars(formatBytes($file['size'])) . '</span>';
            echo '</a>';
        }
    }
    if (empty($directories) && empty($files)) {
        echo '<div class="alert alert-info mt-3 empty-folder-alert">這個資料夾是空的。</div>';
    }
    return ob_get_clean();
}
?>

<div class="container-fluid mt-4">
    <?php if (isset($is_root) && $is_root): ?>
        <div class="d-flex justify-content-center align-items-center mb-4 position-relative">
    <h1 class="main-title mb-0">操作指引</h1>
    <a href="<?php echo $baseUrl; ?>/admin/guides-manager" class="btn btn-primary btn-sm position-absolute" style="right: 0;">
        <i class="fas fa-cog me-2"></i>管理後台
    </a>
</div>
        <div class="row">
            <?php foreach ($all_folders_content as $folder_name => $content): ?>
                <div class="col-lg-6 mb-4">
                    <div class="glass-card guide-card">
                        <div class="card-header">
                            <i class="fas fa-book-open me-2"></i>
                            <?php echo htmlspecialchars($folder_name); ?>
                        </div>
                        <div class="card-body">
                            <?php if ($content['error']): ?>
                                <div class="alert alert-danger error-alert"><?php echo $content['error']; ?></div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php echo renderFileList($content['files'], $content['directories']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-card" style="padding: 2rem;">
            <!-- Breadcrumbs for subfolders -->
            <div class="d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
                    <div class="breadcrumb">
                        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                            <?php if ($index < count($breadcrumbs) - 1): ?>
                                <span class="breadcrumb-item"><a href="?route=/external-guides&path=<?php echo urlencode($breadcrumb['path']); ?>"><?php echo htmlspecialchars($breadcrumb['name']); ?></a></span>
                            <?php else: ?>
                                <span class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumb['name']); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </nav>
                <a href="<?php echo $baseUrl; ?>/admin/guides-manager" class="btn btn-primary btn-sm">
                    <i class="fas fa-cog me-2"></i>管理後台
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger error-alert"><?php echo $error; ?></div>
            <?php else: ?>
                <div class="list-group">
                    <?php echo renderFileList($files, $directories); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.main-title {
    color: #333;
    text-align: center;
    font-weight: bold;
}
.guide-card {
    border: none;
    border-radius: 15px;
    height: 100%;
}
.guide-card .card-header {
    background-color: rgba(255, 255, 255, 0.7);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-size: 1.25rem;
    font-weight: bold;
    color: #4c1d95;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
.guide-card .card-body {
    padding: 1rem;
}
.file-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}
.file-item:last-child {
    border-bottom: none;
}
.file-icon, .folder-icon {
    font-size: 1.2rem;
    color: #6c757d;
}
.folder-icon {
    color: #FFD700;
}
.file-name {
    flex-grow: 1;
}
.file-size {
    font-size: 0.85rem;
    color: #6c757d;
}
.empty-folder-alert, .error-alert {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}
</style> 