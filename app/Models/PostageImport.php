<?php
// app/Models/PostageImport.php

namespace App\Models;

use PDO;

class PostageImport extends Model {
    protected $table = 'postage_imports';

    /**
     * 批次匯入國揚郵資 CSV
     * @param string $csvFile
     * @return array 匯入結果與錯誤訊息
     */
    public function batchImport($csvFile) {
        $errors = [];
        $imported = 0;
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (($handle = fopen($csvFile, 'r')) !== false) {
            // 讀取並跳過標題行
            fgetcsv($handle);
            
            $line = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                
                // 依照欄位順序直接對應
                $data = [
                    'tracking_number'     => trim($row[2] ?? ''),
                    'original_tracking'   => trim($row[3] ?? null),
                    'batch_no'            => trim($row[4] ?? null),
                    'received_date'       => !empty(trim($row[5] ?? null)) ? date('Y-m-d H:i:s', strtotime(trim($row[5]))) : null,
                    'receiver_name'       => trim($row[7] ?? ''),
                    'address'             => trim($row[8] ?? ''),
                    'phone'               => trim($row[9] ?? null),
                    'cod_amount'          => is_numeric($row[10] ?? null) ? (float)$row[10] : 0,
                    'order_id'            => trim($row[11] ?? null),
                    'product_name'        => trim($row[12] ?? null),
                    'quantity'            => is_numeric($row[13] ?? null) ? (int)$row[13] : 1,
                    'status'              => trim($row[15] ?? null),
                    'size_weight'         => trim($row[16] ?? null),
                    'postage'             => is_numeric($row[17] ?? null) ? (float)$row[17] : 0,
                    'payment_status'      => trim($row[18] ?? null),
                    'cod_status'          => trim($row[19] ?? null),
                    'created_at'          => date('Y-m-d H:i:s'),
                ];

                // 簡單的必要欄位檢查
                if (empty($data['tracking_number']) && empty($data['original_tracking'])) {
                    $errors[] = "第{$line}行缺少物流編號/原物流編號，已跳過。";
                    continue;
                }

                // 使用 REPLACE INTO 語法，如果主鍵或唯一索引重複，則會覆蓋更新
                $sql = "REPLACE INTO {$this->table} (" . implode(',', array_keys($data)) . ") VALUES (" . str_repeat('?,', count($data)-1) . "?)";
                
                $stmt = $conn->prepare($sql);
                if ($stmt->execute(array_values($data))) {
                    $imported++;
                } else {
                    $errors[] = "第{$line}行匯入失敗：" . implode(', ', $stmt->errorInfo());
                }
            }
            fclose($handle);
        } else {
            $errors[] = '無法開啟上傳的 CSV 檔案。';
        }

        if (file_exists($csvFile)) {
            unlink($csvFile);
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * 查詢郵資資料
     * @param array $filters
     * @param bool $isGeneralAffair
     * @return array
     */
    public function search($filters = [], $ignorePermission = true) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $sql = "SELECT * FROM {$this->table}";
        $where = [];
        $params = [];

        if (!empty($filters['receiver_name'])) {
            $where[] = "receiver_name LIKE ?";
            $params[] = '%' . $filters['receiver_name'] . '%';
        }
        if (!empty($filters['tracking_number'])) {
            $where[] = "tracking_number = ?";
            $params[] = $filters['tracking_number'];
        }
        if (!empty($filters['original_tracking'])) {
            $where[] = "(original_tracking = ? OR original_tracking IS NULL)";
            $params[] = $filters['original_tracking'];
        }
        if (!empty($filters['order_id'])) {
            $where[] = "order_id = ?";
            $params[] = $filters['order_id'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = "DATE(created_at) >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = "DATE(created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 