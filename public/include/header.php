<?php
// public/include/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? '員工管理系統') ?></title>
    <!-- Bootstrap CSS (使用 CDN 引入) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome 圖示套件 (使用 CDN 引入) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <!-- 自訂樣式 CSS -->
    <link href="styles.css" rel="stylesheet" />
</head>
<body>