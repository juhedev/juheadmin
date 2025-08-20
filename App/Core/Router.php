<?php
namespace App\Core;

class Router {
    private array $routes = [];
    private array $dependencies = [];

    private function handle404() {
        $controller = new BaseController();
        $controller->show404();
    }

    /**
     * 添加路由
     * @param string $method 请求方法 GET/POST 等
     * @param string $path 路径，必须以 / 开头，尾部无 /
     * @param callable|string $callback 处理器，格式：Controller@method 或回调函数
     */

    public function add($method, $path, $callback) {
        $methods = explode('|', strtoupper($method)); // 支持多方法
        $normalizedPath = $this->normalizePath($path);

        foreach ($methods as $m) {
            $this->routes[$m][$normalizedPath] = $callback;
        }
    }

    public function get($path, $callback) {
        $this->add('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->add('POST', $path, $callback);
    }

    public function hasRoute($method, $path): bool {
        $method = strtoupper($method);
        $normalizedPath = $this->normalizePath($path);
        return isset($this->routes[$method][$normalizedPath]);
    }


     public function setDependencies(array $deps) {
        $this->dependencies = $deps;
    }
    /**
     * 路由分发
     * @param string $method 请求方法
     * @param string $uri 请求路径
     * @param array $dependencies 依赖注入数组，键为类名，值为实例（可选）
     */
    public function dispatch($method, $uri, $dependencies = []) {
        $dependencies = array_merge($this->dependencies, $dependencies);
        $method = strtoupper($method);
        $normalizedPath = $this->normalizePath($uri);

        if (!isset($this->routes[$method])) {
            echo "请求方法 [$method] 无任何注册路由。";
            return;
        }

        // 精确匹配
        if (isset($this->routes[$method][$normalizedPath])) {
            $this->callHandler($this->routes[$method][$normalizedPath], [], $dependencies);
            return;
        }

        // 模糊匹配带参数路由
        foreach ($this->routes[$method] as $routePath => $callback) {
            $pattern = $this->convertToRegex($routePath);
            if (preg_match($pattern, $normalizedPath, $matches)) {
                array_shift($matches); // 去掉完整匹配
                $this->callHandler($callback, $matches, $dependencies);
                return;
            }
        }

        // 404
        $this->handle404();
    }
    
    public function group(string $prefixUri, string $controllerNamespace, array $routes)
    {
        foreach ($routes as $route) {
            if (count($route) < 3) {
                throw new \InvalidArgumentException("每个子路由必须包含 method、uri、handler");
            }

            [$method, $subUri, $handler] = $route;

            // 构造完整 URI
            $uri = rtrim($prefixUri, '/') . '/' . ltrim($subUri, '/');

            // 构造完整处理器（加命名空间）
            if (strpos($handler, '@') !== false) {
                [$controller, $action] = explode('@', $handler);
                $fullHandler = $controllerNamespace . '\\' . $controller . '@' . $action;
            } else {
                $fullHandler = $controllerNamespace . '\\' . $handler;
            }

            $this->add($method, $uri, $fullHandler);
        }
    }

    /**
     * 调用处理器，支持构造函数依赖注入
     * @param callable|string $callback
     * @param array $params 传给方法的参数
     * @param array $dependencies 依赖注入映射，key: 类名，value: 实例
     */
    private function callHandler($callback, $params = [], $dependencies = []) {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '未知请求方法';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '未知请求路径';

        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($class, $method) = explode('@', $callback);

            if (class_exists($class) && method_exists($class, $method)) {
                try {
                    $reflection = new \ReflectionClass($class);
                    $instance = null;
                    $constructor = $reflection->getConstructor();

                    if ($constructor) {
                        $ctorParams = $constructor->getParameters();
                        $args = [];

                        foreach ($ctorParams as $param) {
                            $paramType = $param->getType();
                            if ($paramType && !$paramType->isBuiltin()) {
                                $paramClassName = $paramType->getName();
                                if (isset($dependencies[$paramClassName])) {
                                    $args[] = $dependencies[$paramClassName];
                                } elseif ($param->isDefaultValueAvailable()) {
                                    $args[] = $param->getDefaultValue();
                                } else {
                                    throw new \Exception("依赖注入失败：未提供 {$paramClassName} 实例");
                                }
                            } else {
                                $args[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                            }
                        }

                        $instance = $reflection->newInstanceArgs($args);
                    } else {
                        $instance = new $class();
                    }

                    // ⚠️ 此处加入详细异常捕获
                    try {
                        call_user_func_array([$instance, $method], $params);
                    } catch (\Throwable $e) {
                        http_response_code(500);
                        echo "处理错误：<br>";
                        echo "<strong>" . htmlspecialchars($e->getMessage()) . "</strong><br>";
                        echo "文件：" . $e->getFile() . " 第 " . $e->getLine() . " 行<br>";
                        echo "<pre>" . $e->getTraceAsString() . "</pre>";
                        exit;
                    }

                    return;

                } catch (\Throwable $e) {
                    http_response_code(500);
                    echo "控制器初始化错误：<br>";
                    echo "<strong>" . htmlspecialchars($e->getMessage()) . "</strong><br>";
                    echo "文件：" . $e->getFile() . " 第 " . $e->getLine() . " 行<br>";
                    echo "<pre>" . $e->getTraceAsString() . "</pre>";
                    exit;
                }
            }

            http_response_code(500);
            echo "处理错误：类 <strong>" . htmlspecialchars($class) . "</strong> 或方法 <strong>" . htmlspecialchars($method) . "</strong> 未找到。<br>";
            echo "请求方法：<strong>{$requestMethod}</strong><br>";
            echo "请求路径：<strong>{$requestUri}</strong><br>";
            return;
        }

        if (is_callable($callback)) {
            try {
                call_user_func_array($callback, $params);
            } catch (\Throwable $e) {
                http_response_code(500);
                echo "回调执行错误：<br>";
                echo "<strong>" . htmlspecialchars($e->getMessage()) . "</strong><br>";
                echo "文件：" . $e->getFile() . " 第 " . $e->getLine() . " 行<br>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                exit;
            }
            return;
        }

        http_response_code(500);
        echo "无效的路由处理器。<br>";
        echo "请求方法：<strong>{$requestMethod}</strong><br>";
        echo "请求路径：<strong>{$requestUri}</strong><br>";
    }

    /**
     * 规范化路径，保证统一格式：
     *  - 开头带 /
     *  - 尾部无 /
     *  - 根路径保持 /
     */
    private function normalizePath($path) {
        $path = trim($path);
        if ($path === '' || $path === '/') {
            return '/';
        }
        return '/' . trim($path, '/');
    }

    /**
     * 将路由路径转为正则表达式，支持 {param} 动态参数
     * @param string $routePath
     * @return string 正则表达式
     */
    private function convertToRegex($routePath) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }
}
