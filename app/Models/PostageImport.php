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
            // 讀取標題行
            $header = fgetcsv($handle);
            $line = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                // 依照欄位順序對應
                $data = [
                    'tracking_number'     => $row[2] ?? '',
                    'original_tracking'   => $row[3] ?? null,
                    'batch_no'            => $row[4] ?? null,
                    'received_date'       => $row[5] ?? null,
                    'receiver_name'       => $row[7] ?? '',
                    'address'             => $row[8] ?? '',
                    'phone'               => $row[9] ?? null,
                    'cod_amount'          => is_numeric($row[10] ?? null) ? $row[10] : 0,
                    'order_id'            => $row[11] ?? null,
                    'product_name'        => $row[12] ?? null,
                    'quantity'            => is_numeric($row[13] ?? null) ? $row[13] : 1,
                    'status'              => $row[15] ?? null,
                    'size_weight'         => $row[16] ?? null,
                    'postage'             => is_numeric($row[17] ?? null) ? $row[17] : 0,
                    'payment_status'      => $row[18] ?? null,
                    'cod_status'          => $row[19] ?? null,
                    'created_at'          => date('Y-m-d H:i:s'),
                ];
                // 必要欄位檢查
                if (empty($data['tracking_number']) || empty($data['receiver_name']) || empty($data['address'])) {
                    $errors[] = "第{$line}行缺少必要欄位，已跳過。";
                    continue;
                }
                // 主鍵衝突時更新資料
                $sql = "REPLACE INTO {$this->table} (" . implode(',', array_keys($data)) . ") VALUES (" . str_repeat('?,', count($data)-1) . "?)";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute(array_values($data))) {
                    $imported++;
                } else {
                    $errors[] = "第{$line}行匯入失敗。";
                }
            }
            fclose($handle);
        } else {
            $errors[] = '無法開啟上傳的 CSV 檔案。';
        }
        if (file_exists($csvFile)) unlink($csvFile);
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