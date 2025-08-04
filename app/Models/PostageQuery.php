<?php
// app/Models/PostageQuery.php

namespace App\Models;

use PDO;

class PostageQuery extends Model {
    protected $table = 'postage_imports';

    /**
     * 多條件查詢郵資資料（參考 MailRecord::search）
     * @param array $filters
     * @return array
     */
    public function search($filters = [], $page = 1, $perPage = 10) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // 基礎 SQL 和 WHERE 條件 (用於計數和查詢)
        $baseSql = "FROM {$this->table} WHERE 1=1";
        $whereSql = "";
        $params = [];

        if (!empty($filters['receiver_name'])) {
            $whereSql .= " AND receiver_name LIKE ?";
            $params[] = '%' . $filters['receiver_name'] . '%';
        }
        if (!empty($filters['tracking_number_combined'])) {
            $whereSql .= " AND (tracking_number = ? OR original_tracking = ?)";
            $params[] = $filters['tracking_number_combined'];
            $params[] = $filters['tracking_number_combined'];
        }
        if (!empty($filters['order_id'])) {
            $whereSql .= " AND order_id = ?";
            $params[] = $filters['order_id'];
        }
        if (!empty($filters['start_date'])) {
            $whereSql .= " AND DATE(received_date) >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $whereSql .= " AND DATE(received_date) <= ?";
            $params[] = $filters['end_date'];
        }

        // 1. 取得總筆數
        $totalSql = "SELECT COUNT(*) " . $baseSql . $whereSql;
        $totalStmt = $conn->prepare($totalSql);
        $totalStmt->execute($params);
        $totalRecords = (int) $totalStmt->fetchColumn();

        // 2. 取得分頁資料
        $offset = ($page - 1) * $perPage;
        $dataSql = "SELECT 
                        tracking_number, original_tracking, batch_no, received_date, receiver_name,
                        address, phone, order_id, status, size_weight, postage, cod_status, created_at
                    " . $baseSql . $whereSql . " ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $conn->prepare($dataSql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'records' => $records,
            'total' => $totalRecords,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($totalRecords / $perPage)
        ];
    }
} 