<?php
// public/register.php
require_once __DIR__ . '/../src/auth.php';
// 直接導向登入頁（顯示註冊表單）
header('Location: login.php?register=1');
exit;
?>