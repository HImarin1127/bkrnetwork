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
            // 更新 CSV 檔案路徑為轉換後的檔案
        }
        // 編碼處理結束
        
        // 開啟並讀取 CSV 檔案
        if (($handle = fopen($csvFile, "r")) !== false) {
            // 使用 fopen 開啟檔案
            
            // 跳過標題行
            fgetcsv($handle);
            
            // 開始資料庫交易
            $db = Database::getInstance();
            // 取得資料庫實例
            $conn = $db->getConnection();
            // 取得資料庫連接
            $conn->beginTransaction();
            // 開始交易
            
            // 準備 SQL 插入語句
            $sql = "INSERT INTO mail_records (
                        mail_code, mail_type, receiver_name, receiver_address, 
                        receiver_phone, declare_department, sender_name, sender_ext,
                        item_count, postage, notes, status, registrar_id, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            // 準備 SQL 插入語句
            
            try {
                // 逐行讀取 CSV 資料
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    // 使用 fgetcsv 讀取一行資料
                    $lineNumber++;
                    // 行號加 1
                    
                    // 檢查基本欄位數量
                    if (count($data) < 7) {
                        // 如果欄位數量不足
                        $errors[] = "第 {$lineNumber} 行：欄位數量不足，必須至少包含 7 個欄位";
                        // 記錄錯誤訊息
                        continue;
                        // 繼續下一行處理
                    }
                    // 欄位數量檢查結束
                    
                    // 取得 CSV 欄位資料
                    $mailType = trim($data[0] ?? '');
                    // 寄件方式
                    $receiverName = trim($data[1] ?? '');
                    // 收件者
                    $receiverAddress = trim($data[2] ?? '');
                    // 收件地址
                    $receiverPhone = trim($data[3] ?? '');
                    // 收件者電話
                    $declareDepartment = trim($data[4] ?? '');
                    // 申報部門
                    $senderName = trim($data[5] ?? '');
                    // 寄件者
                    $senderExt = trim($data[6] ?? '');
                    // 寄件者分機
                    
                    // 驗證必要欄位
                    if (empty($mailType) || empty($receiverName)) {
                        // 如果寄件方式或收件者為空
                        $errors[] = "第 {$lineNumber} 行：寄件方式和收件者為必填欄位";
                        // 記錄錯誤訊息
                        continue;
                        // 繼續下一行處理
                    }
                    // 必要欄位驗證結束
                    
                    // 執行插入操作
                    $stmt->execute([
                        $this->generateMailCode(), // 自動生成寄件編號
                        $mailType,
                        $receiverName,
                        $receiverAddress,
                        $receiverPhone,
                        $declareDepartment,
                        $senderName,
                        $senderExt,
                        1,        // 預設件數
                        0,        // 預設郵資
                        '批次匯入', // 備註
                        '已送出',    // 預設狀態
                        $registrarId // 登記者ID
                    ]);
                    // 執行 SQL 插入
                    
                    $imported++;
                    // 成功匯入數量加 1
                }
                // CSV 讀取迴圈結束
                
                // 提交交易
                $conn->commit();
                // 如果所有操作都成功，提交交易
                
            } catch (Exception $e) {
                // 捕獲例外
                // 發生錯誤，回滾交易
                $conn->rollBack();
                // 回滾交易
                $errors[] = "處理第 {$lineNumber} 行時發生資料庫錯誤：" . $e->getMessage();
                // 記錄錯誤訊息
            }
            // try-catch 區塊結束
            
            // 關閉檔案
            fclose($handle);
            // 關閉檔案控制代碼
        } else {
            // 如果無法開啟檔案
            $errors[] = '無法開啟 CSV 檔案';
            // 記錄錯誤訊息
        }
        // 檔案處理結束
        
        // 刪除暫存的 UTF-8 檔案
        if (file_exists($csvFile . '.utf8')) {
            // 如果存在轉換後的檔案
            unlink($csvFile . '.utf8');
            // 刪除檔案
        }
        // 暫存檔案刪除結束
        
        // 回傳匯入結果
        return ['imported' => $imported, 'errors' => $errors];
    }
    // batchImport 方法結束
    
    /**
     * 檢查記錄權限
     * 
     * 確認目前使用者是否有權限查看或修改指定的郵件記錄
     * 
     * @param int $recordId 郵件記錄 ID
     * @param int $userId 使用者 ID
     * @param bool $isAdmin 是否為管理員
     * @return bool 有權限回傳 true，無權限回傳 false
     */
    public function checkPermission($recordId, $userId, $isAdmin = false) {
        // 定義檢查記錄權限方法
        if ($isAdmin) {
            // 如果是管理員，直接回傳 true
            return true;
        }
        // 管理員權限檢查結束
        
        // 查詢記錄的擁有者
        $record = $this->find($recordId);
        // 使用父類別的 find 方法查詢記錄
        
        // 檢查記錄是否存在且使用者是登記者或寄件者
        if ($record && ($record['registrar_id'] == $userId || (isset($record['sender_id']) && $record['sender_id'] == $userId))) {
            // 如果記錄存在，且目前使用者是登記者或寄件者
            return true;
            // 回傳 true
        }
        // 權限檢查結束
        
        return false;
        // 預設回傳 false
    }
    // checkPermission 方法結束
    
    /**
     * 搜尋郵件記錄
     * 
     * 根據關鍵字搜尋郵件記錄，並套用權限控制
     * 
     * @param string $keyword 搜尋關鍵字
     * @param int|null $userId 使用者 ID
     * @param bool $isAdmin 是否為管理員
     * @return array 符合條件的郵件記錄
     */
    public function search($keyword, $userId = null, $isAdmin = false) {
        // 定義搜尋郵件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        // 定義要搜尋的欄位
        $searchableFields = [
            'mail_code', 'mail_type', 'sender_name', 'sender_ext',
            'receiver_name', 'receiver_address', 'receiver_phone',
            'declare_department', 'tracking_number', 'status', 'notes'
        ];
        // 搜尋欄位陣列
        
        // 建立 SQL 查詢語句
        $sql = "SELECT * FROM {$this->table} WHERE (";
        // SQL 查詢語句開頭
        $conditions = [];
        // 條件陣列
        foreach ($searchableFields as $field) {
            // 遍歷所有可搜尋的欄位
            $conditions[] = "{$field} LIKE ?";
            // 加入 LIKE 條件
        }
        // 迴圈結束
        $sql .= implode(' OR ', $conditions) . ")";
        // 組合所有 LIKE 條件
        
        $params = array_fill(0, count($searchableFields), '%' . $keyword . '%');
        // 準備 LIKE 條件的參數
        
        // 權限控制
        if (!$isAdmin && $userId !== null) {
            // 如果不是管理員且提供了使用者 ID
            $sql .= " AND (registrar_id = ? OR sender_id = ?)";
            // 加入權限控制條件
            $params[] = $userId;
            // 加入使用者 ID 參數
            $params[] = $userId;
            // 加入使用者 ID 參數
        }
        // 權限控制結束
        
        $sql .= " ORDER BY created_at DESC";
        // 加入排序條件
        
        // 執行查詢
        $stmt = $conn->prepare($sql);
        // 準備 SQL 查詢語句
        $stmt->execute($params);
        // 執行查詢
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 回傳所有符合條件的記錄
    }
    // search 方法結束
    
    /**
     * 建立收件記錄
     * 
     * 這個方法會處理收件的登記，並儲存到 `incoming_mail_records` 資料表中。
     * - `incoming_mail_records` 資料表如果不存在，會自動建立。
     * - 記錄收件時間、收件人、寄件人、簽收狀態等。
     * 
     * @param array $data 收件記錄的資料
     * @return int 新建立的收件記錄 ID
     */
    public function createIncomingRecord($data) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // 檢查 `incoming_mail_records` 資料表是否存在，如果不存在就建立它
        $this->createIncomingTable();
        
        // 準備要插入的資料
        $insertData = [
            'received_at' => $data['received_at'] ?? date('Y-m-d H:i:s'),
            'recipient_id' => $data['recipient_id'],
            'sender_info' => $data['sender_info'],
            'mail_type' => $data['mail_type'],
            'notes' => $data['notes'] ?? '',
            'registered_by' => $data['registered_by'],
            'status' => '待簽收' // 預設狀態
        ];

        $fields = implode(', ', array_keys($insertData));
        $placeholders = implode(', ', array_fill(0, count($insertData), '?'));

        $sql = "INSERT INTO incoming_mail_records ({$fields}) VALUES ({$placeholders})";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(array_values($insertData));
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            // 可以在這裡記錄錯誤日誌
            error_log("建立收件記錄失敗: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 取得收件記錄
     * 
     * 根據使用者權限取得相關的收件記錄。
     * - 管理員 (`isAdmin` = true) 可以查看所有記錄。
     * - 一般使用者 (`userId`) 只能查看收件人是自己的記錄。
     * 
     * @param int|null $userId 目前登入的使用者ID
     * @param bool $isAdmin 是否為管理員
     * @return array 收件記錄列表
     */
    public function getIncomingRecords($userId = null, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // 檢查資料表是否存在
        $this->createIncomingTable();
        
        $sql = "SELECT imr.*, u.name as recipient_name 
                FROM incoming_mail_records imr
                LEFT JOIN users u ON imr.recipient_id = u.id";
        
        $params = [];
        
        if (!$isAdmin && $userId) {
            $sql .= " WHERE imr.recipient_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY imr.received_at DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("取得收件記錄失敗: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 建立收件記錄資料表
     * 
     * 如果 `incoming_mail_records` 資料表不存在，這個私有方法會自動建立它。
     * 包含收件記錄需要的各個欄位，並設定適當的索引。
     */
    private function createIncomingTable() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        try {
            $conn->query("SELECT 1 FROM incoming_mail_records LIMIT 1");
        } catch (PDOException $e) {
            // 資料表不存在，建立它
            $sql = "
            CREATE TABLE `incoming_mail_records` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `received_at` datetime NOT NULL,
              `recipient_id` int(11) NOT NULL,
              `sender_info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `mail_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
              `notes` text COLLATE utf8mb4_unicode_ci,
              `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '待簽收',
              `signed_at` datetime DEFAULT NULL,
              `registered_by` int(11) NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `recipient_id` (`recipient_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            try {
                $conn->exec($sql);
            } catch (PDOException $ex) {
                // 如果建立資料表失敗，記錄錯誤日誌
                error_log("建立 incoming_mail_records 資料表失敗: " . $ex->getMessage());
            }
        }
    }
    
    /**
     * 搜尋收件記錄
     * 
     * 根據提供的篩選條件搜尋收件記錄。
     * - `startDate`, `endDate`: 篩選收件日期範圍。
     * - `status`: 篩選簽收狀態。
     * - `keyword`: 模糊搜尋寄件人資訊和備註。
     * 
     * @param array $filters 篩選條件
     * @param int|null $userId 使用者ID
     * @param bool $isAdmin 是否為管理員
     * @return array 搜尋結果
     */
    public function searchIncomingRecords($filters, $userId = null, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT imr.*, u.name as recipient_name 
                FROM incoming_mail_records imr
                LEFT JOIN users u ON imr.recipient_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!$isAdmin && $userId) {
            $sql .= " AND imr.recipient_id = ?";
            $params[] = $userId;
        }

        if (!empty($filters['startDate'])) {
            $sql .= " AND imr.received_at >= ?";
            $params[] = $filters['startDate'] . ' 00:00:00';
        }

        if (!empty($filters['endDate'])) {
            $sql .= " AND imr.received_at <= ?";
            $params[] = $filters['endDate'] . ' 23:59:59';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND imr.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (imr.sender_info LIKE ? OR imr.notes LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }

        $sql .= " ORDER BY imr.received_at DESC";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("搜尋收件記錄失敗: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 取得使用者名稱
     * 
     * 根據使用者ID取得使用者名稱，用於在收件記錄中顯示登記人姓名。
     * 
     * @param int $userId 使用者ID
     * @return string|null 使用者名稱
     */
    private function getUserName($userId) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        try {
            $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['name'] ?? null;
        } catch (PDOException $e) {
            error_log("取得使用者名稱失敗: " . $e->getMessage());
            return null;
        }
    }
} 