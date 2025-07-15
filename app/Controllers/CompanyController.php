<?php
/**
 * CompanyController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理與公司資訊相關頁面的控制器。
 */
namespace App\Controllers;

/**
 * Class CompanyController
 * 
 * 負責顯示公司相關的靜態資訊頁面，例如樓層圖。
 *
 * @package App\Controllers
 */
class CompanyController extends Controller
{
    /**
     * 顯示公司樓層圖頁面。
     *
     * @return void
     */
    public function floor()
    {
        $this->view('company/floor', [
            'title' => '樓層圖'
        ]);
    }
}
