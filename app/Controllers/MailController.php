<?php
/**
 * MailController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理所有郵務相關功能的控制器。
 */
namespace App\Controllers;

use App\Models\MailRecord;
use App\Middleware\AuthMiddleware;
use App\Models\User;

/**
 * Class MailController
 *
 * 處理所有與郵務相關的 HTTP 請求，包括寄件、收件、記錄查詢等。
 * 
 * @package App\Controllers
 */
class MailController extends Controller {
    
    /**
     * @var MailRecord 郵件記錄模型的實例。
     */
    private $mailRecordModel;
    
    /**
     * MailController 的建構函式。
     * 
     * 初始化 MailRecord 模型並設定全域視圖資料。
     */
    public function __construct() {
        $this->mailRecordModel = new MailRecord();
        $this->setGlobalViewData();
    }
    
    /**
     * 顯示寄件登記表單 (GET) 或處理表單提交 (POST)。
     *
     * - GET: 顯示一個空白的寄件登記表單。
     * - POST: 驗證並儲存新的寄件記錄到資料庫。
     * 
     * @return void
     */
    public function request() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $errors = [];
        $success = '';
    
        // 處理表單提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'mail_type' => $_POST['mail_type'] ?? '',
                'receiver_name' => $_POST['receiver_name'] ?? '',
                'receiver_address' => $_POST['receiver_address'] ?? '',
                'receiver_phone' => $_POST['receiver_phone'] ?? '',
                'declare_department' => $_POST['declare_department'] ?? '',
                'sender_name' => $_POST['sender_name'] ?? '',
                'sender_ext' => $_POST['sender_ext'] ?? '',
                'item_count' => $_POST['item_count'] ?? 1,
                'postage' => $_POST['postage'] ?? 0,
                'tracking_number' => $_POST['tracking_number'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ];
    
            // 基本的後端驗證
            if (empty($formData['mail_type']) || empty($formData['receiver_name']) || empty($formData['receiver_address']) || empty($formData['sender_name'])) {
                $errors[] = '寄件方式、收件者姓名、收件地址和寄件者姓名為必填欄位。';
            } else {
                // 呼叫模型建立記錄
                $newMailCode = $this->mailRecordModel->createMailRecord($formData, $user['username']);
                if ($newMailCode) {
                    $success = "寄件登記成功！您的郵件編號是： " . htmlspecialchars($newMailCode);
                    // 成功後清空表單數據
                    $formData = [
                        'mail_type' => '', 'receiver_name' => '', 'receiver_address' => '', 'receiver_phone' => '',
                        'declare_department' => '', 'sender_name' => '', 'sender_ext' => '',
                        'item_count' => 1, 'postage' => 0, 'tracking_number' => '', 'notes' => ''
                    ];
                } else {
                    $errors[] = '登記失敗，無法寫入資料庫。請稍後再試或聯繫管理員。';
                }
            }
        } else {
            // GET 請求時，準備一個空的表單數據陣列
            $formData = [
                'mail_type' => '', 'receiver_name' => '', 'receiver_address' => '', 'receiver_phone' => '',
                'declare_department' => $user['department'] ?? '', 'sender_name' => '', 'sender_ext' => ''
            ];
        }
        
        // 渲染視圖並傳遞所需變數
        $this->view('mail/request', [
            'title' => '寄件登記',
            'formData' => $formData,
            'errors' => $errors,
            'success' => $success,
            'registrarName' => $user['name'] ?? $user['username']
        ]);
    }
    
    /**
     * 顯示寄件記錄列表，支援搜尋和匯出功能。
     *
     * - 管理員可以查看所有記錄，一般使用者只能查看自己的記錄。
     * - 支援關鍵字搜尋。
     * - 管理員可以將當前結果匯出為 CSV 檔案。
     * 
     * @return void
     */
    public function records() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
    
        // 如果是管理員且 URL 中有 'export' 參數，則執行匯出
        if ($isAdmin && isset($_GET['export'])) {
            $this->mailRecordModel->exportToCsv();
            return; // 匯出後結束執行
        }
    
        // 根據關鍵字搜尋或獲取列表
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
     * 顯示批次匯入頁面 (GET) 或處理 CSV 檔案上傳 (POST)。
     *
     * - POST: 處理上傳的 CSV 檔案，並將資料批次寫入資料庫。
     * 
     * @return void
     */
    public function import() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $message = '';
        $messageType = '';
    
        // 處理檔案上傳
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                // 呼叫模型處理批次匯入
                $result = $this->mailRecordModel->batchImport($file['tmp_name'], $user['username']);
                $message = "匯入完成！成功匯入 {$result['imported']} 筆記錄。";
                $messageType = 'success';
                // 如果有錯誤，附加錯誤資訊
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
     * 顯示編輯寄件記錄表單 (GET) 或處理更新 (POST)。
     *
     * - 驗證使用者是否有權限編輯該筆記錄。
     * - POST: 根據提交的資料更新資料庫中的記錄。
     * 
     * @return void
     */
    public function edit() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $mailCode = $_GET['mail_code'] ?? null;

        // 必須提供郵件編號
        if (!$mailCode) {
            $this->redirect('/mail/records');
            return;
        }

        // 權限檢查
        if (!$this->mailRecordModel->checkPermission($mailCode, $user['username'], $isAdmin)) {
            $_SESSION['error_message'] = '找不到該筆記錄或您沒有權限編輯。';
            $this->redirect('/mail/records');
            return;
        }

        $record = $this->mailRecordModel->find($mailCode);
        $errors = [];
        $success = '';

        // 處理表單提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updatedData = $_POST;
            unset($updatedData['mail_code']); // 防止郵件編號被修改

            if ($this->mailRecordModel->updateByMailCode($mailCode, $updatedData)) {
                $success = '紀錄更新成功！';
                $record = $this->mailRecordModel->find($mailCode); // 重新獲取更新後的資料
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
     * 處理刪除寄件記錄的請求 (POST)。
     *
     * - 驗證使用者權限。
     * - 根據提供的郵件編號刪除記錄。
     * 
     * @return void
     */
    public function delete() {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getCurrentUser();
        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user['username']);
        $mailCode = $_POST['mail_code'] ?? null;

        // 必須提供郵件編號
        if (!$mailCode) {
            $_SESSION['error_message'] = '未指定要刪除的紀錄。';
            $this->redirect('/mail/records');
            return;
        }

        // 權限檢查
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
    
    /**
     * 顯示郵資查詢頁面 (GET) 或處理查詢計算 (POST)。
     *
     * @return void
     */
    public function postage() {
        AuthMiddleware::requireLogin();
        $this->setGlobalViewData();
        
        // 定義郵資費率表
        $postageRates = [
            '掛號' => ['本島' => 33, '離島' => 38],
            '黑貓' => ['常溫' => 65, '冷藏' => 90, '冷凍' => 120],
            '新竹貨運' => ['一般' => 80, '快遞' => 120]
        ];
        
        $result = null;
        // 處理查詢請求
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
    
    /**
     * 顯示外寄郵件記錄 (功能與 records() 相似)。
     * 
     * @todo 可考慮與 records() 合併以減少重複程式碼。
     * @return void
     */
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
    
    /**
     * 顯示收件登記表單 (GET) 或處理登記 (POST)。
     * 
     * @return void
     */
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

        // TODO: 需實作 User 模型來獲取使用者列表，以供前端選擇收件人
        // $userModel = new \App\Models\User();
        // $users = $userModel->findAll();

        $this->view('mail/incoming-register', [
            'title' => '收件登記',
            'errors' => $errors,
            'success' => $success,
            'users' => [] // 暫時傳遞空陣列
        ]);
    }

    /**
     * 顯示收件記錄列表，並支援篩選。
     * 
     * @return void
     */
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

    /**
     * 根據基本費率、重量和郵件類型計算總郵資。
     *
     * @param float $baseRate 基本費率
     * @param float $weight 重量
     * @param string $mailType 郵件類型
     * @return float 計算後的總郵資
     */
    private function calculatePostage($baseRate, $weight, $mailType) {
        // 1公斤內以基本費率計算
        if ($weight <= 1) return $baseRate;

        // 黑貓：續重每公斤加 20 元
        if ($mailType === '黑貓') {
            return $baseRate + ceil($weight - 1) * 20;
        }

        // 其他 (掛號、新竹貨運)：續重每公斤加 15 元
        return $baseRate + ceil($weight - 1) * 15;
    }
}