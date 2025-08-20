<?php
namespace App\Core;
class BaseController {

     /**
     * 渲染视图
     * @param string $view 视图名，支持如 admin.dashboard（映射为 app/views/admin/dashboard.php）
     * @param array $data 传递给视图的数据
     */
    protected function render($viewPath, $data = []) {
        if (strpos($viewPath, '/') === 0 || preg_match('/^[a-zA-Z]:\\\\/', $viewPath)) {
            $fullPath = $viewPath;
        } else {
            $viewsDir = __DIR__ . '/../Views/';
            $fullPath = $viewsDir . $viewPath;
        }

        if (!file_exists($fullPath)) {
            throw new \Exception("视图文件不存在: {$fullPath}", 500);
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

            include __DIR__ . '/../Views/Admin/index.php'; // 主后台模板
        }
    }
    


    /**
     * 检查用户登录
     */
    protected function checkLogin() {

        session_start();

        $timeout = 7200;

        // 提取 IP 前三段（IPv4）
        function get_ip_prefix($ip, $segments = 3) {
            $parts = explode('.', $ip);
            return implode('.', array_slice($parts, 0, $segments));
        }

        if (!isset($_SESSION['username'])) {
            header("Location:  /admin/login");
            exit;
        }

        // 宽松 IP 检查（只比对前三段，例如 192.168.1.xxx）
        $current_ip_prefix = get_ip_prefix($_SERVER['REMOTE_ADDR'], 3);
        $session_ip_prefix = get_ip_prefix($_SESSION['ip'] ?? '', 3);

        if ($current_ip_prefix !== $session_ip_prefix) {
            session_destroy();
            header("Location:  /admin/login");
            exit;
        }

        if ($_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_destroy();
            header("Location:  /admin/login");
            exit;
        }

        if (time() - ($_SESSION['last_activity'] ?? 0) > $timeout) {
            session_destroy();
            header("Location:  /admin/login");
            exit;
        }
        $_SESSION['last_activity'] = time();

    }

    /**
     * 显示 404 页面
     */
    public function show404($msg = '') {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        if ($msg) echo "<p>$msg</p>";
        exit;
    }

    protected function showError($errorMessage) {
        // 错误视图文件路径（根据实际项目目录调整）
        $errorViewPath = __DIR__ . '/../Views/Web/error.php';
        
        // 检查错误视图文件是否存在
        if (!file_exists($errorViewPath)) {
            die("错误：找不到错误视图文件，请检查路径是否正确");
        }
        
        // 传递错误信息到视图
        $error = $errorMessage;
        
        // 加载错误视图（通过include将变量传入视图）
        include $errorViewPath;
        
        // 终止后续代码执行
        exit;
    }
}
