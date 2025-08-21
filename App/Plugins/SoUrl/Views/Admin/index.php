<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
    <i class="fa fa-chain text-primary mr-3"></i>
    短链管理中心
</h1>
<div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <!-- 消息提示框 -->
    <div id="message" class="mb-4 px-4 py-3 rounded-lg hidden"></div>
    <!-- 选项卡导航 -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px" id="tabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button id="list-tab" class="inline-block py-4 px-5 border-b-2 border-primary text-sm font-medium text-primary" onclick="switchTab('list')" aria-selected="true">
                    短链列表
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button id="settings-tab" class="inline-block py-4 px-5 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="switchTab('settings')" aria-selected="false">
                    系统设置
                </button>
            </li>
        </ul>
    </div>
    <!-- 短链列表内容 -->
    <div id="list-content" class="tab-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">短链列表</h2>
            <!-- 创建新短链按钮 -->
            <button id="openFormBtn" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center">
                <i class="fa fa-plus mr-2"></i>
                <span>新增短链</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-xl shadow-md overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名称</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">短码</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">跳转链接</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">访问</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="shortlinkList">
                    <!-- 内容将通过JavaScript动态生成 -->
                </tbody>
            </table>

            <div id="loading" class="hidden py-10 text-center"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>
            <div id="empty" class="hidden">
                <tr>
                    <td colspan="5" class="text-center py-12">没有找到短链</td>
                </tr>
            </div>

        </div>
        <div id="pagination" class="flex justify-between items-center mt-6 hidden">
            <div class="text-sm text-gray-500">
                显示 <span id="showingRange">0-0</span> 条，共 <span id="totalItems">0</span> 条
            </div>
            <div class="flex space-x-2">
                <button id="prevPage" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>上一页</button>
                <div id="pageNumbers" class="flex space-x-1"></div>
                <button id="nextPage" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>下一页</button>
            </div>
        </div>
    </div>
    <!-- 设置选项卡内容 -->
    <div id="settings-content" class="tab-content hidden">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">系统设置</h2>
        <form id="settingsForm" class="space-y-6">
            <input type="hidden" id="settingsId" name="id">
            <div class="bg-gray-50 p-5 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fa fa-link text-primary mr-2"></i>短链接设置
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_domain" class="block text-sm font-medium text-gray-700 mb-1">默认域名</label>
                        <input type="text" id="default_domain" name="domain" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="例如: https://t.cn  如果为空则为当前主域名">
                        <p class="mt-1 text-xs text-gray-500">域名需要解析到当前网站才可使用</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" id="resetSettingsBtn" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    重置
                </button>
                <button type="button" id="saveSettingsBtn" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200">
                    保存设置
                </button>
            </div>
        </form>
    </div>
</div>
<!-- 表单弹窗背景 -->
<div id="formBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>
<!-- 短链表单弹窗 -->
<div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 invisible pointer-events-none transition-all duration-300 scale-95">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
            <h3 id="formTitle" class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fa fa-plus-circle text-primary mr-2"></i>
                创建新短链
            </h3>
            <button id="closeFormBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-130px)]">
            <form id="shortlinkForm" class="space-y-5">
                <input type="hidden" id="shortlinkId" name="id">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">短链名称 <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="请输入短链名称">
                </div>
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">目标链接 <span class="text-red-500">*</span></label>
                    <!-- 移除 textarea 的 type 属性，因为它不适用 -->
                    <textarea id="url" name="url" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition transition-colors resize-none"></textarea>
                </div>
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">自定义短码（可选）</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                            /so/
                        </span>
                        <input type="text" id="code" name="code" class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="留空则自动生成">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">仅支持字母、数字和短横线，不超过20个字符</p>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">描述（可选）</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none" placeholder="请输入短链描述信息"></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <!-- 移除默认的checked属性，避免覆盖JS设置的状态 -->
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">启用状态</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
            <button id="cancelBtn" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                取消
            </button>
            <button id="submitBtn" type="button" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200">
                保存短链
            </button>
        </div>
    </div>
</div>
<script type="text/javascript">
// 分页相关功能
const PAGE_SIZE = 10;
let currentPage = 1,
    totalPages = 1;

// 分页DOM元素
const listEl = document.getElementById('shortlinkList');
const [paginationEl, prevBtn, nextBtn, pageNumbers] = ['pagination', 'prevPage', 'nextPage', 'pageNumbers'].map(id => document.getElementById(id));
const [rangeEl, totalEl, loadingEl, emptyEl] = ['showingRange', 'totalItems', 'loading', 'empty'].map(id => document.getElementById(id));

// 初始化分页
document.addEventListener('DOMContentLoaded', () => {
    loadPage(1);
    prevBtn.onclick = () => currentPage > 1 && loadPage(currentPage - 1);
    nextBtn.onclick = () => currentPage < totalPages && loadPage(currentPage + 1);
});

// 加载分页数据
async function loadPage(page) {
    // 显示加载状态
    loadingEl.classList.remove('hidden');
    listEl.innerHTML = '';
    paginationEl.classList.add('hidden');
    emptyEl.classList.add('hidden');

    try {
        // 请求数据
        const res = await fetch(`/admin/sourl/list?page=${page}&page_size=${PAGE_SIZE}`);
        const { data } = await res.json();
        // 更新分页信息
        currentPage = data.current_page;
        totalPages = data.total_pages;
        totalEl.textContent = data.total_items;

        // 渲染列表
        if (data.items.length) {
            data.items.forEach(link => {
                const statusClass = link.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                const statusText = link.is_active == 1 ? '启用' : '停用';
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                tr.innerHTML = `
                    <!-- 短链信息单元格 - 包含名称和描述 -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(link.name ?? '未命名')}</div>
                            <div class="text-xs text-gray-500 truncate max-w-xs">
                                ${escapeHtml(link.description ?? '无描述')}
                            </div>
                        </div>
                    </td>
                    
                    <!-- 短码单元格 -->
                     <td class="px-4 py-4 whitespace-nowrap">
                        <button class="copy-code-btn text-primary hover:text-primary/80 hover:underline font-medium text-sm flex items-center"
                                data-code="${link.code}" 
                                title="点击或按Enter复制短码"
                                tabindex="0" 
                                onclick="copyToClipboard('<?= htmlspecialchars($data['domain'] ?? '', ENT_QUOTES) ?>/so/${escapeHtml(link.code)}', '短码')"
                            <span>${escapeHtml(link.code)}</span>
                            <i class="fa fa-copy ml-1 opacity-70 text-primary"></i>
                        </button>
                    </td>
                    
                    <!-- 跳转链接 - 小屏幕隐藏 -->
                    <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                        <div class="text-sm text-gray-500 truncate max-w-xs">
                            ${escapeHtml(link.url)}
                        </div>
                    </td>
                    <!-- 状态 -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-block px-2 py-1 text-xs rounded-full ${statusClass}">
                          ${statusText}
                        </span>
                    </td>                    
                    <!-- 访问计数 -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            ${link.views} 次
                        </span>
                    </td>
                    
                    <!-- 操作按钮 -->
                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <button class="edit-btn text-gray-500 hover:text-blue-500" 
                                    data-id="${link.id}" title="编辑">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="delete-btn text-gray-500 hover:text-red-500"
                                    data-id="${link.id}" title="删除">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                listEl.appendChild(tr);
            });
        } else {
            listEl.innerHTML = `<tr><td colspan="5" class="text-center py-12">没有查到短链；请创建后查看！</td></tr>`;
        }

        // 更新分页控件
        rangeEl.textContent = `${(page-1)*PAGE_SIZE+1}-${Math.min(page*PAGE_SIZE, data.total_items)}`;
        renderPageNumbers();
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
        paginationEl.classList.remove('hidden');
    } catch (e) {
        listEl.innerHTML = `<tr><td colspan="5" class="text-center py-12">加载失败: ${e.message}</td></tr>`;
    } finally {
        loadingEl.classList.add('hidden');
    }
}

// 渲染页码按钮
function renderPageNumbers() {
    pageNumbers.innerHTML = '';
    const start = Math.max(1, currentPage - 2);
    const end = Math.min(totalPages, start + 4);

    for (let i = start; i <= end; i++) {
        const btn = document.createElement('button');
        btn.className = `px-3 py-1 rounded ${i === currentPage ? 'bg-primary text-white' : 'border'}`;
        btn.textContent = i;
        btn.onclick = () => loadPage(i);
        pageNumbers.appendChild(btn);
    }
}

// HTML转义函数
function escapeHtml(str) {
    return str ? str.toString().replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    } [c])) : '';
}

function generateCode(length = 8) {
    const chars = '1234567890ACDEFGHIJKLMNOPQRSTUVWXYZ';
    let code = '';
    for (let i = 0; i < length; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
}
// 选项卡切换功能
function switchTab(tabName) {
    document.getElementById('list-content').classList.add('hidden');
    document.getElementById('settings-content').classList.add('hidden');

    document.getElementById('list-tab').classList.remove('border-primary', 'text-primary');
    document.getElementById('list-tab').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('settings-tab').classList.remove('border-primary', 'text-primary');
    document.getElementById('settings-tab').classList.add('border-transparent', 'text-gray-500');

    document.getElementById(`${tabName}-content`).classList.remove('hidden');
    document.getElementById(`${tabName}-tab`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`${tabName}-tab`).classList.add('border-primary', 'text-primary');

    if (tabName === 'settings' && !window.settingsLoaded) {
        loadSettings();
        window.settingsLoaded = true;
    }
}

// 主功能逻辑
document.addEventListener('DOMContentLoaded', function() {
    const formModal = document.getElementById('formModal');
    const formBackdrop = document.getElementById('formBackdrop');
    const openFormBtn = document.getElementById('openFormBtn');
    const closeFormBtn = document.getElementById('closeFormBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const submitBtn = document.getElementById('submitBtn');
    const formTitle = document.getElementById('formTitle');
    const shortlinkForm = document.getElementById('shortlinkForm');
    const shortlinkList = document.getElementById('shortlinkList');
    const settingsForm = document.getElementById('settingsForm');
    const saveSettingsBtn = document.getElementById('saveSettingsBtn');
    window.settingsLoaded = false;

    // 检查必要元素
    function checkElements() {
        const elements = [formModal, formBackdrop, openFormBtn, closeFormBtn, cancelBtn, submitBtn];
        const missing = elements.filter(el => !el);
        if (missing.length > 0) {
            console.error('缺少必要的DOM元素，弹窗功能无法正常工作');
            return false;
        }
        return true;
    }

    // 打开表单弹窗
    function openFormModal() {
        if (!checkElements()) return;
        resetForm();
        formModal.classList.remove('invisible', 'pointer-events-none', 'scale-95');
        formModal.classList.add('scale-100');
        formBackdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
        void formModal.offsetWidth; // 强制重绘
        document.getElementById('code').value = generateCode();
    }

    // 关闭表单弹窗
    function closeFormModal() {
        if (!checkElements()) return;
        formModal.classList.add('invisible', 'pointer-events-none', 'scale-95');
        formModal.classList.remove('scale-100');
        formBackdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }

    // 重置表单
    function resetForm() {
        shortlinkForm.reset();
        document.getElementById('shortlinkId').value = '';
        formTitle.innerHTML = '<i class="fa fa-plus-circle text-primary mr-2"></i> 创建新短链';
        submitBtn.innerHTML = '保存短链';
        submitBtn.disabled = false;
    }

    // 加载设置
    window.loadSettings = async function() {
        try {
            const response = await fetch('/admin/sourl/settings');
            if (!response.ok) throw new Error('获取设置失败');
            const data = await response.json();

            if (data.success && data.data) {
                const settings = data.data;
                document.getElementById('settingsId').value = settings.id || '';
                document.getElementById('default_domain').value = settings.domain || '';
            }
        } catch (e) {
            showMessage(e.message, 'error');
        }
    }

    // 表单验证
    function validateForm(formElement) {
        if (formElement.id === 'shortlinkForm') {
            const name = formElement.querySelector('#name').value.trim();
            const url = formElement.querySelector('#url').value.trim();

            if (!name) {
                showMessage('请输入短链名称', 'error');
                return false;
            }

            if (!url) {
                showMessage('请输入目标链接', 'error');
                return false;
            }

            // 简单URL验证
            const urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w.-]*)*\/?$/;
            if (!urlPattern.test(url)) {
                showMessage('请输入有效的URL地址', 'error');
                return false;
            }
        } else if (formElement.id === 'settingsForm') {
            // 设置表单验证
            const domain = formElement.querySelector('#default_domain').value.trim();
            if (!domain) {
                showMessage('请输入默认域名', 'error');
                return false;
            }
        }
        return true;
    }

    // 表单提交
    async function submitFormData(url, formElement, successMsg) {
        if (!validateForm(formElement)) return;

        const submitButton = formElement.id === 'shortlinkForm' ? submitBtn : document.getElementById('saveSettingsBtn');
        const originalText = submitButton.innerHTML;

        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 保存中...';

        try {
            const formData = new FormData(formElement);
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();
            if (data.success) {
                showMessage(data.message, 'info');
                closeFormModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || '操作失败');
            }
        } catch (e) {
            showMessage(e.message, 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    // 加载短链数据（编辑用）
    async function loadShortlinkData(id) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 加载中...';

        try {
            const response = await fetch(`/admin/sourl/get/${id}`);
            if (!response.ok) throw new Error('获取数据失败');
            const data = await response.json();

            if (data.success && data.data) {
                const { id, name, url, code, description, is_active } = data.data;
                document.getElementById('shortlinkId').value = id;
                document.getElementById('name').value = name || '';
                document.getElementById('url').value = url || '';
                document.getElementById('code').value = code || '';
                document.getElementById('description').value = description || '';
                document.getElementById('is_active').checked = Boolean(Number(is_active));
                formTitle.innerHTML = '<i class="fa fa-pencil text-primary mr-2"></i> 编辑短链';
            } else {
                throw new Error(data.message || '获取数据失败');
            }
        } catch (e) {
            showMessage(e.message, 'error');
            closeFormModal();
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '保存短链';
        }
    }

    // 删除短链
    async function deleteLink(id) {
        if (!confirm('确定要删除该短链吗？此操作不可恢复！')) return;

        try {
            const response = await fetch(`/admin/sourl/delete/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showMessage('短链已删除', 'info');
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    // 添加删除动画
                    row.classList.add('opacity-0', 'transform', 'translate-x-4', 'transition-all', 'duration-300');
                    setTimeout(() => {
                        row.remove();
                        const rows = shortlinkList.querySelectorAll('tr:not(:last-child)');
                        if (rows.length === 0) {
                            shortlinkList.innerHTML = `
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fa fa-link text-gray-300 text-5xl mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900">没有找到短链</h3>
                                            <p class="mt-1 text-gray-500">尝试调整筛选条件或添加新短链</p>
                                            <button class="mt-4 bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center" id="openFormBtn">
                                                <i class="fa fa-plus mr-2"></i>
                                                <span>新增短链</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                        }
                    }, 300);
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
        formBackdrop.addEventListener('click', closeFormModal);

        // 短链表单提交
        submitBtn.addEventListener('click', function() {
            submitFormData('/admin/sourl/update', shortlinkForm, '短链保存成功');
        });

        // 设置表单提交
        saveSettingsBtn.addEventListener('click', function() {
            submitFormData('/admin/sourl/settings', settingsForm, '设置保存成功');
        });

        // 列表操作事件委托
        shortlinkList.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');

            if (editBtn) {
                const id = editBtn.getAttribute('data-id');
                if (id) {
                    openFormModal();
                    setTimeout(() => loadShortlinkData(id), 300);
                }
            } else if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');
                if (id) deleteLink(id);
            }
        });

        // ESC键关闭弹窗
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !formModal.classList.contains('invisible')) {
                closeFormModal();
            }
        });

        // 阻止表单默认提交
        shortlinkForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }
});
</script>