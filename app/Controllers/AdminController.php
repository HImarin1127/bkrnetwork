<?php
// app/Controllers/AdminController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
// 引入認證中介軟體，用於驗證使用者權限
require_once __DIR__ . '/../Models/User.php';
// 引入使用者模型
require_once __DIR__ . '/../Models/Announcement.php';
// 引入公告模型

/**
 * 管理員控制器
 * 
 * 處理所有管理員相關的頁面和功能，包括：
 * - 管理員後台首頁
 * - 使用者管理（新增、編輯、查看）
 * - 公告管理（新增、編輯、查看）
 * 
 * 所有方法都需要管理員權限才能存取
 * 
 * @package BKRNetwork\Controllers
 * @author BKR Network Team
 * @since 1.0.0
 */
class AdminController extends Controller {
    // 定義 AdminController 類別，繼承自 Controller 父類別
    
    /** @var User 使用者模型實例 */
    private $userModel;
    
    /** @var Announcement 公告模型實例 */
    private $announcementModel;
    
    /**
     * 建構函數
     * 
     * 初始化管理員控制器，確保使用者已登入且具有管理員權限
     * 如果使用者非管理員，會自動重新導向到首頁並顯示錯誤訊息
     * 
     * @throws Exception 當使用者未登入時
     */
    public function __construct() {
        // 建構函數，當類別被實例化時自動執行
        // 確保使用者已登入
        AuthMiddleware::requireLogin();
        // 呼叫認證中介軟體的 requireLogin 方法，檢查使用者是否已登入
        
        // 初始化模型
        $this->userModel = new User();
        $this->announcementModel = new Announcement();
    }
    
    /**
     * 檢查當前方法是否需要特殊權限
     */
    private function checkMethodPermission($method) {
        $currentUsername = $_SESSION['username'] ?? null;
        
        // 確保使用者已登入
        if (!$currentUsername) {
            $this->redirect(BASE_URL . '/login?error=請先登入');
            return false;
        }

        // 使用者 'admin' 擁有所有權限
        if ($currentUsername === 'admin') {
            return true;
        }
        
        // 公告相關方法：有公告管理權限的使用者可以訪問
        $announcementMethods = ['announcements', 'createAnnouncement', 'editAnnouncement'];
        if (in_array($method, $announcementMethods)) {
            if ($this->userModel->canManageAnnouncements($currentUsername)) {
                return true;
            } else {
                error_log("權限檢查失敗 - 使用者: $currentUsername, 方法: $method");
                $this->redirect(BASE_URL . '?error=您沒有公告管理權限');
                return false;
            }
        }
        
        // 其他管理功能：預設需要 admin 等級權限 (目前邏輯下只有 admin 能訪問)
        // 這裡可以根據未來需求擴充其他角色的權限
        error_log("權限檢查失敗 - 使用者: $currentUsername, 方法: $method, 需要更高權限");
        $this->redirect(BASE_URL . '?error=權限不足');
        return false;
    }
    // 建構函數結束
    
    /**
     * 管理員後台首頁
     * 
     * 顯示管理員控制台的主要儀表板，提供系統管理功能的入口
     * 包含系統狀態總覽、快速操作連結等
     * 
     * @return void
     */
    public function dashboard() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
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
    
    /**
     * 使用者管理頁面
     * 
     * 顯示系統中所有使用者的列表，提供使用者管理功能
     * 包含新增、編輯、刪除使用者等操作入口
     * 
     * @return void
     */
    public function users() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
        // 定義使用者管理頁面方法
        $this->setGlobalViewData();
        // 設定全域視圖資料
        $this->view('admin/users', [
            // 呼叫視圖方法，載入使用者管理頁面模板
            'title' => '使用者管理',
            'users' => $this->userModel->all() // 獲取所有使用者
        ]);
        // 視圖參數陣列結束
    }
    // users 方法結束
    
    /**
     * 新增使用者頁面
     * 
     * 顯示新增使用者的表單頁面
     * 管理員可以在此新增新的系統使用者
     * 
     * @return void
     */
    public function createUser() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
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
    
    /**
     * 編輯使用者頁面
     * 
     * 顯示編輯現有使用者的表單頁面
     * 管理員可以在此修改使用者的基本資料和權限
     * 
     * @return void
     */
    public function editUser() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
        // 定義編輯使用者頁面方法
        $this->setGlobalViewData();
        $this->view('admin/edit-user', [
            // 呼叫視圖方法，載入編輯使用者頁面模板
            'title' => '編輯使用者'
            // 傳遞頁面標題到視圖
        ]);
        // 視圖參數陣列結束
    }
    // editUser 方法結束
    
    /**
     * 公告管理頁面
     * 
     * 顯示系統中所有公告的列表，提供公告管理功能
     * 包含新增、編輯、刪除公告等操作入口
     * 
     * @return void
     */
    public function announcements() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
        $currentUsername = $_SESSION['username'];
        
        // 取得所有公告
        $announcements = $this->announcementModel->getAllAnnouncementsWithAuthor();
        
        $this->setGlobalViewData();
        $this->view('admin/announcements', [
            'title' => '公告管理',
            'announcements' => $announcements,
            'canUploadPDF' => $this->userModel->canUploadPDF($currentUsername)
        ]);
    }
    // announcements 方法結束
    
    /**
     * 新增公告頁面
     * 
     * 顯示新增公告的表單頁面
     * 管理員可以在此發布新的系統公告
     * 
     * @return void
     */
    public function createAnnouncement() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
        $currentUsername = $_SESSION['username'];
        
        // 處理POST請求（表單提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateAnnouncement();
            return;
        }
        
        $this->setGlobalViewData();
        $this->view('admin/create-announcement', [
            'title' => '新增公告',
            'canUploadPDF' => $this->userModel->canUploadPDF($currentUsername)
        ]);
    }
    // createAnnouncement 方法結束
    
    /**
     * 處理新增公告的POST請求
     */
    private function handleCreateAnnouncement() {
        $currentUsername = $_SESSION['username'];
        $currentUser = AuthMiddleware::getCurrentUser(); // 獲取當前使用者物件
        
        // 驗證必填欄位
        if (empty($_POST['title']) || empty($_POST['content'])) {
            $this->redirectWithError(BASE_URL . '/admin/announcements/create', '標題和內容為必填欄位');
            return;
        }

        try {
            // 準備公告資料
            $data = [
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'type' => $_POST['type'] ?? 'general',
                'status' => $_POST['status'] ?? 'draft',
                'date' => !empty($_POST['date']) ? $_POST['date'] : null,
                'sort_order' => !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
            ];

            // 呼叫新的 Model 方法，傳入作者 username
            $announcementId = $this->announcementModel->createAnnouncementWithDetails($data, $currentUsername);

            if ($announcementId) {
                // 如果有上傳 PDF 檔案，進行處理
                if (!empty($_FILES['attachment']['name']) && $this->userModel->canUploadPDF($currentUsername)) {
                    $this->handlePDFUpload($announcementId, $_FILES['attachment']);
                }
                
                $this->redirectWithSuccess(BASE_URL . '/admin/announcements', '公告已成功建立');
            } else {
                $this->redirectWithError(BASE_URL . '/admin/announcements/create', '建立公告失敗，請稍後再試');
            }
        } catch (Exception $e) {
            // 記錄詳細錯誤
            error_log("創建公告失敗： " . $e->getMessage());
            // 給使用者一個通用的錯誤訊息
            $this->redirectWithError(BASE_URL . '/admin/announcements/create', '建立失敗<br>' . $e->getMessage());
        }
    }
    
    /**
     * 處理PDF附件上傳
     */
    private function handlePDFUpload($announcementId, $fileInfo) {
        // 檢查檔案類型
        $allowedTypes = ['application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileInfo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('只允許上傳PDF檔案');
        }
        
        // 檢查檔案大小（最大10MB）
        if ($fileInfo['size'] > 10 * 1024 * 1024) {
            throw new Exception('檔案大小不能超過10MB');
        }
        
        // 上傳檔案
        if (!$this->announcementModel->uploadAttachment($announcementId, $fileInfo)) {
            throw new Exception('檔案上傳失敗');
        }
    }
    
    /**
     * 編輯公告頁面
     * 
     * 顯示編輯現有公告的表單頁面
     * 管理員可以在此修改公告的內容和發布狀態
     * 
     * @return void
     */
    public function editAnnouncement() {
        // 檢查權限
        if (!$this->checkMethodPermission(__FUNCTION__)) return;
        
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