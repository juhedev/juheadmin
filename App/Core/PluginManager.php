<?php
namespace App\Core;

class PluginManager {
    protected $db;
    protected $pluginDir;
    protected $loadedPlugins = [];
    protected $router;
    protected $logFile;
    protected $installedPluginsCache = null;
    protected $registeredRoutes = [];
    protected $plugins = null;
    protected $enabledPlugins = [];
    protected $systemRoutes = [];
    protected $enabledRoutes = [];
    private $lastError = '';

    private function setError(string $msg) {
        $this->lastError = $msg;
        $this->log("[ERROR] $msg");
    }

    public function getLastError(): string {
        return $this->lastError;
    }


    /**
     * 构造函数
     * @param object $db 数据库对象
     * @param object $router 路由对象
     * @param string $pluginDir 插件目录
     */
    public function __construct($db, $router, string $pluginDir) {
        $this->db = $db;
        $this->router = $router;
        $this->pluginDir = rtrim($pluginDir, '/');

        $rootDir = realpath(__DIR__ . '/../../');
        $logDir = $rootDir . '/Storage/log';

        // 确保日志目录（如不存在则创建）
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new \RuntimeException("无法创建日志目录: $logDir ，请检查权限");
            }
        }

        // 检查目录可写性
        if (!is_writable($logDir)) {
            throw new \RuntimeException("日志目录不可写: $logDir ，请检查权限");
        }

        $this->logFile = $logDir . '/plugin_manager.log';
        
        // 检查日志文件大小并自动清理（大于2MB时）
        $this->cleanupLogFile(2); // 传入最大允许的MB数
    }

    /**
     * 清理日志日志文件清理
     * @param int $maxSizeMB 最大允许的文件大小(MB)
     */
    private function cleanupLogFile(int $maxSizeMB) {
        // 检查文件是否存在
        if (!file_exists($this->logFile)) {
            return;
        }
        
        // 转换MB为字节
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        
        // 获取当前文件大小
        $currentSize = filesize($this->logFile);
        
        // 如果文件大小超过限制，清空文件
        if ($currentSize > $maxSizeBytes) {
            // 先备份当前日志内容（可选）
            $backupFile = $this->logFile . '.bak_' . date('YmdHis');
            copy($this->logFile, $backupFile);
            
            // 清空日志文件
            file_put_contents($this->logFile, '');
            
            // 记录清理日志
            $message = "[" . date('Y-m-d H:i:s') . "] 日志文件超过{$maxSizeMB}MB，已自动清理\n";
            file_put_contents($this->logFile, $message, FILE_APPEND);
        }
    }


    /**
     * 设置系统核心路由
     * @param array $routes 格式: [['GET', '/path', 'handler'], ...]
     */
    public function setSystemRoutes(array $routes): void {
        $this->systemRoutes = [];
        foreach ($routes as $route) {
            if (count($route) < 2) continue;
            [$method, $path] = $route;
            $key = strtoupper(trim($method)) . ' ' . trim($path);
            $this->systemRoutes[$key] = true;
        }
    }

    /**
     * 获取所有已注册路由（系统+已启用插件）
     * @return array 路由键名数组
     */
    public function getAllRegisteredRoutes(): array {
        return array_merge(
            array_keys($this->systemRoutes),
            array_keys($this->enabledRoutes)
        );
    }

    public function getDB() {
        return $this->db;
    }

    protected function log(string $msg, string $level = 'INFO'): void {
        $date = date('Y-m-d H:i:s');
        $logMsg = "[$date] [$level] $msg\n";
        $logDir = dirname($this->logFile);

        try {
            if (is_dir($logDir) && is_writable($logDir)) {
                file_put_contents($this->logFile, $logMsg, FILE_APPEND);
            } else {
                error_log("PluginManager log directory not writable: $logDir");
            }
        } catch (\Throwable $e) {
            error_log("Failed to write plugin log: " . $e->getMessage());
        }
    }

    /**
     * 获取所有扫描到的插件信息
     * @return array
     */
    public function getAllPlugins(): array {
        $this->scanPlugins();
        return $this->plugins;
    }

    public function scanPlugins() {
        if ($this->plugins !== null) {
            return $this->plugins;
        }

        $this->log("scanPlugins called");
        $this->plugins = [];

        if (!is_dir($this->pluginDir)) {
            mkdir($this->pluginDir, 0755, true);
            $this->log("Plugin directory created: {$this->pluginDir}");
            return $this->plugins;
        }

        $dirs = scandir($this->pluginDir);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $pluginPath = $this->pluginDir . '/' . $dir;
            $pluginFile = $pluginPath . '/mian.php'; 

            if (is_dir($pluginPath) && is_file($pluginFile)) {
                $pluginConfig = $this->getPluginInfo($pluginFile, $dir);

                if (empty($pluginConfig)) {
                    $this->log("[WARN] 插件 {$dir} 不规范，已跳过");
                    continue;
                }

                $this->plugins[$dir] = array_merge([
                    'dir'  => $dir,
                    'path' => $pluginPath,
                ], $pluginConfig);

                $this->log("Plugin found: $dir ({$pluginConfig['name']})");
            }
        }

        return $this->plugins;
    }


    public function getAllPluginIcons(): array
    {
        $pluginDirs = array_filter(scandir(PLUGIN_PATH . '/'), function($dir) {
            return $dir !== '.' && $dir !== '..';
        });

        $icons = [];
        foreach ($pluginDirs as $pluginDir) {
            $pluginFile = PLUGIN_PATH . "/{$pluginDir}/mian.php";
            if (file_exists($pluginFile)) {
                $info = include $pluginFile;
                $icons[$pluginDir] = $info['menus'][0]['icon'] ?? 'fa fa-plug';
            }
        }

        return $icons;
    }


    /**
     * 从插件文件头部注释获取插件信息
     */
    private function getPluginInfo(string $pluginFile, string $pluginDirName): array {
        $info = [];
        $arrayConfig = @include $pluginFile;
        if (!is_array($arrayConfig)) {
            $this->setError("[ERROR] Plugin file {$pluginFile} must return an array");
            return [];
        }
        $lines = file($pluginFile);
        if (!$lines) {
            $this->setError("[ERROR] Cannot read plugin file: {$pluginFile}");
            return [];
        }

        // 读取前 30 行，兼容大部分注释头
        $header = implode('', array_slice($lines, 0, 30));
        //$this->log("Header of {$pluginFile}:\n" . $header);
        if (preg_match_all('/^\s*\*\s*([A-Za-z ]+):\s*(.+)$/m', $header, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = strtolower(str_replace(' ', '_', trim($match[1])));
                $value = trim($match[2]);
                $info[$key] = $value;
            }
        }

        // 必填字段列表
        $requiredFields = ['plugin_name', 'version', 'description', 'author', 'plugin_url'];

        // 检查必要字段是否存在且非空
        foreach ($requiredFields as $field) {
            if (empty($info[$field])) {
                $this->setError("[ERROR] Plugin {$pluginFile} 缺少必要字段或为空: {$field}");
                return [];
            }
        }

        // 插件文件夹名必须和插件名一致
        if ($pluginDirName !== $info['plugin_name']) {
            $this->setError("[ERROR] 插件目录名 {$pluginDirName} 与插件名 {$info['plugin_name']} 不一致");
            return [];
        }



        if (isset($info['plugin_name'])) {
            $arrayConfig['name'] = $info['plugin_name'];
        }
        if (isset($info['description'])) {
            $arrayConfig['description'] = $info['description'];
        }
        if (isset($info['version'])) {
            $arrayConfig['version'] = $info['version'];
        }
        if (isset($info['author'])) {
            $arrayConfig['author'] = $info['author'];
        }
        if (isset($info['plugin_url'])) {
            $arrayConfig['url'] = $info['plugin_url'];
        }

        return $arrayConfig;
    }



    public function clearCache() {
        $this->plugins = null;
    }

    public function getEnabledPluginMenus(): array {
        $menus = [];

        foreach ($this->enabledPlugins as $plugin) {
            if (isset($plugin['menus']) && is_array($plugin['menus'])) {
                $menus = array_merge($menus, $plugin['menus']);
            }
        }

        return $menus;
    }

    /**
     * 获取已安装插件列表
     * @return array
     */
    public function getInstalledPlugins(): array {
        // 如果数据库对象不存在或未初始化，直接返回空数组
        if (!$this->db || !$this->db->medoo) {
            return [];
        }

        if ($this->installedPluginsCache !== null) {
            return $this->installedPluginsCache;
        }

        try {
            $installed = $this->db->select('plugins', '*');
            if (!is_array($installed)) {
                $installed = [];
            }
        } catch (\Exception $e) {
            // 捕获数据库异常，返回空数组
            $installed = [];
        }

        $installedPlugins = [];
        foreach ($installed as $row) {
            $installedPlugins[$row['name']] = $row;
        }

        $this->installedPluginsCache = $installedPlugins;
        return $installedPlugins;
    }


    /**
     * 获取所有插件状态信息
     * @return array
     */
    public function getPluginStatusList(): array {
        $this->scanPlugins();
        $installedPlugins = $this->getInstalledPlugins();
        $result = [];
        foreach ($this->plugins as $name => $plugin) {
            $installed = isset($installedPlugins[$name]);
            $status = $installed ? (int)$installedPlugins[$name]['status'] : 0;
            $result[] = [
                'name' => $name,
                'installed' => $installed ? 1 : 0,
                'status' => $status,
                'path' => $plugin['path'],
                'title' => $installed ? $installedPlugins[$name]['title'] : ($plugin['title'] ?? $name),
                'version' => $installed ? $installedPlugins[$name]['version'] : ($plugin['version'] ?? ''),
                'description' => $installed ? $installedPlugins[$name]['description'] : ($plugin['description'] ?? ''),
                'author' => $installed ? $installedPlugins[$name]['author'] : ($plugin['author'] ?? ''),
                'url' => $installed ? $installedPlugins[$name]['url'] : ($plugin['url'] ?? ''),
            ];
        }
        return $result;
    }

    /**
     * 获取启用的插件名称列表
     * @return array
     */
    public function getEnabledPlugins(): array {
        $enabled = [];
        $installed = $this->getInstalledPlugins();
        foreach ($this->plugins as $name => $plugin) {
            if (isset($installed[$name]) && $installed[$name]['status'] == 1) {
                $enabled[] = $name;
            }
        }
        return $enabled;
    }

    /**
     * 递归加载控制器目录内所有PHP文件
     * @param string $dir
     */
    protected function loadControllersRecursively(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $this->loadControllersRecursively($fullPath);
            } elseif (is_file($fullPath) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $fullPath;
                $this->log("Loaded controller: $fullPath");
            }
        }
    }


    private function isAssoc(array $arr): bool {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function loadEnabledPlugins(): void {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $this->scanPlugins();
        $installed = $this->getInstalledPlugins();

        $this->enabledPlugins = [];
        $this->registeredRoutes = [];
        $this->enabledRoutes = [];

        foreach ($this->plugins as $pluginName => $pluginData) {
            if (isset($installed[$pluginName]) && $installed[$pluginName]['status'] == 1) {
                // 激活插件（调用init）
                if (!empty($pluginData['init']) && is_callable($pluginData['init'])) {
                    try {
                        call_user_func($pluginData['init']);
                        //$this->log("Initialized plugin $pluginName");
                    } catch (\Throwable $e) {
                        $this->log("[ERROR] Exception in init of plugin $pluginName: " . $e->getMessage());
                    }
                }

                // 注册普通路由
                if (!empty($pluginData['routes']) && is_array($pluginData['routes'])) {
                    foreach ($pluginData['routes'] as $route) {
                        if (count($route) < 3) {
                            $this->log("[WARNING] Invalid route config for plugin $pluginName");
                            continue;
                        }
                        [$method, $path, $handler] = $route;
                        $method = strtoupper($method);
                        $routeKey = $method . ' ' . $path;

                        if (in_array($routeKey, $this->registeredRoutes, true)) {
                            $this->log("[WARNING] Route conflict: [$method] $path");
                            continue;
                        }

                        $this->router->add($method, $path, $handler);
                        $this->registeredRoutes[] = $routeKey;
                        $this->enabledRoutes[$routeKey] = true;
                        //$this->log("Registered route [$method] $path for plugin $pluginName");
                    }
                }

                // 注册路由组
                if (!empty($pluginData['route_group'])) {
                    $groups = $this->isAssoc($pluginData['route_group']) 
                            ? [$pluginData['route_group']] 
                            : $pluginData['route_group'];

                    foreach ($groups as $group) {
                        if (isset($group['prefix'], $group['namespace'], $group['routes']) && is_array($group['routes'])) {
                            $this->router->group(
                                $group['prefix'],
                                $group['namespace'],
                                $group['routes']
                            );
                            //$this->log("Registered route group [prefix={$group['prefix']}] for plugin $pluginName");
                        } else {
                            $this->log("[WARNING] Invalid route_group config in plugin $pluginName");
                        }
                    }
                }

                $this->enabledPlugins[] = $pluginData;
            }
        }
    }


    /**
     * 安装插件
     * @param string $name
     * @return bool
     */
    public function installPlugin(string $name): bool|string {
        if (!isset($this->plugins[$name])) {
            $this->log("Install failed: plugin $name not found");
            return false;
        }
        $plugin = $this->plugins[$name];

        // 1. 路由冲突检测
        if (!empty($plugin['route_group']) && is_array($plugin['route_group'])) {
            $prefixes = [];
            foreach ($plugin['route_group'] as $group) {
                if (isset($group['prefix'])) {
                    $prefixes[] = $group['prefix'];
                }
            }
            $prefixes = array_unique($prefixes);

            if (!$this->checkRouteConflicts($prefixes)) {
                return false; // 路由冲突阻止安装
            }
        }

        // 2. 表冲突检测
        if (!empty($plugin['tables']) && is_array($plugin['tables'])) {
            $conflictTables = [];

            foreach ($plugin['tables'] as $table) {
                $stmt = $this->db->query("SHOW TABLES LIKE '{$table}'");
                if ($stmt && $stmt->fetch()) {
                    $conflictTables[] = $table;
                }
            }

            if (!empty($conflictTables)) {
                $conflictList = implode(', ', $conflictTables);
                $errorMsg = "插件 {$name} 安装失败：以下数据表已存在 -> {$conflictList}";
                $this->log("[ERROR] $errorMsg");
                return $errorMsg;  // 返回错误消息字符串阻止安装
            }
        }

        // 3. 插件元信息写入数据库
        $pluginData = [
            'name' => $name,
            'title' => $plugin['title'] ?? $name,
            'description' => $plugin['description'] ?? '',
            'version' => $plugin['version'] ?? '',
            'author' => $plugin['author'] ?? '',
            'url' => $plugin['url'] ?? '',
            'status' => 1,
        ];

        $count = $this->db->count('plugins', '*', ['name' => $name]);
        if ($count > 0) {
            $this->db->update('plugins', $pluginData, ['name' => $name]);
        } else {
            $this->db->insert('plugins', $pluginData);
        }

        $this->installedPluginsCache = null;
        return true;
    }


    /**
     * 路由冲突检测
     */
    public function checkRouteConflicts(array $newPrefixes): bool {
        $installedPlugins = $this->getInstalledPlugins(); 
        $installedPrefixes = [];

        foreach ($this->plugins as $pluginName => $plugin) {
            if (!isset($installedPlugins[$pluginName])) continue;

            if (!empty($plugin['route_group']) && is_array($plugin['route_group'])) {
                if (isset($plugin['route_group'][0]) && is_array($plugin['route_group'][0])) {
                    foreach ($plugin['route_group'] as $group) {
                        $prefix = $group['prefix'] ?? '';
                        if ($prefix) $installedPrefixes[] = $prefix;
                    }
                } else {
                    $prefix = $plugin['route_group']['prefix'] ?? '';
                    if ($prefix) $installedPrefixes[] = $prefix;
                }
            }
        }

        $installedPrefixes = array_unique($installedPrefixes);
        $conflictNewPrefixes = [];
        $conflictInstalledPrefixes = [];

        foreach ($newPrefixes as $newPrefix) {
            foreach ($installedPrefixes as $installedPrefix) {
                if ($this->isPrefixConflict($newPrefix, $installedPrefix)) {
                    $conflictNewPrefixes[] = $newPrefix;
                    $conflictInstalledPrefixes[] = $installedPrefix;
                }
            }
        }

        if (!empty($conflictNewPrefixes)) {
            echo json_encode([
                'success' => false,
                'message' => "路由前缀冲突：新插件的前缀（" . implode(', ', array_unique($conflictNewPrefixes)) . "）与已安装插件的前缀（" . implode(', ', array_unique($conflictInstalledPrefixes)) . "）重复，请修改后再安装。"
            ]);
            exit; 
        }

        return true;
    }



    /**
     * 判断两个路由前缀是否冲突
     */
    protected function isPrefixConflict(string $prefixA, string $prefixB): bool {
        $prefixA = rtrim(trim($prefixA), '/');
        $prefixB = rtrim(trim($prefixB), '/');

        // 两个都是空字符串，视为冲突
        if ($prefixA === '' && $prefixB === '') {
            return true;
        }

        // 完全相等才算冲突
        if ($prefixA === $prefixB) {
            return true;
        }

        // 不再判断包含关系为冲突，直接返回不冲突
        return false;
    }



    /**
     * 卸载插件
     * @param string $name
     * @return bool
     */
    public function uninstallPlugin(string $name): bool {
        if (!isset($this->plugins[$name])) {
            $this->log("Uninstall failed: plugin $name not found");
            return false;
        }

        $plugin = $this->plugins[$name];

        // 删除插件相关的数据库表
        if (!empty($plugin['tables']) && is_array($plugin['tables'])) {
            foreach ($plugin['tables'] as $table) {
                $exists = $this->db->query("SHOW TABLES LIKE '{$table}'")->fetch();
                if ($exists) {
                    try {
                        $this->db->query("DROP TABLE IF EXISTS `{$table}`");
                        $this->log("Dropped table: {$table}");
                    } catch (\Throwable $e) {
                        $this->log("[ERROR] Failed to drop table {$table}: " . $e->getMessage());
                    }
                }
            }
        }


        // 删除插件记录
        $this->db->delete('plugins', ['name' => $name]);

        // 清除已安装插件缓存
        $this->installedPluginsCache = null;

        $this->log("Uninstalled plugin {$name}");
        return true;
    }


    /**
     * 启用插件
     * @param string $name
     * @return bool
     */
    public function enablePlugin(string $name): bool {
        $count = $this->db->count('plugins', '*', ['name' => $name]);
        if ($count == 0) {
            $this->log("Enable failed: plugin $name not installed");
            return false;
        }
        $this->db->update('plugins', ['status' => 1], ['name' => $name]);
        $this->installedPluginsCache = null;
        $this->log("Enabled plugin $name");
        return true;
    }

    /**
     * 禁用插件
     * @param string $name
     * @return bool
     */
    public function disablePlugin(string $name): bool {
        $count = $this->db->count('plugins', '*', ['name' => $name]);
        if ($count == 0) {
            $this->log("Disable failed: plugin $name not installed");
            return false;
        }
        $this->db->update('plugins', ['status' => 0], ['name' => $name]);
        $this->installedPluginsCache = null;
        $this->log("Disabled plugin $name");
        return true;
    }
}
