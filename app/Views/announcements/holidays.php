<?php
// 載入假日行事曆模型
require_once __DIR__ . '/../../Models/Database.php';
require_once __DIR__ . '/../../Models/Model.php';
require_once __DIR__ . '/../../Models/HolidayCalendar.php';

$holidayCalendar = new HolidayCalendar();
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

<!-- 麵包屑導航 -->
<div class="breadcrumb">
    <a href="/">首頁</a> / <a href="/announcements">公告區</a> / 假日資訊
</div>

<!-- 頁面標題 -->
<div class="page-header">
    <h1>🗓️ 假日資訊</h1>
    <p>中華民國114年（西元2025年）政府行政機關辦公日曆表</p>
</div>

<!-- 顯示完整行事曆 -->
<div class="calendar-container">
    <?php
    $holidays = $holidayCalendar->getHolidayCalendar(2025);
    
    // 建立假日對照表
    $holidayMap = [];
    foreach ($holidays as $holiday) {
        $key = sprintf('%02d-%02d', $holiday['month'], $holiday['day']);
        $holidayMap[$key] = $holiday['name'];
    }
    ?>

    <div class="holiday-calendar">
        <h3>中華民國114年（西元2025年）政府行政機關辦公日曆表</h3>
        
        <div class="calendar-grid">
            <?php
            $monthNames = [
                1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六',
                7 => '七', 8 => '八', 9 => '九', 10 => '十', 11 => '十一', 12 => '十二'
            ];
            
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2025);
                $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, 2025));
                ?>
                <div class="month-calendar">
                    <h4><?php echo $monthNames[$month]; ?>月</h4>
                    <table class="calendar-table">
                        <tr>
                            <th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th>
                        </tr>
                        <?php
                        $day = 1;
                        for ($week = 0; $week < 6; $week++) {
                            echo '<tr>';
                            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                                if (($week == 0 && $dayOfWeek < $firstDayOfWeek) || $day > $daysInMonth) {
                                    echo '<td class="empty-day"></td>';
                                } else {
                                    $dateKey = sprintf('%02d-%02d', $month, $day);
                                    $isHoliday = isset($holidayMap[$dateKey]);
                                    $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                                    
                                    $class = 'calendar-day';
                                    if ($isHoliday || $isWeekend) {
                                        $class .= ' holiday';
                                    }
                                    
                                    echo '<td class="' . $class . '">';
                                    echo '<div class="day-number">' . $day . '</div>';
                                    if ($isHoliday) {
                                        echo '<div class="holiday-name">' . htmlspecialchars($holidayMap[$dateKey]) . '</div>';
                                    } elseif ($isWeekend) {
                                        echo '<div class="holiday-name">休假</div>';
                                    }
                                    echo '</td>';
                                    $day++;
                                }
                            }
                            echo '</tr>';
                            
                            if ($day > $daysInMonth) break;
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="calendar-legend">
            <div class="legend-item">
                <span class="holiday-color"></span>
                <span>放假日</span>
            </div>
            <div class="legend-item">
                <span class="workday-color"></span>
                <span>上班日</span>
            </div>
        </div>
    </div>

    <!-- 假日清單 -->
    <div class="holiday-list">
        <h4>📅 2025年國定假日一覽表</h4>
        <div class="holiday-items">
            <?php foreach ($holidays as $holiday): ?>
                <div class="holiday-item">
                    <div class="holiday-date">
                        <?php echo $holiday['month']; ?>月<?php echo $holiday['day']; ?>日
                    </div>
                    <div class="holiday-desc">
                        <?php echo htmlspecialchars($holiday['name']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="update-info">
        <p>📌 資料來源：行政院人事行政總處</p>
        <p>🔄 最後更新：<?php echo date('Y年m月d日 H:i'); ?></p>
        <p>⚠️ 實際放假日期請以政府最新公告為準</p>
    </div>
</div> 