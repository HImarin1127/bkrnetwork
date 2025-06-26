<?php
// config/app.php
return [
    'name' => '讀書共和國員工服務網',
    'url' => 'http://localhost/bkrnetwork',
    'timezone' => 'Asia/Taipei',
    'session_name' => 'republic_session',
    
    // 公開頁面（不需要登入）
    'public_routes' => [
        '/',
        '/home',
        '/announcements',
        '/announcements/holidays',
        '/announcements/handbook',
        '/company',
        '/company/floor',
        '/company/contacts',
        '/company/nas',
        '/login',
        '/register',
        '/logout'
    ],
    
    // 管理員頁面
    'admin_routes' => [
        '/admin',
        '/admin/dashboard',
        '/admin/users',
        '/admin/announcements'
    ]
]; 