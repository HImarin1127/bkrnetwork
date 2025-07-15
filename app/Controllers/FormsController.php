<?php
/**
 * FormsController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理各種行政申請表單頁面的控制器。
 */
namespace App\Controllers;

use App\Middleware\AuthMiddleware;

/**
 * Class FormsController
 * 
 * 負責顯示各種行政申請表單頁面。
 * 所有表單頁面都需要使用者登入才能存取。
 *
 * @package App\Controllers
 */
class FormsController extends Controller {
    
    /**
     * FormsController 的建構函式。
     *
     * - 呼叫父類別建構函式來初始化。
     * - 使用中介軟體強制要求所有表單功能都需要登入。
     */
    public function __construct() {
        parent::__construct();
        AuthMiddleware::requireLogin();
    }
    
    /**
     * 顯示購書申請表單頁面。
     *
     * @return void
     */
    public function bookRequest() {
        $this->view('forms/book-request', [
            'title' => '購書申請'
        ]);
    }
    
    /**
     * 顯示人員異動申請表單頁面。
     *
     * @return void
     */
    public function personnelChange() {
        $this->view('forms/personnel-change', [
            'title' => '人員異動申請'
        ]);
    }
    
    /**
     * 顯示請假申請表單頁面。
     *
     * @return void
     */
    public function leaveRequest() {
        $this->view('forms/leave-request', [
            'title' => '請假申請'
        ]);
    }
    
    /**
     * 顯示加班申請表單頁面。
     *
     * @return void
     */
    public function overtimeRequest() {
        $this->view('forms/overtime-request', [
            'title' => '加班申請'
        ]);
    }
    
    /**
     * 顯示出差申請表單頁面。
     *
     * @return void
     */
    public function businessTrip() {
        $this->view('forms/business-trip', [
            'title' => '出差申請'
        ]);
    }
    
    /**
     * 顯示教育訓練申請表單頁面。
     *
     * @return void
     */
    public function educationTraining() {
        $this->view('forms/education-training', [
            'title' => '教育訓練申請'
        ]);
    }
    
    /**
     * 顯示設備採購申請表單頁面。
     *
     * @return void
     */
    public function equipmentPurchase() {
        $this->view('forms/equipment-purchase', [
            'title' => '設備採購申請'
        ]);
    }
} 