<?php
// app/Middleware/AuthMiddleware.php

/**
 * 認證中介軟體
 * 
 * 處理使用者認證和授權的中介軟體，負責：
 * - 路由存取權限控制
 * - 登入狀態檢查
 * - 管理員權限驗證
 * - 使用者資訊取得
 * 
 * 支援三種路由類型：
 * - 公開路由：任何人都可以存取
 * - 一般路由：需要登入才能存取
 * - 管理員路由：需要管理員權限才能存取
 */
class AuthMiddleware {
    /** @var array 公開路由列表，不需要登入即可存取 */
    private $publicRoutes;
    
    /** @var array 管理員路由列表，需要管理員權限才能存取 */
    private $adminRoutes;
    
    /**
     * 建構函式
     * 
     * 從設定檔載入路由權限設定
     */
    public function __construct() {
        $config = require __DIR__ . '/../../config/app.php';
        $this->publicRoutes = $config['public_routes'];
        $this->adminRoutes = $config['admin_routes'];
    }
    
    /**
     * 處理路由存取權限檢查
     * 
     * 根據路由類型執行相應的權限檢查：
     * 1. 檢查是否為公開路由
     * 2. 檢查使用者登入狀態
     * 3. 檢查管理員權限（如果是管理員路由）
     * 
     * @param string $route 要檢查的路由
     * @return bool 有權限存取回傳 true，否則進行重新導向
     */
    public function handle($route) {
        // 檢查是否為公開路由（無需登入）
        if ($this->isPublicRoute($route)) {
            return true;
        }
        
        // 檢查使用者是否已登入
        if (!$this->isLoggedIn()) {
            // 未登入：重新導向到登入頁面
            header('Location: /bkrnetwork/public/index.php?route=/login');
            exit();
        }
        
        // 檢查是否為管理員路由
        if ($this->isAdminRoute($route)) {
            if (!$this->isAdmin()) {
                // 非管理員：重新導向到首頁
                header('Location: /bkrnetwork/public/index.php?route=/home');
                exit();
            }
        }
        
        return true;
    }
    
    /**
     * 檢查是否為公開路由
     * 
     * @param string $route 路由路徑
     * @return bool 是公開路由回傳 true，否則回傳 false
     */
    private function isPublicRoute($route) {
        return in_array($route, $this->publicRoutes);
    }
    
    /**
     * 檢查是否為管理員路由
     * 
     * 使用前綴比對，支援巢狀管理員路由
     * 例如：/admin、/admin/users、/admin/settings 等
     * 
     * @param string $route 路由路徑
     * @return bool 是管理員路由回傳 true，否則回傳 false
     */
    private function isAdminRoute($route) {
        foreach ($this->adminRoutes as $adminRoute) {
            if (strpos($route, $adminRoute) === 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 檢查使用者是否已登入
     * 
     * 檢查 session 中是否存在 user_id
     * 
     * @return bool 已登入回傳 true，否則回傳 false
     */
    private function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * 檢查使用者是否為管理員
     * 
     * 檢查 session 中的使用者角色是否為 admin
     * 
     * @return bool 是管理員回傳 true，否則回傳 false
     */
    private function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * 靜態方法：要求使用者必須登入
     * 
     * 在控制器方法中呼叫，確保使用者已登入
     * 未登入時自動重新導向到登入頁面
     */
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /bkrnetwork/public/index.php?route=/login');
            exit();
        }
    }
    
    /**
     * 靜態方法：要求使用者必須為管理員
     * 
     * 在控制器方法中呼叫，確保使用者具有管理員權限
     * 非管理員時自動重新導向到首頁
     */
    public static function requireAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /bkrnetwork/public/index.php?route=/home');
            exit();
        }
    }
    
    /**
     * 靜態方法：取得目前登入的使用者資訊
     * 
     * 根據認證模式取得使用者資料：
     * - LDAP模式：從session建構使用者資料
     * - 本地模式：從資料庫查詢使用者資料
     * 
     * @return array|null 使用者資訊陣列，未登入時回傳 null
     */
    public static function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            // 檢查認證模式
            $authMode = $_SESSION['auth_mode'] ?? 'local';
            
            if ($authMode === 'ldap') {
                // LDAP 模式：從 session 建構使用者資料
                return [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'] ?? $_SESSION['user_id'],
                    'name' => $_SESSION['name'] ?? $_SESSION['username'] ?? $_SESSION['user_id'],
                    'email' => $_SESSION['email'] ?? '',
                    'role' => $_SESSION['role'] ?? 'user',
                    'status' => 'active',
                    'auth_source' => 'ldap',
                    'department' => $_SESSION['department'] ?? '',
                    'phone' => $_SESSION['phone'] ?? '',
                    'title' => $_SESSION['title'] ?? ''
                ];
            } else {
                // 本地模式：從資料庫查詢最新的使用者資料
                require_once __DIR__ . '/../Models/Database.php';
                require_once __DIR__ . '/../Models/User.php';
                
                $userModel = new User();
                return $userModel->find($_SESSION['user_id']);
            }
        }
        return null;
    }
} 