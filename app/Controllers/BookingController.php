<?php
/**
 * BookingController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理資源預約功能的控制器。
 */
namespace App\Controllers;

use App\Middleware\AuthMiddleware;

/**
 * Class BookingController
 * 
 * 負責處理會議室預約、設備借用等資源預約相關頁面的顯示。
 * 所有預約功能都需要使用者登入才能存取。
 *
 * @package App\Controllers
 */
class BookingController extends Controller {
    
    /**
     * BookingController 的建構函式。
     *
     * - 呼叫父類別建構函式來初始化。
     * - 使用中介軟體強制要求所有預約功能都需要登入。
     */
    public function __construct() {
        parent::__construct();
        AuthMiddleware::requireLogin();
    }
    
    /**
     * 顯示會議室預約頁面。
     *
     * @return void
     */
    public function meetingRoom() {
        $this->view('booking/meeting-room', [
            'title' => '會議室預約'
        ]);
    }
    
    /**
     * 顯示設備借用頁面。
     *
     * @return void
     */
    public function equipment() {
        $this->view('booking/equipment', [
            'title' => '設備借用'
        ]);
    }
} 