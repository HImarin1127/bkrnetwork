<?php
// app/Models/HolidayCalendar.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Model.php';
// 引入父類別 Model.php，使用 require_once 確保只載入一次

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
        // 定義從政府網站抓取假日資料方法
        // 2025年(民國114年)政府行政機關辦公日曆表
        $calendarUrl = 'https://www.dgpa.gov.tw/files/page/64/3925/114-government-holidays.pdf';
        // PDF 檔案的直接下載連結
        $infoUrl = 'https://www.dgpa.gov.tw/informationlist?uid=30';
        // 人事行政總處的假日資訊頁面
        
        // 首先嘗試抓取PDF或網頁內容
        $holidays = $this->tryFetchCalendarContent($infoUrl);
        // 呼叫內部方法嘗試抓取假日內容
        
        if (empty($holidays)) {
            // 如果失敗，回傳預設假日資料
            $holidays = $this->getDefaultHolidayData();
            // 網路抓取失敗時，使用本地預設的假日資料
        }
        // 條件判斷結束
        
        return $holidays;
        // 回傳假日資料陣列
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
            // 檢查請求是否成功且有回傳內容
            return $this->parseHolidayContent($html);
            // 解析網頁內容中的假日資訊
        }
        // 條件判斷結束
        
        return [];
        // 請求失敗時回傳空陣列
    }
    // tryFetchCalendarContent 方法結束
    
    /**
     * 解析假日內容
     * 
     * 使用正規表達式從 HTML 內容中提取假日資訊
     * 解析格式：月日 + 假日名稱
     * 
     * @param string $html 網頁 HTML 內容
     * @return array 解析後的假日資料陣列
     */
    private function parseHolidayContent($html) {
        // 定義解析假日內容的私有方法
        $holidays = [];
        // 初始化假日資料陣列
        
        // 嘗試從HTML中提取假日資訊
        if (preg_match_all('/(\d{1,2})月(\d{1,2})日[^，]*?([^（]*?)(?:（|$)/u', $html, $matches, PREG_SET_ORDER)) {
            // 使用正規表達式匹配「X月Y日假日名稱」的格式
            foreach ($matches as $match) {
                // 逐一處理每個匹配結果
                $month = intval($match[1]);
                // 提取月份並轉為整數
                $day = intval($match[2]);
                // 提取日期並轉為整數
                $name = trim($match[3]);
                // 提取假日名稱並去除空白
                
                if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31 && !empty($name)) {
                    // 驗證月份、日期的有效性和假日名稱非空
                    $holidays[] = [
                        // 加入假日資料到陣列中
                        'month' => $month,
                        'day' => $day,
                        'name' => $name,
                        'type' => 'holiday'
                    ];
                    // 假日資料陣列結束
                }
                // 條件判斷結束
            }
            // 迴圈結束
        }
        // 正規表達式匹配結束
        
        return $holidays;
        // 回傳解析後的假日資料陣列
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
        // 定義取得預設假日資料的私有方法
        return [
            // 回傳 2025 年完整的法定假日清單
            ['month' => 1, 'day' => 1, 'name' => '中華民國開國紀念日', 'type' => 'holiday'],
            ['month' => 1, 'day' => 27, 'name' => '調整放假', 'type' => 'holiday'],
            ['month' => 1, 'day' => 28, 'name' => '農曆除夕', 'type' => 'holiday'],
            ['month' => 1, 'day' => 29, 'name' => '春節', 'type' => 'holiday'],
            ['month' => 1, 'day' => 30, 'name' => '春節', 'type' => 'holiday'],
            ['month' => 1, 'day' => 31, 'name' => '春節', 'type' => 'holiday'],
            ['month' => 2, 'day' => 3, 'name' => '春節', 'type' => 'holiday'],
            ['month' => 2, 'day' => 28, 'name' => '和平紀念日', 'type' => 'holiday'],
            ['month' => 4, 'day' => 4, 'name' => '兒童節', 'type' => 'holiday'],
            ['month' => 4, 'day' => 5, 'name' => '民族掃墓節（清明節）', 'type' => 'holiday'],
            ['month' => 5, 'day' => 1, 'name' => '勞動節', 'type' => 'holiday'],
            ['month' => 5, 'day' => 30, 'name' => '調整放假', 'type' => 'holiday'],
            ['month' => 5, 'day' => 31, 'name' => '端午節', 'type' => 'holiday'],
            ['month' => 10, 'day' => 6, 'name' => '中秋節', 'type' => 'holiday'],
            ['month' => 10, 'day' => 10, 'name' => '國慶日', 'type' => 'holiday']
        ];
        // 預設假日資料陣列結束
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
                '中華民國115年（西元2026年）政府行政機關辦公日曆表',
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
     * 產生HTML行事曆表格
     */
    public function generateCalendarHTML($year = 2025) {
        $holidays = $this->getHolidayCalendar($year);
        
        // 建立假日對照表
        $holidayMap = [];
        foreach ($holidays as $holiday) {
            $key = sprintf('%02d-%02d', $holiday['month'], $holiday['day']);
            $holidayMap[$key] = $holiday['name'];
        }
        
        $html = '<div class="holiday-calendar">';
        $html .= '<h3>中華民國115年（西元2026年）政府行政機關辦公日曆表</h3>';
        
        // 產生12個月的行事曆
        for ($month = 1; $month <= 12; $month++) {
            $html .= $this->generateMonthHTML($year, $month, $holidayMap);
        }
        
        $html .= '<div class="calendar-legend">';
        $html .= '<div class="legend-item"><span class="holiday-color"></span> 放假日</div>';
        $html .= '<div class="legend-item"><span class="workday-color"></span> 上班日</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 產生單月HTML
     */
    private function generateMonthHTML($year, $month, $holidayMap) {
        $monthNames = [
            1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六',
            7 => '七', 8 => '八', 9 => '九', 10 => '十', 11 => '十一', 12 => '十二'
        ];
        
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
        
        $html = '<div class="month-calendar">';
        $html .= '<h4>' . $monthNames[$month] . '月</h4>';
        $html .= '<table class="calendar-table">';
        $html .= '<tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr>';
        
        $day = 1;
        for ($week = 0; $week < 6; $week++) {
            $html .= '<tr>';
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                if (($week == 0 && $dayOfWeek < $firstDayOfWeek) || $day > $daysInMonth) {
                    $html .= '<td class="empty-day"></td>';
                } else {
                    $dateKey = sprintf('%02d-%02d', $month, $day);
                    $isHoliday = isset($holidayMap[$dateKey]);
                    $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                    
                    $class = 'calendar-day';
                    if ($isHoliday || $isWeekend) {
                        $class .= ' holiday';
                    }
                    
                    $html .= '<td class="' . $class . '">';
                    $html .= '<div class="day-number">' . $day . '</div>';
                    if ($isHoliday) {
                        $html .= '<div class="holiday-name">' . $holidayMap[$dateKey] . '</div>';
                    }
                    $html .= '</td>';
                    $day++;
                }
            }
            $html .= '</tr>';
            
            if ($day > $daysInMonth) break;
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 更新假日資料
     */
    public function updateHolidays() {
        $holidays = $this->fetchGovernmentHolidays();
        $saved = $this->saveHolidays($holidays);
        
        if ($saved) {
            return [
                'status' => 'success', 
                'message' => '成功更新假日行事曆',
                'count' => count($holidays)
            ];
        } else {
            return ['status' => 'error', 'message' => '儲存資料失敗'];
        }
    }
}
?> 