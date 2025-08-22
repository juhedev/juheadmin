    document.addEventListener('DOMContentLoaded', () => {
        // 初始化各功能模块
        initMobileSidebar();
        initSubmenuNavigation();
        highlightActiveMenu();
        initAjaxContentLoader();
        initProfileEditModal();
        
        // 触发一次resize事件用于响应式布局初始化
        window.dispatchEvent(new Event('resize'));
    });


    /**
     * 显示消息提示
     * @param {string} text - 提示内容
     * @param {string} type - 提示类型（success/error/warning/info）
     */

    function showMessage(message, type = 'success') {
        const styles = {success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
        const icons = {success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        const baseClasses = ['fixed', 'top-4', 'right-4', 'z-50', 'px-4', 'py-3', 'rounded-lg', 'shadow-lg', 'text-white', 'flex', 'items-center', 'gap-2', 'max-w-sm', 'transition-transform', 'duration-300' ];
        let note = document.getElementById('notification');
        if (!note) {
            note = document.createElement('div');
            note.id = 'notification';
            note.classList.add(...baseClasses); 
            document.body.appendChild(note);
        }
        note.style.transform = 'translateX(calc(100% + 20px))';
        note.className = ''; 
        note.classList.add(...baseClasses, styles[type]);
        note.innerHTML = `<i class="fas ${icons[type]}"></i><span style="word-wrap: break-word; flex: 1; max-width: calc(100% - 24px);">${message}</span>`;
        setTimeout(() => {
            note.style.transform = 'translateX(0)';
        }, 10);
        clearTimeout(note.timer);
        note.timer = setTimeout(() => {
            note.style.transform = 'translateX(calc(100% + 20px))';
        }, 2000);
    }

    /**
     * 显示确认弹窗
     * @param {string} text - 提示内容
     */
    function showConfirm(title, msg) {
        return new Promise(resolve => {
            const mask = document.createElement("div");
            mask.className = "fixed inset-0 bg-black/50 flex items-center justify-center z-50";
            mask.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg w-64 text-center overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
                </div>
                <div class="px-4 py-5">
                    <p class="text-gray-600">${msg}</p>
                </div>
                <div class="px-4 py-3 flex justify-center gap-3">
                    <button class="px-4 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"> 取消 </button>
                    <button class="px-4 py-1.5 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors"> 确定 </button>
                </div>
            </div>
            `;
            document.body.appendChild(mask);
            const [cancelBtn, okBtn] = mask.querySelectorAll("button");
            cancelBtn.onclick = () => { mask.remove(); resolve(false); };
            okBtn.onclick = () => { mask.remove(); resolve(true); };
        });
    }

    /**
     * 复制内容
     * @param {string} text - 复制的内容
     * @param {string} type - 内容类型
     */
    function copyToClipboard(text, typeName = '内容') {
        navigator.clipboard.writeText(text).then(() => {
            showMessage(`${typeName}  复制成功`, 'success');
        }).catch(err => {
            console.error(`${typeName} 复制失败:`, err);
            showMessage(`${typeName}  复制失败，请手动复制`, 'error');
        });
    }

    /**
     * 移动端侧边栏菜单切换功能
     */
    function initMobileSidebar() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        // 元素不存在则不初始化
        if (!mobileMenuButton || !sidebar || !sidebarOverlay) return;

        // 打开侧边栏
        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            sidebarOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        // 关闭侧边栏
        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        // 切换侧边栏状态
        const toggleSidebar = () => {
            sidebar.classList.contains('translate-x-0') ? closeSidebar() : openSidebar();
        };

        // 绑定事件
        mobileMenuButton.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    /**
     * 子菜单折叠/展开功能
     */
    function initSubmenuNavigation() {
        // 关闭所有子菜单
        const closeAllSubmenus = () => {
            document.querySelectorAll('.submenu').forEach(submenu => {
                submenu.classList.add('hidden');
            });
            document.querySelectorAll('.menu-parent .fa-chevron-down').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
        };

        // 切换单个子菜单
        const toggleSubmenu = (parentElement) => {
            const submenu = parentElement.nextElementSibling;
            const icon = parentElement.querySelector('.fa-chevron-down');
            const isCurrentlyOpen = submenu && !submenu.classList.contains('hidden');

            closeAllSubmenus();

            if (!isCurrentlyOpen && submenu) {
                submenu.classList.remove('hidden');
                icon?.classList.add('rotate-180');
            }
        };

        // 绑定父菜单点击事件
        document.querySelectorAll('.menu-parent').forEach(parent => {
            parent.addEventListener('click', (e) => {
                // 仅处理菜单本身的点击（不影响内部链接）
                if (e.target.tagName.toLowerCase() === 'a' || e.target.closest('a') === parent) {
                    e.preventDefault();
                    toggleSubmenu(parent);
                }
            });
        });
    }

    /**
     * 高亮当前页面对应的菜单
     */
    function highlightActiveMenu() {
        const currentUrl = window.location.pathname;

        // 移除所有菜单的高亮状态
        document.querySelectorAll('.main-menu-item, .submenu-item').forEach(item => {
            item.classList.remove('bg-primary/10', 'text-primary', 'font-medium');
        });

        // 优先高亮子菜单
        let submenuHighlighted = false;
        document.querySelectorAll('.submenu-item').forEach(item => {
            if (item.getAttribute('href') === currentUrl) {
                item.classList.add('bg-primary/10', 'text-primary', 'font-medium');
                submenuHighlighted = true;

                // 高亮父菜单并展开子菜单
                const parentItem = item.closest('.menu-group').querySelector('.menu-parent');
                if (parentItem) {
                    parentItem.classList.add('bg-primary/10', 'text-primary', 'font-medium');
                    const submenu = parentItem.nextElementSibling;
                    if (submenu?.classList.contains('submenu')) {
                        submenu.classList.remove('hidden');
                        parentItem.querySelector('.fa-chevron-down')?.classList.add('rotate-180');
                    }
                }
            }
        });

        // 子菜单未匹配时，尝试高亮主菜单
        if (!submenuHighlighted) {
            document.querySelectorAll('.main-menu-item').forEach(item => {
                if (item.getAttribute('href') === currentUrl) {
                    item.classList.add('bg-primary/10', 'text-primary', 'font-medium');
                    const submenu = item.nextElementSibling;
                    if (submenu?.classList.contains('submenu')) {
                        submenu.classList.remove('hidden');
                        item.querySelector('.fa-chevron-down')?.classList.add('rotate-180');
                    }
                }
            });
        }
    }

    /**
     * 初始化插件内容的Ajax加载功能
     */
    function initAjaxContentLoader() {
        // 通过Ajax加载内容
        const loadContent = (url) => {
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    if (!response.ok) throw new Error(`加载失败: HTTP ${response.status}`);
                    return response.text();
                })
                .then(html => {
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        mainContent.innerHTML = html;
                        highlightActiveMenu();
                        
                        // 触发子页面加载完成事件
                        window.dispatchEvent(new CustomEvent('subpageLoaded', { detail: { url } }));
                        
                        // 移动端自动关闭侧边栏
                        if (window.innerWidth < 768) {
                            document.getElementById('mobile-menu-button')?.click();
                        }
                    }
                })
                .catch(error => {
                    console.error('内容加载失败:', error);
                    showMessage('页面加载失败，请重试', 'error');
                });
        };

        // 绑定菜单点击事件（通过Ajax加载内容）
        document.querySelectorAll('.plugin-menu-link, .plugin-submenu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                // 有子菜单的父项不加载内容
                if (link.classList.contains('menu-parent') && link.nextElementSibling?.classList.contains('submenu')) {
                    return;
                }
                e.preventDefault();
                const url = link.getAttribute('href');
                if (url) {
                    loadContent(url);
                    history.pushState(null, '', url); // 更新浏览器地址栏
                }
            });
        });

        // 监听浏览器前进/后退事件
        window.addEventListener('popstate', () => {
            loadContent(window.location.pathname);
        });

        // 监听子页面加载完成事件
        window.addEventListener('subpageLoaded', (e) => {
            console.log('子页面加载完成:', e.detail.url);
            // 此处注释掉了自动刷新，避免循环刷新
           location.reload();
        });
    }

    /**
     * 初始化个人资料编辑弹窗
     */
    function initProfileEditModal() {
        // 获取DOM元素
        const profileModal = document.getElementById('profileModal');
        const profileModalBackdrop = document.getElementById('profileModalBackdrop');
        const openProfileBtn = document.getElementById('profile');
        const closeProfileModal = document.getElementById('closeProfileModal');
        const cancelProfileBtn = document.getElementById('cancelProfileBtn');
        const profileForm = document.getElementById('profileForm');
        const userIdInput = document.getElementById('profileUserId');

        // 元素不完整则不初始化
        if (!profileModal || !profileForm || !userIdInput) return;

        const userId = userIdInput.value;

        // 从服务器加载用户信息
        const loadUserProfile = async () => {
            try {
                const response = await fetch(`/admin/users/${userId}`);
                if (!response.ok) throw new Error('获取用户信息失败');
                const data = await response.json();
                
                if (data.success && data.data) {
                    // 填充邮箱字段
                    const emailInput = document.getElementById('profileEmail');
                    if (emailInput) emailInput.value = data.data.email || '';
                    const roleDisplay = document.getElementById('userRoleDisplay');
                    if (roleDisplay) {
                        // 从接口数据中获取角色，转换为中文显示
                        const role = data.data.role || 'user';
                        roleDisplay.textContent = role === 'admin' ? '管理员' : '普通用户';
                    }
                    document.getElementById('userRoleInput').value = data.data.role;

                    // 3. 填充状态（新增逻辑）
                    const statusDisplay = document.getElementById('userStatusDisplay');
                    if (statusDisplay) {
                        const rawStatus = data.data.status;
                        const isEnabled = rawStatus == 1 || rawStatus == '1' || rawStatus == true;
                        document.getElementById('userStatusInput').value = data.data.status;
                        // 根据判断结果设置文本和样式
                        const statusText = isEnabled ? '已启用' : '已禁用';
                        const statusClass = isEnabled 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-gray-100 text-gray-800';
                        
                        // 渲染状态标签
                        statusDisplay.innerHTML = `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                        `;

                    }
                }
            } catch (error) {
                console.error('加载个人资料失败:', error);
                showMessage('加载个人资料失败', 'error');
            }
        };

        // 打开弹窗
        const openProfileModal = () => {
            profileModal.classList.remove('invisible', 'opacity-0');
            const modalContent = profileModal.querySelector('div');
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }
            document.body.style.overflow = 'hidden';
            loadUserProfile(); // 加载用户数据
        };

        // 关闭弹窗
        const closeProfileModalFunc = () => {
            profileModal.classList.add('invisible', 'opacity-0');
            const modalContent = profileModal.querySelector('div');
            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }
            document.body.style.overflow = '';
        };

        // 提交表单处理
        const submitProfileForm = async (e) => {
            e.preventDefault();
            
            const submitBtn = profileForm.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            const originalText = submitBtn.innerHTML;
            // 禁用按钮并显示加载状态
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> 保存中...';

            try {
                const formData = new FormData(profileForm);
                const response = await fetch('/admin/users/update', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await response.json();
                if (data.success) {
                    showMessage('个人资料更新成功，请重新登录！');
                    closeProfileModalFunc();
                    setTimeout(() => {
                        window.location.href = '/admin/logout'; 
                    }, 2000); 
                } else {
                    throw new Error(data.message || '更新失败');
                }
            } catch (error) {
                showMessage(error.message, 'error');
            } finally {
                // 恢复按钮状态
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        };

        // 绑定事件
        if (openProfileBtn) {
            openProfileBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openProfileModal();
            });
        }

        if (closeProfileModal) closeProfileModal.addEventListener('click', closeProfileModalFunc);
        if (cancelProfileBtn) cancelProfileBtn.addEventListener('click', closeProfileModalFunc);
        if (profileModalBackdrop) profileModalBackdrop.addEventListener('click', closeProfileModalFunc);
        
        profileForm.addEventListener('submit', submitProfileForm);

        // ESC键关闭弹窗
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !profileModal.classList.contains('invisible')) {
                closeProfileModalFunc();
            }
        });
    }
        // 获取元素
    const contactLink = document.getElementById('contactSupport');
    const popup = document.getElementById('supportPopup');
    const closeBtn = document.getElementById('closePopup');

    // 打开弹出层
    contactLink.addEventListener('click', function(e) {
        e.preventDefault();
        popup.classList.remove('hidden');
        // 添加淡入动画效果
        setTimeout(() => {
            popup.querySelector('div').classList.add('scale-100');
            popup.querySelector('div').classList.remove('scale-95');
        }, 10);
    });

    // 关闭弹出层
    function closePopup() {
        popup.querySelector('div').classList.add('scale-95');
        popup.querySelector('div').classList.remove('scale-100');
        setTimeout(() => {
            popup.classList.add('hidden');
        }, 300);
    }

    closeBtn.addEventListener('click', closePopup);

    // 点击外部关闭
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            closePopup();
        }
    });

