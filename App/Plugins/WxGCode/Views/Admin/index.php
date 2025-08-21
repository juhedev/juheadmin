
        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa fa-qrcode text-primary mr-3"></i>
            微信群活码管理中心
        </h1>
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <!-- 消息提示框 -->
            <div id="message" class="mb-4 px-4 py-3 rounded-lg hidden"></div>
            <!-- 选项卡导航 -->
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px" id="tabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button id="list-tab" class="inline-block py-4 px-5 border-b-2 border-primary text-sm font-medium text-primary" onclick="switchTab('list')" aria-selected="true">
                            活码列表
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button id="settings-tab" class="inline-block py-4 px-5 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="switchTab('settings')" aria-selected="false">
                            系统设置
                        </button>
                    </li>
                </ul>
            </div>
            <!-- 活码列表内容 -->
            <div id="list-content" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">活码列表</h2>
                    <!-- 创建新活码按钮 -->
                    <button id="openFormBtn" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-md transition-all duration-200 flex items-center">
                        <i class="fa fa-plus mr-2"></i>
                        <span>新增群活码</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white rounded-xl shadow-md overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">活码名称</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">活码编码</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">微信群名称</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">访问量</th>
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
                            <td colspan="5" class="text-center py-12">没有找到活码</td>
                        </tr>
                    </div>
                </div>
                <!-- 分页和状态控件 -->
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
                    <!-- 公众号基本信息 -->
                    <div class="bg-gray-50 p-5 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                            <i class="fa fa-wechat text-primary mr-2"></i>公众号基本信息
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="wechat_name" class="block text-sm font-medium text-gray-700 mb-1">公众号名称 <span class="text-red-500">*</span></label>
                                <input type="text" id="wechat_name" name="wechat_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="请输入公众号名称">
                            </div>
                            <div>
                                <label for="wechat_account" class="block text-sm font-medium text-gray-700 mb-1">公众号原始ID <span class="text-red-500">*</span></label>
                                <input type="text" id="wechat_account" name="wechat_account" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="格式为gh_xxxx">
                            </div>
                            <div>
                                <label for="wechat_type" class="block text-sm font-medium text-gray-700 mb-1">公众号类型 <span class="text-red-500">*</span></label>
                                <select id="wechat_type" name="wechat_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
                                    <option value="subscription">订阅号</option>
                                    <option value="service" selected>服务号</option>
                                    <option value="enterprise">企业号</option>
                                    <option value="test">测试号</option>
                                </select>
                            </div>
                            <div>
                                <label for="qrcode_url" class="block text-sm font-medium text-gray-700 mb-1">公众号二维码URL</label>
                                <div class="flex items-center">
                                    <input type="url" id="qrcode_url" name="qrcode_url" 
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" 
                                           placeholder="https://example.com/share.jpg">
                                    <div class="relative">
                                        <button type="button" 
                                                class="w-10 h-10 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center"
                                                onclick="OpenGallery('qrcode_url', 'setting_qrimg')">
                                            <img src="https://cdn-icons-png.flaticon.com/128/10054/10054290.png" alt="预览图" class=" object-cover rounded">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <img class="w-[120px] h-[120px] object-contain" id='setting_qrimg' src="" alt="公众号二维码预览" />
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" id="setting_status" name="status" value="1"  class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-700">启用当前公众号配置</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- 公众号接口配置 -->
                    <div class="bg-gray-50 p-5 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                            <i class="fa fa-plug text-primary mr-2"></i>接口配置信息
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="appid" class="block text-sm font-medium text-gray-700 mb-1">AppID <span class="text-red-500">*</span></label>
                                <input type="text" id="appid" name="appid" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="公众号的AppID">
                            </div>
                            <div>
                                <label for="appsecret" class="block text-sm font-medium text-gray-700 mb-1">AppSecret <span class="text-red-500">*</span></label>
                                <input type="text" id="appsecret" name="appsecret" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="公众号的AppSecret">
                            </div>
                            <div>
                                <label for="token" class="block text-sm font-medium text-gray-700 mb-1">Token</label>
                                <input type="text" id="token" name="token" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="用于接口调用的Token">
                                <p class="mt-1 text-xs text-gray-500">由开发者自定义，用于生成签名</p>
                            </div>
                            <div>
                                <label for="encoding_aes_key" class="block text-sm font-medium text-gray-700 mb-1">EncodingAESKey</label>
                                <input type="text" id="encoding_aes_key" name="encoding_aes_key" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="消息加密密钥">
                                <p class="mt-1 text-xs text-gray-500">消息加解密时使用，43位字符</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" id="saveSettingsBtn" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg shadow hover:shadow-md transition-all duration-200">
                            保存设置
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- 表单弹窗背景 -->
        <div id="formBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>
        <!-- 活码表单弹窗 -->
        <div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 invisible pointer-events-none transition-all duration-300 scale-95">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <h3 id="formTitle" class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fa fa-plus-circle text-primary mr-2"></i>
                        创建新活码
                    </h3>
                    <button id="closeFormBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-130px)]">
                    <form id="shortlinkForm" class="space-y-5">
                        <input type="hidden" id="shortlinkId" name="id">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">活码名称 <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="请输入活码名称（如：技术交流群）">
                        </div>
                        <div>
                            <label for="wx_group_name" class="block text-sm font-medium text-gray-700 mb-1">微信群名称 <span class="text-red-500">*</span></label>
                            <input type="text" id="wx_group_name" name="wx_group_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="请输入微信群名称">
                        </div>
                        <div>
                            <label for="form_qrcode_url" class="block text-sm font-medium text-gray-700 mb-1">群二维码URL <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="url" id="form_qrcode_url" name="qrcode_url" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" 
                                       placeholder="https://example.com/share.jpg">
                                
                                <div class="relative">
                                    <button type="button" 
                                            class="bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2"
                                            onclick="OpenGallery('form_qrcode_url', 'image-preview')">
                                        <img id="image-preview" src="" alt="预览图" class="w-10 h-10 object-cover rounded">

                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">活码编码 <span class="text-red-500">*</span></label>
                            <input type="text" id="code" name="code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" value="" readonly>
                        </div>
                        <div>
                            <label for="max_scans" class="block text-sm font-medium text-gray-700 mb-1">最大扫码次数（0为无限制）</label>
                            <input type="number" id="max_scans" name="max_scans" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="0">
                        </div>
                        <div>
                            <label for="max_members" class="block text-sm font-medium text-gray-700 mb-1">群最大人数</label>
                            <input type="number" id="max_members" name="max_members" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors" placeholder="如：200">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">描述（可选）</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none" placeholder="请输入活码描述（如：技术交流一群，满200人自动切换）"></textarea>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" id="form_status" name="status" value="1" checked class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
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
                        保存活码
                    </button>
                </div>
            </div>
        </div>
        <div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-xs w-full mx-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">扫码分享</h3>
                <div class="flex justify-center mb-4" id="qrCodeContainer"></div>
                <p onclick="copyToClipboard(this.innerText, '分享链接')"id="textId" class="text-sm text-gray-500 text-center mb-4"></p>
                <button onclick="hideQrCode()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded transition-colors">
                    关闭
                </button>
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
                const res = await fetch(`/admin/wxgcode/list?page=${page}&page_size=${PAGE_SIZE}`);
                const { data } = await res.json();
                // 更新分页信息
                currentPage = data.current_page;
                totalPages = data.total_pages;
                totalEl.textContent = data.total_items;

                // 渲染列表
                if (data.items.length) {
                    data.items.forEach(link => {
                        const statusClass = link.status == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                        const statusText = link.status == 1 ? '启用' : '停用';
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition-colors';
                        tr.setAttribute('data-id', link.id);
                        tr.innerHTML = `
                            <!-- 活码名称和描述 -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(link.name ?? '未命名')}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">
                                        ${escapeHtml(link.description ?? '无描述')}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 活码编码 -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-primary truncate max-w-md cursor-pointer" onclick="showQrCode('${escapeHtml(getQrcodeUrl(link.code))}')">
                                    ${link.code}
                                    <i class="fa fa-qrcode ml-1 opacity-70"></i>
                                </div>
                            </td>
                            
                            <!-- 微信群名称（小屏幕隐藏） -->
                            <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                <div class="text-sm text-gray-500 truncate max-w-md">
                                    ${escapeHtml(link.wx_group_name ?? '未设置')}
                                </div>
                            </td>
                            <!-- 状态 -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs rounded-full ${statusClass}">
                                  ${statusText}
                                </span>
                            </td>                    
                            <!-- 访问量 -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    ${link.total_views} 次
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
                    listEl.innerHTML = `<tr><td colspan="5" class="text-center py-12">没有查到活码；请创建后查看！</td></tr>`;
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

        // 生成活码编码
        function generateCode(length = 8) {
            const chars = '1234567890ACDEFGHIJKLMNOPQRSTUVWXYZ';
            let code = '';
            for (let i = 0; i < length; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return code;
        }

        // 生成活码访问链接
        function getQrcodeUrl(code) {
            const protocol = window.location.protocol;
            const host = window.location.host;
            return `${protocol}//${host}/wxgcode/${code}`;
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

        // 二维码相关功能
        function showQrCode(url) {
            // 清空之前的二维码
            document.getElementById('qrCodeContainer').innerHTML = '';
            
            // 生成新的二维码
            QRCode.toCanvas(url, {
                width: 200,
                margin: 1
            }, function(error, canvas) {
                if (error) {
                    console.error('生成二维码失败:', error);
                    return;
                }
                document.getElementById('qrCodeContainer').appendChild(canvas);
            });
            
            // 显示弹窗
            document.getElementById('qrCodeModal').classList.remove('hidden');
            document.getElementById('textId').textContent = url;
            // 阻止页面滚动
            document.body.style.overflow = 'hidden';
        }

        function hideQrCode() {
            document.getElementById('qrCodeModal').classList.add('hidden');
            // 恢复页面滚动
            document.body.style.overflow = '';
        }

        // 点击弹窗外部关闭
        document.addEventListener('DOMContentLoaded', function() {
            // 二维码弹窗事件绑定
            const qrCodeModal = document.getElementById('qrCodeModal');
            if (qrCodeModal) {
                qrCodeModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideQrCode();
                    }
                });
            }
        });

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
                document.getElementById('image-preview').src = 'https://cdn-icons-png.flaticon.com/128/10054/10054290.png';
                formTitle.innerHTML = '<i class="fa fa-plus-circle text-primary mr-2"></i> 创建新活码';
                submitBtn.innerHTML = '保存活码';
                submitBtn.disabled = false;
            }

            // 加载设置
            window.loadSettings = async function() {
                try {
                    const response = await fetch('/admin/wxgcode/settings');
                    if (!response.ok) throw new Error('获取设置失败');
                    const data = await response.json();

                    if (data.success && data.data) {
                        const settings = data.data;

                        // 回填公众号基本信息
                        document.getElementById('settingsId').value = settings.id || '';
                        document.getElementById('wechat_name').value = settings.wechat_name || '';
                        document.getElementById('wechat_account').value = settings.wechat_account || '';
                        document.getElementById('wechat_type').value = settings.wechat_type || 'service';
                        document.getElementById('setting_qrcode_url').value = settings.qrcode_url || '';
                        document.getElementById('setting_qrimg').src = settings.qrcode_url || '';
                        document.getElementById('setting_status').checked = Boolean(Number(settings.status));

                        // 回填接口配置信息
                        document.getElementById('appid').value = settings.appid || '';
                        document.getElementById('appsecret').value = settings.appsecret || '';
                        document.getElementById('token').value = settings.token || '';
                        document.getElementById('encoding_aes_key').value = settings.encoding_aes_key || '';
                    }
                } catch (e) {
                    showMessage(e.message, 'error');
                }
            }

            // 表单验证
            function validateForm(formElement) {
                if (formElement.id === 'shortlinkForm') {
                    const name = formElement.querySelector('#name').value.trim();
                    const wxGroupName = formElement.querySelector('#wx_group_name').value.trim();
                    const qrcodeUrl = formElement.querySelector('#form_qrcode_url').value.trim();
                    const code = formElement.querySelector('#code').value.trim();

                    if (!name) {
                        showMessage('请输入活码名称', 'error');
                        return false;
                    }
                    if (!wxGroupName) {
                        showMessage('请输入微信群名称', 'error');
                        return false;
                    }
                    if (!qrcodeUrl) {
                        showMessage('请输入群二维码URL', 'error');
                        return false;
                    }
                    // 验证二维码URL格式
                    const urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w.-]*)*\/?$/;
                    if (!urlPattern.test(qrcodeUrl)) {
                        showMessage('请输入有效的群二维码URL', 'error');
                        return false;
                    }

                } else if (formElement.id === 'settingsForm') {
                    // 验证实际必填项
                    const wechatName = formElement.querySelector('#wechat_name').value.trim();
                    const wechatAccount = formElement.querySelector('#wechat_account').value.trim();
                    const appid = formElement.querySelector('#appid').value.trim();
                    const appsecret = formElement.querySelector('#appsecret').value.trim();

                    if (!wechatName) { showMessage('请输入公众号名称', 'error'); return false; }
                    if (!wechatAccount) { showMessage('请输入公众号原始ID', 'error'); return false; }
                    if (!appid) { showMessage('请输入AppID', 'error'); return false; }
                    if (!appsecret) { showMessage('请输入AppSecret', 'error'); return false; }
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
                        showMessage(data.message || successMsg, 'success');
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

            // 加载活码数据（编辑用）
            async function loadShortlinkData(id) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 加载中...';

                try {
                    const response = await fetch(`/admin/wxgcode/get/${id}`);
                    if (!response.ok) throw new Error('获取数据失败');
                    const data = await response.json();

                    if (data.success && data.data) {
                        // 活码表字段回填
                        const { id, name, code, wx_group_name, qrcode_url, max_scans, max_members, description, status } = data.data;
                        document.getElementById('shortlinkId').value = id;
                        document.getElementById('name').value = name || '';
                        document.getElementById('code').value = code || '';
                        document.getElementById('wx_group_name').value = wx_group_name || '';
                        document.getElementById('form_qrcode_url').value = qrcode_url || '';
                        document.getElementById('image-preview').src = qrcode_url || '';
                        document.getElementById('max_scans').value = max_scans || 0;
                        document.getElementById('max_members').value = max_members || '';
                        document.getElementById('description').value = description || '';
                        document.getElementById('form_status').checked = Boolean(Number(status));
                        formTitle.innerHTML = '<i class="fa fa-pencil text-primary mr-2"></i> 编辑活码';
                    } else {
                        throw new Error(data.message || '获取数据失败');
                    }
                } catch (e) {
                    showMessage(e.message, 'error');
                    closeFormModal();
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '保存活码';
                }
            }

            // 删除活码
            async function deleteLink(id) {
                if (!confirm('确定要删除该活码吗？此操作不可恢复！')) return;

                try {
                    const response = await fetch(`/admin/wxgcode/delete/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showMessage('活码已删除', 'success');
                        setTimeout(() => location.reload(), 1000);
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

                // 活码表单提交
                submitBtn.addEventListener('click', function() {
                    submitFormData('/admin/wxgcode/update', shortlinkForm, '活码保存成功');
                });

                // 设置表单提交
                saveSettingsBtn.addEventListener('click', function() {
                    submitFormData('/admin/wxgcode/settings', settingsForm, '设置保存成功');
                });

                // 列表操作事件委托
                shortlinkList.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.edit-btn');
                    const deleteBtn = e.target.closest('.delete-btn');

                    if (editBtn) {
                        const id = editBtn.getAttribute('data-id');
                        if (id) {
                            openFormModal();
                            // 监听动画结束后加载数据
                            const loadData = () => {
                                loadShortlinkData(id);
                                formModal.removeEventListener('transitionend', loadData);
                            };
                            formModal.addEventListener('transitionend', loadData, { once: true });
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


            // 复制到剪贴板功能
            window.copyToClipboard = function(text, message) {
                navigator.clipboard.writeText(text).then(() => {
                    showMessage('已复制: ' + message, 'success');
                }).catch(err => {
                    showMessage('复制失败: ' + err.message, 'error');
                });
            }
        });
    </script>
