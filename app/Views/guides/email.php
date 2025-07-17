<?php
// app/Views/guides/email.php

$pageTitle = "電子郵件完整設定指引";
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
    border: none; /* 移除預設邊框 */
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
    font-size: 1.45rem; /* 使用您先前指定的字體大小 */
    font-weight: 600; /* 增加字重 */
    color: #333; /* 設定一個深色文字 */
}

.section-content p { /* 段落文字樣式 */
    margin-bottom: 20px;
    line-height: 1.6;
    font-size: 1.1rem; /* 與內容文字大小統一 */
    color: #4a5568; /* 統一文字顏色 */
}

.btn { /* 自定義按鈕樣式 */
    display: inline-flex;
    align-items: center;
    justify-content: center; /* 按鈕文字和圖示居中 */
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
    font-size: 1rem;
    width: auto; /* 預設不佔滿寬度 */
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
    }
    .content-card h2, .content-card h3 {
        font-size: 1.25rem;
    }
    .section-content p {
        font-size: 1rem;
    }
    .btn {
        width: 100%; /* 手機上按鈕佔滿寬度 */
        font-size: 0.95rem;
        padding: 12px 20px;
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
            <h3><i class="bi bi-mailbox2 me-2 text-primary"></i> Webmail 入口</h3>
            <div class="section-content">
                <p>您可以透過以下網址登入公司的網頁版電子郵件系統：</p>
                <a href="http://mail.bookrep.com.tw"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-primary">
                    <i class="bi bi-box-arrow-up-right me-2"></i> 前往 mail.bookrep.com.tw
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-key-fill me-2 text-primary"></i> 變更密碼及自動回信教學</h3>
            <div class="section-content">
                <p>如果您需要變更信箱密碼，或設定休假時的自動回覆，請參考以下教學文件：</p>
                <a href="<?php echo BASE_URL; ?>assets/files/email/10.公司信箱變更密碼及自動回信教學.pdf"
                   target="_blank"
                   class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> 查看教學文件
                </a>
            </div>
        </div>

        <div class="content-card">
            <h3><i class="bi bi-google me-2 text-primary"></i> 更改由 Gmail 收發公司郵件設定</h3>
            <div class="section-content">
                <p>如果您希望透過 Gmail 來收發公司的電子郵件，請參考以下由資訊部提供的完整設定教學：</p>
                <a href="https://sites.google.com/view/bkrep-g/gmail%E8%A8%AD%E5%AE%9A%E5%85%AC%E5%8F%B8%E4%BF%A1"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-primary">
                    <i class="bi bi-book me-2"></i> 前往 Gmail 設定教學
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>