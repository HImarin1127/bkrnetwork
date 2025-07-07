<?php
// app/Controllers/BookingController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
// 引入認證中介軟體，用於驗證使用者權限

/**
 * 預約控制器
 * 
 * 處理各種資源預約功能，包括：
 * - 會議室預約
 * - 設備借用
 * 
 * 所有預約功能都需要使用者登入才能存取
 */
class BookingController extends Controller {
    // 定義 BookingController 類別，繼承自 Controller 父類別
    
    /**
     * 建構函式
     * 
     * 初始化控制器，確保使用者已登入
     * 未登入的使用者會被重新導向到登入頁面
     */
    public function __construct() {
        // 建構函數，當類別被實例化時自動執行
        AuthMiddleware::requireLogin();
        // 呼叫認證中介軟體的 requireLogin 方法，檢查使用者是否已登入
    }
    // 建構函數結束
    
    /**
     * 會議室預約頁面
     * 
     * 顯示會議室預約表單和當前預約狀況
     * 供員工預約各種會議室使用
     */
    public function meetingRoom() {
        // 定義會議室預約頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料，如當前使用者資訊、登入狀態等
        $this->view('booking/meeting-room', [
            // 呼叫視圖方法，載入會議室預約頁面模板
            'title' => '會議室預約'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // meetingRoom 方法結束
    
    /**
     * 設備借用頁面
     * 
     * 顯示設備借用申請表單和設備庫存狀況
     * 供員工借用各種辦公設備（如投影機、筆電等）
     */
    public function equipment() {
        // 定義設備借用頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('booking/equipment', [
            // 呼叫視圖方法，載入設備借用頁面模板
            'title' => '設備借用'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // equipment 方法結束

} 
// BookingController 類別結束 