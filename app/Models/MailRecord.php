<?php
// app/Models/MailRecord.php

require_once __DIR__ . '/Model.php';

/**
 * 郵務記錄模型
 * 
 * 處理郵務系統的核心業務邏輯，包括：
 * - 寄件記錄的建立、查詢、管理
 * - 收件記錄的處理
 * - 郵件編號自動生成
 * - CSV 匯入匯出功能
 * - 權限控制和資料搜尋
 */
class MailRecord extends Model {
    /** @var string 寄件記錄資料表名稱 */
    protected $table = 'mail_records';
    
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
        // 建立日期前綴：POST + YYYYMMDD
        $dateStr = date('Ymd');
        $prefix = "POST{$dateStr}";
        
        // 查詢當天最大的郵件編號
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("
            SELECT MAX(mail_code) as max_code
            FROM {$this->table}
            WHERE mail_code LIKE ?
        ");
        $like = "{$prefix}%";
        $stmt->execute([$like]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 解析最後的流水號
        $lastSeq = 0;
        if ($result && $result['max_code']) {
            $lastSeq = intval(substr($result['max_code'], -4));
        }
        
        // 生成新的 4 位數流水號
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newSeq;
    }
    
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
        // 自動生成唯一的郵件編號
        $data['mail_code'] = $this->generateMailCode();
        
        // 設定預設值
        $data['status'] = $data['status'] ?? '已送出';
        $data['item_count'] = $data['item_count'] ?? 1;
        $data['postage'] = $data['postage'] ?? 0;
        
        return $this->create($data);
    }
    
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
        $imported = 0;    // 成功匯入的記錄數
        $errors = [];     // 錯誤訊息列表
        $lineNumber = 1;  // 當前處理的行號（從標題行開始計算）
        
        if (($handle = fopen($csvFile, 'r')) !== false) {
            // 跳過標題行
            fgetcsv($handle);
            $lineNumber++;
            
            // 逐行處理 CSV 資料
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $lineNumber++;
                
                // 檢查資料完整性（至少 7 個欄位）
                if (count($row) < 7) {
                    $errors[] = "第 {$lineNumber} 行：欄位數量不足（需要至少 7 個欄位）";
                    continue;
                }
                
                // 清理資料（移除前後空白）
                $data = array_map('trim', $row);
                
                // 解析 CSV 欄位
                list(
                    $mail_type,
                    $receiver_name,
                    $receiver_address,
                    $receiver_phone,
                    $declare_department,
                    $sender_name,
                    $sender_ext
                ) = $data;
                
                // 必填欄位檢查
                if (empty($mail_type) || empty($receiver_name)) {
                    $missingFields = [];
                    if (empty($mail_type)) $missingFields[] = '寄件方式';
                    if (empty($receiver_name)) $missingFields[] = '收件者姓名';
                    $errors[] = "第 {$lineNumber} 行：缺少必填欄位：" . implode('、', $missingFields);
                    continue;
                }
                
                // 驗證寄件方式
                $validMailTypes = ['掛號', '黑貓', '新竹貨運'];
                if (!in_array($mail_type, $validMailTypes)) {
                    $errors[] = "第 {$lineNumber} 行：寄件方式「{$mail_type}」不正確，請使用：" . implode('、', $validMailTypes);
                    continue;
                }
                
                try {
                    // 建立郵件記錄
                    $this->createMailRecord([
                        'mail_type' => $mail_type,
                        'receiver_name' => $receiver_name,
                        'receiver_address' => $receiver_address,
                        'receiver_phone' => $receiver_phone,
                        'declare_department' => $declare_department,
                        'sender_name' => $sender_name,
                        'sender_ext' => $sender_ext,
                        'registrar_id' => $registrarId,
                        'status' => '已送出'
                    ]);
                    
                    $imported++;
                } catch (Exception $e) {
                    $errors[] = "第 {$lineNumber} 行匯入失敗：" . $e->getMessage();
                }
            }
            
            fclose($handle);
        } else {
            $errors[] = "無法讀取 CSV 檔案";
        }
        
        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }
    
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
        // 管理員有所有權限
        if ($isAdmin) {
            return true;
        }
        
        // 檢查記錄是否存在
        $record = $this->find($recordId);
        if (!$record) {
            return false;
        }
        
        return $record['registrar_id'] == $userId || $record['sender_id'] == $userId;
    }
    
    /**
     * 搜尋郵件記錄
     */
    public function search($keyword, $userId = null, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM {$this->table} WHERE 
                (mail_code LIKE ? OR 
                 sender_name LIKE ? OR 
                 receiver_name LIKE ? OR 
                 receiver_address LIKE ?)";
        
        $params = ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%"];
        
        if (!$isAdmin && $userId) {
            $sql .= " AND (registrar_id = ? OR sender_id = ?)";
            $params[] = $userId;
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 建立收件記錄
     */
    public function createIncomingRecord($data) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // 建立收件記錄表（如果不存在）
        $this->createIncomingTable();
        
        $sql = "INSERT INTO incoming_mail_records (
            tracking_number, mail_type, sender_name, sender_company,
            recipient_name, recipient_department, received_date, received_time,
            content_description, urgent, notes, registrar_id, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            $data['tracking_number'],
            $data['mail_type'],
            $data['sender_name'],
            $data['sender_company'],
            $data['recipient_name'],
            $data['recipient_department'],
            $data['received_date'],
            $data['received_time'],
            $data['content_description'],
            $data['urgent'],
            $data['notes'],
            $data['registrar_id'],
            $data['status']
        ]);
        
        return $result ? $conn->lastInsertId() : false;
    }
    
    /**
     * 取得收件記錄
     */
    public function getIncomingRecords($userId = null, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // 確保收件記錄表存在
        $this->createIncomingTable();
        
        if ($isAdmin) {
            $sql = "SELECT * FROM incoming_mail_records ORDER BY received_date DESC, received_time DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM incoming_mail_records WHERE registrar_id = ? ORDER BY received_date DESC, received_time DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 搜尋收件記錄
     */
    public function searchIncomingRecords($filters, $userId = null, $isAdmin = false) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // 確保收件記錄表存在
        $this->createIncomingTable();
        
        $sql = "SELECT * FROM incoming_mail_records WHERE 1=1";
        $params = [];
        
        if (!empty($filters['keyword'])) {
            $sql .= " AND (sender_name LIKE ? OR recipient_name LIKE ? OR content_description LIKE ? OR tracking_number LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params = array_merge($params, [$keyword, $keyword, $keyword, $keyword]);
        }
        
        if (!empty($filters['dateFrom'])) {
            $sql .= " AND received_date >= ?";
            $params[] = $filters['dateFrom'];
        }
        
        if (!empty($filters['dateTo'])) {
            $sql .= " AND received_date <= ?";
            $params[] = $filters['dateTo'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if (!$isAdmin && $userId) {
            $sql .= " AND registrar_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY received_date DESC, received_time DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
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
} 