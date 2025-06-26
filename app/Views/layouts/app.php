<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appName; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/styles.css">
    <style>
        /* 基本樣式重置 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #2d3748;
            line-height: 1.6;
        }

        /* 頭部樣式 */
        .header {
            background: linear-gradient(135deg, #C8102E 0%, #a00d26 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Logo */
        .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .logo-image {
            height: 45px;
            margin-right: 12px;
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 600;
        }

        /* 用戶資訊 */
        .user-info, .auth-links {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .admin-link, .logout-btn, .login-btn, .register-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: background 0.3s ease;
        }

        .admin-link:hover, .logout-btn:hover, .login-btn:hover, .register-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* 手機版選單按鈕 */
        .mobile-menu-toggle,
        .mobile-close {
            display: none;
        }

        /* 導覽選單 */
        .main-nav {
            background: linear-gradient(135deg, #C8102E 0%, #a00d26 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 0 2rem;
            margin: 0;
            list-style: none;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link .icon {
            font-size: 1.1rem;
        }

        .nav-link .arrow {
            font-size: 0.8rem;
            margin-left: 4px;
        }

        /* 下拉選單 */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
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

        /* 修正：只有在父項目沒有被 hover 的第三階層時才應用特殊樣式 */
        .dropdown-submenu:not(:hover) > a:hover {
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

        /* 遮罩層 */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* 手機版樣式 */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .header-container {
                padding: 1rem;
                position: relative;
            }

            .logo-text {
                display: none;
            }

            .user-info span {
                display: none;
            }

            .main-nav {
                position: fixed;
                top: 0;
                left: -100%;
                width: 300px;
                height: 100vh;
                background: linear-gradient(135deg, #C8102E 0%, #a00d26 100%);
                z-index: 1000;
                padding: 60px 20px 20px;
                overflow-y: auto;
                transition: left 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }

            .main-nav.mobile-open {
                left: 0;
            }

            .mobile-close {
                display: flex;
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(255, 255, 255, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                color: white;
                font-size: 1.3rem;
                width: 35px;
                height: 35px;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .mobile-overlay.show {
                display: block;
            }

            .nav-menu {
                flex-direction: column;
                gap: 8px;
            }

            .nav-item {
                width: 100%;
            }

            .nav-link {
                padding: 14px 16px;
                border-radius: 8px;
            }

            .nav-link:hover {
                transform: none;
            }

            /* 手機版下拉選單 */
            .dropdown-menu {
                position: static;
                min-width: auto;
                background: rgba(255, 255, 255, 0.95);
                margin: 8px 0;
                opacity: 1;
                visibility: visible;
                transform: none;
                display: none;
            }

            .dropdown-menu.show {
                display: block;
            }

            .submenu {
                position: static;
                min-width: auto;
                background: rgba(0, 0, 0, 0.2);
                margin: 4px 0;
                opacity: 1;
                visibility: visible;
                display: none;
            }

            .submenu.show {
                display: block;
            }

            .submenu a {
                color: white !important;
                border-left: 3px solid transparent;
            }

            .submenu a:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                border-left-color: white;
            }
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
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
            
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
        
        <nav class="main-nav" id="main-nav">
            <button class="mobile-close" onclick="closeMobileMenu()">×</button>
            
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
                    <a href="#" class="nav-link" onclick="toggleDropdown(this, event)">
                        <span class="icon">📝</span>
                        <span>表單申請</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">郵務管理 <span class="arrow">▶</span></a>
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
                            <a href="#" onclick="toggleSubmenu(this, event)">權限申請 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/forms/mf2000">MF2000</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/forms/nas">NAS權限</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/forms/email">信箱申請</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/forms/vpn">VPN申請</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/it-training">IT培訓</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/forms/qrcode">QR申請</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link" onclick="toggleDropdown(this, event)">
                        <span class="icon">📅</span>
                        <span>資源預約</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $baseUrl; ?>/booking/meeting-room">會議室</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/booking/equipment">設備借用</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <li class="nav-item dropdown">
                    <a href="<?php echo $baseUrl; ?>/company" class="nav-link">
                        <span class="icon">🏢</span>
                        <span>公司資訊</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $baseUrl; ?>/company/floor">樓層圖</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/company/contacts">聯絡資訊</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/company/nas">NAS介紹</a></li>
                    </ul>
                </li>
                
                <?php if ($isLoggedIn): ?>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link" onclick="toggleDropdown(this, event)">
                        <span class="icon">❓</span>
                        <span>操作指引</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">Windows <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/windows/remote">遠端連線</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/guides/windows/audio">音訊設定</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">印表機 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/printer/basic">基本操作</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/guides/printer/troubleshoot">故障排除</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">MAC設備 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/mac/web-print">網頁列印</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/guides/mac/driver">驅動安裝</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo $baseUrl; ?>/guides/pos">POS系統</a></li>
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">NAS使用 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/nas/password">密碼重設</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/guides/nas/web-auth">二次驗證</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">信箱設定 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/email">信箱設定</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" onclick="toggleSubmenu(this, event)">免稅系統 <span class="arrow">▶</span></a>
                            <ul class="submenu">
                                <li><a href="<?php echo $baseUrl; ?>/guides/tax-exempt/process">申請流程</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/guides/tax-exempt/system">系統操作</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="mobile-overlay" id="mobile-overlay" onclick="closeMobileMenu()"></div>
    </header>

    <main class="main-layout">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $appName; ?>. 版權所有.</p>
            <p>系統版本: MVC 2.0 | 最後更新: <?php echo date('Y-m-d'); ?></p>
        </div>
    </footer>

    <script src="<?php echo $baseUrl; ?>/assets/js/scripts.js"></script>
    <script>
        // 簡單穩定的選單控制
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            const overlay = document.getElementById('mobile-overlay');
            
            nav.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
            document.body.style.overflow = nav.classList.contains('mobile-open') ? 'hidden' : '';
        }
        
        function closeMobileMenu() {
            const nav = document.getElementById('main-nav');
            const overlay = document.getElementById('mobile-overlay');
            
            nav.classList.remove('mobile-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            
            // 關閉所有下拉選單
            document.querySelectorAll('.dropdown-menu.show, .submenu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
        
        function toggleDropdown(element, event) {
            // 只在手機版執行
            if (window.innerWidth > 768) return;
            
            event.preventDefault();
            
            const dropdown = element.parentElement;
            const menu = dropdown.querySelector('.dropdown-menu');
            
            // 關閉其他下拉選單
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.remove('show');
            });
            
            // 切換當前選單
            menu.classList.toggle('show');
        }
        
        function toggleSubmenu(element, event) {
            // 只在手機版執行
            if (window.innerWidth > 768) return;
            
            event.preventDefault();
            
            const submenuItem = element.parentElement;
            const submenu = submenuItem.querySelector('.submenu');
            
            // 關閉其他子選單
            document.querySelectorAll('.submenu').forEach(s => {
                if (s !== submenu) s.classList.remove('show');
            });
            
            // 切換當前子選單
            submenu.classList.toggle('show');
        }
        
        // 視窗大小改變時重置
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });
        
        // 點擊選單項目後關閉手機版選單
        document.addEventListener('click', function(event) {
            if (event.target.matches('.dropdown-menu a:not(.dropdown-submenu > a), .submenu a')) {
                if (window.innerWidth <= 768) {
                    setTimeout(closeMobileMenu, 200);
                }
            }
        });
    </script>
</body>
</html> 