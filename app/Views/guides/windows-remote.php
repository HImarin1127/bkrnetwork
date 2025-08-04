<?php
// app/Views/guides/windows-remote.php

$pageTitle = "Windows/Mac遠端連線操作指引";
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

.page-subtitle {
    font-size: 1.1rem;
    color: #666;
}

.guide-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.content-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 30px;
}

.content-card h2 {
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.section-content p {
    margin-bottom: 20px;
    line-height: 1.6;
}

.section-content ul {
    list-style-type: none;
    padding: 0;
}

.section-content li {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.section-content ul li a {
    color: #C8102E;
    text-decoration: none;
    font-weight: 500;
}

.section-content li a {
    margin-left: 10px;
}

.section-content a:hover {
    text-decoration: underline;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #C8102E;
    color: white;
}

.btn-primary:hover {
    background-color: #a00d25;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
    </style>
</head>
<body>

<div class="guide-container">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
    </div>

    <div class="guide-content">
        <div class="content-card">
            <h2>重要資訊</h2>
            <div class="section-content">
                <p>請參考以下連結以取得詳細的 Windows/Mac 遠端工作連線操作手冊：</p>
                <a href="https://sites.google.com/view/bookrepvpntest/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-2"></i> 遠端工作連線操作手冊 (V20210707)
                </a>

                <p style="margin-top: 30px;">手冊內容包含：</p> <ul>
                    <li>
                        <i class="bi bi-check-circle me-2" style="color: #4CAF50;"></i> VPN 安裝
                    </li>
                    <li>
                        <i class="bi bi-check-circle me-2" style="color: #4CAF50;"></i>
                        VPN 連線
                    </li>
                    <li>
                        <i class="bi bi-check-circle me-2" style="color: #4CAF50;"></i>
                        遠端桌面連線
                    </li>
                </ul>
            </div>
        </div>
        </div>
</div>

</body>
</html>