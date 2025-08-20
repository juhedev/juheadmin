<?php
namespace App\Controllers\Admin;

use App\Core\AdminBaseController;
use Db\Database; // Medoo数据库实例
use \Exception;

class ImagesController extends AdminBaseController {
    protected $db;                
    protected $uploadDir;        
    protected array $uploadConfig;

    // 允许的图片MIME类型
    const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/gif',
        'image/webp', 'image/svg+xml'
    ];
    const MAX_UPLOAD_SIZE = 10485760; // 上传大小限制（10MB）
    const MAX_IMAGE_SIZE = 800;       // 主图最大边长（等同缩略图大小）

    public function __construct() {
        $this->checkLogin(); // 登录验证
        $this->db = new Database(); // 初始化数据库连接
        $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
          . "://" . $_SERVER['HTTP_HOST'];

        $this->uploadConfig = [
            'upload_dir' => $_SERVER['DOCUMENT_ROOT'] . '/Storage/images/',
            'url' => $domain . '/Storage/images/',
            'max_size' => 10 * 1024 * 1024, // 10MB
            'allowed_mimes' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/icon'
            ],
            'min_quality' => 60,
            'max_quality' => 100,
            'default_quality' => 80
        ];

    }

    /**
     * 图片管理首页（仅渲染前端页面，数据使用AJAX加载）
     */
    public function index() {
        // 渲染页面模板
        $this->render('Admin/images.php', [
            'title' => '图片管理'
        ]);
    }

    /**
     * 图片列表查询
     */
    public function list() {


        try {
            // 处理分页参数
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? 
                     min(100, max(1, (int)$_GET['limit'])) : 20;
            $offset = ($page - 1) * $limit;

            // 查询总数
            $total = $this->db->count('images');

            // 查询当前页数据
            $data = $this->db->select('images', [
                'id', 'name', 'url',  
                'size', 'width', 'height', 'upload_time'
            ], [
                'LIMIT' => [$offset, $limit],
                'ORDER' => ['upload_time' => 'DESC']
            ]);

            // 返回结果
            echo json_encode([
                'status' => 'success',
                'page' => $page,
                'total' => $total,
                'data' => $data ?: []
            ]);
            exit;

        } catch (Exception $e) {
            $this->jsonError('获取数据失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 图片上传处理
     */
   public function upload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $this->jsonError('文件上传失败: ' . $this->getUploadError($_FILES['image']['error'] ?? -1));
    }

    $file = $_FILES['image'];

    $params = [
        'quality' => isset($_POST['quality']) ? 
                    min($this->uploadConfig['max_quality'], 
                        max($this->uploadConfig['min_quality'], (int)$_POST['quality'])) : 
                    $this->uploadConfig['default_quality'],
        'width' => isset($_POST['width']) && $_POST['width'] !== '' ? max(1, (int)$_POST['width']) : 0,
        'height' => isset($_POST['height']) && $_POST['height'] !== '' ? max(1, (int)$_POST['height']) : 0
    ];

    if ($file['size'] > $this->uploadConfig['max_size']) {
        $this->jsonError("文件过大，最大支持" . $this->formatFileSize($this->uploadConfig['max_size']));
    }

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $this->uploadConfig['allowed_mimes'])) {
        $this->jsonError('不支持的文件类型，仅允许: ' . implode(', ', $this->uploadConfig['allowed_mimes']));
    }

    try {
        $uploadDir = rtrim($this->uploadConfig['upload_dir'], '/') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = basename($file['name']);
        $originalExt = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $originalExt;
        $targetPath = $uploadDir . $filename;

        list($origWidth, $origHeight) = getimagesize($file['tmp_name']);
        list($newWidth, $newHeight, $srcX, $srcY, $cropW, $cropH) = $this->calculateDimensions(
            $origWidth, $origHeight,
            $params['width'], $params['height']
        );

        // 判断是否需要裁剪
        if ($params['width'] > 0 && $params['height'] > 0) {
            $success = $this->processImageWithCrop(
                $file['tmp_name'], $mime, $targetPath,
                $newWidth, $newHeight,
                $srcX, $srcY, $cropW, $cropH,
                $params['quality']
            );
        } else {
            $success = $this->processImage(
                $file['tmp_name'], $mime, $targetPath,
                $newWidth, $newHeight, $params['quality']
            );
        }

        if (!$success) {
            throw new Exception('图片处理失败');
        }

        $record = [
            'name' => $originalName,
            'newname' => $filename,
            'url' => rtrim($this->uploadConfig['url'], '/') . '/' . $filename,
            'size' => filesize($targetPath),
            'width' => $newWidth,
            'height' => $newHeight,
            'upload_time' => time()
        ];

        $imageId = $this->db->insert('images', $record);

        echo json_encode([
            'status' => 'success',
            'data' => $record + ['id' => $imageId]
        ]);
        exit;

    } catch (Exception $e) {
        if (isset($targetPath) && file_exists($targetPath)) {
            unlink($targetPath);
        }
        $this->jsonError($e->getMessage());
    }
}


private function calculateDimensions($origW, $origH, $targetW, $targetH) {
    if ($targetW <= 0 && $targetH <= 0) {
        return [$origW, $origH, 0, 0, $origW, $origH];
    }

    // 只指定宽
    if ($targetW > 0 && $targetH <= 0) {
        $newW = $targetW;
        $newH = (int)($origH * ($targetW / $origW));
        return [$newW, $newH, 0, 0, $origW, $origH];
    }

    // 只指定高
    if ($targetH > 0 && $targetW <= 0) {
        $newH = $targetH;
        $newW = (int)($origW * ($targetH / $origH));
        return [$newW, $newH, 0, 0, $origW, $origH];
    }

    // 指定宽高 → 居中裁剪
    $srcRatio = $origW / $origH;
    $dstRatio = $targetW / $targetH;

    if ($srcRatio > $dstRatio) {
        $cropH = $origH;
        $cropW = (int)($origH * $dstRatio);
        $srcX = (int)(($origW - $cropW) / 2);
        $srcY = 0;
    } else {
        $cropW = $origW;
        $cropH = (int)($origW / $dstRatio);
        $srcX = 0;
        $srcY = (int)(($origH - $cropH) / 2);
    }

    return [$targetW, $targetH, $srcX, $srcY, $cropW, $cropH];
}


// 处理图片，带裁剪逻辑
private function processImageWithCrop($srcPath, $mime, $destPath, $width, $height, $srcX, $srcY, $cropW, $cropH, $quality) {
    switch ($mime) {
        case 'image/jpeg': $src = imagecreatefromjpeg($srcPath); break;
        case 'image/png': $src = imagecreatefrompng($srcPath); imagesavealpha($src, true); break;
        case 'image/gif': $src = imagecreatefromgif($srcPath); break;
        case 'image/webp': $src = imagecreatefromwebp($srcPath); break;
        default: throw new Exception("不支持的图片格式: {$mime}");
    }

    if (!$src) throw new Exception('无法解析图片内容');

    $dest = imagecreatetruecolor($width, $height);

    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $width, $height, $transparent);
    }

    imagecopyresampled(
        $dest, $src,
        0, 0,
        $srcX, $srcY,
        $width, $height,
        $cropW, $cropH
    );

    $result = false;
    switch ($mime) {
        case 'image/jpeg': $result = imagejpeg($dest, $destPath, $quality); break;
        case 'image/png': $result = imagepng($dest, $destPath); break;
        case 'image/gif': $result = imagegif($dest, $destPath); break;
        case 'image/webp': $result = imagewebp($dest, $destPath, $quality); break;
    }

    imagedestroy($src);
    imagedestroy($dest);

    return $result;
}


// 工具函数：图片处理（普通格式，不裁剪）
private function processImage($srcPath, $mime, $destPath, $width, $height, $quality) {
    switch ($mime) {
        case 'image/jpeg': $src = imagecreatefromjpeg($srcPath); break;
        case 'image/png': $src = imagecreatefrompng($srcPath); imagesavealpha($src, true); break;
        case 'image/gif': $src = imagecreatefromgif($srcPath); break;
        case 'image/webp': $src = imagecreatefromwebp($srcPath); break;
        default: throw new Exception("不支持的图片格式: {$mime}");
    }

    if (!$src) throw new Exception('无法解析图片内容');

    $dest = imagecreatetruecolor($width, $height);

    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $width, $height, $transparent);
    }

    imagecopyresampled($dest, $src, 0, 0, 0, 0, $width, $height, imagesx($src), imagesy($src));

    $result = false;
    switch ($mime) {
        case 'image/jpeg': $result = imagejpeg($dest, $destPath, $quality); break;
        case 'image/png': $result = imagepng($dest, $destPath); break;
        case 'image/gif': $result = imagegif($dest, $destPath); break;
        case 'image/webp': $result = imagewebp($dest, $destPath, $quality); break;
    }

    imagedestroy($src);
    imagedestroy($dest);

    return $result;
}

    // 工具函数：上传错误信息
    private function getUploadError($code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => '超过php.ini限制',
            UPLOAD_ERR_FORM_SIZE => '超过表单限制',
            UPLOAD_ERR_PARTIAL => '文件仅部分上传',
            UPLOAD_ERR_NO_FILE => '未上传文件',
            UPLOAD_ERR_NO_TMP_DIR => '缺少临时目录',
            UPLOAD_ERR_CANT_WRITE => '写入文件失败',
            UPLOAD_ERR_EXTENSION => '被扩展阻止'
        ];
        return $errors[$code] ?? "未知错误（代码: {$code}）";
    }

    // 工具函数：格式化文件大小
    private function formatFileSize($bytes) {
        if ($bytes < 1024) return $bytes . 'B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . 'KB';
        return round($bytes / 1048576, 1) . 'MB';
    }

    // 工具函数：JSON错误响应
    private function jsonError($message) {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
  public function delete() {
    header('Content-Type: application/json');

    // 获取 JSON 输入
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['ids']) || !is_array($input['ids'])) {
        echo json_encode(['status' => 'error', 'message' => '缺少参数']);
        return;
    }

    // 将字符串数组转换为整数数组
    $ids = array_map('intval', $input['ids']);
    $ids = array_filter($ids); // 去除无效值

    if (empty($ids)) {
        echo json_encode(['status' => 'error', 'message' => '无效的 ID 列表']);
        return;
    }

    try {
        // 查询图片路径（如果你想同时删除图片文件）
      $images = $this->db->select('images', ['id', 'newname'], [
            'id' => $ids
        ]);

        foreach ($images as $img) {
            $file = $this->uploadConfig['upload_dir'] . $img['newname'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        // 删除数据库记录
        $this->db->delete('images', ['id' => $ids]);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => '删除失败：' . $e->getMessage()]);
    }
}




}
