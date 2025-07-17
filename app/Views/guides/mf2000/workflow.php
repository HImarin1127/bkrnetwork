<?php
// app/Views/guides/mf2000/workflow.php

$pageTitle = "MF2000 公文";
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

/* 卡片容器樣式 */
.content-grid { /* 將 card-deck 更名為 content-grid 以與其他頁面命名風格統一 */
    display: flex;
    flex-wrap: wrap;
    gap: 25px; /* 使用與 content-card 相同的 gap */
    justify-content: center; /* 使卡片在沒有滿行時也能居中 */
}

/* 單一卡片樣式 */
.content-card { /* 將 card 更名為 content-card，並整合您提供的 card 樣式 */
    flex: 1 1 320px; /* 調整 flex-basis，使其在大螢幕上每行約顯示2-3個，小螢幕上自動換行 */
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 30px; /* 使用與 content-card 一致的 padding */
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none; /* 移除您的 card 預設 border，讓 box-shadow 呈現 */
}
.content-card:hover { /* 整合您提供的 card:hover 樣式 */
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

/* 卡片內容樣式 */
.card-body { /* 保持原有的 card-body 樣式，但移除多餘的 padding 定義，由 content-card 控制 */
    padding: 0; /* 讓 content-card 的 padding 生效 */
    display: flex; /* 讓內容垂直居中對齊 */
    flex-direction: column;
    height: 100%; /* 確保卡片高度一致 */
}
.card-body .card-title { /* 整合您提供的 card-title 樣式 */
    margin-bottom: 1rem;
    font-size: 1.45rem; /* 使用您先前指定的 h3/h2 字體大小 */
    font-weight: 600; /* 增加字重 */
    color: #333; /* 設定一個深色文字 */
    flex-grow: 1; /* 讓標題區塊盡量佔據空間 */
}
.card-body .card-text { /* 整合您提供的 card-text 樣式 */
    color: #718096;
    margin-bottom: 1.5rem;
    font-size: 1.1rem; /* 與內容文字大小統一 */
    line-height: 1.6;
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
    width: 100%; /* 讓按鈕佔滿卡片寬度 */
    margin-top: auto; /* 將按鈕推到底部 */
}
.btn-primary {
    background-color: #C8102E;
    color: white;
}
.btn-primary:hover {
    background-color: #a00d25;
}
.btn-secondary { /* 來自您提供的 MF2000 電子公文網址卡片 */
    background-color: #6c757d;
    color: white;
}
.btn-secondary:hover {
    background-color: #5a6268;
}

/* 圖示間距 */
.btn .bi { /* 統一使用 bi 類別 */
    margin-right: 0.3rem;
}
.card-title .bi { /* 卡片標題圖示 */
    margin-right: 0.5rem;
    color: #4a5568;
}

/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .content-card {
        padding: 20px;
        flex: 1 1 100%; /* 小螢幕上卡片佔滿寬度 */
    }
    .card-body .card-title {
        font-size: 1.25rem;
    }
    .card-body .card-text {
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

    <div class="content-grid"> <div class="content-card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-file-earmark-check-fill me-2 text-primary"></i> MF2000 公文簽核
                </h5>
                <p class="card-text">MF2000 公文簽核流程與操作說明文件。</p>
                <a href="https://drive.google.com/file/d/1ag9s8c_zjk34i5MIUq0FLl_FpdEhuQDL/view?usp=drive_link"
                   class="btn btn-primary" target="_blank">
                    <i class="bi bi-book-fill"></i> 閱讀文件
                </a>
            </div>
        </div>

        <div class="content-card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-box-arrow-in-right me-2 text-info"></i> MF2000 電子公文網址
                </h5>
                <p class="card-text">點擊此處直接前往 MF2000 電子公文系統登入頁面。</p>
                <a href="https://eflow.bookrep.com.tw/docm"
                   class="btn btn-secondary" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> 前往系統
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>