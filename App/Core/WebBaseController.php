<?php
namespace App\Core;

class WebBaseController extends BaseController {

    protected function render($viewPath, $data = []) {
        // 如果是绝对路径，直接使用
        if (strpos($viewPath, '/') === 0 || preg_match('/^[a-zA-Z]:\\\\/', $viewPath)) {
            $fullPath = $viewPath;
        } else {
            // 相对路径，按默认视图目录拼接
            $viewsDir = __DIR__ . '/../views/';
            $fullPath = $viewsDir . $viewPath;
        }

        if (!file_exists($fullPath)) {
            throw new Exception("视图文件不存在: $fullPath");
        }

        extract($data);

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            include $fullPath;
        } else {
            ob_start();
            include $fullPath;
            $Content = ob_get_clean();

            include __DIR__ . '/../views/Web/index.php'; // 主后台模板
        }
    }
}
