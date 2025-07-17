<?php
// app/Views/guides/pos.php

$pageTitle = "崧月 POS 收銀機操作手冊";
// $pageType = "guides";  For sidebar highlighting (如果需要，可以取消註解)
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
    max-width: 900px; /* 主要內容容器寬度 */
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

/* 單一卡片樣式 (整合您提供的 .card 樣式到 .content-card) */
.content-card { /* 使用 content-card 以保持命名統一 */
    background-color: #fff;
    border-radius: 8px; /* 您提供的 border-radius */
    box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* 您提供的 box-shadow */
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* 您提供的 transition */
    border: none; /* 移除預設邊框 */
    padding: 30px; /* 統一內邊距 */
    max-width: 700px; /* 限制卡片最大寬度 */
    margin: auto; /* 居中卡片 */
    /* 移除 text-align: center; 以使內容文字靠左 */
}
.content-card:hover { /* 您提供的 card:hover 樣式 */
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

/* 卡片內標題 */
.content-card .card-title { /* 使用更統一的標題樣式 */
    font-size: 2.2rem; /* 較大的標題字體 */
    font-weight: bold;
    letter-spacing: 1px;
    color: #2c3e50;
    margin-bottom: 1.5rem; /* 與下方的間距 */
    text-align: left; /* 確保卡片標題靠左 */
}
.content-card .card-title i {
    margin-right: 15px; /* 調整圖示與文字間距 */
}


/* 內容分隔線 */
.content-card hr {
    margin-bottom: 2rem;
    border: 0;
    border-top: 1px solid #eee; /* 輕微的灰色分隔線 */
}

/* 手冊簡介標題 */
.content-card h4 {
    font-weight: 600;
    color: #007bff; /* 使用您提供的藍色 */
    margin-bottom: 1rem; /* 與下方段落的間距 */
    text-align: left; /* 確保這個 h4 靠左 */
}

/* 內容段落 */
.content-card p {
    font-size: 1.1rem;
    color: #444;
    line-height: 1.6;
    margin-bottom: 2rem; /* 按鈕上方留更多空間 */
    text-align: left; /* 確保 p 標籤靠左 */
}

/* 按鈕樣式 (從您之前提供的 CSS 參考而來) */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center; /* 按鈕文字和圖示居中 */
    gap: 8px;
    padding: 0.75rem 2.5rem; /* 您提供的 padding */
    font-size: 1.2rem; /* 您提供的 font-size */
    text-decoration: none;
    border-radius: 5px; /* 統一圓角 */
    transition: all 0.2s;
    white-space: nowrap; /* 防止文字換行 */
}

.btn-primary { /* 主按鈕顏色 */
    background-color: #C8102E;
    color: white;
}
.btn-primary:hover {
    background-color: #a00d25;
}

/* 圖示間距 */
.btn .bi {
    margin-right: 0.5rem;
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
        font-size: 1.8rem;
    }
    .content-card h4 {
        font-size: 1.2rem;
    }
    .content-card p {
        font-size: 1rem;
    }
    .btn {
        width: 100%; /* 手機上按鈕佔滿寬度 */
        font-size: 1.1rem;
        padding: 0.7rem 2rem;
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
        <h2 class="card-title">
            <i class="bi bi-cash-register me-3" style="font-size: 2.5rem; color: #3498db;"></i>
            崧月 POS 收銀機操作手冊
        </h2>
        <hr>

        <div class="mb-4">
            <h4><i class="bi bi-info-circle-fill me-2" style="color: #007bff;"></i> 手冊簡介</h4>
            <p>
                本手冊詳細說明 POS 收銀機的基本操作流程，包括開機、結帳、日結、常見問題排除等內容。<br>
                請點擊下方按鈕下載或線上檢視完整 PDF 文件，若有操作疑問，歡迎聯絡資訊部門協助。
            </p>
        </div>

        <div class="text-center"> <a href="<?php echo $baseUrl; ?>/assets/files/pos/收銀機操作手冊.pdf"
               target="_blank"
               class="btn btn-primary">
                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> 下載或線上檢視 PDF
            </a>
        </div>
    </div>
</div>

</body>
</html>