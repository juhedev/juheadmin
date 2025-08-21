<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加入我们的微信群</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#10b981',
                        neutral: '#f3f4f6'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .card-shadow {
                box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1), 0 8px 10px -6px rgba(59, 130, 246, 0.05);
            }
            .animate-float {
                animation: float 3s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">


    <main class="container mx-auto px-4 py-8 md:py-16">
        <!-- 加载状态 -->
        <div id="loadingContainer" class="max-w-4xl mx-auto py-16 text-center">
            <i class="fa fa-spinner fa-spin text-primary text-3xl mb-4"></i>
            <p class="text-gray-600">加载中，请稍候...</p>
        </div>

        <!-- 错误状态 -->
        <div id="errorContainer" class="max-w-4xl mx-auto py-16 text-center hidden">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 mb-4">
                <i class="fa fa-exclamation-triangle text-2xl text-red-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">加载失败</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-4" id="errorMessage">无法加载群组数据，请稍后重试</p>
            <button id="retryBtn" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg shadow hover:shadow-md transition-all duration-200">
                重试
            </button>
        </div>

        <!-- 内容区域 (默认隐藏) -->
        <div id="contentContainer" class="hidden">
            <!-- 页面标题 -->
            <div class="text-center mb-12">
                <h1 class="text-[clamp(1.8rem,5vw,3rem)] font-bold text-gray-800 mb-4">加入我们的微信群</h1>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">扫码加入感兴趣的群组，与志同道合的朋友交流互动</p>
            </div>

            <!-- 活码展示区 -->
            <div class="max-w-4xl mx-auto">
                <!-- 主要活码卡片 -->
                <div id="mainQrcodeCard" class="bg-white rounded-2xl p-6 md:p-8 card-shadow mb-10 transform transition-all duration-300 hover:scale-[1.01]">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <!-- 二维码区域 -->
                        <div class="w-full md:w-1/3 flex justify-center">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-md animate-float">
                                <div id="qrcodeContainer" class="w-56 h-56 mx-auto">
                                    <!-- 二维码将通过JS动态生成 -->
                                </div>
                                <p class="text-center mt-3 text-sm text-gray-500">扫码加入群聊</p>
                            </div>
                        </div>
                        
                        <!-- 群信息区域 -->
                        <div class="w-full md:w-2/3">
                            <div class="flex items-center mb-4">
                                <span id="groupStatusBadge" class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mr-3">
                                    <i class="fa fa-check-circle mr-1"></i> 活跃中
                                </span>
                                <span class="text-gray-500 text-sm"><i class="fa fa-eye mr-1"></i> 已被查看 <span id="viewCount">1,234</span> 次</span>
                            </div>
                            
                            <h2 id="groupName" class="text-2xl font-bold text-gray-800 mb-3">技术交流微信群</h2>
                            
                            <p id="groupDescription" class="text-gray-600 mb-6">
                                这是一个技术爱好者交流群，欢迎大家分享编程经验、解决技术难题，一起学习进步。群内禁止广告和无关话题。
                            </p>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                <div class="bg-neutral rounded-lg p-3 text-center">
                                    <p class="text-gray-500 text-sm">群人数</p>
                                    <p id="memberCount" class="font-semibold text-gray-800">186人</p>
                                </div>
                                <div class="bg-neutral rounded-lg p-3 text-center">
                                    <p class="text-gray-500 text-sm">创建时间</p>
                                    <p id="createTime" class="font-semibold text-gray-800">2023-05-12</p>
                                </div>
                                <div class="bg-neutral rounded-lg p-3 text-center">
                                    <p class="text-gray-500 text-sm">最大人数</p>
                                    <p id="maxMembers" class="font-semibold text-gray-800">200人</p>
                                </div>
                                <div class="bg-neutral rounded-lg p-3 text-center">
                                    <p class="text-gray-500 text-sm">今日新增</p>
                                    <p id="todayNew" class="font-semibold text-gray-800">8人</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap hidden gap-3">
                                <button id="refreshQrcodeBtn" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center">
                                    <i class="fa fa-refresh mr-2"></i> 刷新二维码
                                </button>
                                <button id="shareBtn" class="px-5 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center">
                                    <i class="fa fa-share-alt mr-2"></i> 分享
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 群规则说明 -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-10">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fa fa-info-circle text-primary mr-2"></i> 入群须知
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <i class="fa fa-check-circle text-secondary mt-1 mr-2"></i>
                            <span>请遵守群规，文明交流，友善互动</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fa fa-check-circle text-secondary mt-1 mr-2"></i>
                            <span>禁止发布广告、色情、暴力等违规内容</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fa fa-check-circle text-secondary mt-1 mr-2"></i>
                            <span>本群二维码有效期为7天，过期请重新获取</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fa fa-check-circle text-secondary mt-1 mr-2"></i>
                            <span>群满200人后将自动切换至新群，请重新扫码</span>
                        </li>
                    </ul>
                </div>
                
                <!-- 推荐群组 -->
                <div class="mb-10 hidden">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fa fa-th-large text-primary mr-2"></i> 推荐群组
                    </h3>
                    
                    <div id="recommendedGroups" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- 推荐群将通过JS动态生成 -->
                    </div>
                </div>
            </div>
        </div>
    </main>



    <!-- 分享弹窗 -->
    <div id="shareModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 transform transition-transform duration-300 scale-95">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-800">分享群二维码</h3>
                <button id="closeShareModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <p class="text-gray-600 mb-2">通过以下方式分享</p>
                    <div class="flex justify-center space-x-6">
                        <a href="#" class="flex flex-col items-center text-gray-600 hover:text-green-500 transition-colors">
                            <i class="fa fa-weixin text-2xl mb-1"></i>
                            <span class="text-sm">微信</span>
                        </a>
                        <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-500 transition-colors">
                            <i class="fa fa-qq text-2xl mb-1"></i>
                            <span class="text-sm">QQ</span>
                        </a>
                        <a href="#" class="flex flex-col items-center text-gray-600 hover:text-red-500 transition-colors">
                            <i class="fa fa-weibo text-2xl mb-1"></i>
                            <span class="text-sm">微博</span>
                        </a>
                        <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fa fa-link text-2xl mb-1"></i>
                            <span class="text-sm">复制链接</span>
                        </a>
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-gray-600 text-sm">
                        <i class="fa fa-info-circle text-primary mr-1"></i>
                        分享后，好友可以通过您分享的链接加入相同的群组
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        // 全局变量存储群组数据
        let groupData = null;
        // API基础地址 - 请根据实际情况修改
        const API_BASE_URL = '';
        
        // 页面加载完成后执行
        document.addEventListener('DOMContentLoaded', function() {
            // 从URL获取活码编码（假设URL格式为 ...?code=XXX）
            const urlParams = new URLSearchParams(window.location.search);
            const code = "<?= $code ?>";
            
            if (!code) {
                showError('未找到活码编码，请检查链接是否正确');
                return;
            }
            
            // 加载群组数据
            loadGroupData(code);
            
            // 绑定按钮事件
            document.getElementById('refreshQrcodeBtn').addEventListener('click', refreshQrcode);
            document.getElementById('shareBtn').addEventListener('click', openShareModal);
            document.getElementById('closeShareModal').addEventListener('click', closeShareModal);
            document.getElementById('retryBtn').addEventListener('click', () => loadGroupData(code));
            
            // 点击分享弹窗外部关闭
            document.getElementById('shareModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeShareModal();
                }
            });
            
        });
        
        // 从API加载群组数据
        function loadGroupData(code) {
            showLoading();
            
            // 构建API请求URL
            const apiUrl = `${API_BASE_URL}/wxgcode/get/${code}`;
            
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP错误，状态码: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        // 保存数据
                        groupData = data.data;
                        // 渲染页面
                        renderPage();
                        // 显示内容
                        showContent();
                    } else {
                        showError(data.message || '获取群组数据失败');
                    }
                })
                .catch(error => {
                    console.error('加载群组数据失败:', error);
                    showError('网络请求失败，请稍后重试');
                });
        }
        
        // 渲染页面内容
        function renderPage() {
            if (!groupData) return;
            
            // 更新页面标题
            document.title = `${groupData.name || '微信群'} - 加入我们的微信群`;
            
            // 填充群组信息
            document.getElementById('groupName').textContent = groupData.name || '微信群';
            document.getElementById('groupDescription').textContent = groupData.description || '暂无群组描述';
            document.getElementById('viewCount').textContent = formatNumber(groupData.total_views || 0);
            document.getElementById('memberCount').textContent = groupData.current_members ? `${groupData.current_members}人` : '未知';
            document.getElementById('createTime').textContent = groupData.created_at || '未知时间';
            document.getElementById('maxMembers').textContent = groupData.max_members ? `${groupData.max_members}人` : '无限制';
            document.getElementById('todayNew').textContent = groupData.today_new || '0人';
            
            // 更新状态标签
            const statusBadge = document.getElementById('groupStatusBadge');
            if (groupData.status === 0) {
                statusBadge.className = 'px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium mr-3';
                statusBadge.innerHTML = '<i class="fa fa-times-circle mr-1"></i> 已禁用';
            } else if (groupData.is_full === 1) {
                statusBadge.className = 'px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium mr-3';
                statusBadge.innerHTML = '<i class="fa fa-exclamation-circle mr-1"></i> 已满员';
            }
            
            // 生成二维码
            generateQrcode(groupData.qrcode_url);
            
            // 渲染推荐群组
            renderRecommendedGroups(groupData.recommended || []);
        }
        
        // 生成二维码
        function generateQrcode(qrcodeUrl) {
            const container = document.getElementById('qrcodeContainer');
            container.innerHTML = '';
            
            if (qrcodeUrl) {
                // 如果有提供二维码URL，直接使用
                const img = document.createElement('img');
                img.src = qrcodeUrl;
                img.alt = `${groupData.name || '微信群'}的二维码`;
                img.className = 'w-full h-full object-contain';
                container.appendChild(img);
            } else {
                // 否则生成当前页面URL的二维码
                const url = window.location.href;
                
                QRCode.toCanvas(url, {
                    width: 220,
                    margin: 1,
                    color: {
                        dark: '#333333',
                        light: '#ffffff'
                    }
                }, function(error, canvas) {
                    if (error) {
                        console.error('生成二维码失败:', error);
                        container.innerHTML = '<p class="text-center text-red-500 py-10">生成二维码失败</p>';
                        return;
                    }
                    container.appendChild(canvas);
                });
            }
        }
        
        // 渲染推荐群组
        function renderRecommendedGroups(groups) {
            const container = document.getElementById('recommendedGroups');
            container.innerHTML = '';
            
            if (groups.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-6">暂无推荐群组</p>';
                return;
            }
            
            groups.forEach(group => {
                const groupCard = document.createElement('div');
                groupCard.className = 'bg-white rounded-xl overflow-hidden shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1';
                groupCard.innerHTML = `
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-semibold text-gray-800">${group.name || '未命名群组'}</h4>
                            <span class="px-2 py-0.5 bg-${getCategoryColor(group.category)}-100 text-${getCategoryColor(group.category)}-800 rounded-full text-xs">${group.category || '其他'}</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">${group.description || '暂无群组描述'}</p>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500"><i class="fa fa-users mr-1"></i> ${group.member_count || 0}人</span>
                            <a href="${group.url || '#'}" class="text-primary hover:text-primary/80 transition-colors">查看 <i class="fa fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                `;
                container.appendChild(groupCard);
            });
        }
        
        // 刷新二维码
        function refreshQrcode() {
            if (!groupData || !groupData.code) {
                showNotification('无法获取活码信息，刷新失败', 'error');
                return;
            }
            
            const btn = document.getElementById('refreshQrcodeBtn');
            const originalText = btn.innerHTML;
            
            // 显示加载状态
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 刷新中...';
            
            // 发送请求刷新二维码
            fetch(`${API_BASE_URL}/wxgcode/refresh/${groupData.code}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.qrcode_url) {
                        // 更新本地数据
                        groupData.qrcode_url = data.data.qrcode_url;
                        // 更新二维码
                        generateQrcode(data.data.qrcode_url);
                        // 显示成功提示
                        showNotification('二维码已刷新', 'success');
                    } else {
                        // 显示错误信息
                        showNotification(data.message || '刷新二维码失败', 'error');
                    }
                })
                .catch(error => {
                    console.error('刷新二维码错误:', error);
                    showNotification('网络错误，刷新失败', 'error');
                })
                .finally(() => {
                    // 恢复按钮状态
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }
        
        // 打开分享弹窗
        function openShareModal() {
            const modal = document.getElementById('shareModal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
            document.body.style.overflow = 'hidden';
        }
        
        // 关闭分享弹窗
        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            modal.classList.add('opacity-0', 'pointer-events-none');
            modal.querySelector('div').classList.remove('scale-100');
            modal.querySelector('div').classList.add('scale-95');
            document.body.style.overflow = '';
        }
        
        // 显示通知消息
        function showNotification(message, type = 'info') {
            // 创建通知元素
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
            
            // 设置通知类型样式
            if (type === 'success') {
                notification.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
                notification.innerHTML = `<i class="fa fa-check-circle mr-2"></i>${message}`;
            } else if (type === 'error') {
                notification.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
                notification.innerHTML = `<i class="fa fa-exclamation-circle mr-2"></i>${message}`;
            } else {
                notification.classList.add('bg-blue-50', 'text-blue-800', 'border', 'border-blue-200');
                notification.innerHTML = `<i class="fa fa-info-circle mr-2"></i>${message}`;
            }
            
            // 添加到页面
            document.body.appendChild(notification);
            
            // 显示通知
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // 3秒后隐藏通知
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // 显示加载状态
        function showLoading() {
            document.getElementById('loadingContainer').classList.remove('hidden');
            document.getElementById('contentContainer').classList.add('hidden');
            document.getElementById('errorContainer').classList.add('hidden');
        }
        
        // 显示内容
        function showContent() {
            document.getElementById('loadingContainer').classList.add('hidden');
            document.getElementById('contentContainer').classList.remove('hidden');
            document.getElementById('errorContainer').classList.add('hidden');
        }
        
        // 显示错误状态
        function showError(message) {
            document.getElementById('loadingContainer').classList.add('hidden');
            document.getElementById('contentContainer').classList.add('hidden');
            document.getElementById('errorContainer').classList.remove('hidden');
            document.getElementById('errorMessage').textContent = message;
        }
        
        // 格式化数字（添加千位分隔符）
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // 根据分类获取颜色
        function getCategoryColor(category) {
            const colorMap = {
                '技术': 'blue',
                '产品': 'purple',
                '商业': 'amber',
                '设计': 'pink',
                '教育': 'green',
                '生活': 'teal'
            };
            return colorMap[category] || 'gray';
        }
    </script>
</body>
</html>
    