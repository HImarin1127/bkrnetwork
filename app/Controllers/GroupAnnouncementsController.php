<?php
/**
 * GroupAnnouncementsController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理從本地掛載目錄讀取集團公告的控制器。
 */

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

/**
 * Class GroupAnnouncementsController
 *
 * 這個控制器專門用於與本地掛載的目錄進行互動。
 * 存取掛載在 /mnt/共和國公告區 的 NAS 共享資料夾。
 *
 * @package App\Controllers
 */
class GroupAnnouncementsController extends Controller
{
    /** @var string 本地掛載的目錄路徑。 */
    private const BASE_PATH = '/mnt/共和國公告區';

    /**
     * 建構函式
     * 
     * 初始化控制器並設定全域視圖資料
     */
    public function __construct()
    {
        $this->setGlobalViewData();
    }

    /**
     * 顯示本地掛載目錄中的檔案和目錄列表。
     *
     * @return void
     */
    public function sharedFiles()
    {
        // 基礎路徑
        $base_path = self::BASE_PATH;

        // 從查詢字串中取得並清理相對路徑
        $relative_path = '';
        if (isset($_GET['path'])) {
            $relative_path = urldecode($_GET['path']);
            // 安全性：防止目錄遍歷攻擊，移除 '..' 字元
            $relative_path = str_replace('..', '', $relative_path);
            // 清理路徑，移除開頭和結尾的斜線
            $relative_path = trim($relative_path, '/');
        }

        // 組合出完整的路徑
        $full_path = $base_path . (empty($relative_path) ? '' : '/' . $relative_path);

        $directories = [];
        $files = [];
        $error = null;

        // 檢查目錄是否存在
        if (!is_dir($full_path)) {
            $error = '指定的路徑不存在或無法存取 (Code: F01)。';
        } else {
            // 讀取目錄內容
            $items = scandir($full_path);
            
            foreach ($items as $item) {
                // 跳過 . 和 .. 目錄
                if ($item === '.' || $item === '..') {
                    continue;
                }
                
                $item_path = $full_path . '/' . $item;
                
                if (is_dir($item_path)) {
                    // 處理目錄
                    $directories[] = [
                        'name' => $item,
                        'path' => $relative_path . ($relative_path ? '/' : '') . $item
                    ];
                } else {
                    // 處理檔案
                    $file_size = filesize($item_path);
                    $files[] = [
                        'name' => $item,
                        'size' => $file_size,
                        'path' => $relative_path . ($relative_path ? '/' : '') . $item,
                        'type' => strtolower(pathinfo($item, PATHINFO_EXTENSION))
                    ];
                }
            }
        }
        
        // 準備麵包屑導航資料
        $breadcrumbs = [['name' => '集團公告', 'path' => '']];
        if (!empty($relative_path)) {
            $parts = explode('/', $relative_path);
            $current_path_for_breadcrumb = '';
            foreach ($parts as $part) {
                $current_path_for_breadcrumb .= ($current_path_for_breadcrumb ? '/' : '') . $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $current_path_for_breadcrumb];
            }
        }

        return $this->view('announcements/shared-files', [
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'current_path' => $relative_path,
            'error' => $error
        ]);
    }

    /**
     * 處理檔案下載或瀏覽請求。
     *
     * @return void
     */
    public function download()
    {
        // 從 GET 參數獲取檔案路徑
        $file_path_from_get = $_GET['path'] ?? '';
        
        // 清理路徑
        $file_path_from_get = str_replace('..', '', $file_path_from_get);
        $file_path_from_get = trim($file_path_from_get, '/');
        
        // 組合完整路徑
        $full_path = self::BASE_PATH . '/' . $file_path_from_get;

        // 檢查檔案是否存在
        if (!file_exists($full_path) || !is_file($full_path)) {
            http_response_code(404);
            die('錯誤：檔案不存在或無法讀取 (Code: F02)。');
        }

        // 清除輸出緩衝
        if (ob_get_level()) { 
            ob_end_clean(); 
        }

        $original_file_name = basename($full_path);
        $file_size = filesize($full_path);
        
        $ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        
        // 建立一個副檔名與 MIME 類型的映射表
        $mime_map = [
            'pdf' => 'application/pdf', 
            'jpg' => 'image/jpeg', 
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png', 
            'gif' => 'image/gif',
            'doc' => 'application/msword', 
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint', 
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip', 
            'rar' => 'application/x-rar-compressed',
        ];
        $mime_type = $mime_map[$ext] ?? 'application/octet-stream';
        
        // 根據副檔名決定檔案是在瀏覽器內直接顯示 (inline) 還是作為附件下載 (attachment)
        $disposition = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']) ? 'inline' : 'attachment';

        // 設定 HTTP 標頭
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($original_file_name) . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // 讀取並輸出檔案內容
        readfile($full_path);
        exit;
    }
} 