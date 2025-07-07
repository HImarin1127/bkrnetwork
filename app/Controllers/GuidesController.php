<?php
// app/Controllers/GuidesController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
// 引入認證中介軟體，用於驗證使用者權限

/**
 * 操作指引控制器
 * 
 * 處理各種系統操作指引頁面的顯示，包括：
 * - Windows 系統操作指引
 * - 印表機操作指引
 * - MAC 系統操作指引
 * - NAS 網路儲存操作指引
 * - 電子郵件操作指引
 * - 文化部免稅系統操作指引
 * 
 * 所有指引頁面都需要使用者登入才能存取
 */
class GuidesController extends Controller {
    // 定義 GuidesController 類別，繼承自 Controller 父類別
    
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
     * Windows 系統操作指引頁面
     * 
     * 顯示 Windows 作業系統的操作說明和使用指引
     * 包括基本操作、故障排除、軟體安裝等
     */
    public function windows() {
        // 定義 Windows 操作指引頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料，如當前使用者資訊、登入狀態等
        $this->view('guides/windows', [
            // 呼叫視圖方法，載入 Windows 操作指引頁面模板
            'title' => 'Windows 操作指引'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // windows 方法結束
    
    /**
     * 印表機操作指引頁面
     * 
     * 顯示印表機的操作說明和故障排除指引
     * 包括列印設定、驅動程式安裝、常見問題解決等
     */
    public function printer() {
        // 定義印表機操作指引頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('guides/printer', [
            // 呼叫視圖方法，載入印表機操作指引頁面模板
            'title' => '印表機操作指引'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // printer 方法結束
    
    /**
     * MAC 系統操作指引頁面
     * 
     * 顯示 MAC 作業系統的操作說明和使用指引
     * 包括基本操作、軟體安裝、系統設定等
     */
    public function mac() {
        // 定義 MAC 操作指引頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('guides/mac', [
            // 呼叫視圖方法，載入 MAC 操作指引頁面模板
            'title' => 'MAC 操作指引'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // mac 方法結束
    
    /**
     * NAS 網路儲存操作指引頁面
     * 
     * 顯示網路附加儲存設備（NAS）的使用說明
     * 包括檔案上傳下載、權限設定、遠端存取等
     */
    public function nas() {
        // 定義 NAS 操作指引頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('guides/nas', [
            // 呼叫視圖方法，載入 NAS 操作指引頁面模板
            'title' => 'NAS 操作指引'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // nas 方法結束
    
    /**
     * 文化部免稅系統操作指引頁面
     * 
     * 顯示文化部圖書免稅系統的操作說明
     * 包括免稅申請流程、系統操作步驟、注意事項等
     */
    public function taxExempt() {
        // 定義文化部免稅操作指引頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('guides/tax-exempt', [
            // 呼叫視圖方法，載入文化部免稅操作指引頁面模板
            'title' => '文化部免稅操作指引'
            // 設定頁面標題
        ]);
        // 視圖參數陣列結束
    }
    // taxExempt 方法結束
} 
// GuidesController 類別結束 