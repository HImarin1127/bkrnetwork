<?php
// app/Controllers/MailController.php

namespace App\Controllers;

use App\Models\MailRecord;
use App\Middleware\AuthMiddleware;
use App\Models\User;

/**
 * 郵務控制器
 * 處理郵務系統的所有功能
 */
class MailController extends Controller {
    
    private $mailRecordModel;
    
    public function __construct() {
        $this->mailRecordModel = new MailRecord();
        $this->setGlobalViewData();
    }
    
    /**
     * 寄件登記頁面 (GET & POST)
     */
    public function request() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $errors = [];
        $success = '';
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'mail_type' => $_POST['mail_type'] ?? '',
                'receiver_name' => $_POST['receiver_name'] ?? '',
                'receiver_address' => $_POST['receiver_address'] ?? '',
                'receiver_phone' => $_POST['receiver_phone'] ?? '',
                'declare_department' => $_POST['declare_department'] ?? '',
                'sender_name' => $_POST['sender_name'] ?? ($user['name'] ?? $user['username']),
                'sender_ext' => $_POST['sender_ext'] ?? '',
                'item_count' => $_POST['item_count'] ?? 1,
                'postage' => $_POST['postage'] ?? 0,
                'tracking_number' => $_POST['tracking_number'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ];
    
            if (empty($formData['mail_type']) || empty($formData['receiver_name']) || empty($formData['receiver_address'])) {
                $errors[] = '寄件方式、收件者姓名和收件地址為必填欄位。';
            } else {
                $newMailCode = $this->mailRecordModel->createMailRecord($formData, $user['username']);
                if ($newMailCode) {
                    $success = "寄件登記成功！您的郵件編號是： " . htmlspecialchars($newMailCode);
                    $formData = [
                        'mail_type' => '', 'receiver_name' => '', 'receiver_address' => '', 'receiver_phone' => '',
                        'declare_department' => '', 'sender_name' => $user['name'] ?? $user['username'], 'sender_ext' => '',
                        'item_count' => 1, 'postage' => 0, 'tracking_number' => '', 'notes' => ''
                    ];
                } else {
                    $errors[] = '登記失敗，無法寫入資料庫。請稍後再試或聯繫管理員。';
                }
            }
        } else {
            $formData = [
                'mail_type' => '', 'receiver_name' => '', 'receiver_address' => '', 'receiver_phone' => '',
                'declare_department' => $user['department'] ?? '', 'sender_name' => $user['name'] ?? $user['username'], 'sender_ext' => ''
            ];
        }
        
        $this->view('mail/request', [
            'title' => '寄件登記',
            'formData' => $formData,
            'errors' => $errors,
            'success' => $success,
            'registrarName' => $user['name'] ?? $user['username']
        ]);
    }
    
    /**
     * 寄件記錄列表與搜尋
     */
    public function records() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
    
        if ($isAdmin && isset($_GET['export'])) {
            $this->mailRecordModel->exportToCsv();
            return;
        }
    
        $keyword = trim($_GET['search'] ?? '');
        if (!empty($keyword)) {
            $records = $this->mailRecordModel->search($keyword, $user['username'], $isAdmin);
        } else {
            $records = $this->mailRecordModel->getByUsername($user['username'], $isAdmin);
        }
        
        $this->view('mail/records', [
            'title' => '寄件記錄',
            'records' => $records,
            'isAdmin' => $isAdmin,
            'keyword' => $keyword
        ]);
    }
    
    /**
     * 批次匯入頁面 (GET & POST)
     */
    public function import() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $message = '';
        $messageType = '';
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $result = $this->mailRecordModel->batchImport($file['tmp_name'], $user['username']);
                $message = "匯入完成！成功匯入 {$result['imported']} 筆記錄。";
                $messageType = 'success';
                if (!empty($result['errors'])) {
                    $message .= " 但有以下錯誤發生：<br>" . implode('<br>', $result['errors']);
                    $messageType = 'warning';
                }
            } else {
                $message = '檔案上傳失敗，請檢查檔案或伺服器設定。';
                $messageType = 'error';
            }
        }
        
        $this->view('mail/import', [
            'title' => '批次匯入寄件資料',
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * 編輯寄件記錄 (GET & POST)
     */
    public function edit() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $mailCode = $_GET['mail_code'] ?? null;

        if (!$mailCode) {
            $this->redirect('/mail/records');
            return;
        }

        if (!$this->mailRecordModel->checkPermission($mailCode, $user['username'], $isAdmin)) {
            $_SESSION['error_message'] = '找不到該筆記錄或您沒有權限編輯。';
            $this->redirect('/mail/records');
            return;
        }

        $record = $this->mailRecordModel->find($mailCode);
        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updatedData = $_POST;
            unset($updatedData['mail_code']);

            if ($this->mailRecordModel->updateByMailCode($mailCode, $updatedData)) {
                $success = '紀錄更新成功！';
                $record = $this->mailRecordModel->find($mailCode);
            } else {
                $errors[] = '更新失敗，請再試一次。';
            }
        }

        $this->view('mail/edit', [
            'title' => '編輯寄件記錄',
            'record' => $record,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    /**
     * 刪除寄件記錄 (POST)
     */
    public function delete() {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $mailCode = $_POST['mail_code'] ?? null;

        if (!$mailCode) {
            $_SESSION['error_message'] = '未指定要刪除的紀錄。';
            $this->redirect('/mail/records');
            return;
        }

        if ($this->mailRecordModel->checkPermission($mailCode, $user['username'], $isAdmin)) {
            if ($this->mailRecordModel->deleteByMailCode($mailCode)) {
                $_SESSION['success_message'] = '紀錄 ' . htmlspecialchars($mailCode) . ' 已成功刪除。';
            } else {
                $_SESSION['error_message'] = '刪除失敗。';
            }
        } else {
            $_SESSION['error_message'] = '您沒有權限刪除此記錄。';
        }

        $this->redirect('/mail/records');
    }
    
    // --- 以下為未修改的方法 ---

    public function postage() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        
        $postageRates = [
            '掛號' => ['本島' => 33, '離島' => 38],
            '黑貓' => ['常溫' => 65, '冷藏' => 90, '冷凍' => 120],
            '新竹貨運' => ['一般' => 80, '快遞' => 120]
        ];
        
        $result = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mailType = $_POST['mail_type'] ?? '';
            $destination = $_POST['destination'] ?? '';
            $weight = floatval($_POST['weight'] ?? 0);
            
            if ($mailType && $destination) {
                $baseRate = $postageRates[$mailType][$destination] ?? 0;
                if ($baseRate > 0) {
                    $result = [
                        'mail_type' => $mailType,
                        'destination' => $destination,
                        'weight' => $weight,
                        'base_rate' => $baseRate,
                        'total_rate' => $this->calculatePostage($baseRate, $weight, $mailType)
                    ];
                }
            }
        }
        
        $this->view('mail/postage', [
            'title' => '郵資查詢',
            'postageRates' => $postageRates,
            'result' => $result
        ]);
    }
    
    public function outgoingRecords() {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $this->setGlobalViewData();
        
        $keyword = trim($_GET['search'] ?? '');
        if (!empty($keyword)) {
            $records = $this->mailRecordModel->search($keyword, $user['username'], $isAdmin);
        } else {
            $records = $this->mailRecordModel->getByUsername($user['username'], $isAdmin);
        }
        
        $this->view('mail/outgoing-records', [
            'title' => '外寄郵件記錄',
            'records' => $records,
            'isAdmin' => $isAdmin,
            'keyword' => $keyword
        ]);
    }
    
    public function incomingRegister() {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getCurrentUser();
        $this->setGlobalViewData();
        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'recipient_id' => $_POST['recipient_id'],
                'sender_info' => $_POST['sender_info'],
                'mail_type' => $_POST['mail_type'],
                'notes' => $_POST['notes'],
                'registered_by' => $user['id']
            ];

            if ($this->mailRecordModel->createIncomingRecord($data)) {
                $success = '收件登記成功！';
            } else {
                $errors[] = '登記失敗。';
            }
        }

        // 假設有一個 User 模型來獲取使用者列表
        // $userModel = new \App\Models\User();
        // $users = $userModel->findAll();

        $this->view('mail/incoming-register', [
            'title' => '收件登記',
            'errors' => $errors,
            'success' => $success,
            'users' => [] // $users
        ]);
    }

    public function incomingRecords() {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $this->setGlobalViewData();
        
        $filters = [
            'startDate' => $_GET['start_date'] ?? '',
            'endDate' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'keyword' => $_GET['keyword'] ?? ''
        ];
        
        $records = $this->mailRecordModel->searchIncomingRecords($filters, $user['username'], $isAdmin);
        
        $this->view('mail/incoming-records', [
            'title' => '收件記錄',
            'records' => $records,
            'filters' => $filters
        ]);
    }

    private function calculatePostage($baseRate, $weight, $mailType) {
        if ($weight <= 1) return $baseRate;
        if ($mailType === '黑貓') {
            return $baseRate + ceil($weight - 1) * 20;
        }
        return $baseRate + ceil($weight - 1) * 15;
    }
}