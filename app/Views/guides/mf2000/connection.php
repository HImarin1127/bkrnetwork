<?php
// app/Views/guides/mf2000/connection.php

$pageTitle = "MF2000 連線說明";
// $pageType = "guides"; // For sidebar highlighting (如果需要，可以取消註解)
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
/* 基礎容器和頁面標題樣式，從您之前的範例中整合 */
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

/* 單一卡片樣式 (整合您提供的 .card 樣式到 .content-card) */
.content-card { /* 使用 content-card 以保持命名統一 */
    background-color: #fff; /* 確保背景為白色 */
    border-radius: 8px; /* 您提供的 border-radius */
    box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* 您提供的 box-shadow */
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* 您提供的 transition */
    border: 1px solid #e2e8f0; /* 您提供的 border */
    padding: 2.5rem; /* 您提供的 card-body padding */
    max-width: 600px; /* 限制卡片最大寬度 */
    margin: auto; /* 居中卡片 */
    text-align: center; /* 內容居中 */
}
.content-card:hover { /* 您提供的 card:hover 樣式 */
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

/* 卡片內標題 */
.content-card .card-title {
    font-size: 1.45rem; /* 採用您先前指定的 h3/h2 字體大小 */
    font-weight: 600; /* 增加字重 */
    color: #333; /* 設定一個深色文字 */
    margin-bottom: 1rem; /* 與文字的間距 */
}

/* 卡片內文字 */
.content-card .card-text {
    color: #718096;
    font-size: 1.1rem; /* 與內容文字大小統一 */
    line-height: 1.6;
    margin-bottom: 2rem; /* 按鈕上方留更多空間 */
}

/* 按鈕樣式 (從您之前提供的 CSS 參考而來) */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center; /* 按鈕文字和圖示居中 */
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
    font-size: 1rem;
}
.btn-primary { /* 這是 workflow.php 的主按鈕顏色 */
    background-color: #C8102E;
    color: white;
}
.btn-primary:hover {
    background-color: #a00d25;
}
.btn-secondary { /* 這是 workflow.php 的次按鈕顏色 */
    background-color: #6c757d;
    color: white;
}
.btn-secondary:hover {
    background-color: #5a6268;
}
/* 移除或覆蓋原 btn-success 的定義，改用 btn-primary */
/* .btn-success {
    background-color: #28a745;
    color: white;
}
.btn-success:hover {
    background-color: #218838;
} */

/* 圖示間距 */
.btn .bi { /* 統一使用 bi 類別 */
    margin-right: 0.5rem; /* 按照您提供的原始碼增大圖示間距 */
}
.card-title .bi { /* 卡片標題圖示使用 workflow.php 的顏色 */
    margin-right: 0.5rem;
    color: #4a5568; /* 從 workflow.php 複製過來的顏色 */
}

/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .content-card {
        padding: 20px;
        max-width: 100%; /* 小螢幕上卡片佔滿寬度 */
    }
    .content-card .card-title {
        font-size: 1.25rem;
    }
    .content-card .card-text {
        font-size: 1rem;
    }
    .btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>

<div class="guide-container mt-5 mb-5">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
    </div>

    <div class="content-card">
        <h3 class="card-title"><i class="bi bi-link-45deg me-2 text-primary"></i> MF2000 連線說明文件</h3>
        <p class="card-text">此文件將引導您如何設定與連線至 MF2000 系統。</p>
        <a href="https://drive.google.com/file/d/1IvG8pcqCZiZb5UxGkEsKhWkGvuf_GXdP/view?usp=drive_link"
           class="btn btn-primary btn-lg" target="_blank">
            <i class="bi bi-download"></i> 下載並閱讀說明
        </a>
    </div>
</div>

</body>
</html>