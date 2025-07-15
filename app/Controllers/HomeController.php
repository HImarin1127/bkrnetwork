<?php
/**
 * HomeController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理網站主要公共頁面的控制器。
 */
namespace App\Controllers;

use App\Models\Announcement;
use App\Models\CompanyInfo;
use App\Models\User;
use App\Models\HolidayCalendar;

/**
 * Class HomeController
 *
 * 負責處理網站的公開頁面，如首頁、最新公告、公司資訊等。
 * 這些頁面通常不需要使用者登入即可存取。
 *
 * @package App\Controllers
 */
class HomeController extends Controller {
    
    /** @var Announcement 公告模型實例 */
    private $announcementModel;
    
    /** @var CompanyInfo 公司資訊模型實例 */
    private $companyInfoModel;
    
    /**
     * HomeController 的建構函式。
     *
     * 初始化所需的模型並設定全域視圖資料。
     */
    public function __construct() {
        $this->announcementModel = new Announcement();
        $this->companyInfoModel = new CompanyInfo();
        $this->setGlobalViewData();
    }
    
    /**
     * 顯示網站首頁。
     *
     * 從資料庫獲取最新的5條公開公告並顯示。
     *
     * @return void
     */
    public function index() {
        $announcements = $this->announcementModel->getPublicAnnouncements(5);
        
        $this->view('home/index', [
            'title' => '首頁',
            'announcements' => $announcements,
            'pageType' => 'home'
        ]);
    }
    
    /**
     * 顯示最新公告列表頁面。
     *
     * 獲取所有公開的公告，並檢查當前使用者是否具備管理權限，
     * 以便在視圖中決定是否顯示管理按鈕。
     *
     * @return void
     */
    public function announcements() {
        $announcements = $this->announcementModel->getPublicAnnouncements();
        
        $canManageAnnouncements = false;
        if (isset($_SESSION['username'])) {
            $userModel = new User();
            $canManageAnnouncements = $userModel->canManageAnnouncements($_SESSION['username']);
        }
        
        $this->view('announcements/index', [
            'title' => '最新公告',
            'announcements' => $announcements,
            'canManageAnnouncements' => $canManageAnnouncements,
            'pageType' => 'announcements'
        ]);
    }
    
    /**
     * 顯示假日資訊頁面。
     *
     * 獲取並顯示當年度的假日日曆。
     *
     * @return void
     */
    public function holidays() {
        $holidayModel = new HolidayCalendar();
        $holidays = $holidayModel->getHolidayCalendar();
        $calendarHtml = $holidayModel->generateCalendarHTML(date('Y'));

        $this->view('announcements/holidays', [
            'title' => '假日資訊',
            'holidays' => $holidays,
            'calendarHtml' => $calendarHtml,
            'pageType' => 'announcements'
        ]);
    }
    
    /**
     * 顯示員工手冊頁面。
     *
     * @return void
     */
    public function handbook() {
        $handbook = $this->announcementModel->getHandbookContents();
        
        $this->view('announcements/handbook', [
            'title' => '員工手冊',
            'handbook' => $handbook,
            'pageType' => 'announcements'
        ]);
    }
    
    /**
     * 顯示公司資訊主頁。
     *
     * @return void
     */
    public function company() {
        $this->view('company/index', [
            'title' => '公司資訊',
            'pageType' => 'company'
        ]);
    }
    
    /**
     * 顯示公司樓層平面圖頁面。
     *
     * 獲取樓層資訊與員工座位表並傳遞至視圖。
     *
     * @return void
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
    
    /**
     * 顯示公司聯絡資訊頁面。
     *
     * 獲取各部門聯絡人與分機號碼表並傳遞至視圖。
     *
     * @return void
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
    
    /**
     * 顯示公司 NAS（網路附加儲存）使用說明頁面。
     *
     * @return void
     */
    public function companyNas() {
        $this->view('company/nas', [
            'title' => 'NAS 網路存儲',
            'pageType' => 'company'
        ]);
    }
    
    /**
     * 顯示文化部免稅申請流程圖頁面。
     *
     * @return void
     */
    public function taxExemptProcess() {
        $this->view('guides/tax-exempt', [
            'title' => '免稅申請流程'
        ]);
    }
    
    /**
     * 顯示 Windows 遠端桌面連線指引頁面。
     *
     * @return void
     */
    public function windowsRemote() {
        $this->view('guides/windows-remote', [
            'title' => 'Windows 遠端連線'
        ]);
    }

    /**
     * 顯示印表機基本操作說明頁面。
     *
     * @return void
     */
    public function printerBasic() {
        $this->view('guides/printer-basic', [
            'title' => '印表機基本操作'
        ]);
    }

    /**
     * 顯示印表機常見問題疑難排解頁面。
     *
     * @return void
     */
    public function printerTroubleshoot() {
        $this->view('guides/printer-troubleshoot', [
            'title' => '印表機疑難排解'
        ]);
    }

    /**
     * 顯示文化部免稅系統操作說明頁面。
     *
     * @return void
     */
    public function taxExemptSystem() {
        $this->view('guides/tax-exempt-system', [
            'title' => '免稅系統操作說明'
        ]);
    }

    /**
     * 顯示電子郵件系統操作指引頁面。
     *
     * @return void
     */
    public function email() {
        $this->view('guides/email', [
            'title' => '電子郵件操作指引'
        ]);
    }
} 