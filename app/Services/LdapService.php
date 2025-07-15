<?php
// app/Services/LdapService.php

namespace App\Services;

use Exception;

/**
 * LDAP 認證服務
 * 
 * 提供完整的 LDAP 認證功能，包括：
 * - LDAP 伺服器連接管理
 * - 使用者認證驗證
 * - 使用者資料同步
 * - 群組權限檢查
 * - 錯誤處理和日誌記錄
 */
class LdapService {
    /** @var array LDAP 配置 */
    private $config;
    
    /** @var resource LDAP 連接資源 */
    private $connection;
    
    /** @var bool 是否已連接 */
    private $connected = false;
    
    /**
     * 建構函式
     * 
     * 載入 LDAP 配置並初始化服務
     */
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/ldap.php';
    }
    
    /**
     * 建立 LDAP 連接
     * 
     * 連接到 LDAP 伺服器並進行管理員身分驗證
     * 
     * @return bool 連接成功回傳 true，失敗回傳 false
     * @throws Exception 當連接失敗時拋出例外
     */
    public function connect() {
        if ($this->connected) {
            return true; // 已經連接
        }
        
        try {
            // 檢查 LDAP 擴充套件
            if (!extension_loaded('ldap')) {
                throw new Exception('PHP LDAP 擴充套件未安裝');
            }
            
            // 建立 LDAP 連接
            $server = $this->config['use_ssl'] 
                ? "ldaps://{$this->config['server']}:{$this->config['port']}"
                : "ldap://{$this->config['server']}:{$this->config['port']}";
                
            $this->connection = ldap_connect($server);
            
            if (!$this->connection) {
                throw new Exception('無法連接到 LDAP 伺服器');
            }
            
            // 設定 LDAP 選項
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, $this->config['timeout']);
            
            // 啟用 TLS（如果配置）
            if ($this->config['use_tls'] && !ldap_start_tls($this->connection)) {
                throw new Exception('無法啟動 TLS 加密：' . ldap_error($this->connection));
            }
            
            // 管理員身分驗證
            if (!ldap_bind($this->connection, $this->config['admin_username'], $this->config['admin_password'])) {
                throw new Exception('LDAP 管理員認證失敗：' . ldap_error($this->connection));
            }
            
            $this->connected = true;
            $this->log('LDAP 連接成功');
            
            return true;
            
        } catch (Exception $e) {
            $this->log('LDAP 連接失敗：' . $e->getMessage(), 'error');
            $this->disconnect();
            throw $e;
        }
    }
    
    /**
     * 中斷 LDAP 連接
     */
    public function disconnect() {
        if ($this->connection) {
            ldap_unbind($this->connection);
            $this->connection = null;
            $this->connected = false;
        }
    }
    
    /**
     * 使用者 LDAP 認證
     * 
     * 驗證使用者帳號密碼是否正確
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼
     * @return array|false 認證成功回傳使用者資料，失敗回傳 false
     */
    public function authenticate($username, $password) {
        try {
            // 檢查 LDAP 是否啟用
            if (!$this->config['enabled']) {
                return false;
            }
            
            // 建立連接
            if (!$this->connect()) {
                return false;
            }
            
            // 搜尋使用者
            $userDn = $this->findUserDn($username);
            if (!$userDn) {
                $this->log("找不到使用者：{$username}");
                return false;
            }
            
            // 驗證使用者密碼
            $userConnection = ldap_connect($this->getServerUrl());
            if (!$userConnection) {
                throw new Exception('無法建立使用者認證連接');
            }
            
            // 設定連接選項
            ldap_set_option($userConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($userConnection, LDAP_OPT_REFERRALS, 0);
            
            // 啟用 TLS（如果配置）
            if ($this->config['use_tls']) {
                ldap_start_tls($userConnection);
            }
            
            // 嘗試使用使用者憑證進行綁定
            if (!@ldap_bind($userConnection, $userDn, $password)) {
                ldap_unbind($userConnection);
                $this->log("使用者認證失敗：{$username}");
                return false;
            }
            
            ldap_unbind($userConnection);
            
            // 取得使用者詳細資料
            $userData = $this->getUserData($username);
            if (!$userData) {
                return false;
            }
            
            $this->log("使用者認證成功：{$username}");
            return $userData;
            
        } catch (Exception $e) {
            $this->log("認證過程發生錯誤：" . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * 搜尋使用者 DN
     * 
     * 根據使用者名稱搜尋對應的 Distinguished Name
     * 如果搜尋失敗（權限不足），則構造標準的 DN 格式
     * 
     * @param string $username 使用者名稱
     * @return string|false 找到回傳 DN，否則回傳 false
     */
    private function findUserDn($username) {
        $filter = str_replace('{username}', ldap_escape($username, '', LDAP_ESCAPE_FILTER), $this->config['user_filter']);
        
        // 嘗試搜尋使用者
        $search = @ldap_search(
            $this->connection,
            $this->config['user_search_base'],
            $filter,
            ['dn']
        );
        
        if ($search) {
            $entries = ldap_get_entries($this->connection, $search);
            if ($entries['count'] === 1) {
                return $entries[0]['dn'];
            }
        }
        
        // 如果搜尋失敗且啟用了直接綁定模式，構造標準的 DN
        if (isset($this->config['direct_bind_mode']) && $this->config['direct_bind_mode']) {
            $this->log("搜尋失敗，嘗試構造 DN（直接綁定模式）：" . ldap_error($this->connection), 'warning');
            
            // 根據配置的 user_search_base 構造 DN
            // 假設使用 uid 屬性，這是最常見的格式
            $constructedDn = "uid={$username},{$this->config['user_search_base']}";
            $this->log("構造的 DN：{$constructedDn}");
            
            return $constructedDn;
        }
        
        // 如果未啟用直接綁定模式，則搜尋失敗時返回 false
        $this->log("搜尋失敗且未啟用直接綁定模式", 'error');
        return false;
    }
    
    /**
     * 取得使用者資料
     * 
     * 從 LDAP 取得使用者的完整資料
     * 如果搜尋失敗（權限不足），則返回基本的使用者資料
     * 
     * @param string $username 使用者名稱
     * @return array|false 使用者資料陣列或 false
     */
    public function getUserData($username) {
        try {
            if (!$this->connect()) {
                return false;
            }
            
            $filter = str_replace('{username}', ldap_escape($username, '', LDAP_ESCAPE_FILTER), $this->config['user_filter']);
            
            // 設定要取得的屬性
            $attributes = array_values($this->config['attributes']);
            $attributes[] = 'memberOf'; // 加入群組成員資訊
            
            $search = @ldap_search(
                $this->connection,
                $this->config['user_search_base'],
                $filter,
                $attributes
            );
            
            if ($search) {
                $entries = ldap_get_entries($this->connection, $search);
                if ($entries['count'] === 1) {
                    $ldapUser = $entries[0];
                    
                    // 對應屬性到系統欄位
                    $userData = [];
                    foreach ($this->config['attributes'] as $field => $attribute) {
                        $userData[$field] = isset($ldapUser[$attribute][0]) ? $ldapUser[$attribute][0] : '';
                    }
                    
                    // 檢查群組權限
                    $userData['role'] = $this->determineUserRole($ldapUser);
                    $userData['status'] = 'active';
                    $userData['auth_source'] = 'ldap';
                    
                    return $userData;
                }
            }
            
            // 如果搜尋失敗且啟用了直接綁定模式，返回基本使用者資料
            if (isset($this->config['direct_bind_mode']) && $this->config['direct_bind_mode']) {
                $this->log("搜尋使用者資料失敗，使用基本資料（直接綁定模式）：" . ldap_error($this->connection), 'warning');
                
                return [
                    'username' => $username,
                    'name' => $username, // 使用用戶名作為顯示名稱
                    'email' => $username . '@bookrep.com.tw', // 構造基本郵箱
                    'department' => '',
                    'phone' => '',
                    'title' => '',
                    'role' => 'user', // 預設為一般使用者
                    'status' => 'active',
                    'auth_source' => 'ldap'
                ];
            }
            
            // 如果未啟用直接綁定模式，則搜尋失敗時返回 false
            $this->log("搜尋使用者資料失敗且未啟用直接綁定模式", 'error');
            return false;
            
        } catch (Exception $e) {
            $this->log("取得使用者資料失敗：" . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * 確定使用者角色
     * 
     * 根據 LDAP 群組成員資格確定使用者在系統中的角色
     * 
     * @param array $ldapUser LDAP 使用者資料
     * @return string 使用者角色（admin 或 user）
     */
    private function determineUserRole($ldapUser) {
        if (!isset($ldapUser['memberof'])) {
            return 'user'; // 預設為一般使用者
        }
        
        $userGroups = $ldapUser['memberof'];
        if (!is_array($userGroups)) {
            $userGroups = [$userGroups];
        }
        
        // 檢查是否為管理員群組成員
        foreach ($this->config['admin_groups'] as $adminGroup) {
            foreach ($userGroups as $userGroup) {
                if (is_string($userGroup) && stripos($userGroup, $adminGroup) !== false) {
                    return 'admin';
                }
            }
        }
        
        return 'user';
    }
    
    /**
     * 檢查使用者是否有權限登入
     * 
     * @param array $ldapUser LDAP 使用者資料
     * @return bool 有權限回傳 true，否則回傳 false
     */
    public function checkUserPermissions($ldapUser) {
        // 如果沒有設定允許群組，表示允許所有使用者
        if (empty($this->config['allowed_groups'])) {
            return true;
        }
        
        if (!isset($ldapUser['memberof'])) {
            return false;
        }
        
        $userGroups = $ldapUser['memberof'];
        if (!is_array($userGroups)) {
            $userGroups = [$userGroups];
        }
        
        // 檢查是否為允許登入的群組成員
        foreach ($this->config['allowed_groups'] as $allowedGroup) {
            foreach ($userGroups as $userGroup) {
                if (is_string($userGroup) && stripos($userGroup, $allowedGroup) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 測試 LDAP 連接
     * 
     * 測試 LDAP 設定是否正確，用於系統設定檢查
     * 
     * @return array 測試結果
     */
    public function testConnection() {
        $result = [
            'success' => false,
            'message' => '',
            'details' => []
        ];
        
        try {
            // 檢查擴充套件
            if (!extension_loaded('ldap')) {
                throw new Exception('PHP LDAP 擴充套件未安裝');
            }
            $result['details'][] = '✓ PHP LDAP 擴充套件已載入';
            
            // 測試連接
            $this->connect();
            $result['details'][] = '✓ LDAP 伺服器連接成功';
            
            // 測試搜尋（如果失敗也不是致命錯誤）
            $testSearch = @ldap_search(
                $this->connection,
                $this->config['base_dn'],
                '(objectClass=*)',
                ['dn'],
                0,
                1
            );
            
            if ($testSearch) {
                $result['details'][] = '✓ LDAP 搜尋測試成功';
            } else {
                $result['details'][] = '⚠️ LDAP 搜尋權限有限（這是正常的，如果使用普通帳號）';
            }
            
            $result['success'] = true;
            $result['message'] = 'LDAP 連接測試成功';
            
        } catch (Exception $e) {
            $result['message'] = 'LDAP 連接測試失敗：' . $e->getMessage();
            $result['details'][] = '✗ ' . $e->getMessage();
        } finally {
            $this->disconnect();
        }
        
        return $result;
    }
    
    /**
     * 取得伺服器 URL
     * 
     * @return string LDAP 伺服器 URL
     */
    private function getServerUrl() {
        return $this->config['use_ssl']
            ? "ldaps://{$this->config['server']}:{$this->config['port']}"
            : "ldap://{$this->config['server']}:{$this->config['port']}";
    }
    
    /**
     * 取得所有 LDAP 使用者
     * 
     * 搜尋 LDAP 目錄中的所有使用者帳號
     * 
     * @return array 使用者陣列
     */
    public function getAllUsers() {
        try {
            if (!$this->connect()) {
                return [];
            }
            
            $search = ldap_search(
                $this->connection,
                $this->config['user_search_base'],
                '(objectClass=inetOrgPerson)',
                ['uid', 'cn', 'mail']
            );
            
            if (!$search) {
                return [];
            }
            
            $entries = ldap_get_entries($this->connection, $search);
            $users = [];
            
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];
                if (isset($entry['uid'][0]) && !empty($entry['uid'][0])) {
                    $users[] = [
                        'username' => $entry['uid'][0],
                        'name' => $entry['cn'][0] ?? '',
                        'email' => $entry['mail'][0] ?? ''
                    ];
                }
            }
            
            return $users;
            
        } catch (Exception $e) {
            $this->log("取得使用者列表失敗：" . $e->getMessage(), 'error');
            return [];
        }
    }
    
    /**
     * 記錄日誌
     * 
     * @param string $message 日誌訊息
     * @param string $level 日誌等級（info, error, warning）
     */
    private function log($message, $level = 'info') {
        if ($this->config['debug']) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] LDAP {$level}: {$message}\n";
            
            // 寫入日誌檔案
            $logFile = __DIR__ . '/../../logs/ldap.log';
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * 解構函式
     * 
     * 確保 LDAP 連接被正確關閉
     */
    public function __destruct() {
        $this->disconnect();
    }
} 