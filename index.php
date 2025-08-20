<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Max-Age: 86400");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}
// 常量定义
define('ROOT_PATH', __DIR__ . '/');
define('APP_CORE_PATH', __DIR__ . '/App/Core/');
define('APP_CONTROLLERS_PATH', __DIR__ . '/App/Controllers/');
define('PLUGIN_PATH', __DIR__ . '/App/Plugins/');
define('DB_PATH', __DIR__ . '/Db/');
define('LOG_PATH', __DIR__ . '/Storage/log/plugin_manager.log');

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (str_starts_with($class, 'App\\Controllers\\')) {
        $relativePath = substr($classPath, strlen('App' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR));
        $file = APP_CONTROLLERS_PATH . $relativePath;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    if (str_starts_with($class, 'App\\Core\\')) {
        $relativePath = substr($classPath, strlen('App' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR));
        $file = APP_CORE_PATH . $relativePath;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    if (str_starts_with($class, 'Db\\')) {
        $relativePath = substr($classPath, strlen('Db' . DIRECTORY_SEPARATOR));
        $file = DB_PATH . $relativePath;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
if (str_starts_with($class, 'Plugins\\')) {
    $relativePath = substr($classPath, strlen('Plugins' . DIRECTORY_SEPARATOR));
    $file = PLUGIN_PATH . $relativePath;
    if (file_exists($file)) {
        require_once $file;
        return;
    }
}

    error_log("自动加载失败，未找到文件: $classPath");
});

// 设置错误日志
ini_set('error_log', __DIR__ . '/storage/log/php_error.log');

// 初始化核心组件
$db = new \Db\Database();
$router = new \App\Core\Router();
$pluginManager = new \App\Core\PluginManager($db, $router, PLUGIN_PATH);

// 1. 加载系统核心路由
$coreRoutes = require __DIR__ . '/routes.php';

// 2. 设置系统路由到插件管理器（冲突检测）
$pluginManager->setSystemRoutes($coreRoutes);

// 3. 加载启用的插件（自动注册路由等）
$pluginManager->loadEnabledPlugins();

// 4. 路由依赖注入配置（可选，方便控制器拿依赖）
$router->setDependencies([
    \Db\Database::class => $db,
    \App\Core\Router::class => $router,
    \App\Core\PluginManager::class => $pluginManager,
]);

// 5. 注册核心路由到路由器
foreach ($coreRoutes as $route) {
    [$method, $path, $handler] = $route;
    $router->add(strtoupper($method), $path, $handler);
}

// 6. 解析请求路径
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!empty($_SERVER['PATH_INFO'])) {
    $uri = $_SERVER['PATH_INFO'];
} else {
    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($scriptDir !== '' && strpos($requestUri, $scriptDir) === 0) {
        $uri = substr($requestUri, strlen($scriptDir));
    } else {
        $uri = $requestUri;
    }

    if ($uri === '' || $uri[0] !== '/') {
        $uri = '/' . $uri;
    }
}

// 7. 派发路由处理请求
$router->dispatch($method, $uri);

exit;
