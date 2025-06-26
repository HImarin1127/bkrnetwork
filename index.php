<?php
/**
 * 應用程式入口檔案
 * 
 * 這是整個 BKR Network 系統的主要入口點，負責：
 * 1. 初始化應用程式環境
 * 2. 處理 HTTP 請求路由
 * 3. 載入並執行對應的控制器
 * 4. 錯誤處理和 404 頁面顯示
 * 
 * 系統架構：MVC (Model-View-Controller)
 * 路由系統：基於關聯陣列的簡單路由對應
 * 
 * @author BKR Network Team
 * @version 1.0
 */

// ========================================
// 1. 系統初始化
// ========================================

// 啟動 PHP Session（用於使用者登入狀態管理）
session_start();

// 開發環境錯誤報告設定（生產環境建議關閉）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定義應用程式基本常數
define('BASE_PATH', __DIR__ . '/');                                    // 檔案系統基礎路徑
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/'); // 網址基礎路徑

// ========================================
// 2. 載入核心組件
// ========================================

// 載入應用程式設定檔
require_once BASE_PATH . 'config/app.php';
require_once BASE_PATH . 'config/database.php';

// 自動載入類別機制
// 當實例化類別時自動載入對應的檔案
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . 'app/Models/' . $class . '.php',        // 模型檔案
        BASE_PATH . 'app/Controllers/' . $class . '.php',   // 控制器檔案
        BASE_PATH . 'app/Middleware/' . $class . '.php'     // 中介軟體檔案
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// ========================================
// 3. 路由處理
// ========================================

// 載入路由定義檔案
$routes = require_once BASE_PATH . 'routes/web.php';

// 解析當前請求的 URI
$requestUri = $_SERVER['REQUEST_URI'];

// 處理基礎路徑，移除應用程式所在的目錄路徑
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && $basePath !== '') {
    // 確保 basePath 以 / 結尾
    $basePath = rtrim($basePath, '/') . '/';
    if (strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
}

// 移除查詢字串並清理 URI
$uri = parse_url($requestUri, PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

// 特殊處理：如果 URI 是空的，設為根路由
if ($uri === '//') {
    $uri = '/';
}

// ========================================
// 4. 路由匹配和控制器執行
// ========================================

$routeFound = false;

// 遍歷路由定義，尋找匹配的路由
foreach ($routes as $route => $action) {
    if ($route === $uri) {
        $routeFound = true;
        
        // 解析控制器和方法名稱
        if (is_array($action)) {
            list($controller, $method) = $action;
        } else {
            // 支援 'Controller@method' 格式（備用）
            list($controller, $method) = explode('@', $action);
        }
        
        // 檢查控制器檔案是否存在
        $controllerFile = BASE_PATH . 'app/Controllers/' . $controller . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            die('控制器檔案不存在: ' . $controller);
        }
        
        // 實例化控制器
        $controllerInstance = new $controller();
        
        // 檢查控制器方法是否存在
        if (!method_exists($controllerInstance, $method)) {
            http_response_code(500);
            die('方法不存在: ' . $method . ' in ' . $controller);
        }
        
        // 執行控制器方法
        $controllerInstance->$method();
        break;
    }
}

// ========================================
// 5. 404 錯誤處理
// ========================================

// 如果沒有找到匹配的路由，顯示 404 錯誤頁面
if (!$routeFound) {
    http_response_code(404);
    echo '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - 頁面不存在</title>
    <link rel="stylesheet" href="' . BASE_URL . 'assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="glass-card" style="text-align: center; max-width: 500px; margin: 100px auto;">
            <h1 style="color: #6b46c1; margin-bottom: 20px;">404</h1>
            <h2 style="color: #4c1d95; margin-bottom: 20px;">頁面不存在</h2>
            <p style="color: #666; margin-bottom: 20px;">您要尋找的頁面：<strong>' . htmlspecialchars($uri) . '</strong></p>
            <p style="color: #666; margin-bottom: 30px;">可能已被移動或刪除。</p>
            <a href="' . BASE_URL . '" class="btn btn-primary">返回首頁</a>
            <div style="margin-top: 20px; font-size: 0.9rem; color: #999;">
                <p>除錯資訊：</p>
                <p>原始 URI: ' . htmlspecialchars($_SERVER['REQUEST_URI']) . '</p>
                <p>基礎路徑: ' . htmlspecialchars($basePath) . '</p>
                <p>解析後 URI: ' . htmlspecialchars($uri) . '</p>
            </div>
        </div>
    </div>
</body>
</html>';
}
?> 