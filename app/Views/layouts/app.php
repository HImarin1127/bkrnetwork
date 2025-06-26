<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appName; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Microsoft JhengHei', 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #2d3748;
            position: relative;
        }

        /* 頂部導覽區域 */
        .top-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .top-bar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 2rem;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .user-info span {
            font-weight: 500;
        }

        .admin-link, .logout-btn, .login-btn, .register-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .admin-link {
            background: #f59e0b;
            color: white;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
        }

        .login-btn, .register-btn {
            background: #3b82f6;
            color: white;
        }

        .admin-link:hover {
            background: #d97706;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .login-btn:hover, .register-btn:hover {
            background: #2563eb;
        }

        /* 主要header區域 - 純紅色背景設計 */
        .main-header {
            background: #C8102E;
            color: white;
            padding: 2rem 0 1rem;
            position: relative;
            overflow: visible;
            border-bottom: 3px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
            opacity: 0.8;
            z-index: 1;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1002;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            text-decoration: none;
            color: white;
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: translateY(-2px);
        }

        .logo-container:hover .logo-image {
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            background: rgba(244, 244, 244, 1);
            transform: scale(1.05);
        }

        .logo-image {
            height: 80px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
            background: rgba(244, 244, 244, 0.95);
            padding: 8px;
            transition: all 0.3s ease;
        }

        .logo-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: -0.02em;
        }

        /* 主導覽選單 */
        .main-nav {
            position: relative;
            z-index: 1001;
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }



        .nav-item {
            position: relative;
        }

        .nav-item.dropdown {
            position: relative;
            z-index: 1002;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(244, 244, 244, 0.15);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(244, 244, 244, 0.2);
            backdrop-filter: blur(10px);
            gap: 0.75rem;
        }

        .nav-link:hover {
            background: rgba(244, 244, 244, 0.25);
            border-color: rgba(244, 244, 244, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .nav-link .icon {
            font-size: 1.2rem;
        }

        .nav-link .arrow {
            margin-left: 0.5rem;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .nav-item.dropdown:hover .arrow {
            transform: rotate(180deg);
        }

        /* 下拉選單樣式 */
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: rgba(68, 68, 68, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.5rem 0;
            min-width: 250px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            z-index: 1003;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            list-style: none;
            margin: 0;
        }

        .nav-item.dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            margin: 0;
            list-style: none;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.75rem 1.25rem;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .dropdown-menu a:hover {
            background: linear-gradient(135deg, rgba(200, 16, 46, 0.2) 0%, rgba(102, 102, 102, 0.1) 100%);
            color: white;
            border-left-color: #C8102E;
            padding-left: 1.5rem;
        }



        /* 子選單樣式 */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu > a {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
        }

        .submenu-arrow {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .dropdown-submenu:hover .submenu-arrow {
            transform: rotate(90deg);
        }

        .submenu {
            position: absolute;
            left: 100%;
            top: 0;
            background: rgba(85, 85, 85, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
            min-width: 200px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            z-index: 1004;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            list-style: none;
            margin: 0;
        }

        .dropdown-submenu:hover .submenu {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        /* 修復懸停延遲問題 */
        .nav-item.dropdown {
            position: relative;
        }

        .nav-item.dropdown:hover .dropdown-menu,
        .dropdown-menu:hover {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .submenu li {
            margin: 0;
            list-style: none;
        }

        .submenu a {
            padding: 0.6rem 1rem !important;
            font-size: 0.9rem !important;
        }

        /* 主要內容區域 */
        .main-layout {
            flex: 1;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 200px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* 頁面標題樣式 */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(200,16,46,0.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 0.5rem 0;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin: 0;
        }

        /* 內容卡片樣式 */
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border: 1px solid rgba(200,16,46,0.1);
            transition: all 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        /* 表單樣式統一 */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2d3748;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid rgba(200,16,46,0.15);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #C8102E;
            box-shadow: 0 0 0 3px rgba(200,16,46,0.1);
            background: rgba(255,255,255,0.95);
        }

        .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid rgba(200,16,46,0.15);
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-textarea {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid rgba(200,16,46,0.15);
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            resize: vertical;
            min-height: 120px;
        }

        /* 按鈕組合 */
        .btn-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 2rem;
        }

        /* 表格美化 */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
        }

        .data-table th {
            background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
            color: white;
            padding: 1.2rem 1rem;
            font-weight: 600;
            text-align: left;
            font-size: 0.95rem;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(200,16,46,0.1);
            transition: background-color 0.2s ease;
        }

        .data-table tr:hover {
            background-color: rgba(200,16,46,0.05);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            position: relative;
            z-index: 1;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #666666 0%, #444444 100%);
            color: #e2e8f0;
            text-align: center;
            padding: 2rem 1rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            .top-bar-container {
                padding: 0 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .header-content {
                padding: 0 1rem;
            }

            .logo-section {
                margin-bottom: 1.5rem;
            }

            .logo-text {
                font-size: 1.8rem;
            }

            .logo-image {
                height: 60px;
            }

            .nav-menu {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }

            .nav-link {
                padding: 0.75rem 1.25rem;
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .dropdown-menu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                margin-top: 0.5rem;
                background: rgba(68, 68, 68, 0.9);
                display: none;
            }

            .dropdown-menu.show {
                display: block;
            }

            .submenu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                margin-left: 1rem;
                background: rgba(85, 85, 85, 0.9);
                display: none;
            }

            .submenu.show {
                display: block;
            }

            .container {
                padding: 1.5rem;
                margin: 0 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .main-header {
                padding: 1.5rem 0 1rem;
            }

            .logo-text {
                font-size: 1.5rem;
            }

            .logo-image {
                height: 50px;
            }

            .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }

            .container {
                padding: 1rem;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- 頂部資訊欄 -->
    <div class="top-bar">
        <div class="top-bar-container">
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <span>歡迎，<?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['username']); ?></span>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo $baseUrl; ?>/admin" class="admin-link">管理後台</a>
                    <?php endif; ?>
                    <a href="<?php echo $baseUrl; ?>/logout" class="logout-btn">登出</a>
                </div>
            <?php else: ?>
                <div class="user-info">
                    <a href="<?php echo $baseUrl; ?>/login" class="login-btn">登入</a>
                    <a href="<?php echo $baseUrl; ?>/register" class="register-btn">註冊</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 主要header區域 -->
    <header class="main-header">
        <div class="header-content">
            <!-- Logo區域 -->
            <div class="logo-section">
                <a href="<?php echo $baseUrl; ?>/" class="logo-container">
                    <img src="<?php echo $baseUrl; ?>/assets/images/logo-horizontal.png" alt="讀書共和國出版集團" class="logo-image">
                    <h1 class="logo-text"><?php echo $appName; ?></h1>
                </a>
            </div>
            
            <!-- 主導覽選單 -->
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo $baseUrl; ?>/" class="nav-link">
                            <span class="icon">🏠</span>
                            <span>首頁</span>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="<?php echo $baseUrl; ?>/announcements" class="nav-link">
                            <span class="icon">📢</span>
                            <span>公告區</span>
                            <span class="arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $baseUrl; ?>/announcements">最新公告</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/announcements/holidays">假日資訊</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/announcements/handbook">員工手冊</a></li>
                        </ul>
                    </li>
                    
                    <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            <span class="icon">📝</span>
                            <span>表單申請</span>
                            <span class="arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu">
                                <a href="#">
                                    郵務管理
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/mail/request">寄件登記</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/mail/import">寄件匯入</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/mail/outgoing-records">寄件記錄</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/mail/incoming-register">收件登記</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/mail/incoming-records">收件記錄</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/mail/postage">郵資查詢</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    人員異動
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/forms/personnel-onboard">人員到職</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/personnel-transfer">人員調動</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/personnel-resignation">離職程序</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    設備管理
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/forms/equipment-purchase">設備採購</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/equipment-disposal">設備報廢</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    系統權限申請
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/forms/mf2000">MF2000系統</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/nas">NAS公區權限</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/email">公司EMAIL</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/forms/vpn">VPN權限申請</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo $baseUrl; ?>/forms/it-training">資訊部基礎教育</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/forms/qrcode">QRcode申請</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            <span class="icon">📅</span>
                            <span>資源預約</span>
                            <span class="arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $baseUrl; ?>/booking/meeting-room">會議室預約</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/booking/equipment">設備預約</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/booking/vehicle">車輛預約</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            <span class="icon">🏢</span>
                            <span>公司資訊</span>
                            <span class="arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $baseUrl; ?>/company">公司簡介</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/company/contacts">聯絡資訊</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/company/floor">樓層資訊</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/company/nas">NAS資源</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            <span class="icon">🔧</span>
                            <span>操作指引</span>
                            <span class="arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu">
                                <a href="#">
                                    Windows相關
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/guides/windows/remote">Windows遠端連線</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/guides/windows/audio">Windows音訊更新</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    印表機使用
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/guides/printer/basic">基本操作說明</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/guides/printer/troubleshoot">印表機疑難處理</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    MAC影印
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/guides/mac/web-print">WEB列印網頁</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/guides/mac/driver">MAC影印機驅動安裝</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo $baseUrl; ?>/guides/pos">POS收銀機操作手冊</a></li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    NAS公區相關
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/guides/nas/password">忘記密碼</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/guides/nas/web-auth">網頁版使用與二次驗證</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo $baseUrl; ?>/guides/email">電子郵件完整設定指引</a></li>
                            <li class="dropdown-submenu">
                                <a href="#">
                                    文化部免稅相關
                                    <span class="submenu-arrow">▶</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="<?php echo $baseUrl; ?>/guides/tax-exempt/process">免稅申請流程</a></li>
                                    <li><a href="<?php echo $baseUrl; ?>/guides/tax-exempt/system">免稅系統操作說明</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- 主要內容區域 -->
    <main class="main-layout">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2024 <?php echo $appName; ?>. 版權所有.</p>
            <p>為讀書共和國出版集團員工提供便利的內部服務平台</p>
        </div>
    </footer>

    <script>
        // 簡單的移動設備下拉選單處理
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                const dropdowns = document.querySelectorAll('.nav-item.dropdown');
                
                dropdowns.forEach(dropdown => {
                    const link = dropdown.querySelector('.nav-link');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    
                    if (link && menu) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            menu.classList.toggle('show');
                        });
                    }
                });

                const submenus = document.querySelectorAll('.dropdown-submenu');
                submenus.forEach(submenu => {
                    const link = submenu.querySelector('a');
                    const menu = submenu.querySelector('.submenu');
                    
                    if (link && menu) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            menu.classList.toggle('show');
                        });
                    }
                });
            }
        });
    </script>
</body>
</html> 