<?php
// app/Controllers/AuthController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Models/User.php';

/**
 * 認證控制器
 * 
 * 處理使用者認證相關功能，包括登入、註冊、登出
 * 管理 session 狀態和使用者權限驗證
 */
class AuthController extends Controller {
    /** @var User 使用者模型實例 */
    private $userModel;
    
    /**
     * 建構函式
     * 
     * 初始化使用者模型和全域視圖資料
     */
    public function __construct() {
        $this->userModel = new User();
        $this->setGlobalViewData();
    }
    
    /**
     * 使用者登入處理
     * 
     * GET：顯示登入表單
     * POST：處理登入驗證
     * 
     * 功能：
     * - 驗證使用者輸入的帳號密碼
     * - 設定 session 儲存登入狀態
     * - 重新導向到原本要去的頁面或首頁
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 取得表單資料
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // 基本輸入驗證
            if (empty($username) || empty($password)) {
                $this->view('auth/login', [
                    'title' => '登入',
                    'error' => '請輸入帳號和密碼',
                    'pageType' => 'auth'
                ]);
                return;
            }
            
            // 驗證使用者身分
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                // 登入成功，設定 session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['auth_source'] = $user['auth_source'] ?? 'local';
                
                // 重新導向到原來要去的頁面或首頁
                $redirectTo = $_SESSION['redirect_after_login'] ?? BASE_URL;
                unset($_SESSION['redirect_after_login']);
                
                $this->redirect($redirectTo);
            } else {
                // 登入失敗，顯示錯誤訊息
                $errorMessage = $this->getAuthenticationErrorMessage($username);
                
                $this->view('auth/login', [
                    'title' => '登入',
                    'error' => $errorMessage,
                    'pageType' => 'auth'
                ]);
            }
        } else {
            // GET 請求：顯示登入表單
            
            // 如果已經登入，重新導向到首頁
            if ($this->isLoggedIn()) {
                $this->redirect(BASE_URL);
            }
            
            $this->view('auth/login', [
                'title' => '登入',
                'pageType' => 'auth'
            ]);
        }
    }
    
    /**
     * 使用者註冊處理
     * 
     * GET：顯示註冊表單
     * POST：處理註冊邏輯
     * 
     * 功能：
     * - 驗證註冊資料完整性和正確性
     * - 檢查帳號是否已存在
     * - 建立新使用者帳號
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 取得表單資料
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // 驗證輸入資料
            $errors = [];
            
            // 必填欄位檢查
            if (empty($username)) $errors[] = '請輸入帳號';
            if (empty($password)) $errors[] = '請輸入密碼';
            if (empty($name)) $errors[] = '請輸入姓名';
            if (empty($email)) $errors[] = '請輸入電子郵件';
            
            // 密碼驗證
            if ($password !== $confirmPassword) $errors[] = '密碼確認不一致';
            if (strlen($password) < 6) $errors[] = '密碼至少需要 6 個字元';
            
            // 檢查帳號是否已存在
            if ($this->userModel->userExists($username)) {
                $errors[] = '此帳號已存在';
            }
            
            if (empty($errors)) {
                // 無錯誤：建立新使用者
                $userData = [
                    'username' => $username,
                    'password' => $password,
                    'name' => $name,
                    'email' => $email,
                    'role' => 'user',        // 預設為一般使用者
                    'status' => 'active'     // 預設為啟用狀態
                ];
                
                $userId = $this->userModel->createUser($userData);
                
                if ($userId) {
                    // 註冊成功
                    $this->view('auth/register', [
                        'title' => '註冊',
                        'success' => '註冊成功，請使用您的帳號密碼登入',
                        'pageType' => 'auth'
                    ]);
                } else {
                    // 註冊失敗
                    $this->view('auth/register', [
                        'title' => '註冊',
                        'error' => '註冊失敗，請稍後再試',
                        'pageType' => 'auth'
                    ]);
                }
            } else {
                // 有錯誤：重新顯示表單並保留已輸入的資料
                $this->view('auth/register', [
                    'title' => '註冊',
                    'errors' => $errors,
                    'formData' => $_POST,
                    'pageType' => 'auth'
                ]);
            }
        } else {
            // GET 請求：顯示註冊表單
            $this->view('auth/register', [
                'title' => '註冊',
                'pageType' => 'auth'
            ]);
        }
    }
    
    /**
     * 使用者登出處理
     * 
     * 清除 session 資料並重新導向到首頁
     * 確保完全清除使用者的登入狀態
     */
    public function logout() {
        // 完全銷毀 session
        session_destroy();
        // 重新啟動 session（為了後續使用）
        session_start();
        
        // 重新導向到首頁
        $this->redirect(BASE_URL);
    }
    
    /**
     * 取得認證錯誤訊息
     * 
     * 根據 LDAP 設定提供更詳細的錯誤訊息
     * 
     * @param string $username 使用者名稱
     * @return string 錯誤訊息
     */
    private function getAuthenticationErrorMessage($username) {
        // 載入 LDAP 設定
        $ldapConfig = require __DIR__ . '/../../config/ldap.php';
        
        if ($ldapConfig['enabled']) {
            if ($ldapConfig['fallback_to_local']) {
                return '帳號或密碼錯誤。請確認您的 LDAP 帳號密碼或本地帳號密碼是否正確。';
            } else {
                return '帳號或密碼錯誤。請確認您的 LDAP 帳號密碼是否正確，或聯繫資訊部門檢查帳號狀態。';
            }
        } else {
            return '帳號或密碼錯誤。請確認您的帳號密碼是否正確。';
        }
    }
    
    /**
     * LDAP 連接測試頁面
     * 
     * 提供LDAP診斷和測試功能
     */
    public function ldapTest() {
        // 檢查是否有POST請求
        $testUsername = $_POST['test_username'] ?? '';
        $testPassword = $_POST['test_password'] ?? '';
        $testResult = null;

        if (!empty($testUsername) && !empty($testPassword)) {
            // 執行認證測試
            try {
                $result = $this->userModel->authenticate($testUsername, $testPassword);
                
                if ($result) {
                    $testResult = [
                        'success' => true,
                        'message' => '認證成功！',
                        'data' => $result
                    ];
                } else {
                    $testResult = [
                        'success' => false,
                        'message' => '認證失敗 - 帳號或密碼錯誤'
                    ];
                }
            } catch (Exception $e) {
                $testResult = [
                    'success' => false,
                    'message' => '認證過程發生錯誤: ' . $e->getMessage()
                ];
            }
        }

        // 載入LDAP配置進行顯示
        $ldapConfig = require __DIR__ . '/../../config/ldap.php';

        // 測試LDAP連接
        $connectionTest = null;
        $ldapUsers = [];
        
        if ($ldapConfig['enabled']) {
            try {
                require_once __DIR__ . '/../Services/LdapService.php';
                $ldapService = new LdapService();
                $connectionTest = $ldapService->testConnection();
                
                // 如果連接成功，獲取使用者清單
                if ($connectionTest['success']) {
                    $ldapUsers = $this->getLdapUsersList();
                }
            } catch (Exception $e) {
                $connectionTest = [
                    'success' => false,
                    'message' => 'LDAP服務載入失敗: ' . $e->getMessage(),
                    'details' => []
                ];
            }
        }

        $this->view('auth/ldap-test', [
            'title' => 'LDAP 測試工具',
            'ldapConfig' => $ldapConfig,
            'connectionTest' => $connectionTest,
            'testResult' => $testResult,
            'testUsername' => $testUsername,
            'ldapUsers' => $ldapUsers,
            'pageType' => 'auth'
        ]);
    }
    
    /**
     * 獲取LDAP使用者清單
     * 
     * @return array LDAP使用者清單
     */
    private function getLdapUsersList() {
        try {
            $ldapConfig = require __DIR__ . '/../../config/ldap.php';
            
            // 建立LDAP連接
            $server = $ldapConfig['use_ssl'] 
                ? "ldaps://{$ldapConfig['server']}:{$ldapConfig['port']}"
                : "ldap://{$ldapConfig['server']}:{$ldapConfig['port']}";
                
            $connection = ldap_connect($server);
            
            if (!$connection) {
                return [];
            }
            
            // 設定LDAP選項
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $ldapConfig['timeout']);
            
            // 啟用TLS（如果配置）
            if ($ldapConfig['use_tls']) {
                ldap_start_tls($connection);
            }
            
            // 嘗試綁定
            if (!ldap_bind($connection, $ldapConfig['admin_username'], $ldapConfig['admin_password'])) {
                ldap_unbind($connection);
                return [];
            }
            
            // 搜尋使用者
            $searchFilter = '(objectClass=inetOrgPerson)';
            $attributes = ['uid', 'cn', 'mail', 'telephoneNumber', 'department', 'title'];
            
            $search = ldap_search(
                $connection,
                $ldapConfig['user_search_base'],
                $searchFilter,
                $attributes
            );
            
            if (!$search) {
                ldap_unbind($connection);
                return [];
            }
            
            $entries = ldap_get_entries($connection, $search);
            $users = [];
            
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];
                $users[] = [
                    'uid' => isset($entry['uid'][0]) ? $entry['uid'][0] : '未設定',
                    'cn' => isset($entry['cn'][0]) ? $entry['cn'][0] : '未設定',
                    'mail' => isset($entry['mail'][0]) ? $entry['mail'][0] : '未設定',
                    'department' => isset($entry['department'][0]) ? $entry['department'][0] : '未設定',
                    'title' => isset($entry['title'][0]) ? $entry['title'][0] : '未設定',
                    'phone' => isset($entry['telephonenumber'][0]) ? $entry['telephonenumber'][0] : '未設定'
                ];
            }
            
            ldap_unbind($connection);
            return $users;
            
        } catch (Exception $e) {
            return [];
        }
    }
} 