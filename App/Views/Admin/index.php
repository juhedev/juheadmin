<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo htmlspecialchars($title ?? '后台管理'); ?> </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <!-- 配置Tailwind自定义颜色 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {primary: '#3b82f6', secondary: '#36CFC9', success: '#52C41A', warning: '#FAAD14', danger: '#FF4D4F', dark: '#1D2129', 'gray-light': '#F2F3F5', 'gray-medium': '#C9CDD4' },
                    fontFamily: {
                        inter: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body id="app" class="font-inter bg-gray-50 text-dark min-h-screen flex flex-col">
    <!-- 顶部导航栏 -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- 左侧Logo和标题 -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center text-primary font-bold text-xl">
                        <img src="/Static/img/logo.png" class="w-10 mr-2" alt="JuheAdmin Logo">
                        <span>JuheAdmin</span>
                    </a>
                </div>
                <!-- 左侧区域：移动端菜单按钮 + 用户区域 -->
                <div class="flex items-center space-x-4">
                    <!-- 用户菜单 -->
                    <div class="relative order-2 group">
                        <!-- 触发按钮 -->
                        <button class="flex items-center space-x-2 focus:outline-none user-menu-button">
                            <img class="h-8 w-8 rounded-full object-cover" src="https://picsum.photos/200/200?random=1" alt="<?=htmlspecialchars($_SESSION['username'] ?? '用户')?>的头像">
                            <span class="hidden md:inline-block text-sm font-medium"> <?=htmlspecialchars($_SESSION['username'] ?? '用户')?> </span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        <!-- 下拉菜单 (默认隐藏) -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 transform opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right scale-95 group-hover:scale-100">
                            <!-- 个人资料 -->
                            <div id="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-user mr-2 text-gray-500"></i>个人资料
                            </div>
                            <!-- 分割线 -->
                            <div class="border-t border-gray-200 my-1"></div>
                            <!-- 退出登录 -->
                            <a href="/admin/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>退出登录
                            </a>
                        </div>
                    </div>
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-md hover:bg-gray-light order-3">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    <!-- 主要内容区 -->
    <div class="flex flex-1 overflow-hidden">
        <!-- 侧边栏导航 - 固定不动 -->
        <aside id="sidebar" class="w-64 bg-white border-r border-gray-200 fixed left-0 top-16 h-[calc(100vh-4rem)] z-30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto ">
            <div class="p-4 h-full flex flex-col">
                <nav class="space-y-1 flex-1">
                    <!-- 主菜单项 -->
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light main-menu-item">
                        <i class="fas fa-home w-5 text-center"></i>
                        <span>控制台首页</span>
                    </a>
                    <?php
                    global $pluginManager;
                    $menus = $pluginManager->getEnabledPluginMenus();
                    if (is_array($menus)) {
                        foreach ($menus as $menu) {
                            $title       = htmlspecialchars($menu['title'] ?? '');
                            $icon        = htmlspecialchars($menu['icon'] ?? 'fa fa-plug');
                            $path        = htmlspecialchars($menu['path'] ?? '#');
                            $hasChildren = !empty($menu['children']) && is_array($menu['children']);

                            echo "<div class='menu-group'>";
                            echo "<a href='{$path}' class='flex items-center justify-between gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light plugin-menu-link main-menu-item " . ($hasChildren ? 'menu-parent' : '') . "'>";
                            echo "  <div class='flex items-center gap-3'> <i class='{$icon} w-5 text-center'></i> <span>{$title}</span> </div>";

                            if ($hasChildren) {
                                echo "<i class='fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200'></i>";
                            }
                            echo "</a>";
                            if ($hasChildren) {
                                echo "<div class='submenu hidden pl-8'>";
                                foreach ($menu['children'] as $child) {
                                    $childTitle = htmlspecialchars($child['title'] ?? '');
                                    $childPath  = htmlspecialchars($child['path'] ?? '#');
                                    $childIcon  = htmlspecialchars($child['icon'] ?? 'fa fa-circle'); // 默认 icon

                                    echo "<a href='{$childPath}' class='flex items-center gap-3 px-6 py-2 rounded-lg text-sm transition-all mt-1 duration-200 hover:bg-gray-light plugin-submenu-link submenu-item'>";
                                    echo "  <i class='{$childIcon} w-4 text-center'></i> <span>{$childTitle}</span>";
                                    echo "</a>";
                                }
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                    }else {
                        echo "<p>菜单数据为空或格式错误</p>";
                    }
                ?>
                    <a href="/admin/images" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light main-menu-item">
                        <i class="fas fa-image w-5 text-center"></i>
                        <span>图库管理</span>
                    </a>
                    <?php

                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'):
                ?>
                    <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light main-menu-item">
                        <i class="fas fa-user-plus w-5 text-center"></i>
                        <span>用户管理</span>
                    </a>
                    <a href="/admin/plugins" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light main-menu-item">
                        <i class="fas fa-puzzle-piece w-5 text-center"></i>
                        <span>插件管理</span>
                    </a>
                    <a href="/admin/settings" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-light main-menu-item">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span>系统设置</span>
                    </a>
                    <?php endif; ?>
                </nav>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-center hover:bg-danger/10">
                        <span>当前版本：V 1.0.0</span>
                    </div>
                </div>
            </div>
        </aside>
        <!-- 主内容 - 可滚动 -->
        <main id="main-content" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50 md:ml-64 transition-all duration-300">
            <?php
                // 插件主体内容
                if (isset($Content)) {
                    echo $Content;
                } else {
                    echo "<div class='bg-white rounded-lg p-6 shadow-[0_4px_20px_rgba(0,0,0,0.08)] mb-6'>
                            <h1 class='text-2xl font-semibold mb-4'>控制台首页</h1>
                            <p class='text-gray-600 mb-6'>欢迎使用系统控制台，请从左侧菜单选择需要操作的功能。</p>
                            <div class='space-y-6'>";
                }
            ?>
        </main>
    </div>
    <!-- 个人资料弹窗 (默认隐藏) -->
    <div id="profileModal" class="fixed inset-0 z-50 flex items-center justify-center invisible opacity-0 transition-all duration-300">
        <!-- 背景遮罩 -->
        <div class="absolute inset-0 bg-black bg-opacity-50" id="profileModalBackdrop"></div>
        <!-- 弹窗内容 -->
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-300">
            <!-- 弹窗头部 -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">编辑个人资料</h3>
                    <button id="closeProfileModal" class="text-gray-500 hover:text-gray-700"> <i class="fas fa-times"></i> </button>
                </div>
            </div>
            <!-- 表单内容 -->
            <form id="profileForm" class="p-6">
                <!-- 隐藏的用户ID -->
                <input type="hidden" id="profileUserId" name="id" value="<?=htmlspecialchars($_SESSION['user_id'] ?? '')?>">
                <!-- 用户名 -->
                <div class="mb-4">
                    <label for="profileUsername" class="block text-sm font-medium text-gray-700 mb-1"> 用户名 </label>
                    <input type="text" id="profileUsername" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" value="<?=htmlspecialchars($_SESSION['username'] ?? '')?>">
                </div>
                <!-- 邮箱 -->
                <div class="mb-4">
                    <label for="profileEmail" class="block text-sm font-medium text-gray-700 mb-1"> 邮箱地址 </label>
                    <input type="email" id="profileEmail" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
                </div>
                <!-- 密码 (可选修改) -->
                <div class="mb-6">
                    <label for="profilePassword" class="block text-sm font-medium text-gray-700 mb-1"> 密码（不填则不修改） </label>
                    <input type="password" id="profilePassword" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="至少8位，包含字母和数字">
                    <p class="mt-1 text-xs text-gray-500">不修改密码请留空</p>
                </div>
                <!-- 角色（空容器，等待JS填充） -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"> 用户角色 </label>
                    <div id="userRoleDisplay" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700"></div>
                    <input type="hidden" name="role" id="userRoleInput">
                </div>
                <!-- 状态（空容器，等待JS填充） -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1"> 账号状态 </label>
                    <div id="userStatusDisplay" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg"></div>
                    <input type="hidden" name="status" id="userStatusInput">
                </div>
                <!-- 提交按钮 -->
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelProfileBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"> 取消 </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"> 保存修改 </button>
                </div>
            </form>
        </div>
    </div>
    <!-- 页脚 -->
    <footer class="bg-white border-t border-gray-200 py-4 md:ml-64 transition-all duration-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500"> &copy; <?=date('Y')?> JuheAdmin. 保留所有权利. </p>
                <div class="flex space-x-6 mt-2 md:mt-0">
                    <a href="#" class="text-sm text-gray-500 hover:text-primary transition-colors">使用帮助</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-primary transition-colors">隐私政策</a>
                    <a href="#" id="contactSupport" class="text-sm text-gray-500 hover:text-primary transition-colors">联系支持</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- 弹出层 -->
    <div id="supportPopup" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full mx-4 transform transition-all">
            <!-- 关闭按钮 -->
            <button id="closePopup" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"> <i class="fa fa-times"></i> </button>
            <!-- 内容 -->
            <div class="text-center">
                <h3 class="text-xl font-semibold mb-6">联系支持</h3>
                <!-- 二维码图片 -->
                <div class="flex justify-center mb-6">
                    <img src="https://admin.juhe.me/Storage/images/20250817194643_68a1c123c00c0.png" alt="支持二维码" class="w-48 h-48 object-cover rounded-lg border border-gray-100 p-2">
                </div>
                <!-- 提示文字 -->
                <p class="text-sm text-gray-500">
                    扫码添加微信，进群获取技术支持
                </p>
            </div>
        </div>
    </div>
    <!-- 移动端菜单遮罩层 -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>
    <script> const targetUsername = '<?= htmlspecialchars($_SESSION[' username '] ?? ' ') ?>'; </script>
    <script src="/Static/js/controller.js"> </script>
    <script src="/Static/js/admin.js"> </script>
</body>

</html>