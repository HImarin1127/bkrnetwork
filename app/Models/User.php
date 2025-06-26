<?php
// app/Models/User.php

require_once __DIR__ . '/Model.php';

/**
 * 使用者模型
 * 
 * 處理使用者相關的資料操作，包括認證、註冊、使用者管理等功能
 * 繼承基礎模型類別，擴充使用者特有的業務邏輯
 */
class User extends Model {
    /** @var string 資料表名稱 */
    protected $table = 'users';
    
    /**
     * 使用者身分驗證
     * 
     * 根據使用者名稱和密碼驗證使用者身分
     * 使用安全的密碼驗證機制
     * 
     * @param string $username 使用者名稱
     * @param string $password 密碼（明文）
     * @return array|false 驗證成功回傳使用者資料陣列，失敗回傳 false
     */
    public function authenticate($username, $password) {
        // 根據使用者名稱查詢使用者
        $user = $this->findBy('username', $username);
        
        // 如果使用者存在且密碼驗證正確
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * 建立新使用者
     * 
     * 建立新的使用者帳號，自動處理密碼加密和時間戳記
     * 
     * @param array $data 使用者資料陣列，必須包含 password 欄位
     * @return int 新建立的使用者 ID
     */
    public function createUser($data) {
        // 加密密碼（使用 PHP 預設的強化演算法）
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // 自動加入建立和更新時間戳記
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
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
        // 如果有密碼且不為空，進行加密
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // 如果密碼為空，不更新密碼欄位
            unset($data['password']);
        }
        
        // 自動更新時間戳記
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    /**
     * 檢查使用者是否為管理員
     * 
     * 根據使用者 ID 檢查該使用者是否具有管理員權限
     * 
     * @param int $userId 使用者 ID
     * @return bool 是管理員回傳 true，否則回傳 false
     */
    public function isAdmin($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }
    
    /**
     * 取得所有啟用的使用者
     * 
     * 查詢所有狀態為 active 的使用者，按使用者名稱排序
     * 用於管理介面的使用者列表
     * 
     * @return array 啟用使用者的陣列集合
     */
    public function getActiveUsers() {
        return $this->where(['status' => 'active'], 'username ASC');
    }
    
    /**
     * 檢查使用者名稱是否已存在
     * 
     * 用於註冊時檢查帳號重複
     * 
     * @param string $username 要檢查的使用者名稱
     * @return bool 使用者名稱已存在回傳 true，否則回傳 false
     */
    public function userExists($username) {
        return $this->findBy('username', $username) !== false;
    }
} 