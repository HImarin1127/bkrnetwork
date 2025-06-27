<?php
// config/app.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

/**
 * 應用程式核心設定檔案
 * 
 * 定義整個應用程式的基本設定和參數，包括：
 * - 應用程式基本資訊（名稱、URL、時區）
 * - Session 設定
 * - 路由權限控制設定
 * 
 * 此設定檔會被各個控制器和中介軟體載入使用
 */

return [
    // ========================================
    // 應用程式基本設定
    // ========================================
    
    'name' => '讀書共和國員工服務網',
    // 應用程式名稱，會顯示在頁面標題和導覽列
    
    'url' => 'http://localhost/bkrnetwork',
    // 應用程式的基礎 URL，用於產生絕對路徑連結
    
    'timezone' => 'Asia/Taipei',
    // 應用程式時區設定，影響所有日期時間的顯示和記錄
    
    'session_name' => 'republic_session',
    // Session 名稱，用於識別不同應用程式的 Session
    
    // ========================================
    // 路由權限設定
    // ========================================
    
    // 公開頁面（不需要登入）
    'public_routes' => [
        '/',                           // 首頁
        '/home',                       // 首頁（別名）
        '/announcements',              // 公告列表
        '/announcements/holidays',     // 假日資訊
        '/announcements/handbook',     // 員工手冊
        '/company',                    // 公司介紹
        '/company/floor',              // 樓層介紹
        '/company/contacts',           // 聯絡資訊
        '/company/nas',                // 網路存儲介紹
        '/login',                      // 登入頁面
        '/register',                   // 註冊頁面
        '/logout'                      // 登出處理
    ],
    
    // 管理員頁面（需要管理員權限）
    'admin_routes' => [
        '/admin',                      // 管理員控制台首頁
        '/admin/dashboard',            // 管理員儀表板
        '/admin/users',                // 使用者管理
        '/admin/announcements'         // 公告管理
    ]
    // 路由設定陣列結束
]; 
// 設定陣列結束 