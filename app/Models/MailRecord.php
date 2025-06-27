<?php
// app/Models/MailRecord.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Model.php';
// 引入父類別 Model.php，使用 require_once 確保只載入一次

/**
 * 郵務記錄模型
 * 
 * 處理郵務系統的核心業務邏輯，包括：
 * - 寄件記錄的建立、查詢、管理
 * - 收件記錄的處理
 * - 郵件編號自動生成
 * - CSV 匯入匯出功能
 * - 權限控制和資料搜尋
 * 
 * 繼承基礎模型類別，擴充郵務管理特有的業務邏輯
 */
class MailRecord extends Model {
    // 定義 MailRecord 類別，繼承自 Model 父類別
    
    /** @var string 寄件記錄資料表名稱 */
    protected $table = 'mail_records';
    // 宣告受保護的成員變數，指定對應的資料庫資料表名稱
    
    /**
     * 生成新的郵件編號
     * 
     * 自動生成唯一的郵件編號，格式：POST{YYYYMMDD}{流水號}
     * 例如：POST202501010001
     * 
     * 邏輯：
     * 1. 根據當天日期建立前綴
     * 2. 查詢當天最大的流水號
     * 3. 產生新的 4 位數流水號
     * 
     * @return string 新的郵件編號
     */
    public function generateMailCode() {
        // 定義生成郵件編號的方法
        // 建立日期前綴：POST + YYYYMMDD
        $dateStr = date('Ymd');
        // 取得今天的日期，格式為 YYYYMMDD
        $prefix = "POST{$dateStr}";
        // 組合郵件編號的前綴部分
        
        // 查詢當天最大的郵件編號
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        $stmt = $conn->prepare("
            SELECT MAX(mail_code) as max_code
            FROM {$this->table}
            WHERE mail_code LIKE ?
        ");
        // 準備 SQL 查詢語句，查詢今天已使用的最大郵件編號
        $like = "{$prefix}%";
        // 設定 LIKE 查詢條件，匹配今天的所有郵件編號
        $stmt->execute([$like]);
        // 執行查詢
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // 取得查詢結果
        
        // 解析最後的流水號
        $lastSeq = 0;
        // 初始化流水號為 0
        if ($result && $result['max_code']) {
            // 如果找到已存在的郵件編號
            $lastSeq = intval(substr($result['max_code'], -4));
            // 提取最後 4 位數字作為流水號
        }
        // 條件判斷結束
        
        // 生成新的 4 位數流水號
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        // 將流水號加 1 並補零到 4 位數
        return $prefix . $newSeq;
        // 回傳完整的新郵件編號
    }
    // generateMailCode 方法結束
    
    /**
     * 建立新的郵件記錄
     * 
     * 建立寄件記錄並自動處理以下項目：
     * - 自動生成郵件編號
     * - 設定預設狀態和數量
     * - 處理郵資計算
     * 
     * @param array $data 郵件記錄資料
     * @return int 新建立的記錄 ID
     */
    public function createMailRecord($data) {
        // 定義建立新郵件記錄方法
        // 自動生成唯一的郵件編號
        $data['mail_code'] = $this->generateMailCode();
        // 呼叫郵件編號生成方法，設定郵件編號欄位
        
        // 設定預設值
        $data['status'] = $data['status'] ?? '已送出';
        // 如果未設定狀態，預設為「已送出」
        $data['item_count'] = $data['item_count'] ?? 1;
        // 如果未設定件數，預設為 1 件
        $data['postage'] = $data['postage'] ?? 0;
        // 如果未設定郵資，預設為 0 元
        
        return $this->create($data);
        // 呼叫父類別的 create 方法新增記錄，並回傳新建立的記錄 ID
    }
    // createMailRecord 方法結束
    
    /**
     * 根據使用者ID取得郵件記錄
     * 
     * 實作權限控制：
     * - 管理員：可查看所有記錄
     * - 一般使用者：只能查看自己相關的記錄（registrar_id 或 sender_id）
     * 
     * @param int $userId 使用者 ID
     * @param bool $isAdmin 是否為管理員
     * @return array 郵件記錄陣列，按建立時間倒序排列
     */
    public function getByUserId($userId, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        if ($isAdmin) {
            // 管理員可以查看所有記錄
            $stmt = $conn->prepare("
                SELECT * FROM {$this->table} 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
        } else {
            // 一般使用者只能查看自己相關的記錄
            // registrar_id：登記者 ID
            // sender_id：寄件者 ID（如果不同人）
            $stmt = $conn->prepare("
                SELECT * FROM {$this->table} 
                WHERE registrar_id = ? OR sender_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId, $userId]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 匯出郵件記錄為 CSV 檔案
     * 
     * 功能特色：
     * - 支援中文字元正確顯示（UTF-8 BOM）
     * - 自動處理 CSV 特殊字元轉義
     * - 包含完整的郵件記錄欄位
     * - 檔名包含匯出日期
     */
    public function exportToCsv() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        
        // 設定 CSV 檔案的 HTTP 標頭
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="mail_records_' . date('Y-m-d') . '.csv"');
        
        // 輸出 UTF-8 BOM，確保 Excel 正確顯示中文
        echo "\xEF\xBB\xBF";
        
        // 輸出 CSV 標題行
        echo "寄件編號,寄件方式,寄件者,寄件者分機,收件者,收件地址,收件者電話,申報部門,件數,郵資,追蹤號碼,狀態,備註,登記時間\n";
        
        // 逐行輸出資料
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $line = [
                $row['mail_code'] ?? '',
                $row['mail_type'] ?? '',
                $row['sender_name'] ?? '',
                $row['sender_ext'] ?? '',
                $row['receiver_name'] ?? '',
                $row['receiver_address'] ?? '',
                $row['receiver_phone'] ?? '',
                $row['declare_department'] ?? '',
                $row['item_count'] ?? 1,
                $row['postage'] ?? 0,
                $row['tracking_number'] ?? '',
                $row['status'] ?? '',
                $row['notes'] ?? '',
                $row['created_at'] ?? ''
            ];
            
            // 處理 CSV 特殊字元（逗號、換行、雙引號）
            $escapedLine = array_map(function($field) {
                if (strpos($field, ',') !== false || strpos($field, "\n") !== false || strpos($field, '"') !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $line);
            
            echo implode(',', $escapedLine) . "\n";
        }
        
        exit;
    }
    
    /**
     * 批次匯入郵件記錄
     * 
     * 從 CSV 檔案批次匯入郵件記錄
     * 
     * CSV 格式要求：
     * - 第一行為標題行（會被跳過）
     * - 至少包含 7 個欄位：寄件方式、收件者、收件地址、收件者電話、申報部門、寄件者、寄件者分機
     * - 必填欄位：寄件方式、收件者
     * 
     * @param string $csvFile CSV 檔案路徑
     * @param int $registrarId 登記者 ID
     * @return array 匯入結果，包含 imported（成功數量）和 errors（錯誤列表）
     */
    public function batchImport($csvFile, $registrarId) {
        // 定義批次匯入郵件記錄方法
        $imported = 0;    // 成功匯入的記錄數
        $errors = [];     // 錯誤訊息列表
        $lineNumber = 1;  // 當前處理的行號（從標題行開始計算）
        
        // 檢查檔案是否存在
        if (!file_exists($csvFile)) {
            // 如果檔案不存在，回傳錯誤
            return [
                'imported' => 0,
                'errors' => ['CSV 檔案不存在或無法存取']
            ];
        }
        // 檔案存在性檢查結束
        
        // 偵測檔案編碼並處理
        $content = file_get_contents($csvFile);
        // 讀取檔案內容
        $encoding = mb_detect_encoding($content, ['UTF-8', 'Big5', 'CP950', 'GB2312'], true);
        // 偵測檔案編碼
        if ($encoding && $encoding !== 'UTF-8') {
            // 如果不是 UTF-8 編碼，進行轉換
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // 轉換為 UTF-8 編碼
            file_put_contents($csvFile . '.utf8', $content);
            // 儲存轉換後的檔案
            $csvFile = $csvFile . '.utf8';
            // 使用轉換後的檔案
        }
        // 編碼處理結束
        
        if (($handle = fopen($csvFile, 'r')) !== false) {
            // 如果檔案開啟成功
            // 設定檔案編碼
            stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
            // 加入編碼過濾器，忽略無效字元
            
            // 跳過標題行
            $headerRow = fgetcsv($handle, 0, ',');
            // 讀取標題行，設定無字元限制
            $lineNumber++;
            // 行號增加
            
            // 驗證標題行是否正確
            if (!$headerRow || count($headerRow) < 7) {
                // 如果標題行格式不正確
                fclose($handle);
                // 關閉檔案
                return [
                    'imported' => 0,
                    'errors' => ['CSV 檔案格式不正確，標題行至少需要 7 個欄位']
                ];
            }
            // 標題行驗證結束
            
            // 逐行處理 CSV 資料
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // 讀取每一行資料，設定無字元限制
                $lineNumber++;
                // 行號增加
                
                // 跳過空行
                if (empty(array_filter($row))) {
                    // 如果是空行則跳過
                    continue;
                }
                // 空行檢查結束
                
                // 檢查資料完整性（至少 7 個欄位）
                if (count($row) < 7) {
                    // 如果欄位數量不足
                    $errors[] = "第 {$lineNumber} 行：欄位數量不足（需要至少 7 個欄位，實際 " . count($row) . " 個）";
                    continue;
                }
                // 欄位數量檢查結束
                
                // 清理資料（移除前後空白和特殊字元）
                $data = array_map(function($field) {
                    // 處理每個欄位
                    $field = trim($field);
                    // 移除前後空白
                    $field = str_replace(["\r", "\n", "\t"], '', $field);
                    // 移除換行符號和製表符
                    return $field;
                }, $row);
                // 資料清理結束
                
                // 解析 CSV 欄位
                list(
                    $mail_type,          // 寄件方式
                    $receiver_name,      // 收件者姓名
                    $receiver_address,   // 收件地址
                    $receiver_phone,     // 收件者電話
                    $declare_department, // 申報部門
                    $sender_name,        // 寄件者姓名
                    $sender_ext          // 寄件者分機
                ) = $data;
                // 欄位解析結束
                
                // 必填欄位檢查
                $missingFields = [];
                // 初始化缺少欄位列表
                if (empty($mail_type)) $missingFields[] = '寄件方式';
                if (empty($receiver_name)) $missingFields[] = '收件者姓名';
                if (empty($receiver_address)) $missingFields[] = '收件地址';
                // 檢查必填欄位
                
                if (!empty($missingFields)) {
                    // 如果有缺少的必填欄位
                    $errors[] = "第 {$lineNumber} 行：缺少必填欄位：" . implode('、', $missingFields);
                    continue;
                }
                // 必填欄位檢查結束
                
                // 驗證寄件方式
                $validMailTypes = ['掛號', '黑貓', '新竹貨運', '郵局掛號', '宅急便'];
                // 允許的寄件方式列表
                if (!in_array($mail_type, $validMailTypes)) {
                    // 如果寄件方式不在允許列表中
                    $errors[] = "第 {$lineNumber} 行：寄件方式「{$mail_type}」不正確，請使用：" . implode('、', $validMailTypes);
                    continue;
                }
                // 寄件方式驗證結束
                
                // 驗證收件地址長度
                if (strlen($receiver_address) > 500) {
                    // 如果地址太長
                    $errors[] = "第 {$lineNumber} 行：收件地址過長（超過 500 字元）";
                    continue;
                }
                // 地址長度檢查結束
                
                try {
                    // 準備要插入的資料
                    $recordData = [
                        'mail_type' => $mail_type,
                        'receiver_name' => $receiver_name,
                        'receiver_address' => $receiver_address,
                        'receiver_phone' => $receiver_phone,
                        'declare_department' => $declare_department,
                        'sender_name' => $sender_name,
                        'sender_ext' => $sender_ext,
                        'registrar_id' => $registrarId,
                        'sender_id' => $registrarId,  // 修復：設定 sender_id
                        'status' => '已送出',
                        'item_count' => 1,           // 預設件數
                        'postage' => 0               // 預設郵資
                    ];
                    // 記錄資料準備結束
                    
                    // 建立郵件記錄
                    $recordId = $this->createMailRecord($recordData);
                    // 呼叫建立記錄方法
                    
                    if ($recordId) {
                        // 如果記錄建立成功
                        $imported++;
                        // 成功匯入數量增加
                    } else {
                        // 如果記錄建立失敗
                        $errors[] = "第 {$lineNumber} 行：記錄建立失敗";
                    }
                    // 記錄建立檢查結束
                    
                } catch (Exception $e) {
                    // 捕獲建立記錄時的例外
                    $errors[] = "第 {$lineNumber} 行匯入失敗：" . $e->getMessage();
                }
                // 例外處理結束
            }
            // CSV 資料處理迴圈結束
            
            fclose($handle);
            // 關閉檔案
            
            // 清理臨時檔案
            if (strpos($csvFile, '.utf8') !== false) {
                // 如果有建立編碼轉換的臨時檔案
                unlink($csvFile);
                // 刪除臨時檔案
            }
            // 臨時檔案清理結束
            
        } else {
            // 如果檔案開啟失敗
            $errors[] = "無法讀取 CSV 檔案，請檢查檔案是否被其他程式佔用";
        }
        // 檔案開啟檢查結束
        
        return [
            'imported' => $imported,
            'errors' => $errors
        ];
        // 回傳匯入結果
    }
    // batchImport 方法結束
    
    /**
     * 檢查使用者是否有權限查看/編輯記錄
     * 
     * 權限規則：
     * - 管理員：可操作所有記錄
     * - 一般使用者：只能操作自己相關的記錄
     * 
     * @param int $recordId 記錄 ID
     * @param int $userId 使用者 ID
     * @param bool $isAdmin 是否為管理員
     * @return bool 有權限回傳 true，否則回傳 false
     */
    public function checkPermission($recordId, $userId, $isAdmin = false) {
        // 定義權限檢查方法
        // 管理員有所有權限
        if ($isAdmin) {
            // 如果是管理員，直接回傳 true
            return true;
        }
        // 管理員權限檢查結束
        
        // 檢查記錄是否存在
        $record = $this->find($recordId);
        // 使用父類別的 find 方法查詢記錄
        if (!$record) {
            // 如果記錄不存在，回傳 false
            return false;
        }
        // 記錄存在性檢查結束
        
        return $record['registrar_id'] == $userId || $record['sender_id'] == $userId;
        // 檢查使用者是否為登記者或寄件者，符合其中之一即有權限
    }
    // checkPermission 方法結束
    
    /**
     * 搜尋郵件記錄
     * 
     * 根據關鍵字搜尋郵件記錄，支援多欄位模糊搜尋
     * 搜尋範圍包括：郵件編號、寄件者姓名、收件者姓名、收件地址
     * 
     * @param string $keyword 搜尋關鍵字
     * @param int|null $userId 使用者 ID（用於權限控制）
     * @param bool $isAdmin 是否為管理員
     * @return array 符合搜尋條件的記錄陣列
     */
    public function search($keyword, $userId = null, $isAdmin = false) {
        // 定義搜尋郵件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        $sql = "SELECT * FROM {$this->table} WHERE 
                (mail_code LIKE ? OR 
                 sender_name LIKE ? OR 
                 receiver_name LIKE ? OR 
                 receiver_address LIKE ?)";
        // 建立 SQL 查詢語句，使用 LIKE 進行模糊搜尋
        // 搜尋欄位：郵件編號、寄件者姓名、收件者姓名、收件地址
        
        $params = ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%"];
        // 準備參數陣列，在關鍵字前後加上 % 以進行模糊搜尋
        
        if (!$isAdmin && $userId) {
            // 如果不是管理員且有提供使用者 ID，加入權限限制
            $sql .= " AND (registrar_id = ? OR sender_id = ?)";
            // 加入權限檢查條件：只能搜尋自己相關的記錄
            $params[] = $userId;
            $params[] = $userId;
            // 將使用者 ID 加入參數陣列
        }
        // 權限控制結束
        
        $sql .= " ORDER BY created_at DESC";
        // 加入排序條件：按建立時間降冪排序
        
        $stmt = $conn->prepare($sql);
        // 準備 SQL 語句
        $stmt->execute($params);
        // 執行查詢
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 回傳所有符合條件的記錄
    }
    // search 方法結束
    
    /**
     * 建立收件記錄
     * 
     * 建立新的收件記錄到 incoming_mail_records 資料表
     * 如果資料表不存在會自動建立
     * 
     * @param array $data 收件記錄資料陣列
     * @return int|false 成功時回傳新記錄的 ID，失敗時回傳 false
     */
    public function createIncomingRecord($data) {
        // 定義建立收件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        // 建立收件記錄表（如果不存在）
        $this->createIncomingTable();
        // 呼叫內部方法確保收件記錄表存在
        
        $sql = "INSERT INTO incoming_mail_records (
            tracking_number, mail_type, sender_name, sender_company,
            recipient_name, recipient_department, received_date, received_time,
            content_description, urgent, notes, registrar_id, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        // 建立插入收件記錄的 SQL 語句
        
        $stmt = $conn->prepare($sql);
        // 準備 SQL 語句
        $result = $stmt->execute([
            // 執行 SQL 語句並傳入資料
            $data['tracking_number'],     // 追蹤號碼
            $data['mail_type'],           // 郵件類型
            $data['sender_name'],         // 寄件者姓名
            $data['sender_company'],      // 寄件者公司
            $data['recipient_name'],      // 收件者姓名
            $data['recipient_department'], // 收件者部門
            $data['received_date'],       // 收件日期
            $data['received_time'],       // 收件時間
            $data['content_description'], // 內容描述
            $data['urgent'],              // 是否緊急
            $data['notes'],               // 備註
            $data['registrar_id'],        // 登記者 ID
            $data['status']               // 狀態
        ]);
        // SQL 執行結束
        
        return $result ? $conn->lastInsertId() : false;
        // 如果執行成功回傳新記錄的 ID，否則回傳 false
    }
    // createIncomingRecord 方法結束
    
    /**
     * 取得收件記錄
     * 
     * 根據使用者權限取得收件記錄列表
     * 管理員可查看所有記錄，一般使用者只能查看自己的記錄
     * 
     * @param int|null $userId 使用者 ID
     * @param bool $isAdmin 是否為管理員
     * @return array 收件記錄陣列，按收件時間倒序排列
     */
    public function getIncomingRecords($userId = null, $isAdmin = false) {
        // 定義取得收件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        try {
            // 使用 try-catch 處理可能的資料表不存在錯誤
            if ($isAdmin) {
                // 管理員可以查看所有收件記錄
                $stmt = $conn->prepare("
                    SELECT * FROM incoming_mail_records 
                    ORDER BY received_date DESC, received_time DESC
                ");
                // 準備查詢所有記錄的 SQL 語句
                $stmt->execute();
                // 執行查詢
            } else {
                // 一般使用者只能查看自己登記的記錄
                $stmt = $conn->prepare("
                    SELECT * FROM incoming_mail_records 
                    WHERE registrar_id = ? OR recipient_name LIKE ?
                    ORDER BY received_date DESC, received_time DESC
                ");
                // 準備查詢特定使用者記錄的 SQL 語句
                // 可查看自己登記的記錄或以自己為收件者的記錄
                $userName = $userId ? $this->getUserName($userId) : '';
                // 取得使用者姓名用於比對收件者
                $stmt->execute([$userId, "%{$userName}%"]);
                // 執行查詢
            }
            // 權限控制結束
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            // 回傳查詢結果
        } catch (PDOException $e) {
            // 捕獲 PDO 例外（例如資料表不存在）
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                // 如果是資料表不存在的錯誤
                $this->createIncomingTable();
                // 建立收件記錄表
                return [];
                // 回傳空陣列
            }
            // 資料表不存在處理結束
            throw $e;
            // 重新拋出其他例外
        }
        // 例外處理結束
    }
    // getIncomingRecords 方法結束
    
    /**
     * 建立收件記錄表
     */
    private function createIncomingTable() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $sql = "CREATE TABLE IF NOT EXISTS incoming_mail_records (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tracking_number VARCHAR(255),
            mail_type VARCHAR(50) NOT NULL,
            sender_name VARCHAR(100) NOT NULL,
            sender_company VARCHAR(200),
            recipient_name VARCHAR(100) NOT NULL,
            recipient_department VARCHAR(100),
            received_date DATE NOT NULL,
            received_time TIME,
            content_description TEXT,
            urgent TINYINT(1) DEFAULT 0,
            notes TEXT,
            status VARCHAR(50) DEFAULT '已收件',
            registrar_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_received_date (received_date),
            INDEX idx_status (status),
            INDEX idx_registrar (registrar_id),
            INDEX idx_tracking (tracking_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
    }
    
    /**
     * 搜尋收件記錄
     * 
     * 根據多種條件搜尋收件記錄
     * 支援關鍵字、日期範圍、狀態等複合搜尋條件
     * 
     * @param array $filters 搜尋條件陣列
     * @param int|null $userId 使用者 ID（用於權限控制）
     * @param bool $isAdmin 是否為管理員
     * @return array 符合搜尋條件的收件記錄陣列
     */
    public function searchIncomingRecords($filters, $userId = null, $isAdmin = false) {
        // 定義搜尋收件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        // 確保收件記錄表存在
        $this->createIncomingTable();
        // 呼叫內部方法確保資料表存在
        
        $sql = "SELECT * FROM incoming_mail_records WHERE 1=1";
        // 建立基本 SQL 查詢語句，使用 WHERE 1=1 便於後續條件拼接
        $params = [];
        // 初始化參數陣列
        
        if (!empty($filters['keyword'])) {
            // 如果有關鍵字搜尋條件
            $sql .= " AND (sender_name LIKE ? OR recipient_name LIKE ? OR content_description LIKE ? OR tracking_number LIKE ?)";
            // 加入關鍵字搜尋條件：寄件者、收件者、內容描述、追蹤號碼
            $keyword = "%{$filters['keyword']}%";
            // 準備模糊搜尋的關鍵字
            $params = array_merge($params, [$keyword, $keyword, $keyword, $keyword]);
            // 將關鍵字參數加入參數陣列（4個欄位各需要一個參數）
        }
        // 關鍵字搜尋條件結束
        
        if (!empty($filters['dateFrom'])) {
            // 如果有起始日期條件
            $sql .= " AND received_date >= ?";
            // 加入起始日期條件
            $params[] = $filters['dateFrom'];
            // 將起始日期加入參數陣列
        }
        // 起始日期條件結束
        
        if (!empty($filters['dateTo'])) {
            // 如果有結束日期條件
            $sql .= " AND received_date <= ?";
            // 加入結束日期條件
            $params[] = $filters['dateTo'];
            // 將結束日期加入參數陣列
        }
        // 結束日期條件結束
        
        if (!empty($filters['status'])) {
            // 如果有狀態篩選條件
            $sql .= " AND status = ?";
            // 加入狀態條件
            $params[] = $filters['status'];
            // 將狀態加入參數陣列
        }
        // 狀態條件結束
        
        if (!$isAdmin && $userId) {
            // 如果不是管理員且有提供使用者 ID，加入權限限制
            $sql .= " AND registrar_id = ?";
            // 加入權限檢查條件：只能搜尋自己登記的記錄
            $params[] = $userId;
            // 將使用者 ID 加入參數陣列
        }
        // 權限控制結束
        
        $sql .= " ORDER BY received_date DESC, received_time DESC";
        // 加入排序條件：按收件日期和時間降冪排序
        
        $stmt = $conn->prepare($sql);
        // 準備 SQL 語句
        $stmt->execute($params);
        // 執行查詢
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 回傳搜尋結果
    }
    // searchIncomingRecords 方法結束
    
    /**
     * 取得使用者姓名
     * 
     * 根據使用者 ID 取得使用者的姓名
     * 用於收件記錄的權限檢查
     * 
     * @param int $userId 使用者 ID
     * @return string 使用者姓名，找不到時回傳空字串
     */
    private function getUserName($userId) {
        // 定義取得使用者姓名的私有方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        try {
            $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
            // 準備查詢使用者姓名的 SQL 語句
            $stmt->execute([$userId]);
            // 執行查詢
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // 取得查詢結果
            
            return $result ? $result['name'] : '';
            // 如果找到使用者回傳姓名，否則回傳空字串
        } catch (PDOException $e) {
            // 捕獲查詢過程中的例外
            error_log("查詢使用者姓名失敗: " . $e->getMessage());
            // 記錄錯誤訊息到系統日誌
            return '';
            // 發生錯誤時回傳空字串
        }
        // 例外處理結束
    }
    // getUserName 方法結束
} 