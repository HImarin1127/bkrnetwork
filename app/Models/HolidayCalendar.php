<?php
// app/Models/HolidayCalendar.php
class HolidayCalendar extends Model {
    protected $table = 'holiday_calendar';
    
    /**
     * 從政府網站抓取完整的假日行事曆內容
     */
    public function fetchGovernmentHolidays() {
        // 2025年(民國114年)政府行政機關辦公日曆表
        $calendarUrl = 'https://www.dgpa.gov.tw/files/page/64/3925/114-government-holidays.pdf';
        $infoUrl = 'https://www.dgpa.gov.tw/informationlist?uid=30';
        
        // 首先嘗試抓取PDF或網頁內容
        $holidays = $this->tryFetchCalendarContent($infoUrl);
        
        if (empty($holidays)) {
            // 如果失敗，回傳預設假日資料
            $holidays = $this->getDefaultHolidayData();
        }
        
        return $holidays;
    }
    
    /**
     * 嘗試抓取行事曆內容
     */
    private function tryFetchCalendarContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $html) {
            return $this->parseHolidayContent($html);
        }
        
        return [];
    }
    
    /**
     * 解析假日內容
     */
    private function parseHolidayContent($html) {
        $holidays = [];
        
        // 嘗試從HTML中提取假日資訊
        if (preg_match_all('/(\d{1,2})月(\d{1,2})日[^，]*?([^（]*?)(?:（|$)/u', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $month = intval($match[1]);
                $day = intval($match[2]);
                $name = trim($match[3]);
                
                if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31 && !empty($name)) {
                    $holidays[] = [
                        'month' => $month,
                        'day' => $day,
                        'name' => $name,
                        'type' => 'holiday'
                    ];
                }
            }
        }
        
        return $holidays;
    }
    
    /**
     * 取得預設假日資料（2025年實際假日）
     */
    private function getDefaultHolidayData() {
        return [
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
    }
    
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