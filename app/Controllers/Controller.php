<?php
// app/Controllers/Controller.php

/**
 * 控制器基礎類別
 * 
 * 提供所有控制器的共用功能，包括視圖渲染、重新導向、JSON回應等
 * 實作 MVC 架構中控制器層的核心功能
 */
abstract class Controller {
    /** @var array 視圖資料陣列，用於傳遞資料到視圖 */
    protected $viewData = [];
    
    /**
     * 渲染視圖檔案
     * 
     * 載入指定的視圖檔案並使用佈局包裝輸出
     * 支援資料傳遞和佈局系統
     * 
     * @param string $view 視圖檔案路徑（相對於 Views/ 目錄）
     * @param array $data 要傳遞給視圖的資料陣列
     * @param string|null $layout 佈局檔案路徑，null 表示不使用佈局
     * @throws Exception 當視圖或佈局檔案不存在時拋出例外
     */
    protected function view($view, $data = [], $layout = 'layouts/app') {
        // 合併新資料到現有視圖資料中
        $this->viewData = array_merge($this->viewData, $data);
        
        // 提取變數到視圖中，讓視圖可以直接使用變數名稱
        extract($this->viewData);
        
        // 建構視圖檔案完整路徑
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        // 檢查視圖檔案是否存在
        if (!file_exists($viewFile)) {
            throw new Exception("視圖檔案不存在: {$view}");
        }
        
        // 使用輸出緩衝捕獲視圖內容
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        
        // 如果指定了佈局，使用佈局包裝內容
        if ($layout) {
            $layoutFile = __DIR__ . "/../Views/{$layout}.php";
            
            if (!file_exists($layoutFile)) {
                throw new Exception("佈局檔案不存在: {$layout}");
            }
            
            // 在佈局中，$content 變數包含視圖內容
            require $layoutFile;
        } else {
            // 直接輸出內容（不使用佈局）
            echo $content;
        }
    }
    
    /**
     * HTTP 重新導向
     * 
     * 設定 Location 標頭並結束腳本執行
     * 
     * @param string $url 要重新導向的 URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    /**
     * 回傳 JSON 回應
     * 
     * 設定適當的 HTTP 狀態碼和 Content-Type 標頭，
     * 將資料轉換為 JSON 格式並輸出
     * 
     * @param mixed $data 要轉換為 JSON 的資料
     * @param int $statusCode HTTP 狀態碼，預設為 200
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        // 使用 JSON_UNESCAPED_UNICODE 確保中文字元正確顯示
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * 取得目前登入的使用者資訊
     * 
     * 透過 AuthMiddleware 取得目前已登入使用者的完整資訊
     * 
     * @return array|null 使用者資訊陣列，未登入時回傳 null
     */
    protected function getCurrentUser() {
        require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
        return AuthMiddleware::getCurrentUser();
    }
    
    /**
     * 檢查使用者是否已登入
     * 
     * 檢查 session 中是否存在 user_id，判斷使用者登入狀態
     * 
     * @return bool 已登入回傳 true，未登入回傳 false
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * 檢查使用者是否為管理員
     * 
     * 檢查 session 中的使用者角色是否為 admin
     * 
     * @return bool 是管理員回傳 true，否則回傳 false
     */
    protected function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * 取得應用程式基礎 URL
     * 
     * 根據目前請求自動偵測並建構基礎 URL，
     * 包含協定、主機名稱和路徑
     * 
     * @return string 完整的基礎 URL
     */
    protected function getBaseUrl() {
        // 偵測協定（HTTP 或 HTTPS）
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = $_SERVER['SCRIPT_NAME'];
        $path = dirname($script);
        
        // 正規化路徑分隔符號（Windows 和 Unix 相容）
        $path = str_replace('\\', '/', $path);
        
        // 如果路徑是根目錄，不要包含路徑
        if ($path === '/' || $path === '') {
            $path = '';
        } else {
            // 確保路徑以斜線結尾
            $path = rtrim($path, '/');
        }
        
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * 設定全域視圖資料
     * 
     * 設定所有視圖都需要的共用資料，包括：
     * - 目前使用者資訊
     * - 登入狀態
     * - 管理員權限
     * - 基礎 URL
     * - 應用程式名稱
     * - 公告管理權限
     */
    protected function setGlobalViewData() {
        $this->viewData['currentUser'] = $this->getCurrentUser();
        $this->viewData['isLoggedIn'] = $this->isLoggedIn();
        $this->viewData['isAdmin'] = $this->isAdmin();
        $this->viewData['baseUrl'] = $this->getBaseUrl();
        
        // 檢查公告管理權限
        $this->viewData['canManageAnnouncements'] = false;
        if ($this->isLoggedIn() && isset($_SESSION['user_id'])) {
            try {
                require_once __DIR__ . '/../Models/User.php';
                $userModel = new User();
                $this->viewData['canManageAnnouncements'] = $userModel->canManageAnnouncements($_SESSION['user_id']);
            } catch (Exception $e) {
                // 如果出現錯誤，預設為無權限
                $this->viewData['canManageAnnouncements'] = false;
            }
        }
        
        // 載入應用程式設定
        $config = require __DIR__ . '/../../config/app.php';
        $this->viewData['appName'] = $config['name'];
    }
} 