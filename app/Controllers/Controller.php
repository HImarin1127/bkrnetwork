<?php
/**
 * Controller.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 所有控制器的基礎類別，提供共用的輔助函式。
 */
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\User;
use Exception;

/**
 * Class Controller
 * 
 * 作為應用程式中所有控制器的父類別。
 * 它提供了視圖載入、HTTP 重新導向、JSON 回應以及使用者狀態檢查等常用功能。
 *
 * @package App\Controllers
 */
abstract class Controller {
    /** 
     * @var array 用於儲存將傳遞給視圖的資料。
     */
    protected $viewData = [];
    
    /**
     * Controller 的建構函式。
     *
     * - 確保 session 已啟動。
     * - 呼叫 setGlobalViewData() 來設定所有視圖都需要的共用變數。
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->setGlobalViewData();
    }
    
    /**
     * 載入並渲染一個視圖檔案，可選擇性地使用佈局。
     * 
     * @param string $view   視圖檔案的路徑 (相對於 /app/Views)。
     * @param array  $data   要傳遞給視圖的鍵值對陣列。
     * @param string $layout 要使用的佈局檔案路徑 (預設為 'layouts/app')。傳入 null 則不使用佈局。
     * @return void
     * @throws Exception 如果視圖或佈局檔案不存在，則拋出例外。
     */
    protected function view($view, $data = [], $layout = 'layouts/app') {
        $this->viewData = array_merge($this->viewData, $data);
        
        // 使用 extract 將 $viewData 中的鍵轉換為可在視圖中直接使用的變數。
        extract($this->viewData);
        
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            throw new \Exception("視圖檔案不存在: {$viewFile}");
        }
        
        // 使用輸出緩衝來捕獲視圖的 HTML 內容。
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        
        // 如果指定了佈局檔案，則載入佈局並將視圖內容注入其中。
        if ($layout) {
            $layoutFile = __DIR__ . "/../Views/{$layout}.php";
            
            if (!file_exists($layoutFile)) {
                throw new \Exception("佈局檔案不存在: {$layoutFile}");
            }
            
            // 在佈局檔案中，可以透過 $content 變數來存取視圖內容。
            require $layoutFile;
        } else {
            // 如果沒有指定佈局，則直接輸出視圖內容。
            echo $content;
        }
    }
    
    /**
     * 執行 HTTP 重新導向。
     * 
     * @param string $url 要重導向的目標 URL。
     * @return void
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    /**
     * 帶有成功訊息的重新導向 (使用 session flash message)。
     * 
     * @param string $url     目標 URL。
     * @param string $message 要顯示的成功訊息。
     * @return void
     */
    protected function redirectWithSuccess($url, $message) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => $message
        ];
        $this->redirect($url);
    }

    /**
     * 帶有錯誤訊息的重新導向 (使用 session flash message)。
     * 
     * @param string $url     目標 URL。
     * @param string $message 要顯示的錯誤訊息。
     * @return void
     */
    protected function redirectWithError($url, $message) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => $message
        ];
        $this->redirect($url);
    }
    
    /**
     * 將資料以 JSON 格式回應給客戶端。
     * 
     * @param mixed $data       要編碼為 JSON 的資料。
     * @param int   $statusCode HTTP 狀態碼 (預設為 200)。
     * @return void
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * 獲取目前已登入的使用者資訊。
     * 
     * @return array|null 成功時返回使用者資訊陣列，若未登入則返回 null。
     */
    protected function getCurrentUser() {
        return AuthMiddleware::getCurrentUser();
    }
    
    /**
     * 檢查目前使用者是否已登入。
     * 
     * @return bool 如果已登入則返回 true，否則返回 false。
     */
    protected function isLoggedIn() {
        return isset($_SESSION['username']);
    }
    
    /**
     * 檢查目前使用者是否為管理員。
     * 
     * @return bool 如果是管理員則返回 true，否則返回 false。
     */
    protected function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * 獲取應用程式的基礎 URL。
     *
     * @return string 應用程式的完整基礎 URL (例如 http://localhost/bkrnetwork)。
     */
    protected function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = $_SERVER['SCRIPT_NAME'];
        $path = dirname($script);
        
        $path = str_replace('\\', '/', $path);
        
        // 如果專案在根目錄，路徑為空字串
        if ($path === '/' || $path === '') {
            $path = '';
        } else {
            $path = rtrim($path, '/');
        }
        
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * 設定所有視圖共用的全域變數。
     *
     * 這些變數會被自動注入到所有透過 `view()` 方法渲染的視圖中。
     *
     * @return void
     */
    protected function setGlobalViewData() {
        $this->viewData['currentUser'] = $this->getCurrentUser();
        $this->viewData['isLoggedIn'] = $this->isLoggedIn();
        $this->viewData['isAdmin'] = $this->isAdmin();
        $this->viewData['baseUrl'] = $this->getBaseUrl();
        
        // 檢查使用者是否擁有管理公告的權限
        $this->viewData['canManageAnnouncements'] = false;
        if ($this->isLoggedIn() && isset($_SESSION['username'])) {
            try {
                $userModel = new User();
                $this->viewData['canManageAnnouncements'] = $userModel->canManageAnnouncements($_SESSION['username']);
            } catch (Exception $e) {
                // 如果在檢查權限時發生錯誤 (例如資料庫連線失敗)，則預設為無權限
                error_log("設定全域權限時發生錯誤: " . $e->getMessage());
                $this->viewData['canManageAnnouncements'] = false;
            }
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $this->viewData['appName'] = $config['name'];
    }
} 