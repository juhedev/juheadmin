(function() {

    // 常量定义 - 类名移除avif并添加ja-前缀
    const CLOSE_ICON = ` <img src="https://yanxuan.nosdn.127.net/0c64ce12c71cf276504cb2e15164d4ff.png"width="25px"> `;
    const RESET_ICON = ` <img src="https://yanxuan.nosdn.127.net/8296ea39d8c548ed320f79a483756cd9.jpg"width="25px" > `;
    const PREVIEW_ICON = ` <img class="ja-action-icon" src="https://yanxuan.nosdn.127.net/520b793c329df6349a25404efefbd0f4.png"/> `;
    const COPY_ICON = ` <img class="ja-action-icon" src="https://yanxuan.nosdn.127.net/0732b6e6d1f249dad11ac7d004af5964.png"/> `;
    const UP_ICON = ` <img class="ja-button-icon" src="https://yanxuan.nosdn.127.net/94494a4dc561ae21157f50764ddf035e.png"/> `;
    const ERRO_ICON = ` <img class="ja-erro-icon" src="https://yanxuan.nosdn.127.net/74777259ff515f056e8fc340df28fcd7.png"/> `;

    // 基础配置
    const scriptBaseUrl = getScriptBaseUrl();
    const API_CONFIG = {
        listEndpoint: scriptBaseUrl + '/admin/images/list',
        uploadEndpoint: scriptBaseUrl + '/admin/images/upload'
    };

    // 组件状态
    const state = {
        targetInputId: null,
        currentPage: 1,
        totalItems: 0,
        totalPages: 0,
        mediaItems: [],
        isLoading: false,
        activeTab: 'gallery'
    };
    let closeBtn = null;
    let resetBtn = null;

    // 获取脚本基础URL
function getScriptBaseUrl() {
    const script = document.currentScript;
    if (!script || !script.src) {
        console.warn('无法获取当前脚本URL，使用相对路径');
        return '';
    }
    
    // 使用URL对象解析完整路径
    const url = new URL(script.src);
    // 拼接 协议 + 域名 + 端口（如果有）
    const domain = `${url.protocol}//${url.host}`;
    
    return domain;
}

    // 工具函数
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(0)) + ' ' + sizes[i];
    }

    function isImageFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif', 'avif'];
        return imageExtensions.includes(extension) || /image\/.*/.test(file.type);
    }

    // 样式和布局相关
    function createStyles() {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = scriptBaseUrl + '/Static/css/controller.css';
        document.head.appendChild(link);
    }

    // 防止地址栏遮挡
    function setVh() {
        document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
    }

    // 注册事件监听
    window.addEventListener('resize', setVh);
    window.addEventListener('orientationchange', setVh);

    // UI 创建函数 - 所有ID和类名添加ja-前缀并移除avif
    function createUI() {
        // 动态插入 CSS，禁止顶部下拉刷新
        const style = document.createElement('style');
        style.innerHTML = `
          html, body {
            overscroll-behavior-y: contain;
          }
        `;

        // 创建基础容器
        const wrapper = document.createElement('div');
        wrapper.id = 'ja-uploader';

        const modal = document.createElement('div');
        modal.className = 'ja-modal';
        modal.setAttribute('role', 'dialog');

        const content = document.createElement('div');
        content.className = 'ja-up-modal-content';

        // 创建关闭按钮
        closeBtn = document.createElement('button');
        closeBtn.className = 'ja-modal-close';
        closeBtn.innerHTML = `${CLOSE_ICON}`;


        // 创建重置按钮
        resetBtn = document.createElement('button');
        resetBtn.className = 'ja-img-close';
        resetBtn.setAttribute('data-action', 'reset');
        resetBtn.innerHTML = `${RESET_ICON}`;

        // 标签页导航
        const tabs = document.createElement('div');
        tabs.className = 'ja-tabs';

        const galleryTab = document.createElement('button');
        galleryTab.className = 'ja-tab-button ja-tab-button-active';
        galleryTab.dataset.tab = 'gallery';
        galleryTab.textContent = '图库';

        const uploadTab = document.createElement('button');
        uploadTab.className = 'ja-tab-button';
        uploadTab.dataset.tab = 'upload';
        uploadTab.textContent = '上传';

        // 图库内容
        const galleryContent = document.createElement('div');
        galleryContent.className = 'ja-tab-content ja-tab-content-active';
        galleryContent.dataset.tab = 'gallery';

        const mediaGrid = document.createElement('div');
        mediaGrid.className = 'mb-2';
        mediaGrid.id = 'ja-media-grid';

        // 上传内容
        const uploadContent = document.createElement('div');
        uploadContent.className = 'ja-tab-content';
        uploadContent.dataset.tab = 'upload';

        // 上传参数
        const params = document.createElement('div');
        params.className = 'ja-params';

        const qualityLabel = document.createElement('label');
        qualityLabel.className = 'ja-param-label';
        qualityLabel.innerHTML = `
        <span class="ja-param-title">质量设置 (1-100)</span>
        <div class="ja-range-wrapper">
            <input type="range" id="ja-quality" min="1" max="100" value="60" class="ja-range-input">
            <span id="ja-quality-value" class="ja-range-span">60</span>
        </div>
      `;

        const widthLabel = document.createElement('label');
        widthLabel.className = 'ja-param-label';
        widthLabel.innerHTML = `
        <span class="ja-param-title">转换宽度</span>
        <input type="number" id="ja-width" placeholder="留空为自动" class="ja-text-input">
      `;

        const heightLabel = document.createElement('label');
        heightLabel.className = 'ja-param-label';
        heightLabel.innerHTML = `
        <span class="ja-param-title">转换高度</span>
        <input type="number" id="ja-height" placeholder="留空为自动" class="ja-text-input">
      `;

        // 上传控件
        const uploadInput = document.createElement('input');
        uploadInput.type = 'file';
        uploadInput.id = 'ja-upload-input';
        uploadInput.multiple = true;
        uploadInput.accept = 'image/*';
        uploadInput.className = 'ja-hidden';

        const uploadBtn = document.createElement('button');
        uploadBtn.id = 'ja-upload-btn';
        uploadBtn.className = 'ja-button ja-button-primary';
        uploadBtn.innerHTML = `
        <div id="ja-upload-svg"> ${UP_ICON} </div>
        <div id="ja-upload-status">开始上传</div>
      `;

        const uploadResults = document.createElement('div');
        uploadResults.id = 'ja-upload-results';
        uploadResults.className = 'ja-upload-results';

        // 加载更多按钮
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.id = 'ja-load-more';
        loadMoreBtn.className = 'ja-load-more ja-button ja-button-secondary mt-4';
        loadMoreBtn.innerHTML = `
            <i class="fa fa-refresh mr-2"></i> 加载更多
        `;

        document.head.appendChild(style);

        // 统一建立父子关系
        // 标签页按钮
        tabs.appendChild(galleryTab);
        tabs.appendChild(uploadTab);

        // 图库内容
        galleryContent.appendChild(mediaGrid);
        galleryContent.appendChild(loadMoreBtn);

        // 上传参数
        params.appendChild(qualityLabel);
        params.appendChild(widthLabel);
        params.appendChild(heightLabel);

        // 上传内容
        uploadContent.appendChild(params);
        uploadContent.appendChild(uploadBtn);
        uploadContent.appendChild(uploadInput);
        uploadContent.appendChild(uploadResults);

        // 主内容区域
        content.appendChild(closeBtn);
        content.appendChild(resetBtn);
        content.appendChild(tabs);
        content.appendChild(galleryContent);
        content.appendChild(uploadContent);

        // 模态框组装
        modal.appendChild(content);
        wrapper.appendChild(modal);

        // 添加到文档
        document.body.appendChild(wrapper);
    }
    function updateButtonVisibility(showReset) {
        if (showReset) {
            closeBtn.style.display = 'none';
            resetBtn.style.display = 'block';
        } else {
            closeBtn.style.display = 'block';
            resetBtn.style.display = 'none';
        }
    }

        // 存储表单状态到本地存储 - 键名移除avif
    function saveFormState(width, height) {
        localStorage.setItem('ja-FormState', JSON.stringify({ width, height }));
    }

    // 从本地存储获取表单状态 - 键名移除avif
    function loadFormState() {
        const state = localStorage.getItem('ja-FormState');
        return state ? JSON.parse(state) : { width: '', height: '' };
    }

    // 设置事件监听 - 选择器同步更新
    function setupEventListeners() {
        const qualityInput = document.getElementById('ja-quality');
        const qualityValue = document.getElementById('ja-quality-value');

        if (qualityInput && qualityValue) {
            qualityInput.addEventListener('input', function() {
                qualityValue.textContent = this.value;
            });
        }

        // 统一处理所有点击事件
        document.querySelector('#ja-uploader').addEventListener('click', (e) => {
            const target = e.target;

            // 1. 模态框背景点击（关闭模态框）
            if (target === document.querySelector('.ja-modal')) {
                toggleModal();
            }
            //
            else if (target === document.querySelector('.ja-modal-close')) {
                toggleModal();
            }
            // 2. 标签页切换
            else if (target.classList.contains('ja-tab-button')) {
                const tab = target.dataset.tab;
                switchTab(tab);

                // 切换到图库时重新获取列表
                if (tab === 'gallery') {
                    fetchMediaList();
                }
            }
            // 3. 预览按钮
            else if (target.classList.contains('ja-preview-button')) {
                const url = target.dataset.url;
                previewFile(url);
            }

            // 4. 填充按钮
            else if (target.classList.contains('ja-copy-button')) {
                const url = target.dataset.url;
                urlToPage(url);
            }

            // 5. 上传按钮
            else if (target.id === 'ja-upload-btn') {
                document.getElementById('ja-upload-input').click();
            }

            // 6. 上传结果中的关闭按钮
            else if (target.closest('.ja-img-close')) {
                resetUploadForm();
            }
        });

        // 文件选择事件
        document.getElementById('ja-upload-input').addEventListener('change', handleFileSelect);

        // 加载更多按钮点击事件
        document.getElementById('ja-load-more').addEventListener('click', loadMoreImages);
    }

    // 核心功能函数：初始化图库
    function Gallery(inputId) {
        const targetInput = document.getElementById(inputId);
        if (!targetInput) {
            showToast('输入框不存在', 'error');
            return;
        }
        state.targetInputId = inputId;
        toggleModal();
    }

    // 切换模态窗口
    function toggleModal() {
        fetchMediaList();
        const modal = document.querySelector('.ja-modal');
        modal.classList.toggle('ja-modal-active');
        setVh();
        if (!modal.classList.contains('ja-modal-active')) {
            resetUploadForm();
        }
        resetUploadForm();
    }


    function resetUploadForm() {
        // 从本地存储加载表单状态
        const { width, height } = loadFormState();

        // 清空上传结果
        const uploadResults = document.getElementById('ja-upload-results');
        if (uploadResults) uploadResults.innerHTML = '';

        // 清空文件选择
        const uploadInput = document.getElementById('ja-upload-input');
        if (uploadInput) uploadInput.value = '';

        // 重置质量滑块
        const qualityInput = document.getElementById('ja-quality');
        if (qualityInput) qualityInput.value = 60;

        // 填充宽度和高度
        const widthInput = document.getElementById('ja-width');
        const heightInput = document.getElementById('ja-height');
        if (widthInput) widthInput.value = width ;
        if (heightInput) heightInput.value = height ;
    }

    // 切换标签
    function switchTab(tab) {
        state.activeTab = tab;

        document.querySelectorAll('.ja-tab-button').forEach(btn => {
            btn.classList.toggle('ja-tab-button-active', btn.dataset.tab === tab);
        });

        document.querySelectorAll('.ja-tab-content').forEach(content => {
            content.classList.toggle('ja-tab-content-active', content.dataset.tab === tab);
        });
    }

    // 获取图片列表
     function fetchMediaList() {
        if (state.isLoading) return;
        state.isLoading = true;

        const grid = document.getElementById('ja-media-grid');
        const loadMoreBtn = document.getElementById('ja-load-more');
        const isInitialLoad = state.currentPage === 1;

        // 首次加载显示加载状态
        if (isInitialLoad) {
            grid.innerHTML = `
                <div class="ja-loading-container">
                    <i class="loading-icon"></i>
                    <p class="ja-loading-text">加载中...</p>
                </div>
            `;
            // 初始加载时隐藏加载更多按钮
            loadMoreBtn.style.display = 'none';
        } else {
            // 非首次加载时显示加载状态
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'ja-loading-container';
            loadingIndicator.innerHTML = `
                <i class="loading-icon"></i>
                <p class="ja-loading-text">加载中...</p>
            `;
            grid.appendChild(loadingIndicator);
        }

        const itemsPerPage = 20;
        const params = new URLSearchParams({
            page: state.currentPage,
            limit: itemsPerPage
        });

        fetch(`${API_CONFIG.listEndpoint}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    state.currentPage = data.page;
                    state.totalItems = data.total;
                    state.totalPages = Math.ceil(data.total / itemsPerPage);

                    // 合并数据
                    const previousItemCount = state.mediaItems.length;
                    state.mediaItems = isInitialLoad ?
                        data.data :
                        [...state.mediaItems, ...data.data];

                    renderMediaGrid();

                    // 显示加载更多按钮
                    loadMoreBtn.style.display = 'block';

                    // 控制加载更多按钮显示
                    if (state.currentPage >= state.totalPages) {
                        loadMoreBtn.innerHTML = `
                            <i class="fa fa-check mr-2"></i> 没有更多图片了
                        `;
                        loadMoreBtn.disabled = true;
                        loadMoreBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        loadMoreBtn.innerHTML = `
                            <i class="fa fa-refresh mr-2"></i> 加载更多
                        `;
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }

                    // 恢复滚动位置
                    if (!isInitialLoad) {
                        const newItemsCount = state.mediaItems.length - previousItemCount;
                        const itemHeight = 150;
                        const newContentHeight = newItemsCount * itemHeight;
                        grid.scrollTop += newContentHeight;
                    }
                } else {
                    // 错误处理
                    grid.innerHTML = `
                        <div class="ja-error-container">
                            ${ERRO_ICON}
                            <p class="ja-error-text">${data.message}</p>
                        </div>
                    `;
                    loadMoreBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('获取图片列表失败:', error);
                grid.innerHTML = `
                    <div class="ja-error-container">
                        ${ERRO_ICON}
                        <p class="ja-error-text">网络错误，请重试</p>
                    </div>
                `;
                loadMoreBtn.style.display = 'none';
            })
            .finally(() => {
                state.isLoading = false;
            });
    }

    // 加载更多函数
    function loadMoreImages() {
        if (state.isLoading || state.currentPage >= state.totalPages) return;

        const grid = document.getElementById('ja-media-grid');
        state.currentPage++;

        // 临时禁用滚动事件
        grid.style.overflowY = 'hidden';

        fetchMediaList();

        // 恢复滚动
        setTimeout(() => {
            grid.style.overflowY = 'auto';
        }, 100);
    }

    // 渲染图片网格
    function renderMediaGrid() {
        const grid = document.getElementById('ja-media-grid');
        const itemsToRender = state.mediaItems;
        grid.innerHTML = '';

        if (itemsToRender.length === 0) {
            // 空状态显示
            grid.innerHTML = `
                <div class="ja-empty-container">
                    ${ERRO_ICON}
                    <p class="ja-empty-text">没有找到图片</p>
                </div>
            `;
            return;
        }

        // 正常渲染图片列表
        itemsToRender.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'ja-media-item';
            itemElement.innerHTML = `
                <div class="ja-item-thumbnail">
                    <img src="${item.url}" alt="${item.name}" class="ja-thumbnail-image" loading="lazy">
                    <div class="ja-item-overlay">
                        <button class="ja-copy-button" data-url="${item.url}"> ${COPY_ICON} </button>
                        <button class="ja-preview-button" data-url="${item.url}"> ${PREVIEW_ICON} </button> 
                    </div>
                </div>
                <div class="ja-item-info">
                    <div class="ja-item-name">${item.name}</div>
                    <div class="ja-item-details">
                        <span class="ja-item-size">${formatFileSize(item.size)}</span>
                        <span class="ja-item-dimensions">${item.width}*${item.height}</span>
                    </div>
                </div>
            `;
            grid.appendChild(itemElement);
        });
    }


    // 处理文件选择
    function handleFileSelect(e) {
        const files = e.target.files;
        if (!files.length) return;

        const validFiles = [];
        const invalidFiles = [];

        Array.from(files).forEach(file => {
            if (!isImageFile(file)) {
                invalidFiles.push({ file, reason: '不支持的文件类型，仅支持图片' });
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                invalidFiles.push({ file, reason: '文件过大，最大支持10MB' });
                return;
            }

            validFiles.push(file);
        });

        if (invalidFiles.length > 0) {
            const uploadResults = document.getElementById('ja-upload-results');
            invalidFiles.forEach(({ file, reason }) => {
                const errorItem = document.createElement('div');
                errorItem.className = 'ja-upload-error';
                errorItem.innerHTML = `
                    <div class="ja-error-header">
                        <span class="ja-error-filename"></span>
                        <span class="ja-error-status">错误</span>
                    </div>
                    <div class="ja-error-message">${reason}</div>
                `;
                uploadResults.appendChild(errorItem);
            });
        }

        if (validFiles.length > 0) {
            uploadFiles(validFiles);
        }
    }

// 上传文件
function uploadFiles(files) {
    console.log('uploadEndpoint:', API_CONFIG.uploadEndpoint);

    const quality = document.getElementById('ja-quality').value;
    const width = document.getElementById('ja-width').value;
    const height = document.getElementById('ja-height').value;
    const uploadResults = document.getElementById('ja-upload-results');
    saveFormState(width, height);
    uploadResults.innerHTML = '';

    files.forEach(file => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('quality', quality);
        if (width) formData.append('width', width);
        if (height) formData.append('height', height);

        const progressItem = document.createElement('div');
        progressItem.className = 'ja-upload-item';

        uploadResults.appendChild(progressItem);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', API_CONFIG.uploadEndpoint, true);
        
        // 设置超时时间
        xhr.timeout = 120000; // 120秒超时
        
        // 超时处理
        xhr.ontimeout = function() {
            updateButtonVisibility(true);
            resetUploadButton();
            handleUploadError(file, '等待返回超时，已在后台处理，稍后到图片列表中查看；如果没有则是后台处理失败；大部分问题是图片过大', progressItem);
        };

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                const ja = window.ja;
                progressItem.style.width = `${percentComplete}%`;
                if (ja.statusText) {
                    ja.statusText.textContent = `上传中 ${Math.round(percentComplete)}%`;
                    ja.upsvg.innerHTML = '<i class="uploading-icon"></i>';
                }
            }
        });

        xhr.onload = function() {
            // 重置上传按钮状态
            resetUploadButton();
            
            // 处理成功响应
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    handleUploadSuccess(response, progressItem);
                } catch (error) {
                    console.error('解析响应失败:', error);
                    console.log('原始响应:', xhr.responseText);
                    
                    // 检查是否是HTML响应
                    if (xhr.responseText.includes('<html') || xhr.responseText.includes('DOCTYPE')) {
                        handleUploadError(file, '服务器返回了无效响应，请检查服务器配置', progressItem);
                    } else {
                        handleUploadError(file, `解析服务器响应失败: ${error.message}`, progressItem);
                    }
                }
            } 
            // 处理错误响应
            else {
                updateButtonVisibility(true);
                try {
                    const responseData = JSON.parse(xhr.responseText);
                    console.log('错误响应:', responseData);
                    handleUploadError(file, `${responseData.message || '服务器错误'}`, progressItem);
                } catch (parseError) {
                    console.error('解析错误响应失败:', parseError);
                    console.log('原始错误响应:', xhr.responseText);
                    
                    // 检查是否是HTML响应
                    if (xhr.responseText.includes('<html') || xhr.responseText.includes('DOCTYPE')) {
                        handleUploadError(file, `服务器错误 (${xhr.status}): 请检查服务器配置`, progressItem);
                    } else {
                        handleUploadError(file, `服务器错误 (${xhr.status}): ${parseError.message}`, progressItem);
                    }
                }
            }
        };

        xhr.onerror = function() {
            updateButtonVisibility(true);
            resetUploadButton();
            handleUploadError(file, '网络错误，请重试', progressItem);
        };

        xhr.send(formData);
    });
}

// 重置上传按钮状态的辅助函数
function resetUploadButton() {
    const ja = window.ja;
    if (ja.statusText) {
        ja.statusText.textContent = `开始上传`;
        ja.upsvg.innerHTML = `
            ${UP_ICON}
        `;
    }
}

    // 处理上传成功
    function handleUploadSuccess(response, progressItem) {
        updateButtonVisibility(true);
        progressItem.innerHTML = `

        <div class="ja-media-item">
            <div class="ja-item-thumbnail relative">
                <img src="${response.data.url}" alt="${response.data.name}" class="ja-thumbnail-image">
                <div class="ja-item-overlay">
                    <button class="ja-copy-button" data-url="${response.data.url}"> ${COPY_ICON} </button>
                    <button class="ja-preview-button" data-url="${response.data.url}"> ${PREVIEW_ICON} </button> 
                </div>
            </div>
            <div class="ja-item-info">
                <div class="ja-item-details">
                    <span class="ja-item-name">${response.data.name}</span>
                    <span class="ja-upload-success">已完成</span>
                </div>
                <div class="ja-item-details">
                    <span class="ja-item-size">${formatFileSize(response.data.size || 0)}</span>
                    <span class="ja-item-dimensions">${response.data.width}*${response.data.height}</span>
                </div>
            </div>
        </div>
    `;

        // 确保响应中包含正确的URL
        if (!response.data || !response.data.url) {
            console.error('上传成功但缺少URL:', response);
            return;
        }

        // 构建新图片对象
        const newItem = {
            id: response.data.id || Date.now(),
            name: response.data.name,
            url: response.data.url,
            thumbnail: response.data.thumbnail || response.data.url,
            size: response.data.size || 0,
            dimensions: response.data.dimensions || '未知'
        };

        // 将新图片添加到列表顶部
        state.mediaItems.unshift(newItem);
        state.totalItems = state.mediaItems.length;

        // 如果当前是图库标签且在第一页，立即更新UI
        if (state.activeTab === 'gallery' && state.currentPage === 1) {
            renderMediaGrid();
        } else if (state.currentPage > 1) {
            // 如果不在第一页，更新总页数
            state.totalPages = Math.ceil(state.totalItems / 20);
        }
    }

    // 处理上传失败
    function handleUploadError(file, message, progressItem) {
        progressItem.className = 'ja-upload-item ja-upload-error';
        progressItem.innerHTML = `
            <div class="ja-upload-header">
                <span class="ja-upload-filename"></span>
                <span class="ja-upload-failure"></span>
            </div>
            <div class="ja-upload-error-message">${message}</div>
        `;
    }

    // 预览图片
    function previewFile(url) {
        const modal = document.createElement('div');
        modal.className = 'ja-image-preview-modal';
        modal.innerHTML = `
            <div class="ja-modal-content">
                <img src="${url}" alt="预览图片" class="ja-modal-image">
                <button class="ja-close-button">${CLOSE_ICON}</button>
            </div>
        `;
        const modalContent = modal.querySelector('.ja-modal-content');
        const image = modal.querySelector('.ja-modal-image');
        const closeButton = modal.querySelector('.ja-close-button');

        // 关闭模态窗口
        closeButton.addEventListener('click', () => {
            modal.remove();
        });

        // 点击背景关闭
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        document.body.appendChild(modal);
    }

    // 填充到表单
    function urlToPage(text) {
        if (typeof window.SetImageUrl === 'function') {
            SetImageUrl(text);
        } else {
            navigator.clipboard.writeText(text);
            showToast('没有找到接收函数，URL已复制到剪贴板', 'success');
        }
        toggleModal(); // 关闭模态框
    }

    // 显示提示消息
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `ja-toast ja-toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('ja-toast-visible');
        }, 10);
        setTimeout(() => {
            toast.classList.remove('ja-toast-visible');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 2000);
        }, 300);
    }

    // 初始化
    function init() {
        createStyles();
        createUI();
        setupEventListeners();
        setVh();
        updateButtonVisibility(false);
    }

    // 执行初始化
    init();

    function SetImageUrl(url) {
        if (SetImageUrl.inputElement) {
            SetImageUrl.inputElement.value = url;
        }
        if (SetImageUrl.previewElement) {
            SetImageUrl.previewElement.src = url;
        }
    }

    function OpenGallery(inputId, previewId) {
        SetImageUrl.inputElement = document.getElementById(inputId);
        SetImageUrl.previewElement = previewId !== undefined 
            ? document.getElementById(previewId) 
            : null;
        toggleModal();
    }
    window.SetImageUrl = SetImageUrl;
    window.OpenGallery = OpenGallery;
    window.ja = {
        statusText: document.getElementById('ja-upload-status'),
        upsvg: document.getElementById('ja-upload-svg')
    };

})();