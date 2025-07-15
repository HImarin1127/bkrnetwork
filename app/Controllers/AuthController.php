<?php
/**
 * AuthController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理使用者認證，包括登入、登出、註冊和 LDAP 整合。
 */
namespace App\Controllers;

use App\Models\User;
use App\Services\LdapService;

// require_once __DIR__ . '/Controller.php'; // 由 composer 的 autoloader 處理
// require_once __DIR__ . '/../Models/Database.php'; // 由 composer 的 autoloader 處理
// require_once __DIR__ . '/../Services/LdapService.php'; // 由 composer 的 autoloader 處理

/**
 * Class AuthController
 * 
 * 負責處理所有與使用者身份驗證相關的功能。
 * 支援 LDAP 優先，本地資料庫備援的混合認證模式。
 *
 * @package App\Controllers
 */
class AuthController extends Controller {
    /** @var User 使用者模型實例 */
    private $userModel;
    
    /** @var LdapService|null LDAP服務實例，僅在啟用時實例化 */
    private $ldapService = null;
    
    /**
     * AuthController 的建構函式。
     *
     * 初始化 User 模型，並根據設定檔條件性地初始化 LdapService。
     */
    public function __construct() {
        // 呼叫父類別的建構函式，以確保 session 啟動和全域變數設定
        parent::__construct();
        $this->userModel = new User();
        
        // 只有在 LDAP 功能啟用時，才建立 LdapService 實例
        $config = require __DIR__ . '/../../config/ldap.php';
        if (isset($config['enabled']) && $config['enabled']) {
            $this->ldapService = new LdapService();
        }
    }
    
    /**
     * 處理使用者登入請求。
     * 
     * - GET: 顯示登入表單。
     * - POST: 驗證使用者憑證。
     *
     * 認證流程：
     * 1. 如果 LDAP 已啟用，優先使用 LDAP 進行認證。
     *    - 成功後，檢查本地資料庫是否存在該使用者，若無則自動同步建立。
     * 2. 如果 LDAP 未啟用或認證失敗，則回退至本地資料庫進行認證。
     * 3. 認證成功後，設定 session 並將使用者導向目標頁面。
     * 4. 認證失敗，則顯示相應的錯誤訊息。
     *
     * @return void
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // 基礎輸入驗證
            if (empty($username) || empty($password)) {
                $this->view('auth/login', ['title' => '登入', 'error' => '請輸入帳號和密碼', 'pageType' => 'auth']);
                return;
            }
            
            // 優先嘗試 LDAP 認證 (如果已啟用)
            if ($this->ldapService && $this->ldapService->authenticate($username, $password)) {
                // LDAP 認證成功
                
                // 檢查使用者是否存在於本地資料庫
                $user = $this->userModel->find($username);
                
                // 如果本地不存在此 LDAP 使用者，則從 LDAP 獲取資訊並在本地建立一筆記錄
                if (!$user) {
                    $userInfo = $this->ldapService->getUserInfo($username);
                    if ($userInfo) {
                        $this->userModel->createOrUpdateFromLdap($userInfo);
                         // 注意：此處未重新獲取 $user，若後續操作依賴新建的 user 會有問題
                    }
                }
                
                // 設定 session
                // 風險：如果上面的 $user 不存在且建立失敗，$user['username'] 會報錯
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['name'];
                
                // 重新導向到原來要去的頁面或首頁
                $redirectTo = $_SESSION['redirect_after_login'] ?? BASE_URL;
                unset($_SESSION['redirect_after_login']);
                
                $this->redirect($redirectTo);
            } else {
                // 如果 LDAP 認證失敗或未啟用，則回退使用本地資料庫認證
                $user = $this->userModel->authenticate($username, $password);
                if ($user) {
                    // 本地認證成功，設定 session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // 重新導向到原來要去的頁面或首頁
                    $redirectTo = $_SESSION['redirect_after_login'] ?? BASE_URL;
                    unset($_SESSION['redirect_after_login']);
                    
                    $this->redirect($redirectTo);
                } else {
                    // 所有方式登入失敗，顯示錯誤訊息
                    $errorMessage = $this->getAuthenticationErrorMessage($username);
                    
                    $this->view('auth/login', [
                        'title' => '登入',
                        'error' => $errorMessage,
                        'pageType' => 'auth'
                    ]);
                }
            }
        } else {
            // GET 請求：顯示登入表單
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
     * 處理使用者註冊請求。
     *
     * - GET: 顯示註冊表單。
     * - POST: 驗證輸入資料並建立新使用者。
     *
     * @return void
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 從表單獲取資料
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // 進行一系列的資料驗證
            $errors = [];
            
            // 必填欄位檢查
            if (empty($username)) $errors[] = '請輸入帳號';
            if (empty($password)) $errors[] = '請輸入密碼';
            if (empty($name)) $errors[] = '請輸入姓名';
            if (empty($email)) $errors[] = '請輸入電子郵件';
            
            // 驗證密碼一致性與長度
            if ($password !== $confirmPassword) $errors[] = '密碼確認不一致';
            if (strlen($password) < 6) $errors[] = '密碼至少需要 6 個字元';
            
            // 檢查使用者名稱是否已被使用
            if ($this->userModel->userExists($username)) {
                $errors[] = '此帳號已存在';
            }
            
            // 如果沒有驗證錯誤，則建立使用者
            if (empty($errors)) {
                $userData = [
                    'username' => $username,
                    'password' => $password,
                    'name' => $name,
                    'email' => $email,
                ];
                
                $newUsername = $this->userModel->createUser($userData);
                
                if ($newUsername) {
                    // 註冊成功，顯示成功訊息
                    $this->view('auth/register', [
                        'title' => '註冊',
                        'success' => '註冊成功，請使用您的帳號密碼登入',
                        'pageType' => 'auth'
                    ]);
                } else {
                    // 註冊失敗，顯示通用錯誤訊息
                    $this->view('auth/register', [
                        'title' => '註冊',
                        'error' => '註冊失敗，請稍後再試',
                        'pageType' => 'auth'
                    ]);
                }
            } else {
                // 如果有驗證錯誤，重新顯示註冊表單並附上錯誤訊息和已填寫的資料
                $this->view('auth/register', [
                    'title' => '註冊',
                    'errors' => $errors,
                    'formData' => $_POST,
                    'pageType' => 'auth'
                ]);
            }
        } else {
            // GET 請求：顯示空白的註冊表單
            $this->view('auth/register', [
                'title' => '註冊',
                'pageType' => 'auth'
            ]);
        }
    }
    
    /**
     * 處理使用者登出請求。
     * 
     * 完全銷毀目前的 session，然後將使用者重新導向至首頁。
     *
     * @return void
     */
    public function logout() {
        // 銷毀所有 session 資料
        session_destroy();
        // 登出後，最好重新啟動一個新的 session，避免潛在的 session fixation 問題
        session_start();
        
        // 將使用者重新導向至網站首頁
        $this->redirect(BASE_URL);
    }
    
    /**
     * 根據系統設定生成對應的登入失敗錯誤訊息。
     * 
     * @param string $username 使用者嘗試登入的帳號。
     * @return string 本地化的錯誤訊息字串。
     */
    private function getAuthenticationErrorMessage($username) {
        $ldapConfig = require __DIR__ . '/../../config/ldap.php';
        
        if ($ldapConfig['enabled']) {
            if ($ldapConfig['fallback_to_local']) {
                // 混合模式下的提示
                return '帳號或密碼錯誤。請確認您的 LDAP 帳號密碼或本地帳號密碼是否正確。';
            } else {
                // 純 LDAP 模式下的提示
                return '帳號或密碼錯誤。請確認您的 LDAP 帳號密碼是否正確，或聯繫資訊部門檢查帳號狀態。';
            }
        } else {
            // 純本地資料庫模式下的提示
            return '帳號或密碼錯誤。請確認您的帳號密碼是否正確。';
        }
    }
    
    /**
     * 顯示 LDAP 連線測試頁面。
     *
     * 此頁面為管理者提供了一個診斷 LDAP 服務連線狀態的工具。
     *
     * @return void
     */
    public function ldapTest() {
        // 檢查 LdapService 是否已初始化
        if (!$this->ldapService) {
            $this->view('auth/ldap-test', [
                'title' => 'LDAP 連線測試',
                'error' => 'LDAP 服務未在設定檔中啟用或設定不正確。'
            ]);
            return;
        }

        try {
            // 嘗試連線並獲取使用者列表
            $connectionTest = $this->ldapService->testConnection();
            $users = $this->ldapService->listAllUsers();
            
            $this->view('auth/ldap-test', [
                'title' => 'LDAP 測試工具',
                'connectionTest' => $connectionTest,
                'users' => $users,
                'pageType' => 'auth'
            ]);
        } catch (\Exception $e) {
            // 捕捉並顯示測試過程中可能發生的任何例外
            $this->view('auth/ldap-test', [
                'title' => 'LDAP 測試工具',
                'error' => '測試過程發生例外錯誤: ' . $e->getMessage(),
                'pageType' => 'auth'
            ]);
        }
    }
    
    /**
     * (已棄用) 獲取 LDAP 使用者列表的內部實作。
     * 
     * @deprecated 1.0.0 此方法邏輯已移至 LdapService 中，請改用 $this->ldapService->listAllUsers()。
     * @return array LDAP 使用者清單。
     */
    private function getLdapUsersList() {
        // 此方法已棄用，直接回傳空陣列或拋出錯誤，以避免使用舊的程式碼。
        // 為了相容性暫時保留，但應盡快移除。
        if ($this->ldapService) {
            try {
                return $this->ldapService->listAllUsers();
            } catch (\Exception $e) {
                error_log("getLdapUsersList (deprecated) failed: " . $e->getMessage());
                return [];
            }
        }
        return [];
    }
} 