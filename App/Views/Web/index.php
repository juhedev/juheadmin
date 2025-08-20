<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JuheAdmin - 轻量级PHP后台管理框架 | 插件化架构系统</title>
    <meta name="description" content="JuheAdmin是一款基于PHP的轻量级后台管理框架，采用插件化架构，支持快速搭建各类Web管理系统，零依赖安装，简单易用。">
    <meta name="keywords" content="JuheAdmin, PHP后台框架, 插件化管理系统, 轻量级CMS, 后台管理系统, 开源PHP框架">
    <meta name="author" content="JuheAdmin开发团队">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="7 days">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 配置 Tailwind 自定义颜色和字体 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-light': '#93c5fd',
                        'primary-dark': '#2563eb',
                        success: '#10b981',
                        'success-light': '#a7f3d0',
                        warning: '#f59e0b',
                        'warning-light': '#fef3c7',
                        danger: '#ef4444',
                        'danger-light': '#fecaca',
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"'],
                    },
                }
            }
        }
    </script>
    
    <!-- 自定义工具类 -->
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .text-shadow-sm {
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans leading-relaxed">
    <!-- 头部导航 -->
    <header class="bg-white shadow-sm sticky top-0 z-50 transition-all duration-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="#" class="flex items-center text-primary font-bold text-xl">
                    <img src="/Static/img/logo.png" class="w-10 mr-2" alt="JuheAdmin Logo"/>
                    <span>JuheAdmin</span>
                </a>
                
                <nav class="hidden md:block">
                    <ul class="flex space-x-8">
                        <li><a href="#features" class="text-gray-600 hover:text-primary font-medium transition-colors">特性</a></li>
                        <li><a href="#architecture" class="text-gray-600 hover:text-primary font-medium transition-colors">架构</a></li>
                        <li><a href="#installation" class="text-gray-600 hover:text-primary font-medium transition-colors">安装</a></li>
                        <li><a href="#use-cases" class="text-gray-600 hover:text-primary font-medium transition-colors">使用场景</a></li>
                        <li><a href="#contribute" class="text-gray-600 hover:text-primary font-medium transition-colors">贡献</a></li>
                    </ul>
                </nav>
                
                <div>
                    <a href="https://github.com" target="_blank" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fab fa-github mr-2"></i>GitHub
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- 主要内容 -->
    <main>
        <!-- 英雄区域 -->
        <section class="py-16 md:py-24 bg-gradient-to-br from-gray-50 to-primary-light text-center">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-6 text-gray-900">聚合一切，自由扩展</h1>
                <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto mb-8">
                    JuheAdmin 是一个基于 PHP 的轻量级后台管理框架，采用插件化架构，像积木一样自由组合后台功能，适合快速搭建各类 Web 管理系统。
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#installation" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>开始使用
                    </a>
                    <a href="#features" class="bg-transparent border border-primary text-primary hover:bg-primary-light px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-info-circle mr-2"></i>了解更多
                    </a>
                </div>
            </div>
        </section>

        <!-- 特性介绍 -->
        <section id="features" class="py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">框架特性</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        JuheAdmin 设计理念是简洁、灵活、高效，让开发者能够专注于业务逻辑而非重复劳动
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-primary-light text-primary-dark flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">零依赖安装</h3>
                        <p class="text-gray-600">
                            上传代码后访问 /install，填写数据库信息并创建管理员账号即可使用，无需 Composer 依赖。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-success-light text-success flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">插件化架构</h3>
                        <p class="text-gray-600">
                            所有功能都以插件形式提供，可自由安装、删除、修改，按需扩展系统功能，保持核心精简。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-warning-light text-warning flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">无残留机制</h3>
                        <p class="text-gray-600">
                            插件卸载时会自动清理相关数据库表和代码，不留垃圾数据，保持系统整洁。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-primary-light text-primary-dark flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">现代化 UI</h3>
                        <p class="text-gray-600">
                            内置 TailwindCSS 和 Font Awesome，界面美观且响应式，适配各种设备屏幕。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-success-light text-success flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">轻量数据库层</h3>
                        <p class="text-gray-600">
                            基于 Medoo 封装，API 简洁易用，支持 MySQL、PostgreSQL、SQLite 等多种数据库。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-warning-light text-warning flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">多场景支持</h3>
                        <p class="text-gray-600">
                            适用于企业官网后台、博客系统、外贸站、小程序 API、物联网后台等多种场景。
                        </p>
                    </div>
                </div>
            </div>
        </section>

<!-- 系统截图展示 -->
<section id="screenshots" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4">系统截图</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                浏览JuheAdmin的界面展示，直观了解系统功能与操作体验
            </p>
        </div>

        <!-- 图片网格 - 6张固定图片 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- 截图1 -->
            <div class="screenshot-item group">
                <div class="bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300 hover:shadow-xl">
                    <div class="overflow-hidden">
                        <img src="https://picsum.photos/id/0/600/400" alt="JuheAdmin后台首页仪表盘" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold">后台首页仪表盘</h3>
                        <p class="text-sm text-gray-600 mt-1">系统概览与核心数据统计</p>
                    </div>
                </div>
            </div>
            
            <!-- 截图2 -->
            <div class="screenshot-item group">
                <div class="bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300 hover:shadow-xl">
                    <div class="overflow-hidden">
                        <img src="https://picsum.photos/id/180/600/400" alt="JuheAdmin插件管理中心" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold">插件管理中心</h3>
                        <p class="text-sm text-gray-600 mt-1">插件安装、启用与更新管理</p>
                    </div>
                </div>
            </div>
            
            <!-- 截图3 -->
            <div class="screenshot-item group">
                <div class="bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300 hover:shadow-xl">
                    <div class="overflow-hidden">
                        <img src="https://picsum.photos/id/160/600/400" alt="JuheAdmin用户管理界面" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold">用户与权限管理</h3>
                        <p class="text-sm text-gray-600 mt-1">用户列表与角色权限分配</p>
                    </div>
                </div>
            </div>
            
            <!-- 截图4 -->
            <div class="screenshot-item group">
                <div class="bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300 hover:shadow-xl">
                    <div class="overflow-hidden">
                        <img src="https://picsum.photos/id/48/600/400" alt="JuheAdmin系统设置界面" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold">系统设置面板</h3>
                        <p class="text-sm text-gray-600 mt-1">站点配置与系统参数设置</p>
                    </div>
                </div>
            </div>
            

        </div>

        <!-- 界面特点 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-smooth">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa fa-desktop text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">直观界面</h3>
                <p class="text-gray-600">简洁清晰的操作界面，降低学习成本，提升工作效率</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-smooth">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa fa-mobile text-secondary text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">响应式设计</h3>
                <p class="text-gray-600">完美适配桌面与移动设备，随时随地管理系统</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-smooth">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa fa-paint-brush text-purple-500 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">主题定制</h3>
                <p class="text-gray-600">支持多种主题切换，可根据个人喜好定制界面风格</p>
            </div>
        </div>
    </div>
</section>
    

        <!-- 安装指南 -->
        <section id="installation" class="py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">快速安装</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        简单几步，即可完成安装并开始使用 JuheAdmin
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">1</div>
                        <h3 class="text-xl font-semibold mb-3">下载源码</h3>
                        <p class="text-gray-600">
                            从 GitHub 下载最新版本的源码包，或通过 Git 克隆仓库。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">2</div>
                        <h3 class="text-xl font-semibold mb-3">上传服务器</h3>
                        <p class="text-gray-600">
                            将代码上传到你的 Web 服务器目录，确保目录有可写权限。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">3</div>
                        <h3 class="text-xl font-semibold mb-3">环境要求</h3>
                        <p class="text-gray-600">
                            确保 PHP 版本 >= 8.0，开启 PDO fileinfo 扩展，MySQL 数据库正常运行。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">4</div>
                        <h3 class="text-xl font-semibold mb-3">运行安装</h3>
                        <p class="text-gray-600">
                            访问 http://your-domain.com/install，按照提示完成安装。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">5</div>
                        <h3 class="text-xl font-semibold mb-3">登录使用</h3>
                        <p class="text-gray-600">
                            安装完成后，使用创建的管理员账号登录系统，开始使用。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md relative">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-6">6</div>
                        <h3 class="text-xl font-semibold mb-3">安装插件</h3>
                        <p class="text-gray-600">
                            在后台插件管理中，安装所需插件，扩展系统功能。
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 使用场景 -->
        <section id="use-cases" class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">适用场景</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        JuheAdmin 灵活的架构设计使其能够满足多种不同的业务需求
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">企业官网后台</h3>
                        <p class="text-gray-600">
                            管理产品、新闻、联系方式等企业信息
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">外贸站管理</h3>
                        <p class="text-gray-600">
                            管理订单、客户、产品目录和物流信息
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-blog"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">博客 / CMS</h3>
                        <p class="text-gray-600">
                            发布文章、管理评论、分类和标签
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">电商后台</h3>
                        <p class="text-gray-600">
                            管理商品、订单、库存和支付信息
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">小程序 API</h3>
                        <p class="text-gray-600">
                            作为小程序后端，提供数据接口和业务逻辑
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-md text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="text-primary text-4xl mb-4">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">物联网后台</h3>
                        <p class="text-gray-600">
                            管理设备、数据采集和远程控制
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 参与贡献 -->
        <section id="contribute" class="py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">参与贡献</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        欢迎加入 JuheAdmin 社区，一起完善这个开源项目
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-primary-light text-primary-dark flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-bug"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">提交 Issue</h3>
                        <p class="text-gray-600">
                            发现 Bug 或有功能建议？在 GitHub 上提交 Issue，帮助我们改进系统。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-success-light text-success flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-code-pull-request"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">提交 PR</h3>
                        <p class="text-gray-600">
                            修复问题或添加新功能，提交 Pull Request，直接参与代码贡献。
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="w-12 h-12 rounded-full bg-warning-light text-warning flex items-center justify-center text-xl mb-6">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">开发插件</h3>
                        <p class="text-gray-600">
                            开发实用插件并分享，丰富 JuheAdmin 生态，帮助更多用户。
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 行动召唤 -->
        <section class="bg-primary text-white rounded-2xl p-12 md:p-16 mx-4 my-16">
            <div class="container mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6">准备好开始使用 JuheAdmin 了吗？</h2>
                <p class="text-lg opacity-90 max-w-2xl mx-auto mb-8">
                    立即下载并体验这款灵活高效的后台管理框架，快速搭建你的专属管理系统
                </p>
                <a href="https://github.com" target="_blank" class="bg-white text-primary hover:bg-gray-100 px-6 py-3 rounded-lg font-semibold transition-colors inline-flex items-center">
                    <i class="fas fa-download mr-2"></i>下载最新版本
                </a>
            </div>
        </section>
    </main>

    <!-- 页脚 -->
    <footer class="bg-gray-900 text-gray-400 py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <h3 class="text-white text-xl font-semibold mb-6">JuheAdmin</h3>
                    <p class="mb-4">轻量级后台管理框架，聚合一切，自由扩展。</p>
                    <div class="flex space-x-4">
                        <a href="#" title="GitHub" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" title="Gitter" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fab fa-gitter"></i>
                        </a>
                        <a href="#" title="文档" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fas fa-book"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white text-xl font-semibold mb-6">快速链接</h3>
                    <ul class="space-y-3">
                        <li><a href="#features" class="hover:text-white transition-colors">特性</a></li>
                        <li><a href="#architecture" class="hover:text-white transition-colors">架构</a></li>
                        <li><a href="#installation" class="hover:text-white transition-colors">安装指南</a></li>
                        <li><a href="#use-cases" class="hover:text-white transition-colors">使用场景</a></li>
                        <li><a href="#contribute" class="hover:text-white transition-colors">参与贡献</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-xl font-semibold mb-6">资源</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-white transition-colors">开发文档</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">插件市场</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">示例代码</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API 参考</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">常见问题</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-xl font-semibold mb-6">法律</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-white transition-colors">MIT 许可证</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">隐私政策</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">使用条款</a></li>
                    </ul>
                </div>
            </div>

            <div class="text-center pt-8 border-t border-gray-800 text-sm">
                &copy; <script>document.write(new Date().getFullYear())</script> JuheAdmin 开源项目 | 聚合一切，自由扩展
            </div>
        </div>
    </footer>

    <script>
        // 平滑滚动
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // 导航栏滚动效果
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('shadow');
            } else {
                header.classList.remove('shadow');
            }
        });
    </script>
</body>
</html>
