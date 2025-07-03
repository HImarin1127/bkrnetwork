<?php
// app/Controllers/HomeController.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Controller.php';
// 引入父類別 Controller.php，使用 require_once 確保只載入一次
require_once __DIR__ . '/../Models/Database.php';
// 引入資料庫連接類別，提供資料庫操作功能
require_once __DIR__ . '/../Models/Announcement.php';
// 引入公告模型類別，用於公告相關資料操作
require_once __DIR__ . '/../Models/CompanyInfo.php';
// 引入公司資訊模型類別，用於公司資訊相關資料操作

/**
 * 首頁控制器
 * 
 * 處理首頁和公共頁面的路由請求，包括：
 * - 首頁顯示
 * - 公告查看
 * - 假日資訊
 * - 員工手冊
 * - 公司資訊
 * 
 * 這些頁面大部分都是公開的，不需要登入即可瀏覽
 */
class HomeController extends Controller {
    // 定義 HomeController 類別，繼承自 Controller 父類別
    
    /** @var Announcement 公告模型實例，用於操作公告相關資料 */
    private $announcementModel;
    // 宣告私有成員變數，存放公告模型的實例
    
    /** @var CompanyInfo 公司資訊模型實例，用於操作公司資訊相關資料 */
    private $companyInfoModel;
    // 宣告私有成員變數，存放公司資訊模型的實例
    
    /**
     * 建構函式
     * 
     * 初始化控制器，設定必要的模型實例和全域視圖資料
     */
    public function __construct() {
        // 建構函數，當類別被實例化時自動執行
        $this->announcementModel = new Announcement();
        // 實例化公告模型，用於後續的公告相關操作
        $this->companyInfoModel = new CompanyInfo();
        // 實例化公司資訊模型，用於後續的公司資訊相關操作
        $this->setGlobalViewData();
        // 設定全域視圖資料，如當前使用者資訊、登入狀態等
    }
    // 建構函數結束
    
    /**
     * 首頁顯示方法
     * 
     * 顯示網站首頁，包含最新的公告資訊
     * 提供員工快速瀏覽最新動態的入口
     */
    public function index() {
        // 定義首頁方法，處理首頁的顯示邏輯
        // 首頁顯示最新公告
        $announcements = $this->announcementModel->getPublicAnnouncements(5);
        // 從公告模型取得最新的 5 筆公開公告
        
        $this->view('home/index', [
            // 呼叫視圖方法，載入首頁模板
            'title' => '首頁',
            // 設定頁面標題
            'announcements' => $announcements,
            // 將公告資料傳遞到視圖
            'pageType' => 'home'
            // 設定頁面類型，用於導覽列的active狀態等
        ]);
        // 視圖參數陣列結束
    }
    // index 方法結束
    
    /**
     * 公告列表頁面
     * 
     * 顯示所有公開的公告資訊
     * 提供完整的公告瀏覽功能
     */
    public function announcements() {
        // 定義公告頁面方法
        $announcements = $this->announcementModel->getPublicAnnouncements();
        // 從公告模型取得所有公開公告（無數量限制）
        
        // 檢查當前使用者是否有公告管理權限
        $canManageAnnouncements = false;
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();
            $canManageAnnouncements = $userModel->canManageAnnouncements($_SESSION['user_id']);
        }
        
        $this->view('announcements/index', [
            // 呼叫視圖方法，載入公告列表頁面模板
            'title' => '最新公告',
            // 設定頁面標題
            'announcements' => $announcements,
            // 將公告資料傳遞到視圖
            'canManageAnnouncements' => $canManageAnnouncements,
            // 傳遞公告管理權限檢查結果
            'pageType' => 'announcements'
            // 設定頁面類型為公告區
        ]);
        // 視圖參數陣列結束
    }
    // announcements 方法結束
    
    /**
     * 假日資訊頁面
     * 
     * 顯示公司假日行事曆和相關資訊
     * 幫助員工了解放假日期和工作日安排
     */
    public function holidays() {
        // 定義假日資訊頁面方法
        // 直接載入假日公告頁面
        // 頁面內部會自動載入 HolidayCalendar 模型
        $this->view('announcements/holidays', [
            // 呼叫視圖方法，載入假日資訊頁面模板
            'title' => '假日資訊',
            // 設定頁面標題
            'pageType' => 'announcements'
            // 設定頁面類型為公告區
        ]);
        // 視圖參數陣列結束
    }
    // holidays 方法結束
    
    /**
     * 員工手冊頁面
     * 
     * 顯示員工手冊內容和相關規範
     * 提供員工查閱公司規章制度的管道
     */
    public function handbook() {
        // 定義員工手冊頁面方法
        $handbook = $this->announcementModel->getHandbookContents();
        // 從公告模型取得員工手冊的內容資料
        
        $this->view('announcements/handbook', [
            // 呼叫視圖方法，載入員工手冊頁面模板
            'title' => '員工手冊',
            // 設定頁面標題
            'handbook' => $handbook,
            // 將員工手冊資料傳遞到視圖
            'pageType' => 'announcements'
            // 設定頁面類型為公告區
        ]);
        // 視圖參數陣列結束
    }
    // handbook 方法結束
    
    /**
     * 公司資訊首頁
     * 
     * 顯示公司基本資訊和介紹
     * 提供公司概況的綜合入口
     */
    public function company() {
        // 定義公司資訊頁面方法
        $this->view('company/index', [
            // 呼叫視圖方法，載入公司資訊主頁模板
            'title' => '公司資訊',
            // 設定頁面標題
            'pageType' => 'company'
            // 設定頁面類型為公司資訊區
        ]);
        // 視圖參數陣列結束
    }
    // company 方法結束
    
    /**
     * 公司樓層圖頁面
     * 
     * 顯示公司各樓層的平面圖和配置
     * 幫助員工和訪客了解辦公環境佈局
     */
    public function companyFloor() {
        $floorInfo = $this->companyInfoModel->getFloorInfo();
        $employeeSeats = $this->companyInfoModel->getEmployeeSeats();
        
        $this->view('company/floor', [
            'title' => '樓層圖',
            'pageType' => 'company',
            'floorInfo' => $floorInfo,
            'employeeSeats' => $employeeSeats
        ]);
    }
    // companyFloor 方法結束
    
    /**
     * 公司聯絡資訊頁面
     * 
     * 顯示公司聯絡方式、地址、電話等資訊
     * 提供內外部溝通的聯絡管道
     */
    public function companyContacts() {
        $departmentContacts = $this->companyInfoModel->getDepartmentContacts();
        $extensionNumbers = $this->companyInfoModel->getExtensionNumbers();
        
        $this->view('company/contacts', [
            'title' => '聯絡資訊',
            'pageType' => 'company',
            'departmentContacts' => $departmentContacts,
            'extensionNumbers' => $extensionNumbers
        ]);
    }
    // companyContacts 方法結束
    
    /**
     * 公司 NAS 介紹頁面
     * 
     * 顯示網路附加儲存設備（NAS）的使用說明
     * 包括存取方式、帳號申請、使用須知等資訊
     */
    public function companyNas() {
        // 定義公司 NAS 介紹頁面方法
        $this->view('company/nas', [
            // 呼叫視圖方法，載入 NAS 介紹頁面模板
            'title' => 'NAS 網路存儲',
            // 設定頁面標題
            'pageType' => 'company'
            // 設定頁面類型為公司資訊區
        ]);
        // 視圖參數陣列結束
    }
    // companyNas 方法結束
} 
// HomeController 類別結束 