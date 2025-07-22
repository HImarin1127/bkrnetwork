<?php
// 郵資匯入頁面（僅總務可用）
?>
<div class="page-header">
    <h1 class="page-title">📥 郵資匯入</h1>
    <p class="page-subtitle">請上傳國揚提供的郵資 CSV 檔案，系統將自動匯入所有郵資資料。</p>
</div>
<div class="content-card" style="max-width:600px;margin:auto;">
    <form method="POST" enctype="multipart/form-data" class="import-form">
        <div class="form-group">
            <label for="csv_file" class="form-label">選擇 CSV 檔案：</label>
            <input type="file" name="csv_file" id="csv_file" accept=".csv" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary">開始匯入</button>
    </form>
    <?php if (isset($result)): ?>
        <div class="import-result" style="margin-top:1.5rem;">
            <h3>匯入結果</h3>
            <p>成功匯入 <strong><?php echo (int)($result['imported'] ?? 0); ?></strong> 筆資料。</p>
            <?php if (!empty($result['errors'])): ?>
                <div class="error-list">
                    <p style="color:red;">錯誤訊息：</p>
                    <ul>
                        <?php foreach ($result['errors'] as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<style>
.import-form { margin-top: 1.5rem; }
.import-form .form-group { margin-bottom: 1rem; }
.import-form .form-label { font-weight: bold; }
.import-form .form-input { width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid #ccc; }
.btn-primary { background: linear-gradient(135deg, #7b61ff, #4caaff); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; }
.btn-primary:hover { background: #4caaff; }
.import-result { background: #f8fafc; border-radius: 8px; padding: 1rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.error-list ul { margin: 0.5rem 0 0 1.2rem; }
</style> 