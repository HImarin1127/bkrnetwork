<?php
// routes/web.php

/**
 * 網站路由定義檔案
 * 
 * 定義所有 HTTP 路由與對應的控制器方法
 * 路由格式：'路由路徑' => ['控制器名稱', '方法名稱']
 * 
 * 路由分組說明：
 * 1. 公開路由：任何人都可以存取，無需登入
 * 2. 需要登入的路由：必須登入才能存取
 * 3. 管理員路由：需要管理員權限才能存取
 * 
 * 權限控制由 AuthMiddleware 負責處理
 */

return [
    // ========================================
    // 公開路由（不需要登入）
    // ========================================
    
    // 首頁路由
    '/' => ['HomeController', 'index'],
    '/home' => ['HomeController', 'index'],
    
    // 公告區路由 - 所有員工都可查看的資訊
    '/announcements' => ['HomeController', 'announcements'],
    '/announcements/holidays' => ['HomeController', 'holidays'],      // 假日行事曆
    '/announcements/handbook' => ['HomeController', 'handbook'],      // 員工手冊
    
    // 公司介紹路由 - 公司基本資訊
    '/company' => ['HomeController', 'company'],
    '/company/floor' => ['HomeController', 'companyFloor'],           // 樓層介紹
    '/company/contacts' => ['HomeController', 'companyContacts'],     // 聯絡資訊
    '/company/nas' => ['HomeController', 'companyNas'],               // 網路存儲資訊
    
    // 免稅申請流程
    '/guides/tax-exempt/process' => ['HomeController', 'taxExemptProcess'],   // 免稅流程
    
    // Windows 遠端連線
    '/guides/windows/remote' => ['HomeController', 'windowsRemote'],          // Windows 遠端連線

    // 印表機基本操作
    '/guides/printer/basic' => ['HomeController', 'printerBasic'],            // 印表機基本操作

    // 印表機疑難排解
    '/guides/printer/troubleshoot' => ['HomeController', 'printerTroubleshoot'], // 印表機疑難排解

    // 免稅系統操作說明
    '/guides/tax-exempt/system' => ['HomeController', 'taxExemptSystem'],     // 免稅系統

    // 電子郵件設定指引
    '/guides/email' => ['HomeController', 'email'],                           // 信箱設定

    // 認證路由 - 使用者登入、註冊、登出
    '/login' => ['AuthController', 'login'],
    '/register' => ['AuthController', 'register'],
    '/logout' => ['AuthController', 'logout'],
    '/ldap-test' => ['AuthController', 'ldapTest'],
    
    // ========================================
    // 需要登入的路由 - 核心業務功能
    // ========================================
    
    '/group-announcements' => ['GroupAnnouncementsController', 'sharedFiles'],
    '/group-announcements/download' => ['GroupAnnouncementsController', 'download'],

    // 郵務管理系統 - 企業郵件寄送管理
    '/mail' => ['MailController', 'request'],
    '/mail/request' => ['MailController', 'request'],                 // 寄件登記
    '/mail/import' => ['MailController', 'import'],                   // 批次匯入
    '/mail/records' => ['MailController', 'records'],                 // 寄件記錄
    '/mail/outgoing-records' => ['MailController', 'outgoingRecords'], // 寄件記錄（舊）
    '/mail/incoming-register' => ['MailController', 'incomingRegister'], // 收件登記
    '/mail/incoming-records' => ['MailController', 'incomingRecords'], // 收件記錄
    '/mail/postage' => ['MailController', 'postage'],                 // 郵資查詢
    '/mail/edit' => ['MailController', 'edit'],                       // 編輯記錄
    '/mail/delete' => ['MailController', 'delete'],                   // 刪除記錄
    
    // 行政表單系統 - 各種業務申請表單
    '/forms/personnel-onboard' => ['FormsController', 'personnelOnboard'],      // 人員到職
    '/forms/personnel-transfer' => ['FormsController', 'personnelTransfer'],    // 人員調動
    '/forms/personnel-resignation' => ['FormsController', 'personnelResignation'], // 離職程序
    '/forms/equipment-purchase' => ['FormsController', 'equipmentPurchase'],    // 設備採購
    '/forms/equipment-disposal' => ['FormsController', 'equipmentDisposal'],    // 設備報廢
    '/forms/mf2000' => ['FormsController', 'mf2000'],                           // MF2000 系統
    '/forms/nas' => ['FormsController', 'nas'],                                 // 網路存儲申請
    '/forms/email' => ['FormsController', 'email'],                             // 信箱申請
    '/forms/vpn' => ['FormsController', 'vpn'],                                 // VPN 申請
    '/forms/it-training' => ['FormsController', 'itTraining'],                  // 資訊教育訓練
    '/forms/qrcode' => ['FormsController', 'qrcode'],                           // QR Code 生成
    
    // 資源預約系統 - 會議室、設備預約
    '/booking/meeting-room' => ['BookingController', 'meetingRoom'],            // 會議室預約
    '/booking/equipment' => ['BookingController', 'equipment'],                 // 設備預約
    
    // 操作指引系統 - 各種系統使用說明
    // '/guides/windows/remote' => ['GuidesController', 'windowsRemote'],          // Windows 遠端連線 - 已移至公開路由
    '/guides/windows/audio' => ['GuidesController', 'windowsAudio'],            // Windows 音效設定
    // '/guides/printer/basic' => ['GuidesController', 'printerBasic'],            // 印表機基本操作 - 已移至公開路由
    // '/guides/printer/troubleshoot' => ['GuidesController', 'printerTroubleshoot'], // 印表機故障排除 - 已移至公開路由
    '/guides/mac/web-print' => ['GuidesController', 'macWebPrint'],             // Mac 網頁列印
    '/guides/mac/driver' => ['GuidesController', 'macDriver'],                  // Mac 驅動程式
    '/guides/pos' => ['GuidesController', 'pos'],                               // POS 系統
    '/guides/nas/password' => ['GuidesController', 'nasPassword'],              // NAS 密碼管理
    '/guides/nas/web-auth' => ['GuidesController', 'nasWebAuth'],               // NAS 網頁認證
    '/guides/mf2000/workflow' => ['GuidesController', 'mf2000Workflow'],      // MF2000 公文流程
    '/guides/mf2000/attendance' => ['GuidesController', 'mf2000Attendance'],  // MF2000 出缺勤管理
    '/guides/mf2000/connection' => ['GuidesController', 'mf2000Connection'],  // MF2000 連線說明
    // '/guides/tax-exempt/system' => ['GuidesController', 'taxExemptSystem'],     // 免稅系統 - 已移至公開路由
    
    // ========================================
    // 管理員路由（需要管理員權限）
    // ========================================
    
    // 管理員控制台
    '/admin' => ['AdminController', 'dashboard'],
    '/admin/dashboard' => ['AdminController', 'dashboard'],                     // 管理員儀表板
    
    // 使用者管理
    '/admin/users' => ['AdminController', 'users'],                             // 使用者列表
    '/admin/users/create' => ['AdminController', 'createUser'],                 // 新增使用者
    '/admin/users/edit' => ['AdminController', 'editUser'],                     // 編輯使用者
    
    // 公告管理
    '/admin/announcements' => ['AdminController', 'announcements'],             // 公告列表
    '/admin/announcements/create' => ['AdminController', 'createAnnouncement'], // 新增公告
    '/admin/announcements/edit' => ['AdminController', 'editAnnouncement'],     // 編輯公告
]; 