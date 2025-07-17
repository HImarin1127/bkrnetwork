<?php
// app/Views/guides/contacts.php

$pageTitle = "聯絡資訊"; // 頁面主標題
$pageSubtitle = "讀書共和國各部門聯絡方式與辦公資訊"; // 頁面副標題
$pageType = "guides"; // For sidebar highlighting

// 定義 PDF 檔案的路徑
$pdfPath = $baseUrl . '/extension/extension.pdf';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
/* 基礎容器和頁面標題樣式 */
.guide-container {
    max-width: 100%; /* 將最大寬度改為 90%，即左右各留 5% 空間 */
    /* 或者如果您希望完全佔滿寬度，可以設定 max-width: 100%; */
    width: 100%; /* 確保容器可以完全佔據可用寬度 */
    margin: 0 auto;
    padding: 0; /* 將內邊距設置為 0，讓內容盡可能貼近左右邊緣 */
}

/* 為了讓內容在 guide-container 內部有左右邊距，我們將 padding 移到 .content-card */

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 0 10px; /* 為 page-header 增加左右內邊距 */
}

.page-header h1 {
    margin-bottom: 10px;
    font-size: 2.5rem; /* 大標題字體 */
    color: #333; /* 統一顏色 */
}

.page-subtitle {
    font-size: 1.15rem; /* 副標題字體 */
    color: #6b7280; /* 副標題顏色 */
    max-width: 700px; /* 副標題保持適中寬度 */
    margin: 0 auto 30px auto;
}

/* 內容卡片樣式 (與其他頁面統一) */
.content-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    /* 這裡的 padding 現在是卡片內部的邊距 */
    padding: 0px; 
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    text-align: center; /* 使卡片內容居中 */
    /* 移除 max-width，讓它在 guide-container 內盡可能寬 */
    /* margin 也不需要了，因為 guide-container 已經處理了 */
}
.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.content-card h2, .content-card h3 { /* 卡片內標題 */
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
    font-size: 1.8rem; /* 標題字體大小 */
    font-weight: 600;
    color: #333;
    display: flex; /* 讓圖示和文字對齊 */
    align-items: center;
    justify-content: center; /* 標題居中 */
}
.content-card h2 i, .content-card h3 i {
    margin-right: 15px;
    font-size: 2rem;
    color: #C8102E; /* 主題色 */
}

/* PDF 嵌入容器樣式 */
.pdf-embed-container {
    position: relative;
    width: 100%;
    padding-bottom: 100%; /* 調整為 85% (您可以試著調整這個值) */
    height: 0; 
    overflow: hidden;
    margin-top: 25px; 
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.pdf-embed-container iframe,
.pdf-embed-container embed,
.pdf-embed-container object {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

/* 備用下載按鈕樣式 */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px 30px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
    font-size: 1.2rem;
    white-space: nowrap;
    margin-top: 25px;
}

.btn-primary {
    background-color: #C8102E;
    color: white;
}
.btn-primary:hover {
    background-color: #a00d25;
}

.btn .bi {
    margin-right: 0.5rem;
}

/* 響應式調整 */
@media (max-width: 768px) {
    .guide-container {
        padding: 0; /* 手機上移除外層 padding */
        max-width: 100%; /* 手機上仍然是 100% 寬度 */
    }
    .page-header {
        padding: 0 10px; /* 手機上頁面標題的內邊距 */
    }
    .page-header h1 {
        font-size: 2rem;
    }
    .page-subtitle {
        font-size: 1rem;
        margin-bottom: 20px;
    }
    .content-card {
        padding: 20px; /* 手機上卡片的內邊距 */
    }
    .content-card h2, .content-card h3 {
        font-size: 1.5rem;
        flex-direction: column; 
        gap: 10px;
    }
    .content-card h2 i, .content-card h3 i {
        margin-right: 0;
    }
    .pdf-embed-container {
        padding-bottom: 120%;
    }
    .btn {
        width: 100%;
        font-size: 1.1rem;
        padding: 12px 20px;
    }
}
    </style>
</head>
<body>

<div class="guide-container mt-5 mb-5">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
        <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
    </div>

    <div class="content-card">
        <h2><i class="bi bi-file-earmark-pdf-fill"></i> 社內分機表</h2>
        <div class="section-content">
            <p>您可以在此頁面直接瀏覽讀書共和國社內分機表。如需下載，請點擊下方按鈕。</p>

            <div class="pdf-embed-container">
                <iframe src="<?php echo $pdfPath; ?>" title="讀書共和國社內分機表" frameborder="0">
                    此瀏覽器不支援 PDF 嵌入。請點擊下方按鈕下載。
                </iframe>
            </div>

            <a href="<?php echo $pdfPath; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                <i class="bi bi-download me-2"></i> 下載分機表 PDF
            </a>
        </div>
    </div>
</div>

</body>
</html>