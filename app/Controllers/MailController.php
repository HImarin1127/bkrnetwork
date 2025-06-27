<?php
// app/Controllers/MailController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/MailRecord.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

/**
 * 郵務控制器
 * 
 * 處理郵務系統的所有功能，包括：
 * - 寄件登記與管理
 * - 收件登記與管理
 * - 郵資查詢計算
 * - CSV 匯入匯出
 * - 記錄查詢與編輯
 * 
 * 所有方法都需要使用者登入，部分功能需要管理員權限
 */
class MailController extends Controller {
    /** @var MailRecord 郵務記錄模型實例 */
    private $mailModel;
    
    /**
     * 建構函式
     * 
     * 初始化郵務記錄模型
     */
    public function __construct() {
        $this->mailModel = new MailRecord();
    }
    
    /**
     * 寄件登記頁面
     * 
     * GET：顯示寄件登記表單
     * POST：處理寄件登記邏輯
     * 
     * 功能：
     * - 表單資料驗證
     * - 自動生成郵件編號
     * - 記錄寄件資訊
     * - 預填寄件者資訊（來自登入使用者）
     */
    public function request() {
        // 檢查登入狀態
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $errors = [];
        $success = '';
        
        // 初始化表單資料，預填使用者資訊
        $formData = [
            'mail_type' => '',
            'receiver_name' => '',
            'receiver_address' => '',
            'receiver_phone' => '',
            'declare_department' => $user['department'] ?? '',
            'sender_name' => $user['name'] ?? $user['username'],
            'sender_ext' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 取得並清理表單資料
            $formData = [
                'mail_type' => trim($_POST['mail_type'] ?? ''),
                'receiver_name' => trim($_POST['receiver_name'] ?? ''),
                'receiver_address' => trim($_POST['receiver_address'] ?? ''),
                'receiver_phone' => trim($_POST['receiver_phone'] ?? ''),
                'declare_department' => trim($_POST['declare_department'] ?? ''),
                'sender_name' => trim($_POST['sender_name'] ?? ''),
                'sender_ext' => trim($_POST['sender_ext'] ?? '')
            ];
            
            // 必填欄位驗證
            if (empty($formData['mail_type'])) $errors[] = '請選擇寄件方式';
            if (empty($formData['receiver_name'])) $errors[] = '請填寫收件者姓名';
            if (empty($formData['receiver_address'])) $errors[] = '請填寫收件地址';
            if (empty($formData['receiver_phone'])) $errors[] = '請填寫收件者行動電話';
            if (empty($formData['declare_department'])) $errors[] = '請填寫費用申報單位';
            if (empty($formData['sender_name'])) $errors[] = '請填寫寄件者姓名';
            if (empty($formData['sender_ext'])) $errors[] = '請填寫寄件者分機';
            
            if (empty($errors)) {
                try {
                    // 加入登記者 ID
                    $formData['registrar_id'] = $user['id'];
                    
                    // 建立郵件記錄
                    $recordId = $this->mailModel->createMailRecord($formData);
                    
                    if ($recordId) {
                        // 取得新建立記錄的郵件編號
                        $record = $this->mailModel->find($recordId);
                        $mailCode = $record['mail_code'] ?? '';
                        $success = "寄件已登記成功！寄件序號：<strong>{$mailCode}</strong>";
                        
                        // 成功後重置表單，保留使用者資訊
                        $formData = [
                            'mail_type' => '',
                            'receiver_name' => '',
                            'receiver_address' => '',
                            'receiver_phone' => '',
                            'declare_department' => $user['department'] ?? '',
                            'sender_name' => $user['name'] ?? $user['username'],
                            'sender_ext' => ''
                        ];
                    }
                } catch (Exception $e) {
                    $errors[] = '登記失敗：' . $e->getMessage();
                }
            }
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
     * 寄件記錄頁面
     * 
     * 顯示寄件記錄列表，支援搜尋和 CSV 匯出
     * 
     * 功能：
     * - 根據使用者權限顯示記錄（管理員看全部，一般使用者看自己的）
     * - 關鍵字搜尋功能
     * - CSV 匯出功能（限管理員）
     */
    public function records() {
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $isAdmin = $user['role'] === 'admin';
        
        // 處理 CSV 匯出請求（僅限管理員）
        if ($isAdmin && isset($_GET['export'])) {
            $this->mailModel->exportToCsv();
            return;
        }
        
        // 處理搜尋請求
        $keyword = trim($_GET['search'] ?? '');
        if (!empty($keyword)) {
            // 執行關鍵字搜尋
            $records = $this->mailModel->search($keyword, $user['id'], $isAdmin);
        } else {
            // 顯示所有記錄（根據權限過濾）
            $records = $this->mailModel->getByUserId($user['id'], $isAdmin);
        }
        
        $this->view('mail/records', [
            'title' => '寄件記錄',
            'records' => $records,
            'isAdmin' => $isAdmin,
            'keyword' => $keyword
        ]);
    }
    
    /**
     * 寄件匯入頁面
     * 
     * 提供 CSV 檔案批次匯入功能
     * 
     * GET：顯示匯入表單
     * POST：處理 CSV 檔案匯入
     * 
     * 功能：
     * - 支援 CSV 格式檔案上傳
     * - 批次驗證和匯入
     * - 詳細的錯誤報告
     * - 匯入統計資訊
     */
    public function import() {
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $message = '';
        $messageType = '';
        
        // 檢查是否有從 session 中的訊息（重新導向後顯示）
        if (isset($_SESSION['import_message'])) {
            $message = $_SESSION['import_message'];
            $messageType = $_SESSION['import_message_type'];
            // 清除 session 中的訊息
            unset($_SESSION['import_message']);
            unset($_SESSION['import_message_type']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 檢查檔案上傳
            if (empty($_FILES['csv_file']['tmp_name'])) {
                $message = "請選擇要匯入的 CSV 檔案。";
                $messageType = 'error';
            } elseif ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => '檔案大小超過系統限制',
                    UPLOAD_ERR_FORM_SIZE => '檔案大小超過表單限制',
                    UPLOAD_ERR_PARTIAL => '檔案只有部分被上傳',
                    UPLOAD_ERR_NO_FILE => '沒有檔案被上傳',
                    UPLOAD_ERR_NO_TMP_DIR => '找不到暫存目錄',
                    UPLOAD_ERR_CANT_WRITE => '檔案寫入失敗',
                    UPLOAD_ERR_EXTENSION => 'PHP 擴充功能停止了檔案上傳'
                ];
                $message = "檔案上傳失敗：" . ($uploadErrors[$_FILES['csv_file']['error']] ?? '未知錯誤');
                $messageType = 'error';
            } else {
                try {
                    // 執行批次匯入
                    $result = $this->mailModel->batchImport($_FILES['csv_file']['tmp_name'], $user['id']);
                    
                    if ($result['imported'] > 0) {
                        $message = "🎉 批次匯入完成！共成功匯入 {$result['imported']} 筆寄件資料。";
                        $messageType = 'success';
                        
                        // 如果有部分失敗，顯示警告訊息
                        if (!empty($result['errors'])) {
                            $message .= "<br><br>⚠️ 以下資料匯入失敗：<br>" . implode('<br>', $result['errors']);
                            $messageType = 'warning';
                        }
                        
                        // 成功後重新導向到匯入頁面顯示結果（避免重複提交）
                        $_SESSION['import_message'] = $message;
                        $_SESSION['import_message_type'] = $messageType;
                        $this->redirect($this->getBaseUrl() . '/mail/import?success=1');
                        return;
                    } else {
                        $errorDetails = !empty($result['errors']) ? '<br><br>詳細錯誤：<br>' . implode('<br>', $result['errors']) : '';
                        $message = "❌ 匯入失敗，沒有成功匯入任何資料。請檢查檔案格式是否正確。" . $errorDetails;
                        $messageType = 'error';
                    }
                } catch (Exception $e) {
                    $message = "❌ 匯入失敗：" . $e->getMessage();
                    $messageType = 'error';
                }
            }
        }
        
        $this->view('mail/import', [
            'title' => '寄件匯入',
            'message' => $message,
            'messageType' => $messageType
        ]);
    }
    
    /**
     * 郵資查詢頁面
     * 
     * 提供郵資計算功能
     * 
     * GET：顯示郵資查詢表單
     * POST：計算郵資費用
     * 
     * 功能：
     * - 支援多種寄件方式的費率查詢
     * - 根據重量和目的地計算郵資
     * - 提供常用郵資費率表
     */
    public function postage() {
        // 定義郵資查詢頁面方法
        AuthMiddleware::requireLogin();
        // 確保使用者已登入
        
        $this->setGlobalViewData();
        // 設定全域視圖資料
        
        // 預設郵資費率表（可擴充為從資料庫或 API 取得）
        $postageRates = [
            // 郵資費率設定，依寄件方式和目的地分類
            '掛號' => [
                '本島' => 33,      // 台灣本島掛號郵資
                '離島' => 38       // 離島地區掛號郵資
            ],
            '黑貓' => [
                '常溫' => 65,      // 黑貓宅急便常溫配送
                '冷藏' => 90,      // 黑貓宅急便冷藏配送
                '冷凍' => 120      // 黑貓宅急便冷凍配送
            ],
            '新竹貨運' => [
                '一般' => 80,      // 新竹貨運一般配送
                '快遞' => 120      // 新竹貨運快遞服務
            ]
        ];
        // 郵資費率表設定結束
        
        $result = null;
        // 初始化查詢結果
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 處理 POST 請求（郵資計算）
            // 取得查詢參數
            $mailType = $_POST['mail_type'] ?? '';
            // 寄件方式
            $destination = $_POST['destination'] ?? '';
            // 目的地類型
            $weight = floatval($_POST['weight'] ?? 0);
            // 包裹重量（公斤）
            
            if ($mailType && $destination) {
                // 檢查寄件方式和目的地是否都有輸入
                $baseRate = $postageRates[$mailType][$destination] ?? 0;
                // 取得基本費率
                if ($baseRate > 0) {
                    // 如果找到有效的費率
                    $result = [
                        // 建立查詢結果陣列
                        'mail_type' => $mailType,
                        'destination' => $destination,
                        'weight' => $weight,
                        'base_rate' => $baseRate,
                        'total_rate' => $this->calculatePostage($baseRate, $weight, $mailType)
                        // 呼叫郵資計算方法
                    ];
                    // 查詢結果陣列結束
                }
                // 費率檢查結束
            }
            // 參數檢查結束
        }
        // POST 請求處理結束
        
        $this->view('mail/postage', [
            // 載入郵資查詢頁面視圖
            'title' => '郵資查詢',
            'postageRates' => $postageRates,
            'result' => $result
        ]);
        // 視圖載入結束
    }
    
    /**
     * 編輯寄件記錄
     */
    public function edit() {
        // 定義編輯寄件記錄頁面方法
        AuthMiddleware::requireLogin();
        // 確保使用者已登入
        
        $id = $_GET['id'] ?? 0;
        // 取得要編輯的記錄 ID
        $user = AuthMiddleware::getCurrentUser();
        // 取得當前登入使用者資訊
        $isAdmin = $user['role'] === 'admin';
        // 檢查是否為管理員
        
        // 檢查權限
        if (!$this->mailModel->checkPermission($id, $user['id'], $isAdmin)) {
            // 使用郵務模型檢查使用者是否有權限編輯此記錄
            $this->redirect(BASE_URL . 'mail/records?error=權限不足');
            // 無權限時重新導向到記錄列表頁面
            return;
        }
        // 權限檢查結束
        
        $record = $this->mailModel->find($id);
        // 從資料庫取得要編輯的記錄
        if (!$record) {
            // 檢查記錄是否存在
            $this->redirect(BASE_URL . 'mail/records?error=記錄不存在');
            // 記錄不存在時重新導向
            return;
        }
        // 記錄存在性檢查結束
        
        // 只有草稿狀態才能編輯
        if ($record['status'] !== '草稿' && !$isAdmin) {
            // 非管理員只能編輯草稿狀態的記錄
            $this->redirect(BASE_URL . 'mail/records?error=只有草稿狀態的記錄才能編輯');
            return;
        }
        // 狀態檢查結束
        
        $this->setGlobalViewData();
        // 設定全域視圖資料
        
        $errors = [];
        // 初始化錯誤訊息陣列
        $success = '';
        // 初始化成功訊息
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 處理 POST 請求（表單提交）
            $updateData = [
                // 準備要更新的資料陣列
                'mail_type' => trim($_POST['mail_type'] ?? ''),
                'receiver_name' => trim($_POST['receiver_name'] ?? ''),
                'receiver_address' => trim($_POST['receiver_address'] ?? ''),
                'receiver_phone' => trim($_POST['receiver_phone'] ?? ''),
                'declare_department' => trim($_POST['declare_department'] ?? ''),
                'sender_name' => trim($_POST['sender_name'] ?? ''),
                'sender_ext' => trim($_POST['sender_ext'] ?? ''),
                'notes' => trim($_POST['notes'] ?? '')
            ];
            // 更新資料陣列結束
            
            // 驗證資料
            if (empty($updateData['mail_type'])) $errors[] = '請選擇寄件方式';
            if (empty($updateData['receiver_name'])) $errors[] = '請填寫收件者姓名';
            if (empty($updateData['receiver_address'])) $errors[] = '請填寫收件地址';
            // 基本必填欄位驗證
            
            if (empty($errors)) {
                // 如果沒有驗證錯誤
                try {
                    $this->mailModel->update($id, $updateData);
                    // 呼叫郵務模型更新記錄
                    $success = '記錄更新成功！';
                    // 設定成功訊息
                    
                    // 重新載入記錄
                    $record = $this->mailModel->find($id);
                    // 取得更新後的記錄資料
                } catch (Exception $e) {
                    // 捕獲更新過程中的例外
                    $errors[] = '更新失敗：' . $e->getMessage();
                    // 加入錯誤訊息
                }
                // 例外處理結束
            }
            // 錯誤檢查結束
        }
        // POST 請求處理結束
        
        $this->view('mail/edit', [
            // 載入編輯頁面視圖
            'title' => '編輯寄件記錄',
            'record' => $record,
            'errors' => $errors,
            'success' => $success,
            'isAdmin' => $isAdmin
        ]);
        // 視圖載入結束
    }
    
    /**
     * 刪除寄件記錄
     */
    public function delete() {
        // 定義刪除寄件記錄方法
        AuthMiddleware::requireLogin();
        // 確保使用者已登入
        
        $id = $_POST['id'] ?? 0;
        // 取得要刪除的記錄 ID（來自 POST 請求）
        $user = AuthMiddleware::getCurrentUser();
        // 取得當前登入使用者資訊
        $isAdmin = $user['role'] === 'admin';
        // 檢查是否為管理員
        
        // 檢查權限
        if (!$this->mailModel->checkPermission($id, $user['id'], $isAdmin)) {
            // 使用郵務模型檢查刪除權限
            $this->json(['success' => false, 'message' => '權限不足']);
            // 回傳 JSON 錯誤回應
            return;
        }
        // 權限檢查結束
        
        $record = $this->mailModel->find($id);
        // 從資料庫取得要刪除的記錄
        if (!$record) {
            // 檢查記錄是否存在
            $this->json(['success' => false, 'message' => '記錄不存在']);
            // 回傳 JSON 錯誤回應
            return;
        }
        // 記錄存在性檢查結束
        
        // 只有草稿狀態才能刪除
        if ($record['status'] !== '草稿' && !$isAdmin) {
            // 非管理員只能刪除草稿狀態的記錄
            $this->json(['success' => false, 'message' => '只有草稿狀態的記錄才能刪除']);
            return;
        }
        // 狀態檢查結束
        
        try {
            $this->mailModel->delete($id);
            $this->json(['success' => true, 'message' => '記錄已刪除']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => '刪除失敗：' . $e->getMessage()]);
        }
    }
    
    /**
     * 寄件查詢頁面（只顯示寄件記錄）
     */
    public function outgoingRecords() {
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $isAdmin = $user['role'] === 'admin';
        
        // 處理 CSV 匯出
        if ($isAdmin && isset($_GET['export'])) {
            $this->mailModel->exportToCsv();
            return;
        }
        
        // 處理搜尋
        $keyword = trim($_GET['search'] ?? '');
        if (!empty($keyword)) {
            $records = $this->mailModel->search($keyword, $user['id'], $isAdmin);
        } else {
            $records = $this->mailModel->getByUserId($user['id'], $isAdmin);
        }
        
        $this->view('mail/outgoing-records', [
            'title' => '寄件查詢',
            'records' => $records,
            'isAdmin' => $isAdmin,
            'keyword' => $keyword
        ]);
    }
    
    /**
     * 收件登記頁面
     */
    public function incomingRegister() {
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $errors = [];
        $success = '';
        
        // 初始化表單資料
        $formData = [
            'tracking_number' => '',
            'mail_type' => '',
            'sender_name' => '',
            'sender_company' => '',
            'recipient_name' => '',
            'recipient_department' => '',
            'received_date' => date('Y-m-d'),
            'received_time' => date('H:i'),
            'content_description' => '',
            'urgent' => 0,
            'notes' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 取得表單資料
            $formData = [
                'tracking_number' => trim($_POST['tracking_number'] ?? ''),
                'mail_type' => trim($_POST['mail_type'] ?? ''),
                'sender_name' => trim($_POST['sender_name'] ?? ''),
                'sender_company' => trim($_POST['sender_company'] ?? ''),
                'recipient_name' => trim($_POST['recipient_name'] ?? ''),
                'recipient_department' => trim($_POST['recipient_department'] ?? ''),
                'received_date' => trim($_POST['received_date'] ?? ''),
                'received_time' => trim($_POST['received_time'] ?? ''),
                'content_description' => trim($_POST['content_description'] ?? ''),
                'urgent' => intval($_POST['urgent'] ?? 0),
                'notes' => trim($_POST['notes'] ?? '')
            ];
            
            // 驗證資料
            if (empty($formData['mail_type'])) $errors[] = '請選擇郵件類型';
            if (empty($formData['sender_name'])) $errors[] = '請填寫寄件者姓名';
            if (empty($formData['recipient_name'])) $errors[] = '請填寫收件者姓名';
            if (empty($formData['received_date'])) $errors[] = '請選擇收件日期';
            
            if (empty($errors)) {
                try {
                    $formData['registrar_id'] = $user['id'];
                    $formData['status'] = '已收件';
                    $recordId = $this->mailModel->createIncomingRecord($formData);
                    
                    if ($recordId) {
                        $success = "收件已登記成功！登記編號：<strong>IN-{$recordId}</strong>";
                        
                        // 重置表單
                        $formData = [
                            'tracking_number' => '',
                            'mail_type' => '',
                            'sender_name' => '',
                            'sender_company' => '',
                            'recipient_name' => '',
                            'recipient_department' => '',
                            'received_date' => date('Y-m-d'),
                            'received_time' => date('H:i'),
                            'content_description' => '',
                            'urgent' => 0,
                            'notes' => ''
                        ];
                    }
                } catch (Exception $e) {
                    $errors[] = '登記失敗：' . $e->getMessage();
                }
            }
        }
        
        $this->view('mail/incoming-register', [
            'title' => '收件登記',
            'formData' => $formData,
            'errors' => $errors,
            'success' => $success,
            'registrarName' => $user['name'] ?? $user['username']
        ]);
    }
    
    /**
     * 收件查詢頁面
     */
    public function incomingRecords() {
        AuthMiddleware::requireLogin();
        
        $this->setGlobalViewData();
        
        $user = AuthMiddleware::getCurrentUser();
        $isAdmin = $user['role'] === 'admin';
        
        // 處理搜尋
        $keyword = trim($_GET['search'] ?? '');
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');
        $status = trim($_GET['status'] ?? '');
        
        $filters = compact('keyword', 'dateFrom', 'dateTo', 'status');
        
        if (!empty($keyword) || !empty($dateFrom) || !empty($dateTo) || !empty($status)) {
            $records = $this->mailModel->searchIncomingRecords($filters, $user['id'], $isAdmin);
        } else {
            $records = $this->mailModel->getIncomingRecords($user['id'], $isAdmin);
        }
        
        $this->view('mail/incoming-records', [
            'title' => '收件查詢',
            'records' => $records,
            'isAdmin' => $isAdmin,
            'filters' => $filters
        ]);
    }
    
    /**
     * 計算郵資費用
     */
    private function calculatePostage($baseRate, $weight, $mailType) {
        // 基本郵資計算邏輯
        $totalRate = $baseRate;
        
        // 根據重量計算額外費用
        if ($weight > 1) {
            switch ($mailType) {
                case '掛號':
                    // 每超過 100g 加收 5 元
                    $extraWeight = ceil(($weight - 1) * 10); // 假設輸入的是公斤，轉換為 100g 單位
                    $totalRate += $extraWeight * 5;
                    break;
                    
                case '黑貓':
                    // 每公斤加收 20 元
                    $extraWeight = ceil($weight - 1);
                    $totalRate += $extraWeight * 20;
                    break;
                    
                case '新竹貨運':
                    // 每公斤加收 15 元
                    $extraWeight = ceil($weight - 1);
                    $totalRate += $extraWeight * 15;
                    break;
            }
        }
        
        return $totalRate;
    }
} 