<?php
/**
 * ExternalGuidesController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.1.0
 * @link       https://www.brnetwork.com
 * @description 處理從本地掛載目錄讀取外部操作指引的控制器。
 */

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

/**
 * Class ExternalGuidesController
 *
 * 這個控制器專門用於與本地掛載的目錄進行互動。
 * 存取掛載在 /mnt/ 的共享資料夾。
 *
 * @package App\Controllers
 */
class ExternalGuidesController extends Controller
{
    /** @var string 指引檔案的根路徑。 */
    private const GUIDES_ROOT = BASE_PATH . 'uploads/guides/';

    /**
     * 建構函式
     */
    public function __construct()
    {
        $this->setGlobalViewData();
    }

    /**
     * 顯示操作指引的主頁或子目錄內容。
     *
     * @return void
     */
    public function index()
    {
        $relative_path = '';
        if (isset($_GET['path'])) {
            $relative_path = urldecode($_GET['path']);
            $relative_path = str_replace('..', '', $relative_path);
            $relative_path = trim($relative_path, '/');
        }

        if (empty($relative_path)) {
            $this->showRootFolders();
        } else {
            $this->showSubFolder($relative_path);
        }
    }

    /**
     * 顯示根目錄下所有分類資料夾的內容。
     */
    private function showRootFolders()
    {
        $all_folders_content = [];

        if (!is_dir(self::GUIDES_ROOT)) {
            return $this->view('external-guides/index', [
                'is_root' => true,
                'all_folders_content' => [],
                'error' => '操作指引目錄尚未建立，請聯繫管理員。'
            ]);
        }

        $folder_items = array_diff(scandir(self::GUIDES_ROOT), ['.', '..']);
        
        foreach ($folder_items as $folder_name) {
            $full_path = self::GUIDES_ROOT . $folder_name;
            if (!is_dir($full_path)) continue;

            $directories = [];
            $files = [];
            $error = null;

            $items = scandir($full_path);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                if (trim(strtolower($item)) === '#recycle') continue;

                $item_path_on_disk = $full_path . '/' . $item;
                $item_path_for_link = $folder_name . '/' . $item;

                if (is_dir($item_path_on_disk)) {
                    $directories[] = ['name' => $item, 'path' => $item_path_for_link];
                } else {
                    $file_type = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    $files[] = [
                        'name' => $item,
                        'size' => filesize($item_path_on_disk),
                        'path' => $item_path_for_link,
                        'type' => $file_type,
                        'is_web_content' => ($file_type === 'html')
                    ];
                }
            }
            $all_folders_content[$folder_name] = [
                'directories' => $directories,
                'files' => $files,
                'error' => $error
            ];
        }

        return $this->view('external-guides/index', [
            'is_root' => true,
            'all_folders_content' => $all_folders_content
        ]);
    }

    /**
     * 顯示子目錄的內容。
     */
    private function showSubFolder($relative_path)
    {
        $full_path = self::GUIDES_ROOT . $relative_path;
        $directories = [];
        $files = [];
        $error = null;

        if (!is_dir($full_path)) {
            $error = '指定的路徑不存在或無法存取 (Code: G01)。';
        } else {
            $items = scandir($full_path);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                if (trim(strtolower($item)) === '#recycle') continue;
                
                $item_path = $full_path . '/' . $item;
                
                if (is_dir($item_path)) {
                    $directories[] = ['name' => $item, 'path' => $relative_path . '/' . $item];
                } else {
                    $file_type = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    $files[] = [
                        'name' => $item,
                        'size' => filesize($item_path),
                        'path' => $relative_path . '/' . $item,
                        'type' => $file_type,
                        'is_web_content' => ($file_type === 'html')
                    ];
                }
            }
        }
        
        $breadcrumbs = [['name' => '操作指引', 'path' => '']];
        $parts = explode('/', $relative_path);
        $current_path_for_breadcrumb = '';
        foreach ($parts as $part) {
            $current_path_for_breadcrumb .= ($current_path_for_breadcrumb ? '/' : '') . $part;
            $breadcrumbs[] = ['name' => $part, 'path' => $current_path_for_breadcrumb];
        }

        return $this->view('external-guides/index', [
            'is_root' => false,
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'current_path' => $relative_path,
            'error' => $error
        ]);
    }

    /**
     * 顯示 HTML 網頁內容。
     */
    public function showWebContent()
    {
        $file_path_from_get = $_GET['path'] ?? '';
        $file_path_from_get = str_replace('..', '', $file_path_from_get);
        $file_path_from_get = trim($file_path_from_get, '/');
        
        $full_path = self::GUIDES_ROOT . $file_path_from_get;

        if (!file_exists($full_path) || !is_file($full_path)) {
            http_response_code(404);
            die('錯誤：檔案不存在或無法讀取 (Code: G03)。');
        }

        $ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
        if ($ext !== 'html') {
            http_response_code(400);
            die('錯誤：此檔案不是 HTML 內容。');
        }

        // 直接包含並執行 PHP/HTML 檔案
        include $full_path;
        exit;
    }

    /**
     * 處理檔案下載或瀏覽請求。
     */
    public function download()
    {
        $file_path_from_get = $_GET['path'] ?? '';
        $file_path_from_get = str_replace('..', '', $file_path_from_get);
        $file_path_from_get = trim($file_path_from_get, '/');
        
        $full_path = self::GUIDES_ROOT . $file_path_from_get;

        if (!file_exists($full_path) || !is_file($full_path)) {
            http_response_code(404);
            die('錯誤：檔案不存在或無法讀取 (Code: G02)。');
        }

        if (ob_get_level()) { 
            ob_end_clean(); 
        }

        $original_file_name = basename($full_path);
        $file_size = filesize($full_path);
        $ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        
        $mime_map = [
            'pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 
            'png' => 'image/png', 'gif' => 'image/gif', 'doc' => 'application/msword', 
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint', 
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
        ];
        $mime_type = $mime_map[$ext] ?? 'application/octet-stream';
        $disposition = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']) ? 'inline' : 'attachment';

        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($original_file_name) . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        readfile($full_path);
        exit;
    }
} 