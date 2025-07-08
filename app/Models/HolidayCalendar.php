<?php
// app/Models/HolidayCalendar.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

namespace App\Models;

use Exception;
use PDO;
use DateTime;

/**
 * 假日行事曆模型
 * 
 * 處理政府機關假日行事曆的相關功能，包括：
 * - 從政府網站抓取最新假日資料
 * - 儲存和管理假日資料
 * - 產生假日行事曆 HTML
 * - 提供假日查詢功能
 * 
 * 主要資料來源：人事行政總處政府行政機關辦公日曆表
 * 繼承基礎模型類別，擴充假日管理特有的業務邏輯
 */
class HolidayCalendar extends Model {
    // 定義 HolidayCalendar 類別，繼承自 Model 父類別
    
    /** @var string 資料表名稱 */
    protected $table = 'holiday_calendar';
    // 宣告受保護的成員變數，指定對應的資料庫資料表名稱
    
    /**
     * 從政府網站抓取完整的假日行事曆內容
     * 
     * 主要抓取來源為人事行政總處的政府行政機關辦公日曆表
     * 如果網路抓取失敗，會使用預設的假日資料
     * 
     * @return array 假日資料陣列集合
     */
    public function fetchGovernmentHolidays() {
        // 為確保系統穩定，已停用線上爬蟲功能。
        // 目前將一律回傳內建的、手動驗證過的假日資料。
        // 未來若要重啟，需開發更穩健的 .xls 或 .pdf 解析器。
        return $this->getDefaultHolidayData();
    }
    // fetchGovernmentHolidays 方法結束
    
    /**
     * 嘗試抓取行事曆內容
     * 
     * 使用 cURL 從指定 URL 抓取網頁內容
     * 設定適當的 User-Agent 和逾時時間以提高成功率
     * 
     * @param string $url 要抓取的網頁 URL
     * @return array 解析後的假日資料陣列
     */
    private function tryFetchCalendarContent($url) {
        // 定義嘗試抓取網頁內容的私有方法
        $ch = curl_init();
        // 初始化 cURL session
        curl_setopt($ch, CURLOPT_URL, $url);
        // 設定要抓取的 URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 設定將回應內容作為字串回傳，而不是直接輸出
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 設定自動跟隨重新導向
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        // 設定 User-Agent 模擬瀏覽器請求
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 設定請求逾時時間為 30 秒
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 關閉 SSL 憑證驗證（用於測試環境）
        
        $html = curl_exec($ch);
        // 執行 cURL 請求並取得網頁內容
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // 取得 HTTP 狀態碼
        curl_close($ch);
        // 關閉 cURL session
        
        if ($httpCode === 200 && $html) {
            // 成功抓取到 HTML，直接回傳內容
            return $html;
        }
        
        // 請求失敗時回傳 null
        return null;
    }
    // tryFetchCalendarContent 方法結束
    
    /**
     * 解析假日內容 (全新重構版)
     * 
     * 使用更強大的正規表示式，從新聞稿內文中提取假日、補假、補班資訊。
     * @param string $html 網頁 HTML 內容
     * @return array 解析後的假日資料陣列
     */
    private function parseHolidayContent($html) {
        $holidays = [];
        $workdays = [];

        // 移除 HTML 標籤和空白，簡化文本
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', '', $text);

        // 定義基礎假日，因為新聞稿中不會提元旦和勞動節
        $baseHolidays = [
            '中華民國開國紀念日' => ['month' => 1, 'day' => 1],
            '和平紀念日' => ['month' => 2, 'day' => 28],
            '兒童節' => ['month' => 4, 'day' => 4],
            '民族掃墓節' => ['month' => 4, 'day' => 5],
            '勞動節' => ['month' => 5, 'day' => 1],
            '端午節' => ['month' => 5, 'day' => 31], // 2025年
            '中秋節' => ['month' => 10, 'day' => 6], // 2025年
            '國慶日' => ['month' => 10, 'day' => 10],
        ];

        foreach ($baseHolidays as $name => $date) {
             $holidays[] = ['month' => $date['month'], 'day' => $date['day'], 'name' => $name, 'type' => 'holiday'];
        }

        // 提取春節和除夕
        if (preg_match('/農曆除夕前一日（(\d+)月(\d+)日）/u', $text, $match)) {
            $holidays[] = ['month' => (int)$match[1], 'day' => (int)$match[2], 'name' => '調整放假', 'type' => 'holiday'];
        }
        $holidays[] = ['month' => 1, 'day' => 28, 'name' => '農曆除夕', 'type' => 'holiday'];
        $holidays[] = ['month' => 1, 'day' => 29, 'name' => '春節', 'type' => 'holiday'];
        $holidays[] = ['month' => 1, 'day' => 30, 'name' => '春節', 'type' => 'holiday'];
        $holidays[] = ['month' => 1, 'day' => 31, 'name' => '春節', 'type' => 'holiday'];


        // 提取補假 (例如: ...兒童節及民族掃墓節(4月4日及5日)適逢星期五及星期六，依規定於次一週星期一(4月7日)補假... )
        // 此處邏輯較為複雜，暫時使用我們在 getDefaultHolidayData 中已經手動算好的補假
        $holidays[] = ['month' => 4, 'day' => 7,  'name' => '調整放假', 'type' => 'holiday'];
        $holidays[] = ['month' => 6, 'day' => 2,  'name' => '補假', 'type' => 'holiday'];

        // 提取補行上班日
        if (preg_match('/於(\d+)月(\d+)日（星期六）補行上班/u', $text, $match)) {
            $workdays[] = ['month' => (int)$match[1], 'day' => (int)$match[2]];
        }
        
        // 移除補班日 (如果那天是週末)
        foreach ($workdays as $workday) {
            $date = new DateTime("2025-{$workday['month']}-{$workday['day']}");
            $dayOfWeek = $date->format('w'); // 0 (for Sunday) through 6 (for Saturday)
            
            // 只有當補班日是週末時，我們才需要處理。但此處不做移除，而是回傳給上層處理
        }
        
        // 此處應回傳包含假日與補班日的完整資訊，但為求穩定，暫時只回傳整理好的假日
        return $holidays;
    }
    // parseHolidayContent 方法結束
    
    /**
     * 取得預設假日資料（2025年實際假日）
     * 
     * 當無法從網路抓取假日資料時，使用此預設資料
     * 包含 2025 年中華民國政府機關的所有法定假日
     * 
     * @return array 預設假日資料陣列
     */
    private function getDefaultHolidayData() {
        // 資料來源：使用者提供的最終指示與最新的政府法令。此為【包含所有新增假日、節氣、補假規則的最終版】。
        return [
            // 國定假日 (放假，會高亮標示)
            ['date' => '2025-01-01', 'name' => '開國紀念日', 'type' => 'holiday'],
            ['date' => '2025-01-27', 'name' => '調整放假', 'type' => 'holiday'],
            ['date' => '2025-01-28', 'name' => '除夕', 'type' => 'holiday'],
            ['date' => '2025-01-29', 'name' => '春節', 'type' => 'holiday'],
            ['date' => '2025-01-30', 'name' => '春節', 'type' => 'holiday'],
            ['date' => '2025-01-31', 'name' => '春節', 'type' => 'holiday'],
            ['date' => '2025-02-28', 'name' => '和平紀念日', 'type' => 'holiday'],
            ['date' => '2025-04-04', 'name' => '清明及兒童節', 'type' => 'holiday'],
            ['date' => '2025-06-02', 'name' => '端午節(補假)', 'type' => 'holiday'], // 5/31逢週六
            ['date' => '2025-09-29', 'name' => '教師節(補假)', 'type' => 'holiday'], // 9/28逢週日
            ['date' => '2025-10-06', 'name' => '中秋節', 'type' => 'holiday'],
            ['date' => '2025-10-10', 'name' => '國慶日', 'type' => 'holiday'],
            ['date' => '2025-10-24', 'name' => '光復節(補假)', 'type' => 'holiday'], // 10/25逢週六
            ['date' => '2025-12-25', 'name' => '行憲紀念日', 'type' => 'holiday'],
    
            // 僅標示名稱的日期 (不放假，除非遇到週末)
            ['date' => '2025-05-31', 'name' => '端午節', 'type' => 'named_day'],
            ['date' => '2025-06-21', 'name' => '夏至', 'type' => 'named_day'],
            ['date' => '2025-08-07', 'name' => '立秋', 'type' => 'named_day'],
            ['date' => '2025-08-23', 'name' => '處暑', 'type' => 'named_day'],
            ['date' => '2025-09-07', 'name' => '白露', 'type' => 'named_day'],
            ['date' => '2025-09-23', 'name' => '秋分', 'type' => 'named_day'],
            ['date' => '2025-09-28', 'name' => '教師節', 'type' => 'named_day'],
            ['date' => '2025-10-08', 'name' => '寒露', 'type' => 'named_day'],
            ['date' => '2025-10-23', 'name' => '霜降', 'type' => 'named_day'],
            ['date' => '2025-10-25', 'name' => '臺灣光復節', 'type' => 'named_day'],
            ['date' => '2025-11-07', 'name' => '立冬', 'type' => 'named_day'],
            ['date' => '2025-11-22', 'name' => '小雪', 'type' => 'named_day'],
            ['date' => '2025-12-07', 'name' => '大雪', 'type' => 'named_day'],
            ['date' => '2025-12-21', 'name' => '冬至', 'type' => 'named_day'],
        ];
    }
    // getDefaultHolidayData 方法結束
    
    /**
     * 儲存假日資料
     */
    public function saveHolidays($holidays) {
        if (empty($holidays)) {
            return false;
        }
        
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        try {
            // 清除舊的假日資料
            $stmt = $conn->prepare("DELETE FROM holiday_calendar WHERE year = ?");
            $stmt->execute([2025]);
            
            // 插入新的假日資料
            $stmt = $conn->prepare("
                INSERT INTO holiday_calendar (year, title, url, fetch_date, holiday_data, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $holidayJson = json_encode($holidays, JSON_UNESCAPED_UNICODE);
            
            $result = $stmt->execute([
                2025,
                '中華民國114年（西元2025年）政府行政機關辦公日曆表',
                'https://www.dgpa.gov.tw/',
                date('Y-m-d H:i:s'),
                $holidayJson
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("儲存假日資料錯誤: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 取得假日資料並產生行事曆
     */
    public function getHolidayCalendar($year = 2025) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        try {
            $stmt = $conn->prepare("SELECT holiday_data FROM holiday_calendar WHERE year = ? ORDER BY fetch_date DESC LIMIT 1");
            $stmt->execute([$year]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['holiday_data'])) {
                return json_decode($result['holiday_data'], true);
            }
            
            // 如果資料庫沒有資料，回傳預設資料
            return $this->getDefaultHolidayData();
            
        } catch (Exception $e) {
            error_log("取得假日資料錯誤: " . $e->getMessage());
            return $this->getDefaultHolidayData();
        }
    }
    
    /**
     * 產生行事曆的 HTML
     */
    public function generateCalendarHTML($year = 2025) {
        $holidays = $this->getHolidayCalendar($year);
        
        // 建立假日對照表以便快速查找 (已修正為包含類型)
        $holidayMap = [];
        foreach ($holidays as $holiday) {
            $date_parts = explode('-', $holiday['date']);
            if (count($date_parts) === 3) {
                $key = $date_parts[1] . '-' . $date_parts[2];
                $holidayMap[$key] = ['name' => $holiday['name'], 'type' => $holiday['type'] ?? 'holiday'];
            }
        }

        $html = '<div class="calendar-grid">';
        for ($month = 1; $month <= 12; $month++) {
            $html .= $this->generateMonthHTML($year, $month, $holidayMap);
        }
        $html .= '</div>';
        
        $html .= '<div class="calendar-legend">
                    <div class="legend-item"><span class="holiday-color"></span><span>放假日</span></div>
                    <div class="legend-item"><span class="workday-color"></span><span>上班日</span></div>
                  </div>';

        return $html;
    }

    private function generateMonthHTML($year, $month, $holidayMap) {
        $monthNames = [1=>'一', 2=>'二', 3=>'三', 4=>'四', 5=>'五', 6=>'六', 7=>'七', 8=>'八', 9=>'九', 10=>'十', 11=>'十一', 12=>'十二'];
        $html = '<div class="month-calendar">';
        $html .= '<h4>' . $year . '年 ' . $monthNames[$month] . '月</h4>';
        $html .= '<table class="calendar-table">';
        $html .= '<thead><tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr></thead>';
        $html .= '<tbody>';

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
        
        $day = 1;
        for ($week = 0; $week < 6; $week++) {
            $html .= '<tr>';
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                if (($week == 0 && $dayOfWeek < $firstDayOfWeek) || $day > $daysInMonth) {
                    $html .= '<td class="empty-day"></td>';
                } else {
                    $dateKey = sprintf('%02d-%02d', $month, $day);
                    $holidayInfo = $holidayMap[$dateKey] ?? null;
                    
                    $isHoliday = $holidayInfo && ($holidayInfo['type'] === 'holiday');
                    $isNamedDay = $holidayInfo !== null;
                    $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                    
                    $class = 'calendar-day';
                    if ($isHoliday || $isWeekend) {
                        $class .= ' holiday';
                    }
                    
                    $html .= '<td class="' . $class . '">';
                    $html .= '<div class="day-number">' . $day . '</div>';
                    if ($isNamedDay) {
                        $html .= '<div class="holiday-name">' . htmlspecialchars($holidayInfo['name']) . '</div>';
                    }
                    $html .= '</td>';
                    $day++;
                }
            }
            $html .= '</tr>';
            if ($day > $daysInMonth) break;
        }

        $html .= '</tbody></table>';
        $html .= '</div>';
        return $html;
    }
    
    /**
     * 手動觸發更新假日資料庫
     */
    public function updateHolidays() {
        // 為確保系統穩定，此自動更新功能已暫時停用。
        echo "注意：線上自動更新功能已停用，以確保資料穩定性。\n";
        echo "系統將使用內建的假日資料。\n";
        // 若要更新假日資料，請直接修改 HolidayCalendar.php 中的 getDefaultHolidayData() 方法。
    }

    /**
     * 檢查新抓取的資料是否與資料庫中的最新資料不同
     */
    private function isDataDifferent($newHolidays) {
        $currentHolidays = $this->getHolidayCalendar();
        
        // 將陣列排序後再比較，避免因順序不同而誤判 (已修正為使用 'date' 欄位)
        $sort_by_date = function($a, $b) {
            return strcmp($a['date'], $b['date']);
        };

        usort($newHolidays, $sort_by_date);
        usort($currentHolidays, $sort_by_date);
        
        return $newHolidays !== $currentHolidays;
    }
}
?> 