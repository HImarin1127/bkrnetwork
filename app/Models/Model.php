<?php
// app/Models/Model.php

/**
 * 模型基礎類別
 * 
 * 提供所有模型的共用功能，包括基本的 CRUD 操作
 * 實作 MVC 架構中模型層的核心功能，封裝資料庫操作
 */
abstract class Model {
    /** @var Database 資料庫連接實例 */
    protected $db;
    
    /** @var string 資料表名稱，子類別必須定義 */
    protected $table;
    
    /**
     * 建構函式
     * 
     * 初始化資料庫連接
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * 根據 ID 查詢單筆記錄
     * 
     * @param int $id 記錄的主鍵 ID
     * @return array|false 找到記錄時回傳關聯陣列，找不到時回傳 false
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * 根據指定欄位查詢單筆記錄
     * 
     * @param string $field 欄位名稱
     * @param mixed $value 欄位值
     * @return array|false 找到記錄時回傳關聯陣列，找不到時回傳 false
     */
    public function findBy($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        return $this->db->fetch($sql, [$value]);
    }
    
    /**
     * 查詢所有記錄
     * 
     * @return array 所有記錄的關聯陣列集合
     */
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * 根據條件查詢記錄
     * 
     * 支援複雜查詢條件、排序和限制
     * 
     * @param array $conditions 查詢條件陣列，格式：['欄位名' => '值']
     * @param string $orderBy 排序條件，例如：'created_at DESC'
     * @param string $limit 限制條件，例如：'10' 或 '10, 20'
     * @return array 符合條件的記錄陣列
     */
    public function where($conditions = [], $orderBy = '', $limit = '') {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // 處理查詢條件
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }
        
        // 處理排序
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        // 處理限制
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * 新增記錄
     * 
     * @param array $data 要新增的資料陣列，格式：['欄位名' => '值']
     * @return int 新增記錄的 ID
     */
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }
    
    /**
     * 更新記錄
     * 
     * @param int $id 要更新的記錄 ID
     * @param array $data 要更新的資料陣列，格式：['欄位名' => '值']
     * @return int 受影響的記錄數量
     */
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * 刪除記錄
     * 
     * @param int $id 要刪除的記錄 ID
     * @return int 受影響的記錄數量
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
} 