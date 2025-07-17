<?php
// app/Views/guides/printer-basic.php

$pageTitle = "印表機基本操作說明";
$pageType = "guides"; // For sidebar highlighting
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
.guide-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    margin-bottom: 10px;
}

.guide-content { /* 用於包裹多個 content-card */
    display: flex;
    flex-direction: column;
    gap: 25px; /* 卡片之間的間距 */
}

.content-card { /* 自定義卡片樣式 */
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 30px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.content-card h2, .content-card h3 { /* 統一卡片內標題樣式 */
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.section-content p { /* 段落文字樣式 */
    margin-bottom: 20px;
    line-height: 1.6;
}

.btn { /* 自定義按鈕樣式 */
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
}

.btn-primary { /* 主按鈕顏色 */
    background-color: #C8102E;
    color: white;
}

.btn-primary:hover { /* 主按鈕 hover 效果 */
    background-color: #a00d25;
}

/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .content-card {
        padding: 20px;
    }
    .btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>

<div class="guide-container mt-5 mb-5"> <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
    </div>

    <div class="guide-content">
        <div class="content-card">
            <h3><i class="bi bi-printer-fill me-2 text-primary"></i> 檔案列印</h3>
            <div class="section-content">
                <p>關於如何使用印表機進行文件列印，請參考以下操作手冊：</p>
                <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案列印.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看列印教學
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-scanner-fill me-2 text-primary"></i> 檔案掃描</h3>
            <div class="section-content">
                <p>關於如何使用印表機將實體文件掃描為電子檔，請參考以下操作手冊：</p>
                <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案掃描.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看掃描教學
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-fax-fill me-2 text-primary"></i> 檔案傳真</h3>
            <div class="section-content">
                <p>關於如何使用印表機發送傳真，請參考以下操作手冊：</p>
                <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案傳真.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看傳真教學
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>