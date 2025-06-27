<?php
// app/Controllers/AdminController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
// 引入認證中介軟體，用於驗證使用者權限

class AdminController extends Controller {
    // 定義 AdminController 類別，繼承自 Controller 父類別
    
    public function __construct() {
        // 建構函數，當類別被實例化時自動執行
        // 確保使用者已登入且為管理員
        AuthMiddleware::requireLogin();
        // 呼叫認證中介軟體的 requireLogin 方法，檢查使用者是否已登入
        if (!$this->isAdmin()) {
            // 檢查當前使用者是否為管理員，如果不是則執行以下動作
            $this->redirect(BASE_URL . '?error=權限不足');
            // 重新導向到首頁並顯示權限不足的錯誤訊息
        }
    }
    // 建構函數結束
    
    public function dashboard() {
        // 定義管理後台首頁方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/dashboard', [
            // 呼叫視圖方法，載入管理後台首頁模板
            'title' => '管理後台'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // dashboard 方法結束
    
    public function users() {
        // 定義使用者管理頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/users', [
            // 呼叫視圖方法，載入使用者管理頁面模板
            'title' => '使用者管理'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // users 方法結束
    
    public function createUser() {
        // 定義新增使用者頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/create-user', [
            // 呼叫視圖方法，載入新增使用者頁面模板
            'title' => '新增使用者'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // createUser 方法結束
    
    public function editUser() {
        // 定義編輯使用者頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/edit-user', [
            // 呼叫視圖方法，載入編輯使用者頁面模板
            'title' => '編輯使用者'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // editUser 方法結束
    
    public function announcements() {
        // 定義公告管理頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/announcements', [
            // 呼叫視圖方法，載入公告管理頁面模板
            'title' => '公告管理'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // announcements 方法結束
    
    public function createAnnouncement() {
        // 定義新增公告頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/create-announcement', [
            // 呼叫視圖方法，載入新增公告頁面模板
            'title' => '新增公告'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // createAnnouncement 方法結束
    
    public function editAnnouncement() {
        // 定義編輯公告頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/edit-announcement', [
            // 呼叫視圖方法，載入編輯公告頁面模板
            'title' => '編輯公告'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // editAnnouncement 方法結束
} 
// AdminController 類別結束 