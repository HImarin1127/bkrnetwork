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
     * 取得 LDAP 設定
     * 
     * @return array LDAP 設定陣列
     */
    private function getLdapConfig() {
        $configPath = __DIR__ . '/../../config/ldap.php';
        if (file_exists($configPath)) {
            return require $configPath;
        }
        return [];
    }

    /**
     * 建立本地使用者
     * 
     * @param array $data 使用者資料
     * @return int|false 新使用者的 ID 或 false
     */
    public function createUser($data) {
        // 檢查使用者名稱是否已存在
        if ($this->findBy('username', $data['username'])) {
            return false;
        }
        
        // 雜湊密碼
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        // 設定 auth_source
        $data['auth_source'] = 'local';
        
        return $this->create($data);
    }
    
    /**
     * 新增記錄 (覆寫 Model::create)
     * 
     * @param array $data 要新增的資料
     * @return int 新增記錄的 ID
     */
    public function create($data) {
        // 設定預設值
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * 更新使用者資料 (覆寫 Model::update)
     * 
     * @param string $username 要更新的使用者名稱
     * @param array $data 要更新的資料
     * @return int 受影響的記錄數量
     */
    public function updateUser($username, $data) {
        // 如果提供了新密碼，進行雜湊
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // 避免空密碼覆蓋現有密碼
            unset($data['password']);
        }
        
        return $this->update($username, $data);
    }

    /**
     * 更新記錄 (覆寫 Model::update)
     * 
     * @param string $username 主鍵值
     * @param array $data 更新資料
     * @return int 受影響的行數
     */
    public function update($username, $data) {
        // 設定更新時間
        $data['updated_at'] = date('Y-m-d H:i:s');
        
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
     * @param string $username 使用者名稱
     * @return bool
     */
    public function isAdmin($username) {
        $user = $this->find($username);
        // 先檢查使用者是否存在，再檢查 role 是否存在且為 'admin'
        return $user && isset($user['role']) && $user['role'] === 'admin';
    }
    
    /**
     * 取得所有活躍使用者
     * 
     * @return array
     */
    public function getActiveUsers() {
        return $this->db->fetchAll("SELECT * FROM users WHERE status = 'active' ORDER BY name");
    }
    
    /**
     * 檢查使用者是否存在
     * 
     * @param string $username 使用者名稱
     * @return bool
     */
    public function userExists($username) {
        return (bool) $this->find($username);
    }

    /**
     * 檢查使用者是否有權限管理公告
     *
     * 權限邏輯：
     * 1. 系統管理員 (`role` = 'admin')
     * 2. 被授權的部門主管或人員 (`permission_manage_announcements` = 1)
     *
     * @param string $username 使用者名稱
     * @return bool
     */
    public function canManageAnnouncements($username) {
        // 根據使用者名稱查詢使用者資料
        $user = $this->find($username);

        // 如果找不到使用者，直接回傳 false
        if (!$user) {
            return false;
        }

        // 檢查是否為系統管理員 (先檢查 role 是否存在)
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // 檢查是否有公告管理權限欄位
        // 假設 `permission_manage_announcements` 是一個 INT 或 BOOLEAN 欄位
        if (isset($user['permission_manage_announcements']) && $user['permission_manage_announcements'] == 1) {
            return true;
        }

        // 如果以上條件都不符合，回傳 false
        return false;
    }
    
    /**
     * 檢查使用者是否有權限上傳PDF
     *
     * @param string $username 使用者名稱
     * @return bool
     */
    public function canUploadPDF($username) {
        // 這裡可以加入更複雜的權限邏輯
        // 目前簡單設定為管理員或有公告管理權限者
        return $this->canManageAnnouncements($username);
    }

    /**
     * 根據部門關鍵字取得使用者
     * 
     * @param array $departmentKeywords 部門關鍵字陣列
     * @return array
     */
    public function getUsersByDepartments($departmentKeywords) {
        $placeholders = implode(',', array_fill(0, count($departmentKeywords), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE department IN ($placeholders)";
        return $this->db->fetchAll($sql, $departmentKeywords);
    }
} 
// User 類別結束 