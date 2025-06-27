<?php
// app/Models/Announcement.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Model.php';
// 引入父類別 Model.php，使用 require_once 確保只載入一次

/**
 * 公告模型
 * 
 * 處理公告相關的資料操作，包括：
 * - 一般公告管理
 * - 假日公告處理
 * - 員工手冊內容管理
 * - 公告狀態控制
 * 
 * 繼承基礎模型類別，擴充公告特有的業務邏輯
 */
class Announcement extends Model {
    // 定義 Announcement 類別，繼承自 Model 父類別
    
    /** @var string 資料表名稱 */
    protected $table = 'announcements';
    // 宣告受保護的成員變數，指定對應的資料庫資料表名稱
    
    /**
     * 取得公開的一般公告
     * 
     * 查詢已發布的一般類型公告，按建立時間倒序排列
     * 主要用於首頁和公告列表頁面顯示
     * 
     * @param int $limit 限制回傳的公告數量，預設為 10 筆
     * @return array 公告資料陣列集合
     */
    public function getPublicAnnouncements($limit = 10) {
        // 定義取得公開一般公告方法
        return $this->where(
            // 使用父類別的 where 方法進行條件查詢
            ['status' => 'published', 'type' => 'general'], 
            // 查詢條件：狀態為已發布且類型為一般公告
            'created_at DESC', 
            // 排序條件：按建立時間降冪排序（最新的在前）
            $limit
            // 限制查詢結果數量
        );
        // 方法呼叫結束
    }
    // getPublicAnnouncements 方法結束
    
    /**
     * 取得假日公告
     * 
     * 查詢已發布的假日類型公告，按日期倒序排列
     * 用於假日資訊頁面顯示
     * 
     * @return array 假日公告資料陣列集合
     */
    public function getHolidayAnnouncements() {
        // 定義取得假日公告方法
        return $this->where(
            // 使用父類別的 where 方法進行條件查詢
            ['status' => 'published', 'type' => 'holiday'], 
            // 查詢條件：狀態為已發布且類型為假日公告
            'date DESC'
            // 排序條件：按日期降冪排序（最新的在前）
        );
        // 方法呼叫結束
    }
    // getHolidayAnnouncements 方法結束
    
    /**
     * 取得員工手冊內容
     * 
     * 查詢已發布的員工手冊類型內容，按排序順序排列
     * 用於員工手冊頁面顯示
     * 
     * @return array 員工手冊內容資料陣列集合
     */
    public function getHandbookContents() {
        // 定義取得員工手冊內容方法
        return $this->where(
            // 使用父類別的 where 方法進行條件查詢
            ['status' => 'published', 'type' => 'handbook'], 
            // 查詢條件：狀態為已發布且類型為員工手冊
            'sort_order ASC'
            // 排序條件：按排序順序升冪排列（依照設定的順序顯示）
        );
        // 方法呼叫結束
    }
    // getHandbookContents 方法結束
    
    /**
     * 建立新公告
     * 
     * 建立新的公告記錄，自動加入時間戳記
     * 
     * @param array $data 公告資料陣列
     * @return int 新建立的公告 ID
     */
    public function createAnnouncement($data) {
        // 定義建立新公告方法
        $data['created_at'] = date('Y-m-d H:i:s');
        // 設定記錄建立時間為當前時間
        $data['updated_at'] = date('Y-m-d H:i:s');
        // 設定記錄更新時間為當前時間
        
        return $this->create($data);
        // 呼叫父類別的 create 方法新增公告資料，並回傳新建立的公告 ID
    }
    // createAnnouncement 方法結束
    
    /**
     * 更新公告資料
     * 
     * 更新指定公告的資料，自動更新時間戳記
     * 
     * @param int $id 公告 ID
     * @param array $data 要更新的資料陣列
     * @return int 受影響的記錄數量
     */
    public function updateAnnouncement($id, $data) {
        // 定義更新公告資料方法
        $data['updated_at'] = date('Y-m-d H:i:s');
        // 設定記錄更新時間為當前時間
        
        return $this->update($id, $data);
        // 呼叫父類別的 update 方法更新公告資料，並回傳受影響的記錄數量
    }
    // updateAnnouncement 方法結束
    
    /**
     * 根據類型取得公告
     * 
     * 查詢指定類型的所有公告，按建立時間倒序排列
     * 用於管理介面的公告分類顯示
     * 
     * @param string $type 公告類型（general、holiday、handbook 等）
     * @return array 指定類型的公告資料陣列集合
     */
    public function getAnnouncementsByType($type) {
        // 定義根據類型取得公告方法
        return $this->where(['type' => $type], 'created_at DESC');
        // 使用父類別的 where 方法查詢指定類型的公告，按建立時間降冪排序
    }
    // getAnnouncementsByType 方法結束
    
    /**
     * 切換公告發布狀態
     * 
     * 在已發布（published）和草稿（draft）狀態之間切換
     * 用於管理介面快速啟用或停用公告
     * 
     * @param int $id 公告 ID
     * @return int|false 成功時回傳受影響的記錄數量，失敗時回傳 false
     */
    public function toggleStatus($id) {
        // 定義切換公告狀態方法
        $announcement = $this->find($id);
        // 使用父類別的 find 方法根據 ID 查詢公告資料
        if ($announcement) {
            // 檢查公告是否存在
            $newStatus = $announcement['status'] === 'published' ? 'draft' : 'published';
            // 判斷當前狀態，如果是已發布則改為草稿，否則改為已發布
            return $this->updateAnnouncement($id, ['status' => $newStatus]);
            // 呼叫自己的 updateAnnouncement 方法更新狀態
        }
        // 條件判斷結束
        return false;
        // 公告不存在時回傳 false
    }
    // toggleStatus 方法結束
} 
// Announcement 類別結束 