<?php
// app/Views/guides/tax-exempt-system.php

$pageTitle = "文化部免稅系統操作說明";
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
    /* 調整 guide-container 內的基礎字體大小 */
    font-size: 1.1rem; /* 稍作調整，與您的 1.18rem 接近，但更常用 */
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

/* 針對 content-card 內標題和文字的字體大小調整 */
.content-card h2,
.content-card h3 {
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
    font-size: 1.45rem; /* 採用您指定的字體大小 */
}
.content-card p,
.content-card li {
    font-size: 1.18rem; /* 採用您指定的字體大小 */
    line-height: 1.6; /* 確保行高良好 */
}
.content-card ul {
    list-style-type: none;
    padding-left: 0;
}


.section-content p { /* 段落文字樣式，保留以防特定需求 */
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

/* 針對圖片連結的樣式，使其更具互動性 */
.clickable-image {
    display: block;
    text-decoration: none;
    line-height: 0; /* 消除圖片下方可能的多餘空間 */
    border-radius: 8px;
    overflow: hidden; /* 確保圖片圓角 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    margin: 0 auto; /* 圖片居中 */
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
/* 圖片下方說明文字 */
.image-caption {
    color:#666;
    font-size: 1rem; /* 調整為更適中的大小 */
    margin-top:0.5rem;
}


/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .content-card {
        padding: 20px;
    }
    .content-card h2,
    .content-card h3 {
        font-size: 1.3rem; /* 手機上標題稍微縮小 */
    }
    .content-card p,
    .content-card li {
        font-size: 1rem; /* 手機上內容文字稍微縮小 */
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
            <h3><i class="bi bi-person-fill-lock me-2 text-primary"></i> 操作說明與登入資訊</h3>
            <div class="section-content">
                <p>登入【文化部免稅平台】帳號管理方法如下說明：</p>
                <ul>
                    <li><i class="bi bi-check-circle me-2 text-success"></i> 文化部免稅系統以出版品牌為單位，帳號可看見該出版品牌所有書目申請狀況。</li>
                    <li><i class="bi bi-check-circle me-2 text-success"></i> 集團旗下每一個出版品牌，將發給一個帳號，預設將以出版單位最高主管之電子郵件為帳號ID，並由各出版單位自行保管帳號、密碼，隨時上線申請、查核圖書免稅狀態。</li>
                </ul>
                <p style="margin-top: 30px;"><strong>登入方法：</strong></p>
                <ul>
                    <li><i class="bi bi-link-45deg me-2 text-info"></i> 登入網址：<a href="https://tax.moc.gov.tw/book-apply/login.jsp" target="_blank">https://tax.moc.gov.tw/book-apply/login.jsp</a></li>
                    <li><i class="bi bi-journal-text me-2 text-info"></i> 單位統一編號：53226822</li>
                    <li><i class="bi bi-envelope-fill me-2 text-info"></i> 註冊Email：貴單位帳號管理員之信箱</li>
                    <li><i class="bi bi-key-fill me-2 text-info"></i> 密碼：貴單位自行設定的密碼</li>
                </ul>
                <p style="font-size: 1rem; color: #666; margin-top: 20px; margin-bottom: 0;">（忘記密碼需要聯繫資訊部，後續系統會寄送密碼變更的信給貴單位帳號管理員的信箱）</p>
                <div class="text-center mt-4">
                    <a href="<?php echo BASE_URL; ?>assets/images/文化部登入.png"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="clickable-image">
                        <img src="<?php echo BASE_URL; ?>assets/images/文化部登入.png" alt="文化部免稅系統登入畫面" class="img-fluid">
                    </a>
                    <div class="image-caption">文化部免稅系統登入畫面範例</div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-file-earmark-bar-graph-fill me-2 text-primary"></i> 文化部圖書免稅系統操作總說明</h3>
            <div class="section-content">
                <p>此為免稅系統的整體操作總覽說明。</p>
                <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部圖書免稅系統操作說明.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看總說明
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-pencil-square me-2 text-primary"></i> 單筆 EAN 指對教學</h3>
            <div class="section-content">
                <p>此文件說明如何在免稅系統中進行單筆 EAN 的指對操作。</p>
                <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_單筆EAN指對.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看單筆操作教學
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-journals me-2 text-primary"></i> 批次 EAN 指對教學</h3>
            <div class="section-content">
                <p>此文件說明如何在免稅系統中進行批次的 EAN 指對操作。</p>
                <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_批次EAN指對.pdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看批次操作教學
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>