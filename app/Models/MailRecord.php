<?php
// app/Models/MailRecord.php

namespace App\Models;

use PDO;
use Exception;

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
    public function createMailRecord($data, $registrarUsername) {
        // 自動生成唯一的郵件編號
        $data['mail_code'] = $this->generateMailCode();
        // 根據傳入的參數設定登記者名稱
        $data['registrar_username'] = $registrarUsername;
        
        // 設定預設值
        $data['status'] = $data['status'] ?? '已送出';
        $data['item_count'] = $data['item_count'] ?? 1;
        $data['postage'] = $data['postage'] ?? 0;
        
        $db = Database::getInstance();
        $conn = $db->getConnection();
    
        // 移除不應直接插入的欄位（以防萬一）
        unset($data['id']);

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
    
        $stmt = $conn->prepare($sql);
        if ($stmt->execute(array_values($data))) {
            // 成功後回傳新的郵件編號
            return $data['mail_code'];
        }

        return false;
    }
    
    /**
     * 根據使用者名稱取得郵件記錄
     * 
     * 實作權限控制：
     * - 管理員：可查看所有記錄
     * - 一般使用者：只能查看自己相關的記錄（registrar_username）
     * 
     * @param string $username 使用者名稱
     * @param bool $isAdmin 是否為管理員
     * @return array 郵件記錄陣列，按建立時間倒序排列
     */
    public function getByUsername($username, $isAdmin = false) {
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
            $stmt = $conn->prepare("
                SELECT * FROM {$this->table} 
                WHERE registrar_username = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$username]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 根據郵件編號查找單筆記錄
     * @param string $mailCode 郵件編號
     * @return array|false
     */
    public function find($mailCode) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE mail_code = ?");
        $stmt->execute([$mailCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 根據郵件編號更新記錄
     * @param string $mailCode 郵件編號
     * @param array $data 要更新的資料
     * @return bool
     */
    public function updateByMailCode($mailCode, $data) {
        if (empty($data)) {
            return false;
        }
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = ?";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE mail_code = ?";
        
        $values = array_values($data);
        $values[] = $mailCode;
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * 根據郵件編號刪除記錄
     * @param string $mailCode 郵件編號
     * @return bool
     */
    public function deleteByMailCode($mailCode) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM {$this->table} WHERE mail_code = ?");
        return $stmt->execute([$mailCode]);
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
     * @param string $registrarUsername 執行匯入操作的使用者名稱
     * @return array 包含錯誤訊息的陣列，如果沒有錯誤則為空陣列
     */
    public function batchImport($csvFile, $registrarUsername) {
        $errors = [];
        $importedCount = 0; // 新增成功計數器
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            try {
                $conn->beginTransaction();

                // 跳過標題行
                fgetcsv($handle);
                $lineNumber = 1;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $lineNumber++;
                    
                    // 根據 CSV 欄位順序對應到資料庫欄位
                    $record = [
                        'mail_type'          => $data[0] ?? null,
                        'sender_name'        => $data[1] ?? null,
                        'sender_ext'         => $data[2] ?? null,
                        'receiver_name'      => $data[3] ?? null,
                        'receiver_address'   => $data[4] ?? null,
                        'receiver_phone'     => $data[5] ?? null,
                        'declare_department' => $data[6] ?? null,
                        'item_count'         => isset($data[7]) && is_numeric($data[7]) ? (int)$data[7] : 1,
                        'postage'            => isset($data[8]) && is_numeric($data[8]) ? (float)$data[8] : 0,
                        'tracking_number'    => $data[9] ?? null,
                        'status'             => $data[10] ?? '已送出',
                        'notes'              => $data[11] ?? null,
                    ];

                    // 檢查必要欄位
                    if (empty($record['sender_name']) || empty($record['receiver_name']) || empty($record['receiver_address'])) {
                        $errors[] = "第 {$lineNumber} 行缺少必要欄位 (寄件人、收件人、收件地址)，已跳過。";
                        continue;
                    }

                    // 呼叫我們之前建立的 createMailRecord 方法
                    // 它會自動處理 mail_code 和 registrar_username
                    $this->createMailRecord($record, $registrarUsername);
                    $importedCount++; // 成功處理一筆，計數器加一
                }
                
                // 提交交易
                $conn->commit();

            } catch (Exception $e) {
                // 如果在交易過程中發生任何錯誤，則回滾
                $conn->rollBack();
                $errors[] = "處理 CSV 檔案時發生嚴重錯誤：" . $e->getMessage();
            } finally {
                // 無論成功或失敗，都確保檔案被關閉
                fclose($handle);
            }

        } else {
            // 如果無法開啟檔案
            $errors[] = '無法開啟上傳的 CSV 檔案。';
        }
        
        // 刪除伺服器上的暫存檔案
        if (file_exists($csvFile)) {
            unlink($csvFile);
        }

        // 回傳一個包含成功筆數和錯誤訊息的陣列
        return [
            'imported' => $importedCount,
            'errors' => $errors
        ];
    }

    /**
     * 檢查使用者是否有權限操作指定的郵件記錄
     * @param string $mailCode 郵件編號
     * @param string $username 使用者名稱
     * @param bool $isAdmin 是否為管理員
     * @return bool
     */
    public function checkPermission($mailCode, $username, $isAdmin = false) {
        if ($isAdmin) {
            return true;
        }
        
        $record = $this->find($mailCode);
        
        if (!$record) {
            return false;
        }
        
        return $record['registrar_username'] === $username;
    }
    
    /**
     * 搜尋郵件記錄
     * 
     * @param string $keyword 搜尋關鍵字
     * @param string|null $username 使用者名稱（用於權限控制）
     * @param bool $isAdmin 是否為管理員
     * @return array
     */
    public function search($keyword, $username = null, $isAdmin = false) {
        // 定義搜尋郵件記錄方法
        $db = Database::getInstance();
        // 取得資料庫實例
        $conn = $db->getConnection();
        // 取得資料庫連接
        
        $sql = "SELECT * FROM {$this->table} WHERE 
                (mail_code LIKE :keyword OR
                 sender_name LIKE :keyword OR
                 receiver_name LIKE :keyword OR
                 tracking_number LIKE :keyword OR
                 notes LIKE :keyword)";
        // 準備 SQL 查詢語句，使用 LIKE 進行模糊搜尋
        
        // 加入權限控制
        if (!$isAdmin && $username) {
            $sql .= " AND registrar_username = :username";
        }
        // 如果不是管理員，則只搜尋該使用者的記錄
        
        $sql .= " ORDER BY created_at DESC";
        // 加入排序條件
        
        $stmt = $conn->prepare($sql);
        // 準備 SQL 陳述式
        $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
        // 綁定關鍵字參數
        if (!$isAdmin && $username) {
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        }
        // 綁定使用者 ID 參數
        
        $stmt->execute();
        // 執行查詢
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 回傳所有結果
    }
    
    /**
     * 建立新的收件記錄
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