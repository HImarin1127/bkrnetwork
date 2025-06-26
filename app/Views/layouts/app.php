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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #C8102E 0%, #a00d26 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }

        .logo-image {
            height: 60px;
            width: auto;
            filter: brightness(1.1) contrast(1.1);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }

        .user-info, .auth-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
            font-weight: 500;
        }

        .admin-link, .logout-btn, .login-btn, .register-btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .admin-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-btn, .register-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .admin-link:hover, .logout-btn:hover, .login-btn:hover, .register-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        /* 導覽列 - 簡潔電腦版設計 */
        .main-nav {
            background: transparent;
            padding: 0 2rem 1rem;
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            list-style: none;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .nav-link .icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .nav-link .arrow {
            margin-left: 8px;
            font-size: 0.8rem;
        }

        /* 下拉選單 */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 250px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu li:last-child {
            border-bottom: none;
        }

        .dropdown-menu > li > a {
            display: block;
            padding: 12px 18px;
            color: #333;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .dropdown-menu > li > a:hover {
            background: #C8102E;
            color: white;
        }

        /* 子選單 */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu > a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.05) !important;
            font-weight: 600;
        }

        .dropdown-submenu > a:hover {
            background: #C8102E !important;
            color: white !important;
        }

        .submenu {
            position: absolute;
            left: 100%;
            top: 0;
            min-width: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dropdown-submenu:hover .submenu {
            opacity: 1;
            visibility: visible;
        }

        .submenu a {
            display: block;
            padding: 10px 18px;
            color: #333 !important;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            transition: background 0.2s ease;
        }

        .submenu a:hover {
            background: #C8102E !important;
            color: white !important;
        }

        .submenu li:last-child a {
            border-bottom: none;
        }

        /* 主要內容區域 */
        .main-layout {
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* Footer */
        .footer {
            background: #2d3748;
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer p {
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo $baseUrl; ?>/" class="logo-link">
                    <img src="<?php echo $baseUrl; ?>/assets/images/logo-horizontal.png" alt="讀書共和國出版集團" class="logo-image">
                    <h1 class="logo-text"><?php echo $appName; ?></h1>
                </a>
            </div>
            
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <span>歡迎，<?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['username']); ?></span>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo $baseUrl; ?>/admin" class="admin-link">管理後台</a>
                    <?php endif; ?>
                    <a href="<?php echo $baseUrl; ?>/logout" class="logout-btn">登出</a>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="<?php echo $baseUrl; ?>/login" class="login-btn">登入</a>
                    <a href="<?php echo $baseUrl; ?>/register" class="register-btn">註冊</a>
                </div>
            <?php endif; ?>
        </div>
        
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
                            <a href="#">郵務管理 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/mail/request">寄件登記</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/mail/import">寄件匯入</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/mail/outgoing-records">寄件記錄</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/mail/incoming-register">收件登記</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/mail/incoming-records">收件記錄</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/mail/postage">郵資查詢</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/personnel-onboard">到職申請</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/personnel-transfer">調職申請</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/personnel-resignation">離職申請</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/equipment-purchase">設備採購</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/equipment-disposal">設備報廢</a></li>
                        <li class="dropdown-submenu">
                            <a href="#">權限申請 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/forms/nas-permission">NAS權限</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/forms/system-permission">系統權限</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/forms/email-permission">信箱權限</a></li>
                            </ul>
                        </li>
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
                        <li><a href="<?php echo $baseUrl; ?>/guides/email">信箱設定</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/printer">印表機設定</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/nas">NAS使用</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/windows">Windows操作</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/mac">Mac操作</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/tax-exempt">免稅申請</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main class="main-layout">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2024 <?php echo $appName; ?>. 版權所有.</p>
            <p>為讀書共和國出版集團員工提供便利的內部服務平台</p>
        </div>
    </footer>
</body>
</html> 