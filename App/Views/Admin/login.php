<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - JuheAdmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 配置Tailwind自定义颜色 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-light': '#93c5fd',
                        'primary-dark': '#2563eb',
                    },
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <!-- 成功消息容器 -->
    <div id="successMessage" class="fixed top-4 right-4 px-6 py-3 rounded-lg shadow shadow-lg flex items-center z-50 transition-all duration-300 transform translate-x-full bg-primary-light border border-primary/20 text-primary-dark">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="successText"></span>
        <button onclick="hideMessages()" class="ml-4 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- 错误消息容器 -->
    <div id="errorMessage" class="fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg flex items-center z-50 transition-all duration-300 transform translate-x-full bg-red-50 border border-red-200 text-red-700">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span id="errorText"></span>
        <button onclick="hideMessages()" class="ml-4 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="w-full max-w-md">
        <div class="auth-card bg-white rounded-lg overflow-hidden w-full border border-gray-300 shadow-sm">
            <div class="text-center mt-8">
                <div class="flex flex-col items-center">
                    <a href="#" class="flex items-center text-primary font-bold text-3xl">
                        <img src="/Static/img/logo.png" class="w-10 mr-2" alt="JuheAdmin Logo">
                        <span>JuheAdmin</span>
                    </a>
                    <p class="text-gray-600 mt-4">登录您的管理系统</p>
                </div>
            </div>

            <div class="p-8">
                <?php if (!empty($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="block sm:inline"><?=htmlspecialchars($error)?></span>
                </div>
                <?php endif; ?>

                <form id="loginForm" method="post" action="?s=login" class="space-y-6">
                    <div>
                        <label for="username_or_email" class="block text-sm font-medium text-gray-700 mb-1">用户名或邮箱</label>
                        <input type="text" id="username_or_email" name="username_or_email" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                            placeholder="请输入用户名或邮箱">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">密码</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                                placeholder="请输入您的密码">
                            <button type="button" id="togglePassword" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors duration-300">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <a href="#" class="text-sm text-primary hover:text-primary-dark transition-colors duration-300">忘记密码?</a>
                    </div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300">
                        登录
                    </button>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">还没有账户?</span>
                        </div>
                    </div>
                    <button onclick="showRegisterModal()" 
                        class="mt-6 w-full flex justify-center py-2 px-4 border border-primary/30 rounded-md shadow-sm text-sm font-medium text-primary bg-white hover:bg-primary-light/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300">
                        注册新账户
                    </button>
                </div>
            </div>
        </div>

        <!-- 注册弹窗 -->
        <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg overflow-hidden w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">创建新账户</h3>
                    <button onclick="closeRegisterModal()" class="text-gray-400 hover:text-primary transition-colors duration-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form id="registerForm" method="post" action="?s=register" class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">用户名</label>
                            <input type="text" id="username" name="username" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                                placeholder="请输入用户名">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱地址</label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                                placeholder="请输入您的邮箱">
                        </div>
                        <div>
                            <label for="register-password" class="block text-sm font-medium text-gray-700 mb-1">密码</label>
                            <div class="relative">
                                <input type="password" id="register-password" name="password" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                                    placeholder="请输入密码 (至少8位)">
                                <button type="button" id="toggleRegisterPassword" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors duration-300">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">密码必须包含至少8个字符</p>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">确认密码</label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-300" 
                                    placeholder="请再次输入密码">
                                <button type="button" id="toggleConfirmPassword" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors duration-300">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input id="agree-terms" type="checkbox" name="agree_terms" required 
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded transition-colors duration-300">
                            <label for="agree-terms" class="ml-2 block text-sm text-gray-700">
                                我同意 <a href="#" class="text-primary hover:text-primary-dark transition-colors duration-300">服务条款</a> 和 <a href="#" class="text-primary hover:text-primary-dark transition-colors duration-300">隐私政策</a>
                            </label>
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300">
                            注册
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 显示消息提示
        function showMessage(text, type = 'success') {
            // 隐藏所有消息
            hideMessages();
            
            // 显示对应类型的消息
            const messageElement = document.getElementById(type + 'Message');
            const textElement = document.getElementById(type + 'Text');
            
            textElement.textContent = text;
            messageElement.classList.remove('translate-x-full');
            
            // 3秒后自动隐藏
            setTimeout(hideMessages, 3000);
        }
        
        // 隐藏所有消息
        function hideMessages() {
            document.getElementById('successMessage').classList.add('translate-x-full');
            document.getElementById('errorMessage').classList.add('translate-x-full');
        }

        // 密码可见性切换
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('far', 'fa-eye');
                icon.classList.add('far', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('far', 'fa-eye-slash');
                icon.classList.add('far', 'fa-eye');
            }
        });

        // 注册密码可见性切换
        document.getElementById('toggleRegisterPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('register-password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('far', 'fa-eye');
                icon.classList.add('far', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('far', 'fa-eye-slash');
                icon.classList.add('far', 'fa-eye');
            }
        });

        // 确认密码可见性切换
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('far', 'fa-eye');
                icon.classList.add('far', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('far', 'fa-eye-slash');
                icon.classList.add('far', 'fa-eye');
            }
        });

        // 显示注册弹窗
        function showRegisterModal() {
            const modal = document.getElementById('registerModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // 关闭注册弹窗
        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // 点击弹窗外部关闭
        document.getElementById('registerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRegisterModal();
            }
        });
        // 登录表单处理
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                username_or_email: document.getElementById('username_or_email').value,
                password: document.getElementById('password').value,
            };
            
            try {
                const response = await fetch('/admin/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                    credentials: 'include'
                });
                
                // 直接解析JSON响应（与后端JSON格式匹配）
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message);
                    // 使用后端返回的跳转地址
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                    }
                } else {
                    showMessage(result.message || '登录失败，请检查账号密码', 'error');
                }
            } catch (error) {
                console.error('登录请求失败:', error);
                showMessage('网络错误，登录失败', 'error');
            }
        });

        // 注册表单处理
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // 前端验证
            if (password !== confirmPassword) {
                showMessage('两次输入的密码不一致', 'error');
                return;
            }
            
            if (password.length < 8) {
                showMessage('密码长度不能少于8位', 'error');
                return;
            }
            
            if (!document.getElementById('agree-terms').checked) {
                showMessage('请同意服务条款和隐私政策', 'error');
                return;
            }
            
            const formData = {
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                password: password,
                confirm_password: confirmPassword
            };
            
            try {
                const response = await fetch('/admin/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                
                // 直接解析JSON响应（与后端JSON格式匹配）
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message);
                    closeRegisterModal();
                    this.reset();
                    // 使用后端返回的跳转地址
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                    }
                } else {
                    showMessage(result.message || '注册失败，请稍后再试', 'error');
                }
            } catch (error) {
                console.error('注册请求失败:', error);
                showMessage('网络错误，注册失败', 'error');
            }
        });

    </script>
</body>
</html>
    