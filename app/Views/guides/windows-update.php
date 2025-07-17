<?php
// app/Views/guides/windows-update.php

$pageTitle = "取消 Windows 自動更新"; // 定義頁面標題
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
.guide-container { /* 使用您之前提供的 guide-container，保持頁面寬度居中 */
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.page-header { /* 使用您之前提供的 page-header */
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 { /* 使用您之前提供的 page-header h1 */
    margin-bottom: 10px;
}

.card {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    background-color: #fff; /* 確保卡片背景為白色 */
    padding: 0; /* 重置卡片預設 padding，讓 card-body 控制 */
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}
.card-body {
    padding: 2.5rem;
}
.btn { /* 基礎按鈕樣式，從您之前提供的 CSS 參考而來 */
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
    font-size: 1rem; /* 確保按鈕文字大小適中 */
}
.btn-primary { /* 按鈕主色，從您之前提供的 CSS 參考而來 */
    background-color: #C8102E;
    color: white;
}
.btn-primary:hover { /* 按鈕 hover 效果 */
    background-color: #a00d25;
}

/* 確保圖示與文字有間距 */
.btn .fas, .btn .bi {
    margin-right: 0.5rem;
}

/* 響應式調整 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    .card-body {
        padding: 1.5rem;
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

    <div class="card" style="max-width: 600px; margin-left: auto; margin-right: auto;">
        <div class="card-body text-center">
            <i class="fab fa-windows fa-3x mb-3" style="color: #0078d4;"></i>
            
            <h3 class="card-title" style="font-size: 1.5rem; margin-bottom: 1rem;">取消 Windows 自動更新操作手冊</h3>
            
            <p class="card-text mb-4">點擊下方按鈕，開啟操作說明文件。</p>
            
            <a href="https://drive.google.com/file/d/1hU_VjpGYNR1XOViC8V_hEigm2FJEC7sm/view?usp=sharing"
               class="btn btn-primary btn-lg" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> 開啟文件
            </a>
        </div>
    </div>
</div>

</body>
</html>