<?php
// 管理員假日資料更新頁面
session_start();

// 簡單的權限檢查
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /auth/login');
    exit;
}

require_once 'app/Models/Database.php';
require_once 'app/Models/Model.php';
require_once 'app/Models/HolidayCalendar.php';
require_once 'config/database.php';

$message = '';
$error = '';

// 處理更新請求
if ($_POST['action'] === 'update' ?? '') {
    try {
        $holidayCalendar = new HolidayCalendar();
        $result = $holidayCalendar->updateHolidays();
        
        if ($result['status'] === 'success') {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } catch (Exception $e) {
        $error = '更新失敗: ' . $e->getMessage();
    }
}

// 取得現有假日資料
try {
    $holidayCalendar = new HolidayCalendar();
    $holidays = $holidayCalendar->getHolidays(10);
} catch (Exception $e) {
    $holidays = [];
    $error = '讀取資料失敗: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>假日資料管理 - 讀書共和國員工服務網</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>🗓️ 假日資料管理</h1>
            <nav>
                <a href="/admin/dashboard">回到管理後台</a>
                <a href="/">回到首頁</a>
            </nav>
        </header>

        <main class="admin-main">
            <!-- 操作區域 -->
            <section class="action-section">
                <div class="glass-card">
                    <h2>🔄 更新假日資料</h2>
                    <p>從政府公開資料平台抓取最新的國定假日資訊</p>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <button type="submit" class="btn btn-primary">
                            立即更新假日資料
                        </button>
                    </form>

                    <?php if ($message): ?>
                        <div class="message success">
                            ✅ <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="message error">
                            ❌ <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 資料顯示區域 -->
            <section class="data-section">
                <div class="glass-card">
                    <h2>📊 現有假日資料</h2>
                    
                    <?php if (!empty($holidays)): ?>
                        <div class="holiday-list">
                            <?php foreach ($holidays as $holiday): ?>
                                <div class="holiday-item">
                                    <div class="holiday-year"><?php echo $holiday['year']; ?></div>
                                    <div class="holiday-title">
                                        <?php echo htmlspecialchars($holiday['title']); ?>
                                    </div>
                                    <div class="holiday-date">
                                        抓取時間: <?php echo $holiday['fetch_date']; ?>
                                    </div>
                                    <?php if (!empty($holiday['url'])): ?>
                                        <div class="holiday-link">
                                            <a href="<?php echo htmlspecialchars($holiday['url']); ?>" target="_blank">
                                                查看原始資料 →
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <p>📭 目前沒有假日資料</p>
                            <p>請點擊上方的「立即更新假日資料」按鈕來抓取資料</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 說明區域 -->
            <section class="info-section">
                <div class="glass-card">
                    <h2>ℹ️ 使用說明</h2>
                    <ul>
                        <li>系統會自動從政府公開資料平台抓取國定假日資訊</li>
                        <li>資料來源：行政院人事行政總處公告</li>
                        <li>建議每年初或政府公告更新時手動更新一次</li>
                        <li>重複的資料會自動過濾，不會產生重複記錄</li>
                        <li>更新後的資料會立即顯示在假日公告頁面</li>
                    </ul>
                </div>
            </section>
        </main>
    </div>

    <style>
    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .admin-header {
        background: linear-gradient(135deg, #C8102E, #8B0000);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        text-align: center;
    }

    .admin-header h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
    }

    .admin-header nav a {
        color: white;
        text-decoration: none;
        margin: 0 15px;
        padding: 8px 16px;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .admin-header nav a:hover {
        background: rgba(255,255,255,0.2);
    }

    .admin-main {
        display: grid;
        gap: 30px;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .btn {
        background: linear-gradient(135deg, #C8102E, #8B0000);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(200, 16, 46, 0.4);
    }

    .message {
        margin-top: 15px;
        padding: 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    .message.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .holiday-list {
        display: grid;
        gap: 15px;
    }

    .holiday-item {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #C8102E;
    }

    .holiday-year {
        font-size: 14px;
        color: #C8102E;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .holiday-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .holiday-date {
        font-size: 12px;
        color: #666;
        margin-bottom: 8px;
    }

    .holiday-link a {
        color: #C8102E;
        text-decoration: none;
        font-size: 14px;
    }

    .holiday-link a:hover {
        text-decoration: underline;
    }

    .no-data {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    .info-section ul {
        list-style: none;
        padding: 0;
    }

    .info-section li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .info-section li:before {
        content: "📌 ";
        margin-right: 8px;
    }
    </style>
</body>
</html> 