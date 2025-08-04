<?php
// app/Views/guides/contacts.php

$pageTitle = "聯絡資訊"; // 頁面主標題
$pageSubtitle = "讀書共和國各部門聯絡方式與辦公資訊"; // 頁面副標題
$pageType = "guides"; // For sidebar highlighting

// 【已修正】定義 PDF 的安全存取路徑。
// 這個路徑會觸發 FileViewController，由後端 PHP 去讀取位於 /mnt/ 的實際檔案。
$pdfPath = '/bkrnetwork/view/extension-pdf';
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
    max-width: 100%;
    width: 100%;
    margin: 0 auto;
    padding: 0;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 0 10px;
}

.page-header h1 {
    margin-bottom: 10px;
    font-size: 2.5rem;
    color: #333;
}

.page-subtitle {
    font-size: 1.15rem;
    color: #6b7280;
    max-width: 700px;
    margin: 0 auto 30px auto;
}

.content-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 0px; 
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    text-align: center;
}
.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.content-card h2, .content-card h3 {
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
}
.content-card h2 i, .content-card h3 i {
    margin-right: 15px;
    font-size: 2rem;
    color: #C8102E;
}

.pdf-embed-container {
    position: relative;
    width: 100%;
    padding-bottom: 100%;
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

@media (max-width: 768px) {
    .guide-container {
        padding: 0;
        max-width: 100%;
    }
    .page-header {
        padding: 0 10px;
    }
    .page-header h1 {
        font-size: 2rem;
    }
    .page-subtitle {
        font-size: 1rem;
        margin-bottom: 20px;
    }
    .content-card {
        padding: 20px;
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