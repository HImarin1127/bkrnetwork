<?php
// app/Models/Database.php

/**
 * 資料庫連接類別
 * 
 * 實作單例模式的資料庫連接管理器
 * 提供 PDO 連接和基本的資料庫操作方法
 */
class Database {
    /** @var Database|null 單例實例 */
    private static $instance = null;
    
    /** @var PDO 資料庫連接物件 */
    private $connection;
    
    /**
     * 私有建構函式
     * 
     * 建立資料庫連接，使用設定檔中的連接參數
     * 設定 PDO 錯誤處理模式和字元編碼
     * 
     * @throws Exception 當資料庫連接失敗時拋出例外
     */
    private function __construct() {
        // 載入資料庫設定
        $config = require __DIR__ . '/../../config/database.php';
        
        // 建構 DSN 字串
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        
        try {
            // 建立 PDO 連接
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            throw new Exception('資料庫連接失敗: ' . $e->getMessage());
        }
    }
    
    /**
     * 取得資料庫實例（單例模式）
     * 
     * 確保整個應用程式只有一個資料庫連接實例
     * 
     * @return Database 資料庫實例
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 取得 PDO 連接物件
     * 
     * 提供直接存取底層 PDO 連接的方法
     * 用於需要進階 PDO 功能的情況
     * 
     * @return PDO PDO 連接物件
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * 執行 SQL 查詢並回傳 PDOStatement
     * 
     * 準備並執行 SQL 語句，支援參數綁定
     * 
     * @param string $sql SQL 查詢語句
     * @param array $params 參數陣列，用於綁定 SQL 中的占位符
     * @return PDOStatement 執行後的 PDOStatement 物件
     */
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * 執行查詢並回傳單筆記錄
     * 
     * 適用於預期只會回傳一筆記錄的查詢
     * 
     * @param string $sql SQL 查詢語句
     * @param array $params 參數陣列
     * @return array|false 找到記錄時回傳關聯陣列，找不到時回傳 false
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * 執行查詢並回傳所有記錄
     * 
     * 適用於需要回傳多筆記錄的查詢
     * 
     * @param string $sql SQL 查詢語句
     * @param array $params 參數陣列
     * @return array 所有記錄的關聯陣列集合
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * 執行 SQL 語句並回傳受影響的記錄數
     * 
     * 適用於 INSERT、UPDATE、DELETE 等修改資料的操作
     * 
     * @param string $sql SQL 語句
     * @param array $params 參數陣列
     * @return int 受影響的記錄數量
     */
    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * 取得最後插入記錄的 ID
     * 
     * 適用於 INSERT 操作後取得新記錄的主鍵值
     * 
     * @return string 最後插入記錄的 ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
} 