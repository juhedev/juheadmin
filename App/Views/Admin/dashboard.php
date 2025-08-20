<!-- 页面标题 -->
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-dark">
                    欢迎使用JuheAdmin开源后台管理系统
                </h3>
                <p class="text-gray-500 mt-1">
                    <?=date('Y年m月d日')?> · 今天是星期<?=['日','一','二','三','四','五','六'][date('w')]?>
                </p>
            </div>

            <!-- 状态卡片 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- 项目版本 -->
                <div class="bg-white rounded-xl p-6 card-shadow hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm">当前版本</p>
                            <h3 class="text-2xl font-bold mt-1">v1.0.0</h3>
                            <p class="text-gray-500 text-sm mt-2">
                                <i class="fas fa-check-circle text-success mr-1"></i>
                                已是最新版本
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center">
                            <i class="fas fa-code-branch text-secondary text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- 活跃用户 -->
                <div class="bg-white rounded-xl p-6 card-shadow hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm">活跃用户</p>
                            <h3 class="text-2xl font-bold mt-1">2,500+</h3>
                            <p class="text-gray-500 text-sm mt-2">
                                <i class="fas fa-arrow-up text-success mr-1"></i>
                                较上月增长 12%
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- GitHub 星标 -->
                <div class="bg-white rounded-xl p-6 card-shadow hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm">GitHub 星标</p>
                            <h3 class="text-2xl font-bold mt-1">1.2k+</h3>
                            <p class="text-gray-500 text-sm mt-2">
                                <i class="fas fa-star text-warning mr-1"></i>
                                持续增长中
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center">
                            <i class="fab fa-github text-warning text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- 贡献者 -->
                <div class="bg-white rounded-xl p-6 card-shadow hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm">贡献者</p>
                            <h3 class="text-2xl font-bold mt-1">42</h3>
                            <p class="text-gray-500 text-sm mt-2">
                                <i class="fas fa-code-contract text-secondary mr-1"></i>
                                来自全球开发者
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                            <i class="fas fa-code text-success text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 核心功能介绍 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- 功能概述 -->
                <div class="bg-white rounded-xl p-6 card-shadow lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold">核心功能</h2>
                        <a href="https://github.com/yourusername/yourproject" target="_blank" class="text-xs text-primary hover:underline flex items-center">
                            <i class="fab fa-github mr-1"></i> 查看源码
                        </a>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- 功能1 -->
                        <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-image text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium">多场景支持</h3>
                                    <span class="text-xs bg-success/10 text-success px-2 py-0.5 rounded">核心功能</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">企业官网后台、博客系统、外贸站、营销推广站、工具站、小程序API、物联网等后台。</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">超级轻量</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">功能独立</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">安装简单</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 功能2 -->
                        <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-puzzle-piece text-indigo-500 text-xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium">插件扩展系统</h3>
                                    <span class="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">扩展功能</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">基于灵活的插件架构，支持功能模块化扩展。所有功能都以插件形式提供，可自由安装、删除、修改。</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">模块化</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">API支持</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">自由开发</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 功能3 -->
                        <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-green-500 text-xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium">安全权限管理</h3>
                                    <span class="text-xs bg-success/10 text-success px-2 py-0.5 rounded">核心功能</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">基于角色的访问控制，管理员随时更改角色。员工帐号不可操作核心设置。</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">多角色</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">便捷修改</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">安全可靠</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 功能4 -->
                        <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-purple-500 text-xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium">功能易于开发</h3>
                                    <span class="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">扩展功能</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">基于 <a href="https://medoo.in/" target="_blank" class="text-primary hover:underline">Medoo</a> 封装，API 简洁易用。只要会基本的代码即可修改定制。</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">可视化</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">自定义</span>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">导出</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 项目信息和特性 -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <h2 class="text-lg font-semibold mb-6">项目特性</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-success/10 flex items-center justify-center mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-success text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-sm">开源免费</h3>
                                <p class="text-xs text-gray-500 mt-0.5">基于MIT协议开源，商业和个人使用均免费</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-success/10 flex items-center justify-center mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-success text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-sm">轻量高效</h3>
                                <p class="text-xs text-gray-500 mt-0.5">无冗余依赖，性能优化，资源占用低</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-success/10 flex items-center justify-center mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-success text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-sm">易于部署</h3>
                                <p class="text-xs text-gray-500 mt-0.5">支持多种环境，一键安装，快速部署</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-success/10 flex items-center justify-center mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-success text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-sm">响应式设计</h3>
                                <p class="text-xs text-gray-500 mt-0.5">适配桌面和移动设备，一致的用户体验</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-success/10 flex items-center justify-center mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-success text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-sm">丰富文档</h3>
                                <p class="text-xs text-gray-500 mt-0.5">完善的开发和使用文档，降低使用门槛</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg mb-6">
                        <h3 class="text-sm font-medium mb-3">快速开始</h3>
                        <div class="space-y-3">
                            <a href="https://github.com/yourusername/yourproject/archive/refs/heads/main.zip" class="flex items-center justify-center p-2 text-sm bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                <i class="fas fa-download mr-2"></i> 下载源码
                            </a>
                            <a href="https://docs.yourproject.com/installation" target="_blank" class="flex items-center justify-center p-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-book mr-2"></i> 安装文档
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium mb-2">社区支持</h3>
                        <div class="flex space-x-3">
                            <a href="https://github.com/yourusername/yourproject/issues" target="_blank" class="text-gray-600 hover:text-primary transition-colors" title="提交Issue">
                                <i class="fab fa-github text-xl"></i>
                            </a>
                            <a href="https://discord.gg/yourinvite" target="_blank" class="text-gray-600 hover:text-primary transition-colors" title="Discord社区">
                                <i class="fab fa-discord text-xl"></i>
                            </a>
                            <a href="https://gitter.im/yourproject/community" target="_blank" class="text-gray-600 hover:text-primary transition-colors" title="Gitter聊天">
                                <i class="fab fa-gitter text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 技术栈和兼容性 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 技术栈 -->
                <div class="bg-white rounded-xl p-6 card-shadow lg:col-span-2">
                    <h2 class="text-lg font-semibold mb-6">技术栈</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 border border-gray-100 rounded-lg">
                            <h3 class="font-medium mb-3 flex items-center">
                                <i class="fas fa-code text-primary mr-2"></i> 后端技术
                            </h3>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">PHP</span>
                                    <span class="text-gray-400">8.0+</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Medoo</span>
                                    <span class="text-gray-400">1.7+</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">RESTful API</span>
                                    <span class="text-gray-400">标准实现</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">JWT</span>
                                    <span class="text-gray-400">身份验证</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="p-4 border border-gray-100 rounded-lg">
                            <h3 class="font-medium mb-3 flex items-center">
                                <i class="fas fa-paint-brush text-primary mr-2"></i> 前端技术
                            </h3>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">HTML5</span>
                                    <span class="text-gray-400">语义化</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Tailwind CSS</span>
                                    <span class="text-gray-400">3.0+</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Font Awesome</span>
                                    <span class="text-gray-400">6.0+</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">原生JavaScript</span>
                                    <span class="text-gray-400">无框架依赖</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 兼容性 -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <h2 class="text-lg font-semibold mb-6">兼容性</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-sm mb-2">支持数据库</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fas fa-database mr-1"></i> MySQL
                                </span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fas fa-database mr-1"></i> PostgreSQL
                                </span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fas fa-database mr-1"></i> SQLite
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-sm mb-2">支持浏览器</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fab fa-chrome mr-1"></i> Chrome
                                </span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fab fa-firefox-browser mr-1"></i> Firefox
                                </span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fab fa-edge mr-1"></i> Edge
                                </span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs flex items-center">
                                    <i class="fab fa-safari mr-1"></i> Safari
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-sm mb-2">服务器要求</h3>
                            <ul class="space-y-1 text-xs text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    Apache 2.4+ 或 Nginx 1.18+
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    PHP 8.0+ 并启用必要扩展
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    至少 128MB 内存
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    支持 HTTPS 协议
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
      


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 页面初始化操作
            console.log('Project features page loaded successfully');
            
            // 为功能卡片添加悬停动画效果
            const featureCards = document.querySelectorAll('.hover\\:border-primary\\/30');
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('transform', 'translate-y-[-5px]', 'shadow-md');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('transform', 'translate-y-[-5px]', 'shadow-md');
                });
            });
        });
    </script>
    