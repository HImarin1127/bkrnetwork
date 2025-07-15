<?php
// 這個檔案現在只負責顯示資料，所有的業務邏輯都已移至 HomeController。

$pageTitle = "假日資訊";
//$breadcrumb = [
    //'首頁' => '/bkrnetwork/home',
    //'公告區' => '/bkrnetwork/announcements',
    //'假日資訊' => '/bkrnetwork/holidays'
//];
?>

<style>
    /* 頁面背景 - 讀書共和國主題 */
    .main-layout {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 20%, #dee2e6 40%, #C8102E 100%);
        min-height: calc(100vh - 120px);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    /* 麵包屑導航 */
    .breadcrumb {
        margin-bottom: 20px;
        color: rgba(0, 0, 0, 0.7);
    }

    .breadcrumb a {
        color: #C8102E;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb a:hover {
        color: #8B0000;
        text-decoration: underline;
    }

    /* 頁面標題 */
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        color: #333;
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
        font-weight: 700;
        color: #C8102E;
        text-shadow: 0 2px 4px rgba(200, 16, 46, 0.2);
    }

    .page-header p {
        font-size: 1.1rem;
        color: #666;
        margin: 0;
    }

    /* 假日行事曆樣式 */
    .holiday-calendar {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .holiday-calendar h3 {
        text-align: center;
        color: #C8102E;
        font-size: 1.8rem;
        margin-bottom: 30px;
        font-weight: bold;
        text-shadow: 0 2px 4px rgba(200, 16, 46, 0.1);
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .calendar-grid {
            grid-template-columns: 1fr;
        }
    }

    .month-calendar {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 20px rgba(200, 16, 46, 0.15);
        border: 2px solid rgba(200, 16, 46, 0.2);
        backdrop-filter: blur(10px);
    }

    .month-calendar h4 {
        text-align: center;
        background: linear-gradient(135deg, #C8102E, #8B0000);
        color: white;
        margin: -15px -15px 15px -15px;
        padding: 12px;
        border-radius: 10px 10px 0 0;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .calendar-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .calendar-table th {
        background: #f8f9fa;
        color: #333;
        padding: 8px 4px;
        text-align: center;
        border: 1px solid #ddd;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .calendar-table td {
        border: 1px solid #ddd;
        height: 60px;
        width: 14.28%;
        vertical-align: top;
        position: relative;
        padding: 2px;
    }

    .calendar-day {
        position: relative;
        height: 100%;
    }

    .day-number {
        font-weight: bold;
        font-size: 0.9rem;
        color: #333;
        text-align: center;
        margin-bottom: 2px;
    }

    .holiday-name {
        font-size: 0.7rem;
        color: #C8102E;
        font-weight: bold;
        text-align: center;
        line-height: 1.1;
        padding: 1px;
    }

    .calendar-day.holiday {
        background: linear-gradient(135deg, #FFE5E5, #FFD0D0);
        color: #C8102E;
    }

    .calendar-day.holiday .day-number {
        color: #C8102E;
        font-weight: bold;
    }

    .empty-day {
        background: #f8f9fa;
    }

    .calendar-legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(200, 16, 46, 0.1);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #333;
    }

    .holiday-color,
    .workday-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .holiday-color {
        background: linear-gradient(135deg, #FFE5E5, #FFD0D0);
    }

    .workday-color {
        background: white;
    }

    .update-info {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 0.9rem;
    }

    .holiday-list {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
        box-shadow: 0 4px 20px rgba(200, 16, 46, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(200, 16, 46, 0.1);
    }

    .holiday-list h4 {
        color: #C8102E;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    .holiday-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }

    .holiday-item {
        background: linear-gradient(135deg, #FFE5E5, #FFD0D0);
        border-left: 4px solid #C8102E;
        padding: 12px;
        border-radius: 6px;
    }

    .holiday-date {
        font-weight: bold;
        color: #C8102E;
        font-size: 0.9rem;
    }

    .holiday-desc {
        color: #333;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    /* 響應式設計 */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }
        
        .container {
            padding: 20px 15px;
        }
    }
</style>

<div class="main-layout">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/bkrnetwork/home">首頁</a></li>
                <li class="breadcrumb-item"><a href="/bkrnetwork/announcements">公告區</a></li>-->
                <!--<li class="breadcrumb-item active" aria-current="page">假日資訊</li>-->
            </ol>
        </nav>
        
        <div class="page-header">
            <h1><?= isset($title) ? htmlspecialchars($title) : '假日資訊' ?></h1>
            <p>人事行政總處政府行政機關辦公日曆表</p>
        </div>

        <div class="holiday-calendar">
            <?php if (isset($calendarHtml) && !empty($calendarHtml)): ?>
                <?= $calendarHtml ?>
            <?php else: ?>
                <p>無法載入年度行事曆。</p>
            <?php endif; ?>
        </div>
        
        <div class="update-info">
            <p>資料來源：行政院人事行政總處。最後更新時間：<?= date('Y-m-d') ?></p>
        </div>
        
        <?php if (isset($holidays) && !empty($holidays)): ?>
        <div class="holiday-list">
            <h4><?= date('Y') ?>年 國定假日列表</h4>
            <div class="holiday-items">
                <?php if (!empty($holidays)): ?>
                    <?php foreach ($holidays as $holiday): ?>
                        <?php if (isset($holiday['type']) && $holiday['type'] === 'holiday'): ?>
                            <div class="holiday-item">
                                <div class="holiday-date"><?php echo date('n月j日', strtotime($holiday['date'])); ?></div>
                                <div class="holiday-desc"><?php echo htmlspecialchars($holiday['name']); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div> 