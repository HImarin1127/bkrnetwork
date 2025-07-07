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

    /** @var string 主鍵 */
    protected $primaryKey = 'username';
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
     * 根據使用者名稱查詢單筆記錄 (覆寫 Model::find)
     * 
     * @param string $username 使用者名稱
     * @return array|false 找到記錄時回傳關聯陣列，找不到時回傳 false
     */
    public function find($username) {
        return $this->findBy($this->primaryKey, $username);
    }

    /**
     * 刪除記錄 (覆寫 Model::delete)
     * 
     * @param string $username 要刪除的使用者名稱
     * @return int 受影響的記錄數量
     */
    public function delete($username) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$username]);
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
        // if ($user['auth_source'] === 'ldap' || $user['password'] === 'LDAP_AUTH_ONLY') {
        //     return false;
        // }
        
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
                $this->updateLdapUserData($localUser['username'], $ldapUser);
                // 重新取得更新後的使用者資料
                $localUser = $this->find($localUser['username']);
            }
            return $localUser;
        } else {
            // 使用者不存在，檢查是否自動建立
            if ($this->shouldAutoCreateUsers()) {
                $username = $this->createLdapUser($ldapUser);
                return $this->find($username);
            }
        }
        
        return false;
    }
    
    /**
     * 建立 LDAP 使用者到本地資料庫
     * 
     * @param array $ldapUser LDAP 使用者資料
     * @return string 新建立的使用者 username
     */
    private function createLdapUser($ldapUser) {
        $userData = [
            'username' => $ldapUser['username'],
            'name' => $ldapUser['name'] ?: $ldapUser['username'],
            'email' => $ldapUser['email'] ?: '',
            // 'role' => $ldapUser['role'] ?: 'user',
            // 'status' => 'active',
            // 'auth_source' => 'ldap',
            // 'department' => $ldapUser['department'] ?: '',
            // 'phone' => $ldapUser['phone'] ?: '',
            // 'title' => $ldapUser['title'] ?: '',
            // LDAP 使用者不儲存本地密碼，使用特殊標識符
            'password' => 'LDAP_AUTH_ONLY',
            // 'created_at' => date('Y-m-d H:i:s'),
            // 'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->create($userData);
        return $userData['username'];
    }
    
    /**
     * 更新 LDAP 使用者資料
     * 
     * @param string $username 使用者名稱
     * @param array $ldapUser LDAP 使用者資料
     */
    private function updateLdapUserData($username, $ldapUser) {
        $updateData = [
            'name' => $ldapUser['name'] ?: $ldapUser['username'],
            'email' => $ldapUser['email'] ?: '',
            // 'role' => $ldapUser['role'] ?: 'user',
            // 'department' => $ldapUser['department'] ?: '',
            // 'phone' => $ldapUser['phone'] ?: '',
            // 'title' => $ldapUser['title'] ?: '',
            // 'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->update($username, $updateData);
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
     * 建立新的使用者帳號，自動處理密碼加密和時間戳記
     * 
     * @param array $data 使用者資料陣列，必須包含 password 欄位
     * @return string|false 新建立的使用者 username，失敗回傳 false
     */
    public function createUser($data) {
        // 移除已不存在的欄位
        // $data['created_at'] = date('Y-m-d H:i:s');
        // $data['updated_at'] = date('Y-m-d H:i:s');
        
        // 確保有密碼欄位
        if (isset($data['password'])) {
            // 加密密碼（使用 PHP 預設的強化演算法）
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            // 使用 password_hash 函數加密密碼，採用預設演算法
        }
        // 密碼處理條件結束
        
        if ($this->create($data)) {
            return $data['username'];
        }
        return false;
    }
    
    /**
     * 新增記錄 (覆寫 Model::create)
     * 
     * @param array $data 要新增的資料陣列，格式：['欄位名' => '值']
     * @return bool 新增成功回傳 true
     */
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->db->execute($sql, array_values($data)) !== false;
    }

    /**
     * 更新使用者資料
     * 
     * 更新使用者資料，如果包含密碼則進行加密
     * 空密碼不會更新現有密碼
     * 
     * @param string $username 使用者名稱
     * @param array $data 要更新的資料陣列
     * @return int|false 受影響的記錄數量或 false
     */
    public function updateUser($username, $data) {
        // 移除已不存在的欄位
        // $data['updated_at'] = date('Y-m-d H:i:s');

        // 如果有密碼且不為空，進行加密
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // 如果密碼為空，不更新密碼欄位
            unset($data['password']);
        }
        // 密碼處理條件結束
        
        return $this->update($username, $data);
    }

    /**
     * 更新記錄 (覆寫 Model::update)
     * 
     * @param string $username 要更新的使用者名稱
     * @param array $data 要更新的資料陣列，格式：['欄位名' => '值']
     * @return int 受影響的記錄數量
     */
    public function update($username, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $params = array_values($data);
        $params[] = $username;
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * 檢查使用者是否為管理員
     * 
     * 根據使用者名稱檢查該使用者是否具有管理員權限
     * 
     * @param string $username 使用者名稱
     * @return bool 使用者是否為管理員
     */
    public function isAdmin($username) {
        // $user = $this->find($username);
        // return $user && $user['role'] === 'admin';
        return false; // 移除 role 欄位後，暫時禁用此功能
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
        // return $this->where(['status' => 'active']);
        return $this->all(); // 移除 status 欄位後，回傳所有使用者
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
     * @param string $username 使用者名稱
     * @return bool 是否有公告管理權限
     */
    public function canManageAnnouncements($username) {
        $user = $this->find($username);
        if (!$user) {
            return false;
        }
        
        // 管理員 'admin' 固定有權限
        if ($username === 'admin') {
            return true;
        }
        
        // 檢查姓名中是否包含部門關鍵字
        $name = $user['name'];
        $allowedDepartmentKeywords = [
            '總務',
            '人資',
            '資訊',
            '財務'
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
     * @param string $username 使用者名稱
     * @return bool 是否可以上傳PDF附件
     */
    public function canUploadPDF($username) {
        // 權限與公告管理相同
        return $this->canManageAnnouncements($username);
    }
    
    /**
     * 根據部門關鍵字取得使用者列表
     * 
     * @param array $departmentKeywords 部門關鍵字陣列
     * @return array 使用者資料陣列
     */
    public function getUsersByDepartments($departmentKeywords) {
        // $sql = "SELECT * FROM {$this->table} WHERE ";
        // $conditions = [];
        // $params = [];
        
        // foreach ($departmentKeywords as $keyword) {
        //     $conditions[] = "department LIKE ?";
        //     $params[] = '%' . $keyword . '%';
        // }
        
        // $sql .= implode(' OR ', $conditions);
        
        // return $this->db->fetchAll($sql, $params);
        return []; // 移除 department 欄位後，此功能已失效
    }
} 
// User 類別結束 