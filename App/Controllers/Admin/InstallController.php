<?php
namespace App\Controllers\Admin;

class InstallController
{
    protected $step;
    protected $configFile = ROOT_PATH . "Db/config.php";
    protected $installedLockFile = ROOT_PATH . "Db/installed.lock";
    protected $db;
    protected $message = '';
    protected $messageType = ''; // success, error, warning

    public function __construct()
    {
        // 关键修复：步骤4即使已安装也允许访问
        $currentStep = $_POST['step'] ?? $_GET['step'] ?? 1;
        
        // 只有非步骤4且已安装时才显示已安装页面
        if ($currentStep != 4 && $this->isInstalled()) {
            $this->step = 5; // 已安装状态
            return;
        }
        
        // 处理消息提示
        if (isset($_SESSION['install_message'])) {
            $this->message = $_SESSION['install_message'];
            $this->messageType = $_SESSION['install_message_type'];
            unset($_SESSION['install_message'], $_SESSION['install_message_type']);
        }
        
        // 设置当前步骤
        $this->step = $currentStep;
    }

    public function index()
    {
        // 已安装则显示提示（步骤4除外）
        if ($this->step == 5) {
            $this->renderHeader();
            $this->alreadyInstalled();
            $this->renderFooter();
            return;
        }
        
        // 处理表单提交（在输出任何内容之前）
        $this->handleSubmission();
        
        // 输出页面
        $this->renderHeader();
        
        switch ($this->step) {
            case 1:
                $this->checkEnvironment();
                break;
            case 2:
                $this->databaseForm();
                break;
            case 3:
                $this->adminForm();
                break;
            case 4:
                $this->finish();
                break;
            default:
                $this->setMessage('非法步骤', 'error');
                $this->showMessage();
        }
        
        $this->renderFooter();
    }
    
    // 处理表单提交
    protected function handleSubmission()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $currentStep = $_POST['step'] ?? 1;
        
        switch ($currentStep) {
            case 2:
                $this->processDatabaseForm();
                break;
            case 3:
                $this->processAdminForm();
                break;
        }
    }
    
    // 处理数据库表单提交
    protected function processDatabaseForm()
    {
        $host = $_POST['db_host'] ?? '';
        $name = $_POST['db_name'] ?? '';
        $user = $_POST['db_user'] ?? '';
        $pass = $_POST['db_pass'] ?? '';

        try {
            // 连接数据库服务器
            $pdo = new \PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // 检查数据库是否存在
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'");
            $dbExists = $stmt->fetchColumn() !== false;
            
            // 如果数据库存在且不为空，提示用户
            if ($dbExists) {
                $stmt = $pdo->query("USE $name");
                $stmt = $pdo->query("SHOW TABLES");
                if ($stmt->fetchColumn() !== false) {
                    $this->setSessionMessage(
                        "警告：数据库 '$name' 已存在且包含表。继续安装可能会覆盖现有数据。", 
                        'warning'
                    );
                    return;
                }
            } else {
                // 创建数据库
                $pdo->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
            
            // 连接到指定数据库
            $this->db = new \PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 保存配置文件
            $config = "<?php\nreturn [\n";
            $config .= "    'db_host' => '" . addslashes($host) . "',\n";
            $config .= "    'db_name' => '" . addslashes($name) . "',\n";
            $config .= "    'db_user' => '" . addslashes($user) . "',\n";
            $config .= "    'db_pass' => '" . addslashes($pass) . "',\n];\n";

            if (file_put_contents($this->configFile, $config) === false) {
                throw new \Exception("无法写入配置文件，请检查权限");
            }

            $this->setSessionMessage('数据库配置成功！即将进入管理员设置', 'success');
            header("Location: ?step=3");
            exit;
        } catch (\Exception $e) {
            $this->setSessionMessage('数据库配置失败：' . $e->getMessage(), 'error');
            header("Location: ?step=2");
            exit;
        }
    }
// 处理管理员表单提交
    protected function processAdminForm()
    {
        try {
            if (!file_exists($this->configFile)) {
                throw new \Exception("数据库配置文件不存在，请先完成数据库设置");
            }
            
            $cfg = require $this->configFile;
            $this->db = new \PDO(
                "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4",
                $cfg['db_user'],
                $cfg['db_pass']
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 创建images表
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `images` (
                  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                  `name` varchar(255) NOT NULL COMMENT '文件原名',
                  `newname` varchar(255) NOT NULL COMMENT '新文件名',
                  `url` text NOT NULL COMMENT 'URL',
                  `size` int(11) NOT NULL COMMENT '文件大小（字节）',
                  `width` int(11) NOT NULL COMMENT '宽度（像素）',
                  `height` int(11) NOT NULL COMMENT '高度（像素）',
                  `upload_time` int(11) NOT NULL COMMENT '上传时间戳',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 创建plugins表
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `plugins` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) NOT NULL COMMENT '插件唯一标识，如 SamplePlugin',
                  `title` varchar(255) NOT NULL COMMENT '插件显示名称',
                  `description` text COMMENT '插件描述',
                  `version` varchar(20) DEFAULT NULL COMMENT '插件版本号',
                  `author` varchar(100) DEFAULT NULL COMMENT '作者',
                  `url` varchar(128) NOT NULL,
                  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '启用状态，1=启用，0=禁用',
                  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件管理表';
            ");

            // 创建users表
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `email` varchar(100) NOT NULL,
                  `avatar` VARCHAR(128) DEFAULT NULL,
                  `status` int(11) NOT NULL DEFAULT '0',
                  `role` varchar(20) NOT NULL DEFAULT 'user',
                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `username` (`username`),
                  UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? ''; // 新增邮箱字段

            // 验证逻辑更新
            if (!$username) {
                throw new \Exception("用户名不能为空");
            }
            
            if (!$password) {
                throw new \Exception("密码不能为空");
            }
            
            if (strlen($password) < 8) {
                throw new \Exception("密码长度不能少于8位");
            }

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("请输入有效的邮箱地址");
            }
            
            // 检查用户名是否已存在
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn()) {
                throw new \Exception("用户名 '$username' 已存在，请选择其他用户名");
            }

            // 检查邮箱是否已存在
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn()) {
                throw new \Exception("邮箱 '$email' 已被使用，请选择其他邮箱");
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            // 插入管理员数据（包含邮箱和管理员角色）
            $stmt = $this->db->prepare("
                INSERT INTO users (username, password, email, role, status) 
                VALUES (?, ?, ?, 'admin', 1)
            ");
            $stmt->execute([$username, $hash, $email]);

            // 创建临时标记文件
            file_put_contents(ROOT_PATH . "Db/install_completed.tmp", date('Y-m-d H:i:s') . "\n");
            
            $this->setSessionMessage('管理员账号创建成功！安装已完成', 'success');
            header("Location: ?step=4");
            exit;
        } catch (\Exception $e) {
            $this->setSessionMessage('管理员账号创建失败：' . $e->getMessage(), 'error');
            header("Location: ?step=3");
            exit;
        }
    }
    
    
    // 渲染页面头部和样式
    protected function renderHeader()
    {
        ?>
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>系统安装向导</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: '#3b82f6',
                                secondary: '#10b981',
                                danger: '#ef4444',
                                warning: '#f59e0b',
                                neutral: '#f3f4f6',
                            },
                        }
                    }
                }
            </script>
            <style type="text/tailwindcss">
                @layer utilities {
                    .step-active { @apply bg-primary text-white border-primary; }
                    .step-passed { @apply bg-secondary text-white border-secondary; }
                    .step-pending { @apply bg-gray-100 text-gray-400 border-gray-200; }
                    .card { @apply bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg; }
                    .btn { @apply px-4 py-2 rounded-md font-medium transition-all duration-200; }
                    .btn-primary { @apply bg-primary text-white hover:bg-primary/90; }
                    .btn-secondary { @apply bg-gray-600 text-white hover:bg-gray-700; }
                }
            </style>
        </head>
        <body class="bg-gray-50 min-h-screen font-sans">
            <div class="container mx-auto px-4 py-8 max-w-3xl">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
                        <i class="fa fa-cogs text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">系统安装向导</h1>
                    <p class="text-gray-500 mt-2">请按照步骤完成系统安装</p>
                </div>
                
                <!-- 步骤指示器 -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center 
                                <?php echo $this->step == 1 ? 'step-active' : ($this->step > 1 ? 'step-passed' : 'step-pending'); ?>">
                                <i class="fa fa-check"></i>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">环境检测</span>
                        </div>
                        
                        <div class="flex-1 h-1 mx-4 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-primary transition-all duration-500 
                                <?php echo $this->step > 1 ? 'w-full' : 'w-0'; ?>"></div>
                        </div>
                        
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center 
                                <?php echo $this->step == 2 ? 'step-active' : ($this->step > 2 ? 'step-passed' : 'step-pending'); ?>">
                                <i class="fa fa-database"></i>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">数据库设置</span>
                        </div>
                        
                        <div class="flex-1 h-1 mx-4 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-primary transition-all duration-500 
                                <?php echo $this->step > 2 ? 'w-full' : 'w-0'; ?>"></div>
                        </div>
                        
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center 
                                <?php echo $this->step == 3 ? 'step-active' : ($this->step > 3 ? 'step-passed' : 'step-pending'); ?>">
                                <i class="fa fa-user"></i>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">管理员设置</span>
                        </div>
                        
                        <div class="flex-1 h-1 mx-4 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-primary transition-all duration-500 
                                <?php echo $this->step > 3 ? 'w-full' : 'w-0'; ?>"></div>
                        </div>
                        
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center 
                                <?php echo $this->step == 4 ? 'step-active' : ($this->step > 4 ? 'step-passed' : 'step-pending'); ?>">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">完成</span>
                        </div>
                    </div>
                </div>
                
                <!-- 消息提示 -->
                <?php $this->showMessage(); ?>
                
                <div class="card p-6">
        <?php
    }
    
    // 渲染页面底部
    protected function renderFooter()
    {
        ?>
                </div>
                <div class="mt-8 text-center text-gray-500 text-sm">
                    <p>© 2025 系统安装向导</p>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    // 显示消息提示
    protected function showMessage()
    {
        if (empty($this->message)) {
            return;
        }
        
        $icon = '';
        $class = '';
        
        switch ($this->messageType) {
            case 'success':
                $icon = 'fa-check-circle';
                $class = 'bg-green-50 border-green-200 text-green-700';
                break;
            case 'error':
                $icon = 'fa-exclamation-circle';
                $class = 'bg-red-50 border-red-200 text-red-700';
                break;
            case 'warning':
                $icon = 'fa-exclamation-triangle';
                $class = 'bg-yellow-50 border-yellow-200 text-yellow-700';
                break;
            default:
                $icon = 'fa-info-circle';
                $class = 'bg-blue-50 border-blue-200 text-blue-700';
        }
        
        echo "<div class='mb-6 p-4 border rounded-lg $class'>
            <i class='fa $icon mr-2'></i>$this->message
        </div>";
    }
    
    // 设置消息
    protected function setMessage($message, $type = 'info')
    {
        $this->message = $message;
        $this->messageType = $type;
    }
    
    // 设置会话消息（用于跳转后显示）
    protected function setSessionMessage($message, $type = 'info')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['install_message'] = $message;
        $_SESSION['install_message_type'] = $type;
    }
    
    // 检查是否已安装
    protected function isInstalled()
    {
        return file_exists($this->installedLockFile);
    }
    
    // 已安装提示
    protected function alreadyInstalled()
    {
        echo "<div class='text-center py-8'>
            <div class='inline-flex items-center justify-center w-20 h-20 rounded-full bg-warning/10 text-warning mb-6'>
                <i class='fa fa-exclamation-triangle text-3xl'></i>
            </div>
            <h2 class='text-2xl font-bold text-gray-800 mb-3'>系统已安装</h2>
            <p class='text-gray-600 mb-8 max-w-md mx-auto'>
                检测到系统已经安装，如需重新安装，请先删除安装锁定文件：<br>
                <code class='bg-gray-100 px-2 py-1 rounded text-sm'>{$this->installedLockFile}</code>
            </p>
            
            <div class='flex justify-center'>
                <a href='/admin/login' class='btn btn-primary inline-flex items-center justify-center'>
                    <i class='fa fa-sign-in mr-2'></i> 登录后台
                </a>
            </div>
        </div>";
    }

    // 第一步：检测环境
    protected function checkEnvironment()
    {
        $phpVersion = PHP_VERSION;
        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'];
        $writableFiles = [
            ROOT_PATH . "Db",
            ROOT_PATH . "uploads",
            ROOT_PATH . "storage",
        ];
        
        // 检查PHP版本是否符合要求
        $phpVersionOk = version_compare($phpVersion, '8.0.0', '>=');
        
        // 检查所有扩展是否都已安装
        $allExtensionsOk = true;
        foreach ($extensions as $ext) {
            if (!extension_loaded($ext)) {
                $allExtensionsOk = false;
            }
        }
        
        // 检查所有文件是否可写
        $allFilesWritable = true;
        foreach ($writableFiles as $file) {
            if (!is_dir($file)) {
                mkdir($file, 0755, true);
            }
            
            if (!is_writable($file)) {
                $allFilesWritable = false;
            }
        }

        echo "<h2 class='text-2xl font-bold text-gray-800 mb-6 flex items-center'>
            <i class='fa fa-server text-primary mr-3'></i>环境检测
        </h2>";
        
        // PHP版本检查
        echo "<div class='mb-4 p-4 border rounded-lg " . ($phpVersionOk ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50') . "'>
            <div class='flex justify-between items-center'>
                <div>
                    <h3 class='font-medium text-gray-800'>PHP版本</h3>
                    <p class='text-sm text-gray-600 mt-1'>需要PHP 8.0.0或更高版本</p>
                </div>
                <span class='text-lg " . ($phpVersionOk ? 'text-secondary' : 'text-danger') . "'>
                    " . ($phpVersionOk ? '✅' : '❌') . "
                </span>
            </div>
            <p class='mt-2 text-sm text-gray-700'>当前版本: $phpVersion</p>
        </div>";
        
        // 扩展检查
        echo "<div class='mb-4'>
            <h3 class='font-medium text-gray-800 mb-3'>必要扩展</h3>
            <div class='grid grid-cols-1 md:grid-cols-2 gap-3'>";
                foreach ($extensions as $ext) {
                    $installed = extension_loaded($ext);
                    echo "<div class='p-3 border rounded-lg " . ($installed ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50') . "'>
                        <div class='flex justify-between items-center'>
                            <span>$ext</span>
                            <span class='" . ($installed ? 'text-secondary' : 'text-danger') . "'>
                                " . ($installed ? '已安装' : '未安装') . "
                            </span>
                        </div>
                    </div>";
                }
        echo "</div></div>";
        
        // 文件权限检查
        echo "<div class='mb-6'>
            <h3 class='font-medium text-gray-800 mb-3'>文件权限</h3>
            <div class='space-y-3'>";
                foreach ($writableFiles as $file) {
                    $writable = is_writable($file);
                    echo "<div class='p-3 border rounded-lg " . ($writable ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50') . "'>
                        <div class='flex justify-between items-center'>
                            <span class='text-sm truncate max-w-[70%]'>$file</span>
                            <span class='" . ($writable ? 'text-secondary' : 'text-danger') . "'>
                                " . ($writable ? '可写' : '不可写') . "
                            </span>
                        </div>
                    </div>";
                }
        echo "</div></div>";
        
        // 下一步按钮
        $allChecksPassed = $phpVersionOk && $allExtensionsOk && $allFilesWritable;
        echo "<div class='flex justify-end mt-8'>";
            if ($allChecksPassed) {
                echo "<a href='?step=2' class='btn btn-primary flex items-center'>
                    <span>下一步：数据库设置</span>
                    <i class='fa fa-arrow-right ml-2'></i>
                </a>";
            } else {
                echo "<button class='btn bg-gray-300 text-gray-500 cursor-not-allowed' disabled>
                    <span>请先解决所有问题</span>
                    <i class='fa fa-arrow-right ml-2'></i>
                </button>";
            }
        echo "</div>";
    }

    // 第二步：数据库表单
    protected function databaseForm()
    {
        echo "<h2 class='text-2xl font-bold text-gray-800 mb-6 flex items-center'>
            <i class='fa fa-database text-primary mr-3'></i>数据库设置
        </h2>";
        
        echo '<form method="post" class="space-y-4">
            <input type="hidden" name="step" value="2">
            
            <div>
                <label for="db_host" class="block text-sm font-medium text-gray-700 mb-1">数据库主机</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-server"></i>
                    </div>
                    <input type="text" id="db_host" name="db_host" value="localhost" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    <p class="mt-1 text-xs text-gray-500">通常为 localhost 或 127.0.0.1</p>
                </div>
            </div>

            <div>
                <label for="db_name" class="block text-sm font-medium text-gray-700 mb-1">数据库名称</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-database"></i>
                    </div>
                    <input type="text" id="db_name" name="db_name" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                </div>
            </div>
            
            <div>
                <label for="db_user" class="block text-sm font-medium text-gray-700 mb-1">数据库用户名</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-user"></i>
                    </div>
                    <input type="text" id="db_user" name="db_user" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                </div>
            </div>
            
            <div>
                <label for="db_pass" class="block text-sm font-medium text-gray-700 mb-1">数据库密码</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-lock"></i>
                    </div>
                    <input type="password" id="db_pass" name="db_pass"
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    <p class="mt-1 text-xs text-gray-500">如果数据库无密码，请留空</p>
                </div>
            </div>
            
            <div class="flex justify-between mt-8">
                <a href="?step=1" class="btn btn-secondary flex items-center">
                    <i class="fa fa-arrow-left mr-2"></i>
                    <span>上一步</span>
                </a>
                
                <button type="submit" class="btn btn-primary flex items-center">
                    <span>保存并测试</span>
                    <i class="fa fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>';
    }

    // 第三步：创建管理员
    protected function adminForm()
    {
        // 检查配置文件是否存在
        if (!file_exists($this->configFile)) {
            $this->setMessage('数据库配置文件不存在，请先完成数据库设置', 'error');
            $this->showMessage();
            echo "<div class='flex justify-start mt-8'>
                <a href='?step=2' class='btn btn-secondary flex items-center'>
                    <i class='fa fa-arrow-left mr-2'></i>
                    <span>返回数据库设置</span>
                </a>
            </div>";
            return;
        }
        
        echo '<h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa fa-user text-primary mr-3"></i>创建管理员账号
        </h2>';
        
        echo '<form method="post" class="space-y-4">
            <input type="hidden" name="step" value="3">
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">管理员用户名</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-user-circle"></i>
                    </div>
                    <input type="text" id="username" name="username" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                </div>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-user-circle"></i>
                    </div>
                    <input type="text" id="email" name="email" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                </div>
            </div>            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">管理员密码</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa fa-shield"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    <p class="mt-1 text-xs text-gray-500">请设置强密码（至少8位，包含字母和数字）</p>
                </div>
            </div>
            
            <div class="flex justify-between mt-8">
                <a href="?step=2" class="btn btn-secondary flex items-center">
                    <i class="fa fa-arrow-left mr-2"></i>
                    <span>上一步</span>
                </a>
                
                <button type="submit" class="btn btn-primary flex items-center">
                    <span>创建管理员</span>
                    <i class="fa fa-check ml-2"></i>
                </button>
            </div>
        </form>';
    }

    // 第四步：完成安装
    protected function finish()
    {
        // 关键修复：在完成页面创建最终的安装锁定文件
        if (file_exists(ROOT_PATH . "Db/install_completed.tmp")) {
            rename(ROOT_PATH . "Db/install_completed.tmp", $this->installedLockFile);
        }
        
        echo "<div class='text-center py-8'>
            <div class='inline-flex items-center justify-center w-20 h-20 rounded-full bg-secondary/10 text-secondary mb-6'>
                <i class='fa fa-check-circle text-3xl'></i>
            </div>
            <h2 class='text-2xl font-bold text-gray-800 mb-3'>安装完成！</h2>
            <p class='text-gray-600 mb-8 max-w-md mx-auto'>管理员账号已创建，系统安装成功，您可以登录后台开始使用了。</p>
            
            <div class='flex flex-col sm:flex-row justify-center gap-4'>
                <a href='/admin/login' class='btn btn-primary inline-flex items-center justify-center'>
                    <i class='fa fa-sign-in mr-2'></i> 登录后台
                </a>
                <a href='/' class='btn btn-secondary inline-flex items-center justify-center'>
                    <i class='fa fa-home mr-2'></i> 访问首页
                </a>
            </div>
        </div>";
    }
}
    