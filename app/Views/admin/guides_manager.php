<?php
// app/Views/admin/guides_manager.php

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<div class="container-fluid mt-4">
    <h1 class="mb-4">操作指引管理</h1>

    <!-- 新增分類卡片 -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-folder-plus me-2"></i> 建立新分類
        </div>
        <div class="card-body">
            <form action="<?php echo $baseUrl; ?>/admin/guides-manager/create-category" method="POST" class="d-flex">
                <input type="text" name="category_name" class="form-control me-2" placeholder="請輸入新分類的名稱" required>
                <button type="submit" class="btn btn-primary">建立</button>
            </form>
        </div>
    </div>

    <!-- 現有分類與檔案管理 -->
    <?php if (empty($categories)): ?>
        <div class="alert alert-info">目前沒有任何分類。請使用上面的表單建立一個新分類。</div>
    <?php else: ?>
        <div class="accordion" id="guidesAccordion">
            <?php foreach ($categories as $index => $category): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $index; ?>" aria-expanded="<?php echo $index > 0 ? 'false' : 'true'; ?>" aria-controls="collapse-<?php echo $index; ?>">
                            <i class="fas fa-folder me-2"></i>
                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                            <span class="badge bg-secondary ms-3"><?php echo count($category['files']); ?> 個檔案</span>
                        </button>
                    </h2>
                    <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index == 0 ? 'show' : ''; ?>" aria-labelledby="heading-<?php echo $index; ?>" data-bs-parent="#guidesAccordion">
                        <div class="accordion-body">
                            <!-- 操作選擇區 -->
                            <div class="card mb-3">
                                <div class="card-body bg-light">
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="content_type_<?php echo $index; ?>" id="file_mode_<?php echo $index; ?>" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary" for="file_mode_<?php echo $index; ?>">📁 檔案上傳</label>

                                        <input type="radio" class="btn-check" name="content_type_<?php echo $index; ?>" id="web_mode_<?php echo $index; ?>" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="web_mode_<?php echo $index; ?>">🌐 網頁編輯</label>
                                    </div>
                                </div>
                            </div>

                            <!-- 檔案上傳表單 -->
                            <div class="card mb-3" id="file_upload_<?php echo $index; ?>">
                                <div class="card-body bg-light">
                                    <form action="<?php echo $baseUrl; ?>/admin/guides-manager/upload" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <div class="mb-3">
                                            <label for="guide_file_<?php echo $index; ?>" class="form-label">上傳新檔案到「<?php echo htmlspecialchars($category['name']); ?>」</label>
                                            <input class="form-control" type="file" name="guide_file" id="guide_file_<?php echo $index; ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm">上傳檔案</button>
                                    </form>
                                </div>
                            </div>

                            <!-- 網頁內容編輯表單 -->
                            <div class="card mb-3" id="web_content_<?php echo $index; ?>" style="display: none;">
                                <div class="card-body">
                                    <form action="<?php echo $baseUrl; ?>/admin/guides-manager/create-web-content" method="POST">
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <div class="mb-3">
                                            <label for="content_title_<?php echo $index; ?>" class="form-label">指引標題</label>
                                            <input type="text" class="form-control" name="content_title" id="content_title_<?php echo $index; ?>" placeholder="例如：會議室預約完整教學" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content_body_<?php echo $index; ?>" class="form-label">內容編輯</label>
                                            <textarea class="form-control tinymce-editor" name="content_body" id="content_body_<?php echo $index; ?>" rows="10" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">建立網頁內容</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- 檔案列表 -->
                            <?php if (empty($category['files'])): ?>
                                <div class="text-center text-muted p-3">這個分類目前沒有任何檔案。</div>
                            <?php else: ?>
                                <ul class="list-group">
                                    <?php foreach ($category['files'] as $file): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file-alt me-2"></i>
                                                <?php echo htmlspecialchars($file['name']); ?>
                                                <small class="text-muted ms-2">(<?php echo formatBytes($file['size']); ?>)</small>
                                            </div>
                                            <form action="<?php echo $baseUrl; ?>/admin/guides-manager/delete-file" method="POST" onsubmit="return confirm('您確定要刪除這個檔案嗎？');">
                                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category['name']); ?>">
                                                <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($file['name']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <!-- 刪除分類按鈕 -->
                            <div class="mt-4 text-end">
                                <form action="<?php echo $baseUrl; ?>/admin/guides-manager/delete-category" method="POST" onsubmit="return confirm('您確定要刪除「<?php echo htmlspecialchars($category['name']); ?>」這個分類嗎？此操作只有在分類為空時才能成功。');">
                                    <input type="hidden" name="category_name" value="<?php echo htmlspecialchars($category['name']); ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">刪除此分類</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- CKEditor 5 富文本編輯器 -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
// 初始化 CKEditor 編輯器
document.addEventListener('DOMContentLoaded', function() {
    // 為每個編輯器初始化 CKEditor
    const editorElements = document.querySelectorAll('.tinymce-editor');
    
    editorElements.forEach(function(element) {
        ClassicEditor
            .create(element, {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', '|',
                        'numberedList', 'bulletedList', '|',
                        'outdent', 'indent', '|',
                        'link', 'insertTable', '|',
                        'blockQuote', 'code', '|',
                        'undo', 'redo'
                    ]
                },
                language: 'zh',
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: '段落', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: '標題 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: '標題 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: '標題 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                console.log('CKEditor 已初始化:', editor);
                
                // 設定編輯器高度
                editor.editing.view.change(writer => {
                    writer.setStyle('height', '400px', editor.editing.view.document.getRoot());
                });
                
                // 當表單提交時，確保內容被正確傳送
                const form = element.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // 將編輯器的內容設定到原始的 textarea 中
                        element.value = editor.getData();
                        console.log('表單提交，內容：', editor.getData());
                    });
                }
                
                // 儲存編輯器實例，以便後續使用
                element.ckEditorInstance = editor;
            })
            .catch(error => {
                console.error('CKEditor 初始化失敗:', error);
            });
    });
});

// 處理模式切換
document.addEventListener('DOMContentLoaded', function() {
    // 為每個分類設置模式切換功能
    <?php foreach ($categories as $index => $category): ?>
    const fileMode<?php echo $index; ?> = document.getElementById('file_mode_<?php echo $index; ?>');
    const webMode<?php echo $index; ?> = document.getElementById('web_mode_<?php echo $index; ?>');
    const fileUpload<?php echo $index; ?> = document.getElementById('file_upload_<?php echo $index; ?>');
    const webContent<?php echo $index; ?> = document.getElementById('web_content_<?php echo $index; ?>');

    if (fileMode<?php echo $index; ?> && webMode<?php echo $index; ?>) {
        fileMode<?php echo $index; ?>.addEventListener('change', function() {
            if (this.checked) {
                fileUpload<?php echo $index; ?>.style.display = 'block';
                webContent<?php echo $index; ?>.style.display = 'none';
            }
        });

        webMode<?php echo $index; ?>.addEventListener('change', function() {
            if (this.checked) {
                fileUpload<?php echo $index; ?>.style.display = 'none';
                webContent<?php echo $index; ?>.style.display = 'block';
                
                // 當切換到網頁編輯模式時，如果編輯器還沒有初始化，則初始化它
                const editorElement = webContent<?php echo $index; ?>.querySelector('.tinymce-editor');
                if (editorElement && !editorElement.ckEditorInstance) {
                    setTimeout(function() {
                        ClassicEditor
                            .create(editorElement, {
                                toolbar: {
                                    items: [
                                        'heading', '|',
                                        'bold', 'italic', 'underline', '|',
                                        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                                        'alignment', '|',
                                        'numberedList', 'bulletedList', '|',
                                        'outdent', 'indent', '|',
                                        'link', 'insertTable', '|',
                                        'blockQuote', 'code', '|',
                                        'undo', 'redo'
                                    ]
                                },
                                language: 'zh',
                                table: {
                                    contentToolbar: [
                                        'tableColumn',
                                        'tableRow',
                                        'mergeTableCells'
                                    ]
                                },
                                heading: {
                                    options: [
                                        { model: 'paragraph', title: '段落', class: 'ck-heading_paragraph' },
                                        { model: 'heading1', view: 'h1', title: '標題 1', class: 'ck-heading_heading1' },
                                        { model: 'heading2', view: 'h2', title: '標題 2', class: 'ck-heading_heading2' },
                                        { model: 'heading3', view: 'h3', title: '標題 3', class: 'ck-heading_heading3' }
                                    ]
                                }
                            })
                            .then(editor => {
                                console.log('延遲初始化 CKEditor 成功:', editor);
                                
                                // 設定編輯器高度
                                editor.editing.view.change(writer => {
                                    writer.setStyle('height', '400px', editor.editing.view.document.getRoot());
                                });
                                
                                // 當表單提交時，確保內容被正確傳送
                                const form = editorElement.closest('form');
                                if (form) {
                                    form.addEventListener('submit', function(e) {
                                        editorElement.value = editor.getData();
                                        console.log('表單提交，內容：', editor.getData());
                                    });
                                }
                                
                                editorElement.ckEditorInstance = editor;
                            })
                            .catch(error => {
                                console.error('延遲初始化 CKEditor 失敗:', error);
                            });
                    }, 500);
                }
            }
        });
    }
    <?php endforeach; ?>
});
</script>
