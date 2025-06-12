<?php
// src/auth.php

// 處理使用者認證（註冊、登入、登出）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

/**
 * 註冊新使用者
 *
 * @param string $username
 * @param string $password
 * @param string $fullname
 * @return array ['success'=>bool, 'message'=>string]
 */
function register($username, $password, $fullname) {
    $db = db();

    // 檢查帳號是否存在
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => '帳號已存在'];
    }
    $stmt->close();

    // 雜湊密碼
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 新增使用者
    $stmt = $db->prepare("INSERT INTO users (username, password_hash, fullname) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $hash, $fullname);
    if (!$stmt->execute()) {
        $stmt->close();
        return ['success' => false, 'message' => '系統錯誤，無法建立帳號'];
    }
    $stmt->close();

    return ['success' => true, 'message' => '註冊成功'];
}

/**
 * 使用者登入
 *
 * @param string $username
 * @param string $password
 * @return bool 成功回傳 true，失敗回傳 false
 */
function login($username, $password) {
    $db = db();
    $stmt = $db->prepare("SELECT id, password_hash, fullname FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $hash, $fullname);
    $found = false;
    if ($stmt->fetch()) {
        $found = true;
    }
    $stmt->close();

    if ($found && password_verify($password, $hash)) {
        // 設定 Session
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = $fullname;
        return true;
    }
    return false;
}

/**
 * 檢查是否已登入
 *
 * @return bool
 */
function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

/**
 * 使用者登出
 */
function logout() {
    session_unset();
    session_destroy();
}
?>