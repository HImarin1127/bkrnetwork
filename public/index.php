<?php
// public/index.php
require_once __DIR__ . '/../src/auth.php';

// 根據登入狀態導向相應頁面
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>