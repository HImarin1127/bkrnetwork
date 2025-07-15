<?php
/**
 * GuidesController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理所有操作指引頁面的控制器。
 */
namespace App\Controllers;

use App\Middleware\AuthMiddleware;

/**
 * Class GuidesController
 * 
 * 負責顯示各種系統和流程的操作指引頁面。
 * 所有指引頁面都無需登入即可存取。
 *
 * @package App\Controllers
 */
class GuidesController extends Controller {
    
    /**
     * GuidesController 的建構函式。
     * 
     * - 呼叫父類別建構函式來初始化 session 和全域變數。
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 顯示 Windows 作業系統操作指引頁面。
     *
     * @return void
     */
    public function windows() {
        $this->view('guides/windows', [
            'title' => 'Windows 操作指引'
        ]);
    }
    
    /**
     * 顯示印表機操作指引頁面。
     *
     * @return void
     */
    public function printer() {
        $this->view('guides/printer', [
            'title' => '印表機操作指引'
        ]);
    }
    
    /**
     * 顯示 MAC 作業系統操作指引頁面。
     *
     * @return void
     */
    public function mac() {
        $this->view('guides/mac', [
            'title' => 'MAC 操作指引'
        ]);
    }
    
    /**
     * 顯示 NAS (網路附加儲存) 操作指引頁面。
     *
     * @return void
     */
    public function nas() {
        $this->view('guides/nas', [
            'title' => 'NAS 操作指引'
        ]);
    }
    
    /**
     * 顯示文化部免稅系統操作指引頁面。
     *
     * @return void
     */
    public function taxExempt() {
        $this->view('guides/tax-exempt', [
            'title' => '文化部免稅操作指引'
        ]);
    }

    /**
     * @deprecated 1.0.0 已棄用。此功能已整合至 windows() 指引中。
     * @return void
     */
    public function windowsAudio() {
        $this->redirect($this->viewData['baseUrl'] . '/guides/windows');
    }

    /**
     * 顯示 Windows 自動更新相關說明頁面。 (此頁面無需登入)
     *
     * @return void
     */
    public function windowsUpdate() {
        $this->view('guides/windows-update', [
            'title' => 'Windows 自動更新'
        ]);
    }
    
    /**
     * 顯示 NAS 密碼管理相關指引頁面。
     *
     * @return void
     */
    public function nasPassword() {
        $this->view('guides/nas', [
            'title' => 'NAS 密碼管理'
        ]);
    }

    /**
     * 顯示 NAS 網頁版使用與二次驗證說明頁面。
     *
     * @return void
     */
    public function nasWebAuth() {
        $this->view('guides/nas-web-auth', [
            'title' => '網頁版使用與二次驗證'
        ]);
    }

    /**
     * 顯示 MF2000 公文流程說明頁面。
     *
     * @return void
     */
    public function mf2000Workflow() {
        $this->view('guides/mf2000/workflow', [
            'title' => 'MF2000 公文'
        ]);
    }

    /**
     * 顯示 MF2000 出缺勤管理說明頁面。
     *
     * @return void
     */
    public function mf2000Attendance() {
        $this->view('guides/mf2000/attendance', [
            'title' => 'MF2000 出缺勤管理'
        ]);
    }

    /**
     * 顯示 MF2000 連線說明頁面。
     *
     * @return void
     */
    public function mf2000Connection() {
        $this->view('guides/mf2000/connection', [
            'title' => 'MF2000 連線說明'
        ]);
    }

    /**
     * 顯示 POS 收銀機操作手冊頁面。
     *
     * @return void
     */
    public function pos() {
        $this->view('guides/pos', [
            'title' => 'POS 收銀機操作手冊'
        ]);
    }
} 