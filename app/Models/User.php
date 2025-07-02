<?php
// app/Models/User.php
// PHP 開始標籤，表示這是一個 PHP 檔案
// 檔案路徑註解，說明此檔案位置

require_once __DIR__ . '/Model.php';
// 引入父類別 Model.php，使用 require_once 確保只載入一次

/**
 * 使用者模型
 * 
 * 處理使用者相關的資料操作，包括認證、註冊、使用者管理等功能
 * 繼承基礎模型類別，擴充使用者特有的業務邏輯
 */
class User extends Model {
    // 定義 User 類別，繼承自 Model 父類別
    
    /** @var string 資料表名稱 */
    protected $table = 'users';
    // 宣告受保護的成員變數，指定對應的資料庫資料表名稱
    
    /**
     * 使用者身分驗證
     * 
     * 整合 LDAP 和本地認證機制：
     * 1. 優先嘗試 LDAP 認證
     * 2. LDAP 認證失敗時回歸本地認證（如果啟用）
     * 3. 自動同步 LDAP 使用者資料到本地資料庫
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼（明文）
     * @return array|false 驗證成功回傳使用者資料陣列，失敗回傳 false
     */
    public function authenticate($username, $password) {
        // 1. 嘗試 LDAP 認證
        $ldapUser = $this->authenticateWithLdap($username, $password);
        if ($ldapUser) {
            // LDAP 認證成功，同步使用者資料
            return $this->syncLdapUser($ldapUser);
        }
        
        // 2. LDAP 認證失敗，檢查是否回歸本地認證
        if ($this->shouldFallbackToLocal()) {
            return $this->authenticateLocalPrivate($username, $password);
        }
        
        return false; // 認證失敗
    }
    
    /**
     * LDAP 認證
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼
     * @return array|false LDAP 使用者資料或 false
     */
    private function authenticateWithLdap($username, $password) {
        try {
            // 檢查 LDAP 服務是否可用
            if (!class_exists('LdapService')) {
                require_once __DIR__ . '/../Services/LdapService.php';
            }
            
            $ldapService = new LdapService();
            return $ldapService->authenticate($username, $password);
            
        } catch (Exception $e) {
            // LDAP 認證過程發生錯誤，記錄日誌
            error_log("LDAP 認證錯誤：" . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 本地認證（傳統方式）
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼
     * @return array|false 使用者資料或 false
     */
    private function authenticateLocalPrivate($username, $password) {
        // 根據使用者名稱查詢使用者
        $user = $this->findBy('username', $username);
        
        // 檢查使用者是否存在
        if (!$user) {
            return false;
        }
        
        // 如果是 LDAP 使用者，拒絕本地認證
        if ($user['auth_source'] === 'ldap' || $user['password'] === 'LDAP_AUTH_ONLY') {
            return false;
        }
        
        // 如果使用者存在且密碼驗證正確
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * 公開的本地認證方法（供雙模式登入使用）
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼
     * @return array|false 使用者資料或 false
     */
    public function authenticateLocal($username, $password) {
        return $this->authenticateLocalPrivate($username, $password);
    }
    
    /**
     * 測試資料庫連接
     * 
     * @return bool
     */
    public function testConnection() {
        try {
            $this->db->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * 同步 LDAP 使用者到本地資料庫
     * 
     * @param array $ldapUser LDAP 使用者資料
     * @return array 本地使用者資料
     */
    private function syncLdapUser($ldapUser) {
        // 檢查本地是否已存在該使用者
        $localUser = $this->findBy('username', $ldapUser['username']);
        
        if ($localUser) {
            // 使用者已存在，檢查是否需要同步屬性
            if ($this->shouldSyncAttributes()) {
                $this->updateLdapUserData($localUser['id'], $ldapUser);
                // 重新取得更新後的使用者資料
                $localUser = $this->find($localUser['id']);
            }
            return $localUser;
        } else {
            // 使用者不存在，檢查是否自動建立
            if ($this->shouldAutoCreateUsers()) {
                $userId = $this->createLdapUser($ldapUser);
                return $this->find($userId);
            }
        }
        
        return false;
    }
    
    /**
     * 建立 LDAP 使用者到本地資料庫
     * 
     * @param array $ldapUser LDAP 使用者資料
     * @return int 新建立的使用者 ID
     */
    private function createLdapUser($ldapUser) {
        $userData = [
            'username' => $ldapUser['username'],
            'name' => $ldapUser['name'] ?: $ldapUser['username'],
            'email' => $ldapUser['email'] ?: '',
            'role' => $ldapUser['role'] ?: 'user',
            'status' => 'active',
            'auth_source' => 'ldap',
            'department' => $ldapUser['department'] ?: '',
            'phone' => $ldapUser['phone'] ?: '',
            'title' => $ldapUser['title'] ?: '',
            // LDAP 使用者不儲存本地密碼，使用特殊標識符
            'password' => 'LDAP_AUTH_ONLY',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($userData);
    }
    
    /**
     * 更新 LDAP 使用者資料
     * 
     * @param int $userId 使用者 ID
     * @param array $ldapUser LDAP 使用者資料
     */
    private function updateLdapUserData($userId, $ldapUser) {
        $updateData = [
            'name' => $ldapUser['name'] ?: $ldapUser['username'],
            'email' => $ldapUser['email'] ?: '',
            'role' => $ldapUser['role'] ?: 'user',
            'department' => $ldapUser['department'] ?: '',
            'phone' => $ldapUser['phone'] ?: '',
            'title' => $ldapUser['title'] ?: '',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->update($userId, $updateData);
    }
    
    /**
     * 檢查是否應該回歸本地認證
     * 
     * @return bool
     */
    private function shouldFallbackToLocal() {
        $ldapConfig = $this->getLdapConfig();
        return isset($ldapConfig['fallback_to_local']) && $ldapConfig['fallback_to_local'];
    }
    
    /**
     * 檢查是否應該自動建立使用者
     * 
     * @return bool
     */
    private function shouldAutoCreateUsers() {
        $ldapConfig = $this->getLdapConfig();
        return isset($ldapConfig['auto_create_users']) && $ldapConfig['auto_create_users'];
    }
    
    /**
     * 檢查是否應該同步使用者屬性
     * 
     * @return bool
     */
    private function shouldSyncAttributes() {
        $ldapConfig = $this->getLdapConfig();
        return isset($ldapConfig['sync_attributes']) && $ldapConfig['sync_attributes'];
    }
    
    /**
     * 取得 LDAP 配置
     * 
     * @return array
     */
    private function getLdapConfig() {
        static $config = null;
        if ($config === null) {
            $configFile = __DIR__ . '/../../config/ldap.php';
            $config = file_exists($configFile) ? require $configFile : [];
        }
        return $config;
    }
    // authenticate 方法結束
    
    /**
     * 建立新使用者
     * 
     * 建立新的使用者帳號，自動處理密碼加密和時間戳記
     * 
     * @param array $data 使用者資料陣列，必須包含 password 欄位
     * @return int 新建立的使用者 ID
     */
    public function createUser($data) {
        // 定義建立新使用者方法
        // 加密密碼（使用 PHP 預設的強化演算法）
        if (isset($data['password'])) {
            // 檢查傳入的資料陣列中是否包含 password 欄位
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            // 使用 password_hash 函數加密密碼，採用預設演算法
        }
        // 密碼處理條件結束
        
        // 自動加入建立和更新時間戳記
        $data['created_at'] = date('Y-m-d H:i:s');
        // 設定記錄建立時間為當前時間
        $data['updated_at'] = date('Y-m-d H:i:s');
        // 設定記錄更新時間為當前時間
        
        return $this->create($data);
        // 呼叫父類別的 create 方法新增使用者資料，並回傳新建立的使用者 ID
    }
    // createUser 方法結束
    
    /**
     * 更新使用者資料
     * 
     * 更新使用者資料，如果包含密碼則進行加密
     * 空密碼不會更新現有密碼
     * 
     * @param int $id 使用者 ID
     * @param array $data 要更新的資料陣列
     * @return int 受影響的記錄數量
     */
    public function updateUser($id, $data) {
        // 定義更新使用者資料方法
        // 如果有密碼且不為空，進行加密
        if (isset($data['password']) && !empty($data['password'])) {
            // 檢查是否包含密碼欄位且密碼不為空字串
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            // 對新密碼進行加密
        } else {
            // 如果密碼為空，不更新密碼欄位
            unset($data['password']);
            // 從更新資料中移除密碼欄位，保持原有密碼不變
        }
        // 密碼處理條件結束
        
        // 自動更新時間戳記
        $data['updated_at'] = date('Y-m-d H:i:s');
        // 設定記錄更新時間為當前時間
        
        return $this->update($id, $data);
        // 呼叫父類別的 update 方法更新使用者資料，並回傳受影響的記錄數量
    }
    // updateUser 方法結束
    
    /**
     * 檢查使用者是否為管理員
     * 
     * 根據使用者 ID 檢查該使用者是否具有管理員權限
     * 
     * @param int $userId 使用者 ID
     * @return bool 是管理員回傳 true，否則回傳 false
     */
    public function isAdmin($userId) {
        // 定義檢查管理員權限方法
        $user = $this->find($userId);
        // 使用父類別的 find 方法根據使用者 ID 查詢使用者資料
        return $user && $user['role'] === 'admin';
        // 檢查使用者是否存在且角色欄位值為 'admin'，回傳布林值
    }
    // isAdmin 方法結束
    
    /**
     * 取得所有啟用的使用者
     * 
     * 查詢所有狀態為 active 的使用者，按使用者名稱排序
     * 用於管理介面的使用者列表
     * 
     * @return array 啟用使用者的陣列集合
     */
    public function getActiveUsers() {
        // 定義取得啟用使用者方法
        return $this->where(['status' => 'active'], 'username ASC');
        // 使用父類別的 where 方法查詢狀態為 'active' 的使用者，按使用者名稱升冪排序
    }
    // getActiveUsers 方法結束
    
    /**
     * 檢查使用者名稱是否已存在
     * 
     * 用於註冊時檢查帳號重複
     * 
     * @param string $username 要檢查的使用者名稱
     * @return bool 使用者名稱已存在回傳 true，否則回傳 false
     */
    public function userExists($username) {
        // 定義檢查使用者名稱是否存在方法
        return $this->findBy('username', $username) !== false;
        // 使用父類別的 findBy 方法查詢使用者名稱，如果找到資料則回傳 true，否則回傳 false
    }
    // userExists 方法結束
    
    /**
     * 檢查使用者是否有公告管理權限
     * 
     * 檢查使用者姓名是否包含資訊、人資、總務、財務關鍵字或為管理員
     * 
     * @param int $userId 使用者ID
     * @return bool 是否有公告管理權限
     */
    public function canManageAnnouncements($userId) {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }
        
        // 管理員一律有權限
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // 檢查姓名中是否包含部門關鍵字
        $name = $user['name'];
        $allowedDepartmentKeywords = [
            '資訊',      // 資訊部門
            '人資',      // 人資部門  
            '總務',      // 總務部門
            '財務'       // 財務部門
        ];
        
        foreach ($allowedDepartmentKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 檢查使用者是否可以上傳PDF附件
     * 
     * 檢查使用者姓名是否包含總務人資、資訊、財務關鍵字或為管理員
     * 
     * @param int $userId 使用者ID
     * @return bool 是否可以上傳PDF附件
     */
    public function canUploadPDF($userId) {
        return $this->canManageAnnouncements($userId); // 與公告管理權限相同
    }
    
    /**
     * 根據部門關鍵字取得使用者列表
     * 
     * @param array $departmentKeywords 部門關鍵字陣列
     * @return array 使用者資料陣列
     */
    public function getUsersByDepartments($departmentKeywords) {
        if (empty($departmentKeywords)) {
            return [];
        }
        
        // 建立查詢條件，檢查name欄位是否包含任何一個關鍵字
        $conditions = [];
        $params = [];
        
        foreach ($departmentKeywords as $keyword) {
            $conditions[] = "name LIKE ?";
            $params[] = "%{$keyword}%";
        }
        
        $whereClause = implode(' OR ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE ({$whereClause}) AND status = 'active'";
        
        return $this->db->fetchAll($sql, $params);
    }
} 
// User 類別結束 