<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
    <i class="fa fa-users text-primary mr-3"></i>
    用户管理中心
</h1>
<div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <!-- 搜索和操作区 -->
<div class="flex justify-between items-center mb-6 gap-4">
    <h2 class="text-xl font-semibold text-gray-700 whitespace-nowrap">用户列表</h2>
    
    <div class="flex gap-3">
        <!-- 新增用户按钮 -->
        <button id="openFormBtn" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center whitespace-nowrap">
            <i class="fa fa-plus mr-2"></i>
            <span>新增用户</span>
        </button>
    </div>
</div>
    
    <!-- 用户列表表格 -->
 <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-xl shadow-md overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户信息</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">角色</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">注册时间</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="userList">
    <?php if (!empty($users) && is_array($users)): ?>
        <?php foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50 transition-colors" data-id="<?php echo $user['id']; ?>">
                <!-- 用户名和头像单元格 - 自适应宽度 -->
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <img src="<?php 
                            if (!empty($user['avatar'])) {
                                echo htmlspecialchars($user['avatar']);
                            } else {
                                // 管理员与普通用户使用不同CDN头像
                                echo $user['role'] === 'admin' 
                                    ? "https://robohash.org/admin" . $user['id'] . "?size=40x40" 
                                    : "https://robohash.org/user" . $user['id'] . "?size=40x40";
                            }
                        ?>" 
                        alt="用户头像" class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>
                </td>
                
                <!-- 角色单元格 - 自适应宽度 -->
                <td class="px-4 py-4 whitespace-nowrap">
                    <?php 
                    $roleClass = match($user['role']) {
                        'admin' => 'bg-red-100 text-red-800',
                        default => 'bg-blue-100 text-blue-800'
                    };
                    $roleText = match($user['role']) {
                        'admin' => '管理员',
                        default => '员工'
                    };
                    ?>
                    <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $roleClass; ?>">
                        <?php echo $roleText; ?>
                    </span>
                </td>
                
                <!-- 创建时间 - 自适应宽度 -->
                <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                    <div class="text-sm text-gray-500"><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></div>
                </td>
                
                <!-- 状态 - 自适应宽度 -->
                <td class="px-4 py-4 whitespace-nowrap">
                    <?php 
                    $statusClass = $user['status'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    $statusText = $user['status'] ? '启用' : '停用';
                    ?>
                    <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                    </span>
                </td>
                
                <!-- 操作按钮 - 自适应宽度 -->
                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <button class="view-btn text-gray-500 hover:text-purple-500" 
                                data-id="<?php echo $user['id']; ?>" title="查看详情">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="edit-btn text-gray-500 hover:text-blue-500" 
                                data-id="<?php echo $user['id']; ?>" title="编辑">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="delete-btn text-gray-500 hover:text-red-500"
                                data-id="<?php echo $user['id']; ?>" title="删除">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <i class="fa fa-users text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">没有找到用户</h3>
                    <p class="mt-1 text-gray-500">尝试调整筛选条件或添加新用户</p>
                    <button class="mt-4 bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center"
                            onclick="openFormModal()">
                        <i class="fa fa-plus mr-2"></i>
                        <span>添加新用户</span>
                    </button>
                </div>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
</div>
    
    
    <!-- 分页控件 -->
    <div class="flex justify-between items-center mt-6">
        <p class="text-sm text-gray-500">显示 1 至 <?php echo min(10, count($users ?? [])); ?> 条，共 <?php echo count($users ?? []); ?> 条</p>

    </div>
</div>

<!-- 用户表单弹窗背景 -->
<div id="formBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>

<!-- 用户表单弹窗 -->
<div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 invisible pointer-events-events-none pointer-none transition transition-all duration-300 scale-95">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
            <h3 id="formTitle" class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fa fa-plus-circle text-primary mr-2"></i>
                创建新用户
            </h3>
            <button id="closeFormBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-130px)]">
            <form id="userForm" class="space-y-5">
                <input type="hidden" id="userId" name="id">
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">用户名 <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="请输入用户名">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱 <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="请输入邮箱地址">
                </div>
                
                <!-- 用户表单表单弹窗中密码字段部分修改 -->
                <div id="passwordField">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        密码 <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="请输入密码">
                    <p class="mt-1 text-xs text-gray-500">密码长度至少8位，包含字母和数字</p>
                </div>
                    
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">用户角色 <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
                        <option value="user">普通用户</option>
                        <option value="admin">管理员</option>
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="status" name="status" value="1" checked
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">启用用户</span>
                    </label>
                </div>
            </form>
        </div>
        
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
            <button id="cancelBtn" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                取消
            </button>
            <button id="submitBtn" type="button"
                    class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200">
                保存用户
            </button>
        </div>
    </div>
</div>

<!-- 用户详情弹窗 -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 invisible pointer-events-none transition-all duration-300 scale-95">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fa fa-user-circle text-primary mr-2"></i>
                用户详情
            </h3>
            <button id="closeDetailBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-100px)]">
            <div class="flex flex-col items-center mb-6">
                <img id="detailAvatar" src="https://picsum.photos/seed/user/100/100" alt="用户头像" class="w-24 h-24 rounded-full mb-4">
                <h4 id="detailUsername" class="text-xl font-bold text-gray-800">用户名</h4>
                <p id="detailRole" class="mt-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">普通用户</p>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-4 items-center">
                    <span class="text-sm text-gray-500">ID</span>
                    <span id="detailId" class="col-span-2 text-gray-800">--</span>
                </div>
                <div class="w-full h-px bg-gray-100"></div>
                
                <div class="grid grid-cols-3 gap-4 items-center">
                    <span class="text-sm text-gray-500">邮箱</span>
                    <span id="detailEmail" class="col-span-2 text-gray-800">--</span>
                </div>
                <div class="w-full h-px bg-gray-100"></div>
                
                <div class="grid grid-cols-3 gap-4 items-center">
                    <span class="text-sm text-gray-500">状态</span>
                    <span id="detailStatus" class="col-span-2">
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">启用</span>
                    </span>
                </div>
                <div class="w-full h-px bg-gray-100"></div>
                
                <div class="grid grid-cols-3 gap-4 items-center">
                    <span class="text-sm text-gray-500">创建时间</span>
                    <span id="detailCreatedAt" class="col-span-2 text-gray-800">--</span>
                </div>
                <div class="w-full h-px bg-gray-100"></div>
                
                <div class="grid grid-cols-3 gap-4 items-center">
                    <span class="text-sm text-gray-500">最后登录</span>
                    <span id="detailLastLogin" class="col-span-2 text-gray-800">--</span>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
            <button id="closeDetailBtn2" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                关闭
            </button>
        </div>
    </div>
</div>



<script>
console.log('用户管理JS加载完成');
document.addEventListener('DOMContentLoaded', function() {
    // 缓存DOM元素
    const formModal = document.getElementById('formModal');
    const formBackdrop = document.getElementById('formBackdrop');
    const detailModal = document.getElementById('detailModal');
    const openFormBtn = document.getElementById('openFormBtn');
    const closeFormBtn = document.getElementById('closeFormBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const submitBtn = document.getElementById('submitBtn');
    const formTitle = document.getElementById('formTitle');
    const userForm = document.getElementById('userForm');
    const userList = document.getElementById('userList');
    const passwordField = document.getElementById('passwordField');
    const closeDetailBtn = document.getElementById('closeDetailBtn');
    const closeDetailBtn2 = document.getElementById('closeDetailBtn2');
    const searchInput = document.getElementById('searchInput');

    // 检查元素是否存在
    function checkElements() {
        const elements = [
            formModal, formBackdrop, openFormBtn, 
            closeFormBtn, cancelBtn, submitBtn
        ];
        
        const missing = elements.filter(el => !el);
        if (missing.length > 0) {
            console.error('缺少必要的DOM元素，功能无法正常工作');
            return false;
        }
        return true;
    }

    // 显示表单弹窗
    function openFormModal() {
        if (!checkElements()) return;
        
        resetForm();
        formModal.classList.remove('invisible', 'pointer-events-none', 'scale-95');
        formModal.classList.add('scale-100');
        formBackdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
        void formModal.offsetWidth; // 强制重绘
    }

    // 隐藏表单弹窗
    function closeFormModal() {
        if (!checkElements()) return;
        
        formModal.classList.add('invisible', 'pointer-events-none', 'scale-95');
        formModal.classList.remove('scale-100');
        formBackdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }

    // 显示详情弹窗
    function openDetailModal() {
        detailModal.classList.remove('invisible', 'pointer-events-none', 'scale-95');
        detailModal.classList.add('scale-100');
        formBackdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
        void detailModal.offsetWidth;
    }

    // 隐藏详情弹窗
    function closeDetailModal() {
        detailModal.classList.add('invisible', 'pointer-events-none', 'scale-95');
        detailModal.classList.remove('scale-100');
        formBackdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }

    // 重置表单（新增模式）
    function resetForm() {
        userForm.reset();
        document.getElementById('userId').value = '';
        formTitle.innerHTML = '<i class="fa fa-plus-circle text-primary mr-2"></i> 创建新用户';
        
        // 新增模式：密码必填设置
        const passwordLabel = document.querySelector('#passwordField label');
        const passwordInput = document.getElementById('password');
        passwordLabel.innerHTML = '密码 <span class="text-red-500">*</span>';
        passwordInput.required = true;
        passwordInput.placeholder = '请输入密码';
        passwordField.style.display = 'block';
        
        submitBtn.innerHTML = '保存用户';
        submitBtn.disabled = false;
    }



    // 加载用户数据（编辑模式）
    async function loadUserData(id) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 加载中...';

        try {
            const response = await fetch(`/admin/users/${id}`);
            if (!response.ok) throw new Error('获取数据失败');
            const data = await response.json();

            if (data.success && data.data) {
                const { id, username, email, role, status } = data.data;
                document.getElementById('userId').value = id;
                document.getElementById('username').value = username || '';
                document.getElementById('email').value = email || '';
                document.getElementById('role').value = role || 'user';
                document.getElementById('status').checked = status == 1;
                formTitle.innerHTML = '<i class="fa fa-pencil text-primary mr-2"></i> 编辑用户';
                
                // 编辑模式：密码可选设置
                const passwordLabel = document.querySelector('#passwordField label');
                const passwordInput = document.getElementById('password');
                passwordLabel.innerHTML = '密码（不填则不修改）';
                passwordInput.required = false;
                passwordInput.placeholder = '不修改密码请留空';
                passwordField.style.display = 'block';
            } else {
                throw new Error(data.message || '获取数据失败');
            }
        } catch (e) {
            showMessage(e.message, 'error');
            closeFormModal();
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '保存用户';
        }
    }

    // 加载用户详情
    async function loadUserDetail(id) {
        try {
            const response = await fetch(`/admin/users/${id}`);
            if (!response.ok) throw new Error('获取详情失败');
            const data = await response.json();

            if (data.success && data.data) {
                const { id, username, email, role, status, created_at, last_login, avatar } = data.data;
                
                // 填充详情数据
                document.getElementById('detailId').textContent = id;
                document.getElementById('detailUsername').textContent = username || '未知用户';
                document.getElementById('detailEmail').textContent = email || '未设置';
                document.getElementById('detailCreatedAt').textContent = created_at ? new Date(created_at).toLocaleString() : '未知';
                document.getElementById('detailLastLogin').textContent = last_login ? new Date(last_login).toLocaleString() : '从未登录';
                document.getElementById('detailAvatar').src = avatar || `https://picsum.photos/seed/user${id}/100/100`;
                
                // 设置角色标签样式
                let roleClass = 'bg-green-100 text-green-800';
                let roleText = '员工';
                if (role === 'admin') {
                    roleClass = 'bg-red-100 text-red-800';
                    roleText = '管理员';
                }
                document.getElementById('detailRole').className = `mt-1 px-3 py-1 text-sm rounded-full ${roleClass}`;
                document.getElementById('detailRole').textContent = roleText;
                
                // 设置状态标签样式
                const statusClass = status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                const statusText = status ? '启用' : '禁用';
                document.getElementById('detailStatus').innerHTML = 
                    `<span class="inline-block px-2 py-1 text-xs rounded-full ${statusClass}">${statusText}</span>`;
                
                openDetailModal();
            } else {
                throw new Error(data.message || '获取详情失败');
            }
        } catch (e) {
            showMessage(e.message, 'error');
        }
    }

    // 表单验证
    function validateForm() {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const isEditMode = !!document.getElementById('userId').value;

        if (!username) {
            showMessage('请输入用户名', 'error');
            return false;
        }
        
        if (!email) {
            showMessage('请输入邮箱地址', 'error');
            return false;
        }
        
        // 验证邮箱格式
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showMessage('请输入有效的邮箱地址', 'error');
            return false;
        }
        
        // 仅在新增或编辑时填写了密码的情况下验证长度
        if ((!isEditMode || password) && password.length < 8) {
            showMessage('密码长度至少8位', 'error');
            return false;
        }
        
        return true;
    }

    // 提交表单（创建/更新）
    async function submitFormData() {
        if (!validateForm()) return;

        const formData = new FormData(userForm);
        const isEditMode = !!document.getElementById('userId').value;
        const statusCheckbox = document.getElementById('status');

        formData.delete('status'); // 先除可能存在的旧值
        formData.append('status', statusCheckbox.checked ? '1' : '0');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 保存中...';

        try {
            const response = await fetch('users/update', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();
            if (data.success) {
                showMessage(isEditMode ? '用户更新成功' : '用户创建成功');
                closeFormModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || (isEditMode ? '更新失败' : '创建失败'));
            }
        } catch (e) {
            showMessage(e.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '保存用户';
        }
    }

    // 删除用户
    async function deleteUser(id) {
        if (!confirm('确定要删除该用户吗？此操作不可恢复！')) return;

        try {
            const response = await fetch(`/admin/users/delete/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showMessage('用户已删除');
                // 移除DOM元素
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.remove();
                    // 检查是否还有数据行
                    const rows = userList.querySelectorAll('tr:not(:last-child)');
                    if (rows.length === 0) {
                        userList.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 border border-dashed border-gray-200">
                                    <div>
                                        <i class="fa fa-info-circle text-2xl mb-2 text-gray-300"></i>
                                        <p>暂无用户数据</p>
                                    </div>
                                </td>
                            </tr>`;
                    }
                }
            } else {
                throw new Error(data.message || '删除失败');
            }
        } catch (e) {
            showMessage(e.message, 'error');
        }
    }


    // 绑定事件
    if (checkElements()) {
        // 打开表单
        openFormBtn.addEventListener('click', openFormModal);
        
        // 关闭表单
        closeFormBtn.addEventListener('click', closeFormModal);
        cancelBtn.addEventListener('click', closeFormModal);
        formBackdrop.addEventListener('click', () => {
            if (!formModal.classList.contains('invisible')) closeFormModal();
            if (!detailModal.classList.contains('invisible')) closeDetailModal();
        });
        
        // 关闭详情
        closeDetailBtn.addEventListener('click', closeDetailModal);
        closeDetailBtn2.addEventListener('click', closeDetailModal);
        
        // 提交表单
        submitBtn.addEventListener('click', submitFormData);
        

        
        // 编辑、删除、查看事件委托
        userList.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');
            const viewBtn = e.target.closest('.view-btn');
            
            if (editBtn) {
                const id = editBtn.getAttribute('data-id');
                if (id) {
                    openFormModal();
                    setTimeout(() => loadUserData(id), 300);
                }
            } else if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');
                if (id) deleteUser(id);
            } else if (viewBtn) {
                const id = viewBtn.getAttribute('data-id');
                if (id) loadUserDetail(id);
            }
        });

        // ESC键关闭弹窗
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (!formModal.classList.contains('invisible')) closeFormModal();
                if (!detailModal.classList.contains('invisible')) closeDetailModal();
            }
        });

        // 阻止表单默认提交
        userForm.addEventListener('submit', e => {
            e.preventDefault();
            submitFormData();
        });
    }
});
</script>