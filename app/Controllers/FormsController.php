<?php
// app/Controllers/FormsController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
// 引入認證中介軟體，用於驗證使用者權限

/**
 * 行政表單控制器
 * 
 * 處理各種行政申請表單的顯示，包括：
 * - 購書申請
 * - 人員異動申請
 * - 請假申請
 * - 加班申請
 * - 出差申請
 * - 教育訓練申請
 * - 設備採購申請
 * 
 * 所有表單頁面都需要使用者登入才能存取
 */
class FormsController extends Controller {
    // 定義 FormsController 類別，繼承自 Controller 父類別
    
    /**
     * 建構函式
     * 
     * 初始化控制器，確保使用者已登入
     * 未登入的使用者會被重新導向到登入頁面
     */
    public function __construct() {
        // 建構函數，當類別被實例化時自動執行
        // 確保使用者已登入
        AuthMiddleware::requireLogin();
        // 呼叫認證中介軟體的 requireLogin 方法，檢查使用者是否已登入
    }
    // 建構函數結束
    
    /**
     * 購書申請表單頁面
     * 
     * 顯示購書申請表單，供員工申請購買業務相關書籍
     */
    public function bookRequest() {
        // 定義購書申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料，如當前使用者資訊、登入狀態等
        $this->view('forms/book-request', [
            // 呼叫視圖方法，載入購書申請表單頁面模板
            'title' => '購書申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // bookRequest 方法結束
    
    /**
     * 人員異動申請表單頁面
     * 
     * 顯示人員異動申請表單，處理員工調動、升遷等異動申請
     */
    public function personnelChange() {
        // 定義人員異動申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/personnel-change', [
            // 呼叫視圖方法，載入人員異動申請表單頁面模板
            'title' => '人員異動申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // personnelChange 方法結束
    
    /**
     * 請假申請表單頁面
     * 
     * 顯示請假申請表單，供員工申請各種假別
     * 包括特休、病假、事假等
     */
    public function leaveRequest() {
        // 定義請假申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/leave-request', [
            // 呼叫視圖方法，載入請假申請表單頁面模板
            'title' => '請假申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // leaveRequest 方法結束
    
    /**
     * 加班申請表單頁面
     * 
     * 顯示加班申請表單，供員工申請加班時數
     * 包括平日加班、假日加班等
     */
    public function overtimeRequest() {
        // 定義加班申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/overtime-request', [
            // 呼叫視圖方法，載入加班申請表單頁面模板
            'title' => '加班申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // overtimeRequest 方法結束
    
    /**
     * 出差申請表單頁面
     * 
     * 顯示出差申請表單，供員工申請公務出差
     * 包括國內出差、國外出差等
     */
    public function businessTrip() {
        // 定義出差申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/business-trip', [
            // 呼叫視圖方法，載入出差申請表單頁面模板
            'title' => '出差申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // businessTrip 方法結束
    
    /**
     * 教育訓練申請表單頁面
     * 
     * 顯示教育訓練申請表單，供員工申請參加教育訓練課程
     * 包括內訓、外訓、研習會等
     */
    public function educationTraining() {
        // 定義教育訓練申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/education-training', [
            // 呼叫視圖方法，載入教育訓練申請表單頁面模板
            'title' => '教育訓練申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // educationTraining 方法結束
    
    /**
     * 設備採購申請表單頁面
     * 
     * 顯示設備採購申請表單，供員工申請購買辦公設備
     * 包括電腦、印表機、辦公用品等
     */
    public function equipmentPurchase() {
        // 定義設備採購申請頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('forms/equipment-purchase', [
            // 呼叫視圖方法，載入設備採購申請表單頁面模板
            'title' => '設備採購申請'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // equipmentPurchase 方法結束
} 
// FormsController 類別結束 