<?php
// config/database.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

/**
 * 資料庫連接設定檔案
 * 
 * 定義 MySQL 資料庫的連接參數和 PDO 選項
 * 此設定檔會被 Database 類別載入使用
 * 
 * 設定內容包括：
 * - 資料庫主機位址和連接資訊
 * - PDO 連接選項和行為設定
 * - 字元編碼設定
 */

return [
    // ========================================
    // 資料庫連接基本資訊
    // ========================================
    
    'host' => 'localhost',
    // MySQL 資料庫主機位址，localhost 的 IP 形式
    
    'username' => 'root',
    // 資料庫使用者名稱，本地開發環境預設為 root
    
    'password' => '',
    // 資料庫密碼，本地開發環境通常為空字串
    
    'database' => 'bkrnetwork',
    // 要連接的資料庫名稱，BKR Network 專案的主要資料庫
    
    'charset' => 'utf8mb4',
    // 字元編碼設定，utf8mb4 支援完整的 UTF-8 字元集（包含 emoji）
    
    // ========================================
    // PDO 連接選項設定
    // ========================================
    
    'options' => [
        // PDO 選項陣列，用於設定資料庫連接的行為
        
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // 錯誤處理模式：拋出例外，便於錯誤追蹤和處理
        
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // 預設的資料提取模式：關聯陣列（鍵值對應欄位名稱）
        
        PDO::ATTR_EMULATE_PREPARES => false,
        // 關閉預處理語句模擬，使用原生的預處理語句（更安全、效能更好）
    ]
    // PDO 選項陣列結束
]; 
// 設定陣列結束 