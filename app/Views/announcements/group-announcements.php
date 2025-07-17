<?php
// app/Views/guides/group-announcements.php (這個檔案名更符合內容)

$pageTitle = "集團公告";
$pageSubtitle = "公司內部共用檔案、教學文件與資源";
$pageType = "guides"; // For sidebar highlighting

// 假設 $baseUrl 已經正確設定，例如在您的應用程式啟動時定義
$baseUrl = ''; // 在本範例中假設為空，實際請確保您的應用程式有正確設定 $baseUrl

// --- 以下為模擬後端傳遞給前端的資料 ---
// 在實際應用中，這些數據會由 PHP 從伺服器檔案系統動態讀取並生成
$breadcrumbs = [
    // 如果是根目錄，麵包屑可能只有一個項目
    ['name' => '集團公告', 'path' => ''],
    // 如果是子資料夾，例如：
    // ['name' => '集團公告', 'path' => ''],
    // ['name' => '2022防疫專區', 'path' => '2022防疫專區']
];

$items = [
    // 範例資料，模擬您圖片中顯示的資料夾和檔案
    ['name' => '#recycle', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('#recycle')],
    ['name' => '2022防疫專區', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('2022防疫專區')],
    ['name' => '國圖與文化部免稅作業', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('國圖與文化部免稅作業')],
    ['name' => '木馬假冒醫療防治公告', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('木馬假冒醫療防治公告')],
    ['name' => '資安提醒與教學', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('資安提醒與教學')],
    ['name' => '遠足往返醫療防治療公告', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('遠足往返醫療防治療公告')],
    ['name' => '集團公告(110年)', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('集團公告(110年)')],
    // 您也可以添加檔案類型
    // ['name' => '重要文件.pdf', 'type' => 'file', 'url' => $baseUrl . '/path/to/important.pdf']
];

$error = null; // 假設目前沒有錯誤信息
// --- 模擬資料結束 ---
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* 基礎樣式重置 */
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

        /* 頂部導覽區域 (保留，假設這是您網站的全局 header) */
        /* ... 略過您的 top-bar 和 main-header 相關的 CSS，因為它們應該是全局的 ... */


        /* 主要內容區域 - 請確保您的佈局檔案有 <main class="main-layout"> 和 <div class="main-content"> */
        .main-layout {
            flex: 1;
            padding: 2rem 1rem; /* 整體佈局的左右邊距 */
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 200px); /* 假設 header 和 footer 的總高度 */
        }

        .main-content {
            width: 100%;
            max-width: 1200px; /* 內容最大寬度，這裡會是頁面居中的部分 */
            margin: 2rem auto; /* 上下邊距和水平居中 */
            padding: 2rem; /* 內容區塊的內部填充 */
            background-color: #ffffff; /* 確保 main-content 有白色背景 */
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ----- 針對此頁面特有的 .shared-files-container 樣式 ----- */
        .shared-files-container {
            /* 圖片中的內容在一個大的白色卡片裡，沒有獨立的 shared-files-container 外框 */
            /* 所以這個 shared-files-container 應該就是 main-content 本身或者與它高度重合 */
            /* 或者你可以把它當作 .main-content 內部的 padding 控制 */
            max-width: 100%; /* 讓它佔滿 main-content 的寬度 */
            margin: 0; /* 移除 auto margin */
            padding: 0; /* 內容的 padding 會在 .file-browser-card 裡 */
        }

        /* 頁面標題樣式 (根據圖片調整) */
        .page-header {
            text-align: left; /* 圖片中標題靠左 */
            margin-bottom: 20px; /* 調整與卡片的間距 */
            padding: 0; /* 移除 page-header 自身的 padding */
            background: none; /* 移除背景 */
            backdrop-filter: none; /* 移除模糊 */
            border: none; /* 移除邊框 */
            box-shadow: none; /* 移除陰影 */
        }

        .page-header h1 {
            font-size: 1.8rem; /* 圖片中標題字體大小，類似 h3 */
            font-weight: bold; /* 圖片中字體加粗 */
            color: #333; /* 圖片中的標題顏色偏深灰 */
            margin: 0; /* 移除預設 margin-top */
            margin-bottom: 5px; /* 調整與副標題間距 */
            padding-left: 5px; /* 圖片中標題左側有微小縮進 */
        }

        .page-subtitle {
            color: #666; /* 圖片中副標題顏色 */
            font-size: 0.9rem; /* 圖片中副標題字體較小 */
            margin: 0;
            padding-left: 5px; /* 圖片中副標題左側有微小縮進 */
        }

        /* 檔案瀏覽器卡片 (圖片中主要內容的白色卡片) */
        .file-browser-card.glass-card {
            background: #fff; /* 白色背景 */
            backdrop-filter: none; /* 圖片沒有模糊效果 */
            border: none; /* 圖片沒有明顯邊框 */
            border-radius: 8px; /* 圖片中的圓角 */
            padding: 20px 30px; /* 調整內邊距，上下 20px，左右 30px */
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* 圖片中的柔和陰影 */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .file-browser-card.glass-card:hover {
            transform: translateY(-2px); /* 輕微上移 */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); /* hover 時陰影加深 */
        }

        /* 麵包屑導覽樣式 (圖片中沒有顯示，所以可以考慮隱藏或簡化) */
        .breadcrumbs {
            /* display: none; */ /* 如果圖片中確實沒有麵包屑，可以直接隱藏它 */
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee; /* 輕微分隔線 */
            font-size: 0.95rem; /* 麵包屑字體大小 */
            text-align: left; /* 麵包屑靠左 */
            display: flex; /* 確保返回按鈕和麵包屑能正常排列 */
            align-items: center;
            flex-wrap: wrap; /* 內容過長時換行 */
        }
        .breadcrumbs .breadcrumb-back { /* 返回按鈕樣式 */
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 10px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .breadcrumbs .breadcrumb-back:hover {
            background-color: #e0e0e0;
        }
        .breadcrumbs a { /* 麵包屑鏈接 */
            color: #C8102E; /* 主題色 */
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .breadcrumbs a:hover {
            color: #a00d25;
        }
        .breadcrumbs .separator {
            margin: 0 8px;
            color: #ccc;
        }
        .breadcrumbs .current {
            color: #666;
            font-weight: 500;
        }

        /* 檔案列表樣式 */
        .file-list ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0; /* 移除預設 margin */
        }
        .file-list li {
            margin-bottom: 2px; /* 列表項目間距更小，使其更緊密 */
        }
        .file-list a {
            display: flex;
            align-items: center;
            padding: 10px 15px; /* 增加點擊區域和內邊距 */
            border-radius: 4px; /* 圓角更小，更簡潔 */
            color: #4a5568; /* 文字顏色 */
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.2s ease;
            font-size: 1rem; /* 列表文字字體大小 */
        }
        .file-list a:hover {
            background-color: #f5f5f5; /* hover 背景色 */
            transform: translateX(3px); /* hover 時輕微右移 */
        }
        .file-list .icon { /* 資料夾/檔案圖示 */
            font-size: 1.4rem; /* 圖示大小 */
            margin-right: 15px;
            width: 25px; /* 確保圖示佔據固定寬度，方便文字對齊 */
            text-align: center;
            flex-shrink: 0; /* 防止圖示縮小 */
            color: #f7d540; /* 黃色資料夾圖示 */
        }
        .file-list .icon.file-icon { /* 如果是檔案圖示，可以改變顏色 */
            color: #6b7280; /* 例如文件圖示灰色 */
        }
        .file-list .name { /* 檔名/資料夾名稱 */
            font-size: 1rem; /* 與 a 的字體大小一致 */
            font-weight: normal; /* 圖片中文字沒有加粗 */
            flex-grow: 1; /* 佔據剩餘空間 */
        }
        .empty-folder {
            text-align: center;
            color: #6b7280;
            padding: 40px 0;
            font-size: 1.1rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* 響應式調整 */
        @media (max-width: 768px) {
            .shared-files-container {
                padding: 10px;
            }
            .page-header h1 {
                font-size: 1.6rem; /* 手機上標題更小 */
            }
            .page-subtitle {
                font-size: 0.85rem;
            }
            .file-browser-card.glass-card {
                padding: 15px 20px; /* 手機上內邊距更小 */
            }
            .breadcrumbs {
                font-size: 0.85rem;
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            .file-list a {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
            .file-list .icon {
                font-size: 1.2rem;
                margin-right: 10px;
                width: 20px;
            }
            .file-list .name {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div style="min-height: 100vh; display: flex; flex-direction: column;">
        <div style="background: #C8102E; height: 80px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div> 
        <div class="main-layout"> 
            <div class="main-content">
                <div class="shared-files-container">
                    <div class="page-header">
                        <h1><?php echo $pageTitle; ?></h1>
                        <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
                    </div>

                    <div class="file-browser-card glass-card">
                        <nav class="breadcrumbs">
                            <?php if (!empty($breadcrumbs) && count($breadcrumbs) > 1): ?>
                                <a href="<?php echo $baseUrl; ?>/group-announcements<?php echo (count($breadcrumbs) > 2) ? '?path=' . urlencode($breadcrumbs[count($breadcrumbs)-2]['path']) : ''; ?>" class="breadcrumb-back">返回上一層</a>
                            <?php elseif (count($breadcrumbs) == 1 && $breadcrumbs[0]['name'] != $pageTitle): ?>
                                <a href="<?php echo $baseUrl; ?>/group-announcements" class="breadcrumb-back">返回<?php echo htmlspecialchars($pageTitle); ?></a>
                            <?php endif; ?>

                            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                <?php if ($index > 0 || (count($breadcrumbs) == 1 && $breadcrumbs[0]['name'] == $pageTitle)): /* 只在非根目錄或單獨根目錄時顯示麵包屑 */ ?>
                                    <span class="separator">/</span>
                                    <?php if ($index < count($breadcrumbs) - 1): ?>
                                        <a href="<?php echo $baseUrl . '/group-announcements?path=' . urlencode($crumb['path']); ?>"><?php echo htmlspecialchars($crumb['name']); ?></a>
                                    <?php else: ?>
                                        <span class="current"><?php echo htmlspecialchars($crumb['name']); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>

                        <div class="file-list">
                            <?php if (isset($error)): ?>
                                <p class="empty-folder" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                            <?php elseif (empty($items)): ?>
                                <p class="empty-folder">此資料夾是空的。</p>
                            <?php else: ?>
                                <ul>
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($item['url']); ?>">
                                            <span class="icon <?php echo ($item['type'] === 'file' ? 'file-icon' : ''); ?>">
                                                <i class="bi <?php echo ($item['type'] === 'dir' ? 'bi-folder-fill' : 'bi-file-earmark'); ?>"></i>
                                            </span>
                                            <span class="name"><?php echo htmlspecialchars($item['name']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background: #444; color: white; padding: 20px; text-align: center;">網站底部內容</div>
    </div>

    <script>
        // 麵包屑返回按鈕的 JS 邏輯 (如果需要更動態的返回)
        // 例如：history.back()
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.breadcrumb-back');
            if (backButton && backButton.textContent === '返回') { // 如果返回連結的文字是 '返回'
                // 這裡可以選擇性地使用 history.back() 讓瀏覽器返回上一頁
                // backButton.addEventListener('click', function(e) {
                //     e.preventDefault();
                //     history.back();
                // });
            }
        });

        // 這裡也可以加入用於動態載入的 JS，例如您的搜尋功能
        // ... (原來的 JS 程式碼，如果需要，可以放在這裡)
    </script>
</body>
<?php
// app/Views/guides/group-announcements.php (這個檔案名更符合內容)

$pageTitle = "集團公告";
$pageSubtitle = "公司內部共用檔案、教學文件與資源";
$pageType = "guides"; // For sidebar highlighting

// 假設 $baseUrl 已經正確設定，例如在您的應用程式啟動時定義
$baseUrl = ''; // 在本範例中假設為空，實際請確保您的應用程式有正確設定 $baseUrl

// --- 以下為模擬後端傳遞給前端的資料 ---
// 在實際應用中，這些數據會由 PHP 從伺服器檔案系統動態讀取並生成
$breadcrumbs = [
    // 如果是根目錄，麵包屑可能只有一個項目
    ['name' => '集團公告', 'path' => ''],
    // 如果是子資料夾，例如：
    // ['name' => '集團公告', 'path' => ''],
    // ['name' => '2022防疫專區', 'path' => '2022防疫專區']
];

$items = [
    // 範例資料，模擬您圖片中顯示的資料夾和檔案
    ['name' => '#recycle', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('#recycle')],
    ['name' => '2022防疫專區', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('2022防疫專區')],
    ['name' => '國圖與文化部免稅作業', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('國圖與文化部免稅作業')],
    ['name' => '木馬假冒醫療防治公告', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('木馬假冒醫療防治公告')],
    ['name' => '資安提醒與教學', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('資安提醒與教學')],
    ['name' => '遠足往返醫療防治療公告', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('遠足往返醫療防治療公告')],
    ['name' => '集團公告(110年)', 'type' => 'dir', 'url' => $baseUrl . '/group-announcements?path=' . urlencode('集團公告(110年)')],
    // 您也可以添加檔案類型
    // ['name' => '重要文件.pdf', 'type' => 'file', 'url' => $baseUrl . '/path/to/important.pdf']
];

$error = null; // 假設目前沒有錯誤信息
// --- 模擬資料結束 ---
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* 基礎樣式重置 */
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

        /* 頂部導覽區域 (保留，假設這是您網站的全局 header) */
        /* ... 略過您的 top-bar 和 main-header 相關的 CSS，因為它們應該是全局的 ... */


        /* 主要內容區域 - 請確保您的佈局檔案有 <main class="main-layout"> 和 <div class="main-content"> */
        .main-layout {
            flex: 1;
            padding: 2rem 1rem; /* 整體佈局的左右邊距 */
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 200px); /* 假設 header 和 footer 的總高度 */
        }

        .main-content {
            width: 100%;
            max-width: 1200px; /* 內容最大寬度，這裡會是頁面居中的部分 */
            margin: 2rem auto; /* 上下邊距和水平居中 */
            padding: 2rem; /* 內容區塊的內部填充 */
            background-color: #ffffff; /* 確保 main-content 有白色背景 */
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ----- 針對此頁面特有的 .shared-files-container 樣式 ----- */
        .shared-files-container {
            /* 圖片中的內容在一個大的白色卡片裡，沒有獨立的 shared-files-container 外框 */
            /* 所以這個 shared-files-container 應該就是 main-content 本身或者與它高度重合 */
            /* 或者你可以把它當作 .main-content 內部的 padding 控制 */
            max-width: 100%; /* 讓它佔滿 main-content 的寬度 */
            margin: 0; /* 移除 auto margin */
            padding: 0; /* 內容的 padding 會在 .file-browser-card 裡 */
        }

        /* 頁面標題樣式 (根據圖片調整) */
        .page-header {
            text-align: left; /* 圖片中標題靠左 */
            margin-bottom: 20px; /* 調整與卡片的間距 */
            padding: 0; /* 移除 page-header 自身的 padding */
            background: none; /* 移除背景 */
            backdrop-filter: none; /* 移除模糊 */
            border: none; /* 移除邊框 */
            box-shadow: none; /* 移除陰影 */
        }

        .page-header h1 {
            font-size: 1.8rem; /* 圖片中標題字體大小，類似 h3 */
            font-weight: bold; /* 圖片中字體加粗 */
            color: #333; /* 圖片中的標題顏色偏深灰 */
            margin: 0; /* 移除預設 margin-top */
            margin-bottom: 5px; /* 調整與副標題間距 */
            padding-left: 5px; /* 圖片中標題左側有微小縮進 */
        }

        .page-subtitle {
            color: #666; /* 圖片中副標題顏色 */
            font-size: 0.9rem; /* 圖片中副標題字體較小 */
            margin: 0;
            padding-left: 5px; /* 圖片中副標題左側有微小縮進 */
        }

        /* 檔案瀏覽器卡片 (圖片中主要內容的白色卡片) */
        .file-browser-card.glass-card {
            background: #fff; /* 白色背景 */
            backdrop-filter: none; /* 圖片沒有模糊效果 */
            border: none; /* 圖片沒有明顯邊框 */
            border-radius: 8px; /* 圖片中的圓角 */
            padding: 20px 30px; /* 調整內邊距，上下 20px，左右 30px */
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* 圖片中的柔和陰影 */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .file-browser-card.glass-card:hover {
            transform: translateY(-2px); /* 輕微上移 */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); /* hover 時陰影加深 */
        }

        /* 麵包屑導覽樣式 (圖片中沒有顯示，所以可以考慮隱藏或簡化) */
        .breadcrumbs {
            /* display: none; */ /* 如果圖片中確實沒有麵包屑，可以直接隱藏它 */
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee; /* 輕微分隔線 */
            font-size: 0.95rem; /* 麵包屑字體大小 */
            text-align: left; /* 麵包屑靠左 */
            display: flex; /* 確保返回按鈕和麵包屑能正常排列 */
            align-items: center;
            flex-wrap: wrap; /* 內容過長時換行 */
        }
        .breadcrumbs .breadcrumb-back { /* 返回按鈕樣式 */
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 10px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .breadcrumbs .breadcrumb-back:hover {
            background-color: #e0e0e0;
        }
        .breadcrumbs a { /* 麵包屑鏈接 */
            color: #C8102E; /* 主題色 */
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .breadcrumbs a:hover {
            color: #a00d25;
        }
        .breadcrumbs .separator {
            margin: 0 8px;
            color: #ccc;
        }
        .breadcrumbs .current {
            color: #666;
            font-weight: 500;
        }

        /* 檔案列表樣式 */
        .file-list ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0; /* 移除預設 margin */
        }
        .file-list li {
            margin-bottom: 2px; /* 列表項目間距更小，使其更緊密 */
        }
        .file-list a {
            display: flex;
            align-items: center;
            padding: 10px 15px; /* 增加點擊區域和內邊距 */
            border-radius: 4px; /* 圓角更小，更簡潔 */
            color: #4a5568; /* 文字顏色 */
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.2s ease;
            font-size: 1rem; /* 列表文字字體大小 */
        }
        .file-list a:hover {
            background-color: #f5f5f5; /* hover 背景色 */
            transform: translateX(3px); /* hover 時輕微右移 */
        }
        .file-list .icon { /* 資料夾/檔案圖示 */
            font-size: 1.4rem; /* 圖示大小 */
            margin-right: 15px;
            width: 25px; /* 確保圖示佔據固定寬度，方便文字對齊 */
            text-align: center;
            flex-shrink: 0; /* 防止圖示縮小 */
            color: #f7d540; /* 黃色資料夾圖示 */
        }
        .file-list .icon.file-icon { /* 如果是檔案圖示，可以改變顏色 */
            color: #6b7280; /* 例如文件圖示灰色 */
        }
        .file-list .name { /* 檔名/資料夾名稱 */
            font-size: 1rem; /* 與 a 的字體大小一致 */
            font-weight: normal; /* 圖片中文字沒有加粗 */
            flex-grow: 1; /* 佔據剩餘空間 */
        }
        .empty-folder {
            text-align: center;
            color: #6b7280;
            padding: 40px 0;
            font-size: 1.1rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* 響應式調整 */
        @media (max-width: 768px) {
            .shared-files-container {
                padding: 10px;
            }
            .page-header h1 {
                font-size: 1.6rem; /* 手機上標題更小 */
            }
            .page-subtitle {
                font-size: 0.85rem;
            }
            .file-browser-card.glass-card {
                padding: 15px 20px; /* 手機上內邊距更小 */
            }
            .breadcrumbs {
                font-size: 0.85rem;
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            .file-list a {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
            .file-list .icon {
                font-size: 1.2rem;
                margin-right: 10px;
                width: 20px;
            }
            .file-list .name {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div style="min-height: 100vh; display: flex; flex-direction: column;">
        <div style="background: #C8102E; height: 80px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div> 
        <div class="main-layout"> 
            <div class="main-content">
                <div class="shared-files-container">
                    <div class="page-header">
                        <h1><?php echo $pageTitle; ?></h1>
                        <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
                    </div>

                    <div class="file-browser-card glass-card">
                        <nav class="breadcrumbs">
                            <?php if (!empty($breadcrumbs) && count($breadcrumbs) > 1): ?>
                                <a href="<?php echo $baseUrl; ?>/group-announcements<?php echo (count($breadcrumbs) > 2) ? '?path=' . urlencode($breadcrumbs[count($breadcrumbs)-2]['path']) : ''; ?>" class="breadcrumb-back">返回上一層</a>
                            <?php elseif (count($breadcrumbs) == 1 && $breadcrumbs[0]['name'] != $pageTitle): ?>
                                <a href="<?php echo $baseUrl; ?>/group-announcements" class="breadcrumb-back">返回<?php echo htmlspecialchars($pageTitle); ?></a>
                            <?php endif; ?>

                            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                <?php if ($index > 0 || (count($breadcrumbs) == 1 && $breadcrumbs[0]['name'] == $pageTitle)): /* 只在非根目錄或單獨根目錄時顯示麵包屑 */ ?>
                                    <span class="separator">/</span>
                                    <?php if ($index < count($breadcrumbs) - 1): ?>
                                        <a href="<?php echo $baseUrl . '/group-announcements?path=' . urlencode($crumb['path']); ?>"><?php echo htmlspecialchars($crumb['name']); ?></a>
                                    <?php else: ?>
                                        <span class="current"><?php echo htmlspecialchars($crumb['name']); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>

                        <div class="file-list">
                            <?php if (isset($error)): ?>
                                <p class="empty-folder" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                            <?php elseif (empty($items)): ?>
                                <p class="empty-folder">此資料夾是空的。</p>
                            <?php else: ?>
                                <ul>
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($item['url']); ?>">
                                            <span class="icon <?php echo ($item['type'] === 'file' ? 'file-icon' : ''); ?>">
                                                <i class="bi <?php echo ($item['type'] === 'dir' ? 'bi-folder-fill' : 'bi-file-earmark'); ?>"></i>
                                            </span>
                                            <span class="name"><?php echo htmlspecialchars($item['name']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background: #444; color: white; padding: 20px; text-align: center;">網站底部內容</div>
    </div>

    <script>
         //麵包屑返回按鈕的 JS 邏輯 (如果需要更動態的返回)
         //例如：history.back()
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.breadcrumb-back');
            if (backButton && backButton.textContent === '返回') { // 如果返回連結的文字是 '返回'
                // 這裡可以選擇性地使用 history.back() 讓瀏覽器返回上一頁
                // backButton.addEventListener('click', function(e) {
                //     e.preventDefault();
                //     history.back();
                // });
            }
        });

        // 這裡也可以加入用於動態載入的 JS，例如您的搜尋功能
        // ... (原來的 JS 程式碼，如果需要，可以放在這裡)
    </script>
</body>
</html>