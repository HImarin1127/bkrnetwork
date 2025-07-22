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
    public function search($filters = []) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        if (!empty($filters['receiver_name'])) {
            $sql .= " AND receiver_name LIKE ?";
            $params[] = '%' . $filters['receiver_name'] . '%';
        }
        if (!empty($filters['tracking_number'])) {
            $sql .= " AND tracking_number = ?";
            $params[] = $filters['tracking_number'];
        }
        if (!empty($filters['original_tracking'])) {
            $sql .= " AND (original_tracking = ? OR original_tracking IS NULL)";
            $params[] = $filters['original_tracking'];
        }
        if (!empty($filters['order_id'])) {
            $sql .= " AND order_id = ?";
            $params[] = $filters['order_id'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['end_date'];
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 