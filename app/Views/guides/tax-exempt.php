<?php
// app/Views/guides/tax-exempt.php

$pageTitle = "出版品免稅申請流程";
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

/* 針對圖片連結的樣式，使其更具互動性 */
.clickable-image {
    display: block;
    text-decoration: none;
    line-height: 0; /* 消除圖片下方可能的多餘空間 */
    border-radius: 8px;
    overflow: hidden; /* 確保圖片圓角 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.clickable-image:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.clickable-image img {
    max-width: 100%;
    height: auto;
    display: block;
    border-radius: 8px; /* 圖片也要有圓角 */
}

/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .content-card {
        padding: 20px;
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
            <h3><i class="bi bi-info-circle-fill me-2 text-primary"></i> 申請說明</h3>
            <div class="section-content">
                <p>文化部免稅帳號管理權歸屬於集團旗下，必須在國圖申請完成出版成立後，文化部才會接收到國圖的資料，資訊部才能進行免稅帳號歸屬的動作。各出版單位帳號由集團統一開立配給。</p>
                <p style="margin-bottom: 15px;">請提供以下資料：</p>
                <ul>
                    【品牌免稅管理 email 帳號】<br>
                    【品牌帳號管理員姓名】<br>
                    【出版證明文件】（可以提供書籍版權頁或是與讀書共和國有關聯的證明資料）
                </ul>
            </div>
        </div>

        <div class="content-card text-center"> <h3><i class="bi bi-diagram-3-fill me-2 text-primary"></i> 文化部出版品免稅申請流程圖</h3>
            <div class="section-content">
                <a href="<?php echo BASE_URL; ?>assets/images/tax_exemption_flowchart.png"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="clickable-image">
                    <img src="<?php echo BASE_URL; ?>assets/images/tax_exemption_flowchart.png" class="img-fluid" alt="出版品免稅申請流程圖">
                </a>
                <p style="margin-top: 20px; margin-bottom: 0;">點擊圖片可放大查看。</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
