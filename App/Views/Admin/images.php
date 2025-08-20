<!-- 页面标题 -->
<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
    <i class="fas fa-image text-primary mr-3"></i>图片图库管理
</h1>
<!-- 主内容区 -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- 标签页导航 -->
    <div class="border-b border-gray-200">
        <div class="flex">
            <button id="gallery-tab-gallery" class="px-6 py-4 font-medium text-primary border-b-2 border-primary" data-tab="gallery"> <i class="fa fa-th-large mr-2"></i>图库 </button>
            <button id="gallery-upload-modal-trigger" class="px-6 py-4 font-medium text-gray-500 hover:text-gray-700"> <i class="fa fa-upload mr-2"></i>上传 </button>
        </div>
    </div>
    <!-- 图库内容 -->
    <div id="gallery-content-gallery" class="p-6" data-tab="gallery">
        <!-- 批量删除按钮 -->
        <button id="gallery-batch-delete" class="mb-6 px-4 py-2 bg-red-500 text-white rounded-md flex items-center opacity-50 cursor-not-allowed" disabled> <i class="fa fa-trash mr-2"></i> 删除选中的图片 </button>
        <!-- 图片网格 -->
        <div id="gallery-media-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6"></div>
        <!-- 加载更多按钮 -->
        <button id="gallery-load-more" class="w-full py-2 px-4 bg-gray-100 text-gray-800 rounded-md flex items-center justify-center hover:bg-gray-200 transition-colors"> <i class="fa fa-refresh mr-2"></i> 加载更多 </button>
    </div>
</div>
<!-- 上传弹出窗口 -->
<div id="gallery-upload-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">上传图片</h2>
            <button id="gallery-close-upload-modal" class="text-gray-500 hover:text-gray-700"> <i class="fa fa-times text-xl"></i> </button>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- 上传参数设置 -->
                <div class="space-y-4">
                    <!-- 质量设置 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">质量设置 (1-100)</label>
                        <div class="flex items-center space-x-4">
                            <input type="range" id="gallery-quality" min="35" max="100" value="60" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-500">
                            <span id="gallery-quality-value" class="text-sm font-medium min-w-[3rem] text-center">60</span>
                        </div>
                    </div>
                    <!-- 宽度设置 -->
                    <div>
                        <label for="gallery-width" class="block text-sm font-medium text-gray-700 mb-2">转换宽度</label>
                        <input type="number" id="gallery-width" placeholder="留空为自动" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- 高度设置 -->
                    <div>
                        <label for="gallery-height" class="block text-sm font-medium text-gray-700 mb-2">转换高度</label>
                        <input type="number" id="gallery-height" placeholder="留空为自动" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <!-- 上传按钮 -->
                <button id="gallery-upload-btn" class="w-full py-3 bg-blue-500 text-white rounded-md flex items-center justify-center hover:bg-blue-600 transition-colors">
                    <i class="fa fa-cloud-upload mr-2"></i>
                    <span id="gallery-upload-status">开始上传</span>
                </button>
                <input type="file" id="gallery-upload-input" multiple accept="image/*" class="hidden">
                <!-- 上传结果 -->
                <div id="gallery-upload-results" class="mt-6 space-y-4"></div>
            </div>
        </div>
    </div>
</div>
<!-- 图片预览模态框 -->
<div id="gallery-preview-modal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-5xl ">
        <!-- 图片容器 - 用于定位关闭按钮 -->
        <div class="relative inline-block">
            <img src="" alt="预览图片" class="max-w-full max-h-[80vh] bg-white mx-auto object-contain">
            <!-- 关闭按钮 - 绝对定位在图片右上角 -->
            <button id="gallery-preview-close" class="absolute -top-8 bg-white w-8 h-8 p-0 rounded-full  -right-8 text-red text-2xl hover:text-gray-300 transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</div>
<script>
// 组件状态
const galleryState = {
    targetInputId: null,
    currentPage: 1,
    totalItems: 0,
    totalPages: 0,
    mediaItems: [],
    isLoading: false,
    activeTab: 'gallery',
    selectedImageIds: []
};

// DOM元素缓存 - 使用唯一ID避免冲突
const galleryElements = {
    mediaGrid: document.getElementById('gallery-media-grid'),
    loadMoreBtn: document.getElementById('gallery-load-more'),
    batchDeleteBtn: document.getElementById('gallery-batch-delete'),
    previewModal: document.getElementById('gallery-preview-modal'),
    previewImage: document.querySelector('#gallery-preview-modal img'),
    previewCloseBtn: document.getElementById('gallery-preview-close'),
    uploadModal: document.getElementById('gallery-upload-modal'),
    uploadModalTrigger: document.getElementById('gallery-upload-modal-trigger'),
    closeUploadModal: document.getElementById('gallery-close-upload-modal'),
    uploadResults: document.getElementById('gallery-upload-results')
};

// 初始化函数
function initGallery() {
    setupGalleryEventListeners();
    fetchGalleryMediaList();
}

// 格式化文件大小
function galleryFormatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(0)) + ' ' + sizes[i];
}

// 验证图片文件
function galleryIsImageFile(file) {
    const extension = file.name.split('.').pop().toLowerCase();
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif', 'avif'];
    return imageExtensions.includes(extension) || /image\/.*/.test(file.type);
}

// 本地存储表单状态 - 使用唯一键名
function gallerySaveFormState(width, height) {
    localStorage.setItem('galleryFormState', JSON.stringify({ width, height }));
}

function galleryLoadFormState() {
    const state = localStorage.getItem('galleryFormState');
    return state ? JSON.parse(state) : { width: '', height: '' };
}

// 设置事件监听 - 全部使用唯一ID
function setupGalleryEventListeners() {
    // 质量滑块事件
    const qualityInput = document.getElementById('gallery-quality');
    const qualityValue = document.getElementById('gallery-quality-value');
    if (qualityInput && qualityValue) {
        qualityInput.addEventListener('input', function() {
            qualityValue.textContent = this.value;
        });
    }

    // 上传模态框控制
    galleryElements.uploadModalTrigger.addEventListener('click', () => {
        galleryElements.uploadModal.classList.remove('hidden');
        galleryElements.uploadModal.classList.add('flex');
        // 加载保存的表单状态
        const { width, height } = galleryLoadFormState();
        document.getElementById('gallery-width').value = width;
        document.getElementById('gallery-height').value = height;
    });

    galleryElements.closeUploadModal.addEventListener('click', () => {
        galleryElements.uploadModal.classList.add('hidden');
        galleryElements.uploadModal.classList.remove('flex');
    });

    // 点击模态框背景关闭
    galleryElements.uploadModal.addEventListener('click', (e) => {
        if (e.target === galleryElements.uploadModal) {
            galleryElements.uploadModal.classList.add('hidden');
            galleryElements.uploadModal.classList.remove('flex');
        }
    });

    // 加载更多按钮
    galleryElements.loadMoreBtn.addEventListener('click', galleryLoadMoreImages);

    // 批量删除按钮
    galleryElements.batchDeleteBtn.addEventListener('click', () => {
        if (galleryState.selectedImageIds.length > 0) {
            galleryConfirmDeleteImage(galleryState.selectedImageIds);
        }
    });

    // 图片网格事件委托 - 使用数据属性识别元素类型
    galleryElements.mediaGrid.addEventListener('click', (e) => {
        // 预览图片 - 使用数据属性选择
        const previewBtn = e.target.closest('[data-action="preview"]');
        if (previewBtn) {
            const url = previewBtn.dataset.url;
            galleryPreviewFile(url);
            return;
        }

        // 复制链接 - 使用数据属性选择
        const copyBtn = e.target.closest('[data-action="copy"]');
        if (copyBtn) {
            const url = copyBtn.dataset.url;
            galleryCopyToClipboard(url);
            return;
        }

        // 图片复选框 - 使用数据属性选择
        const checkbox = e.target.closest('[data-type="image-checkbox"]');
        if (checkbox) {
            const imageId = checkbox.dataset.id;
            galleryToggleImageSelection(imageId, checkbox);
            return;
        }
    });

    // 上传结果区域事件委托
    galleryElements.uploadResults.addEventListener('click', (e) => {
        const retryBtn = e.target.closest('[data-action="retry-upload"]');
        if (retryBtn) {
            document.getElementById('gallery-upload-input').click();
            return;
        }
    });

    // 上传按钮
    document.getElementById('gallery-upload-btn').addEventListener('click', () => {
        document.getElementById('gallery-upload-input').click();
    });

    // 文件选择事件
    document.getElementById('gallery-upload-input').addEventListener('change', galleryHandleFileSelect);

    // 预览模态框关闭
    galleryElements.previewCloseBtn.addEventListener('click', () => {
        galleryElements.previewModal.classList.remove('flex');
        galleryElements.previewModal.classList.add('hidden');
    });

    galleryElements.previewModal.addEventListener('click', (e) => {
        if (e.target === galleryElements.previewModal) {
            galleryElements.previewModal.classList.remove('flex');
            galleryElements.previewModal.classList.add('hidden');
        }
    });
}

// 切换图片选择状态
function galleryToggleImageSelection(imageId, checkbox) {
    const index = galleryState.selectedImageIds.indexOf(imageId);

    if (index === -1) {
        // 选中
        galleryState.selectedImageIds.push(imageId);
        checkbox.checked = true;
        // 使用DOM导航找到父容器并应用样式
        checkbox.closest('[data-type="image-item"]').classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
    } else {
        // 取消选中
        galleryState.selectedImageIds.splice(index, 1);
        checkbox.checked = false;
        checkbox.closest('[data-type="image-item"]').classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
    }

    // 更新批量删除按钮状态
    if (galleryState.selectedImageIds.length > 0) {
        galleryElements.batchDeleteBtn.disabled = false;
        galleryElements.batchDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        galleryElements.batchDeleteBtn.classList.add('hover:bg-red-600');
    } else {
        galleryElements.batchDeleteBtn.disabled = true;
        galleryElements.batchDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
        galleryElements.batchDeleteBtn.classList.remove('hover:bg-red-600');
    }
}

// 确认删除
function galleryConfirmDeleteImage(ids) {
    const isBatch = ids.length > 1;
    if (confirm(`确定要${isBatch ? '批量删除选中的' : '删除这张'}图片吗？此操作不可撤销。`)) {
        galleryDeleteImages(ids);
    }
}

// 删除图片函数
function galleryDeleteImages(ids) {
    galleryState.isLoading = true;
    galleryShowLoading();

    fetch('/admin/images/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP错误: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showMessage(`成功删除${ids.length}张图片`, 'success');
                galleryState.currentPage = 1;
                galleryState.selectedImageIds = [];
                galleryState.isLoading = false;
                fetchGalleryMediaList();
            } else {
                showMessage(`删除失败：${data.message}`, 'error');
                galleryState.isLoading = false;
                galleryRenderMediaGrid();
            }
        })
        .catch(error => {
            console.error('删除失败:', error);
            showMessage('网络错误，删除失败', 'error');
            galleryState.isLoading = false;
            galleryRenderMediaGrid();
        });
}

// 获取图片列表
function fetchGalleryMediaList() {
    if (galleryState.isLoading) return;
    galleryState.isLoading = true;

    const isInitialLoad = galleryState.currentPage === 1;
    if (isInitialLoad) {
        galleryShowLoading();
    } else {
        const loadingIndicator = galleryCreateLoadingIndicator();
        galleryElements.mediaGrid.appendChild(loadingIndicator);
    }

    const itemsPerPage = 20;
    const params = new URLSearchParams({
        page: galleryState.currentPage,
        limit: itemsPerPage
    });

    fetch(`/admin/images/list?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`列表请求失败: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                galleryState.currentPage = data.page;
                galleryState.totalItems = data.total;
                galleryState.totalPages = Math.ceil(data.total / itemsPerPage);

                galleryState.mediaItems = isInitialLoad ? data.data : [...galleryState.mediaItems, ...data.data];
                galleryRenderMediaGrid();

                if (galleryState.currentPage >= galleryState.totalPages) {
                    galleryElements.loadMoreBtn.innerHTML = `<i class="fa fa-check mr-2"></i> 没有更多图片了`;
                    galleryElements.loadMoreBtn.disabled = true;
                    galleryElements.loadMoreBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    galleryElements.loadMoreBtn.innerHTML = `<i class="fa fa-refresh mr-2"></i> 加载更多`;
                    galleryElements.loadMoreBtn.disabled = false;
                    galleryElements.loadMoreBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                showMessage(data.message, 'error');
                galleryRenderMediaGrid();
            }
        })
        .catch(error => {
            console.error('获取图片列表失败:', error);
            showMessage('网络错误，请重试', 'error');
            galleryRenderMediaGrid();
        })
        .finally(() => {
            galleryState.isLoading = false;
        });
}

// 显示加载状态
function galleryShowLoading() {
    galleryElements.mediaGrid.innerHTML = '';
    const loadingIndicator = galleryCreateLoadingIndicator();
    galleryElements.mediaGrid.appendChild(loadingIndicator);
}

// 加载更多图片
function galleryLoadMoreImages() {
    if (galleryState.isLoading || galleryState.currentPage >= galleryState.totalPages) return;
    galleryState.currentPage++;
    fetchGalleryMediaList();
}

// 创建加载指示器
function galleryCreateLoadingIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'col-span-full flex flex-col items-center justify-center py-12';
    indicator.innerHTML = `
        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500 mb-4"></div>
        <p class="text-gray-500">加载中...</p>
    `;
    return indicator;
}

// 渲染图片网格
function galleryRenderMediaGrid() {
    galleryElements.mediaGrid.innerHTML = '';
    const itemsToRender = galleryState.mediaItems;

    if (itemsToRender.length === 0) {
        galleryElements.mediaGrid.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center px-4">
                <i class="fa fa-picture-o text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">没有找到图片</p>
                <button onclick="document.getElementById('gallery-upload-modal-trigger').click()" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fa fa-upload mr-2"></i>上传图片
                </button>
            </div>
        `;
        return;
    }

    // 渲染图片项 - 使用数据属性代替自定义类名
    itemsToRender.forEach(item => {
        const isSelected = galleryState.selectedImageIds.includes(item.id.toString());
        const itemElement = document.createElement('div');
        // 使用数据属性标识元素类型，而非自定义类名
        itemElement.dataset.type = "image-item";
        itemElement.className = `bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow ${isSelected ? 'ring-2 ring-blue-500 ring-offset-2' : ''}`;
        itemElement.innerHTML = `
            <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                <!-- 复选框 - 使用数据属性标识 -->
                <input type="checkbox" data-type="image-checkbox" data-id="${item.id}" 
                    class="absolute top-2 left-2 z-10 w-4 h-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500" 
                    ${isSelected ? 'checked' : ''}>
                
                <!-- 缩略图 -->
                <img src="${item.url}" alt="${item.name}" class="w-full h-full object-cover" loading="lazy">
                
                <!-- 操作按钮 - 使用数据属性标识操作类型 -->
                <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center gap-2 p-2">
                    <button data-action="copy" data-url="${item.url}" 
                        class="bg-white w-8 h-8 p-0 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors" 
                        title="复制链接">
                        <i class="fa fa-copy text-gray-800"></i>
                    </button>
                    <button data-action="preview" data-url="${item.url}" 
                        class="bg-white w-8 h-8 p-0 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors" 
                        title="预览图片">
                        <i class="fa fa-eye text-gray-800"></i>
                    </button>
                </div>
            </div>
            
            <!-- 图片信息 -->
            <div class="p-2">
                <div class="text-xs font-medium text-gray-800 truncate mb-1" title="${item.name}">${item.name}</div>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>${galleryFormatFileSize(item.size)}</span>
                    <span>${item.width}*${item.height}</span>
                </div>
            </div>
        `;
        galleryElements.mediaGrid.appendChild(itemElement);
    });

    // 添加统计信息
    const statsElement = document.createElement('div');
    statsElement.className = 'col-span-full mt-6 pt-4 border-t border-gray-100 text-sm text-gray-500 flex justify-between items-center';
    statsElement.innerHTML = `
        <div>
            共计 <span class="font-semibold text-gray-800">${galleryState.totalItems}</span> 张图片
        </div>
        <div>
            已显示 <span class="font-semibold text-gray-800">${galleryState.mediaItems.length}</span> 张图片
        </div>
    `;
    galleryElements.mediaGrid.appendChild(statsElement);

    // 更新批量删除按钮状态
    if (galleryState.selectedImageIds.length > 0) {
        galleryElements.batchDeleteBtn.disabled = false;
        galleryElements.batchDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        galleryElements.batchDeleteBtn.classList.add('hover:bg-red-600');
    } else {
        galleryElements.batchDeleteBtn.disabled = true;
        galleryElements.batchDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
        galleryElements.batchDeleteBtn.classList.remove('hover:bg-red-600');
    }
}

// 处理文件选择
function galleryHandleFileSelect(e) {
    const files = e.target.files;
    if (!files.length) return;

    const validFiles = [];
    const invalidFiles = [];

    Array.from(files).forEach(file => {
        if (!galleryIsImageFile(file)) {
            invalidFiles.push({ file, reason: '不支持的文件类型，仅支持图片' });
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            invalidFiles.push({ file, reason: '文件过大，最大支持10MB' });
            return;
        }
        validFiles.push(file);
    });

    galleryElements.uploadResults.innerHTML = '';

    if (invalidFiles.length > 0) {
        invalidFiles.forEach(({ file, reason }) => {
            const errorItem = document.createElement('div');
            errorItem.className = 'p-4 border border-red-200 bg-red-50 rounded-md';
            errorItem.innerHTML = `
                <div class="flex justify-between items-start mb-1">
                    <span class="font-medium text-red-800 text-sm">${file.name}</span>
                    <span class="text-red-600 text-xs">错误</span>
                </div>
                <p class="text-red-700 text-xs">${reason}</p>
            `;
            galleryElements.uploadResults.appendChild(errorItem);
        });
    }

    if (validFiles.length > 0) {
        galleryUploadFiles(validFiles);
    }
}

// 上传文件
function galleryUploadFiles(files) {
    const quality = document.getElementById('gallery-quality').value;
    const width = document.getElementById('gallery-width').value;
    const height = document.getElementById('gallery-height').value;
    const uploadStatus = document.getElementById('gallery-upload-status');

    gallerySaveFormState(width, height);

    // 添加上传中的指示器
    files.forEach(file => {
        const progressItem = document.createElement('div');
        progressItem.className = 'p-4 border border-gray-200 rounded-md overflow-hidden';
        progressItem.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-800 text-sm">${file.name}</span>
                <span class="text-blue-500 text-xs">上传中</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div data-type="upload-progress" class="bg-blue-500 h-1.5 rounded-full w-0 transition-all duration-300"></div>
            </div>
        `;
        galleryElements.uploadResults.appendChild(progressItem);
    });

    files.forEach((file, index) => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('quality', quality);
        if (width) formData.append('width', width);
        if (height) formData.append('height', height);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/images/upload', true);
        xhr.timeout = 120000;

        xhr.ontimeout = function() {
            const progressItems = galleryElements.uploadResults.querySelectorAll('.p-4');
            const progressItem = progressItems[index];
            galleryHandleUploadError(file, '等待返回超时，已在后台处理，稍后到图片列表中查看', progressItem);
        };

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                uploadStatus.textContent = `上传中 ${Math.round(percentComplete)}%`;

                const progressItems = galleryElements.uploadResults.querySelectorAll('.p-4');
                const progressItem = progressItems[index];
                const progressBar = progressItem.querySelector('[data-type="upload-progress"]');
                progressBar.style.width = `${percentComplete}%`;
            }
        });

        xhr.onload = function() {
            uploadStatus.textContent = `开始上传`;

            const progressItems = galleryElements.uploadResults.querySelectorAll('.p-4');
            const progressItem = progressItems[index];

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "error") {
                        galleryHandleUploadError(file, response.message, progressItem);
                    } else {
                        galleryHandleUploadSuccess(response, progressItem);
                    }
                } catch (error) {
                    console.error('解析响应失败:', error);
                    galleryHandleUploadError(file, '解析服务器响应失败', progressItem);
                }
            } else {
                try {
                    const responseData = JSON.parse(xhr.responseText);
                    galleryHandleUploadError(file, responseData.message || '服务器错误', progressItem);
                } catch (parseError) {
                    galleryHandleUploadError(file, `服务器错误 (${xhr.status})`, progressItem);
                }
            }
        };

        xhr.onerror = function() {
            uploadStatus.textContent = `开始上传`;
            const progressItems = galleryElements.uploadResults.querySelectorAll('.p-4');
            const progressItem = progressItems[index];
            galleryHandleUploadError(file, '网络错误，请重试', progressItem);
        };

        xhr.send(formData);
    });
}

// 处理上传成功
function galleryHandleUploadSuccess(response, progressItem) {
    if (!response.data || !response.data.url) {
        console.error('上传成功但缺少URL:', response);
        return;
    }
    const newItem = {
        id: response.data.id || Date.now(),
        name: response.data.name,
        url: response.data.url,
        size: response.data.size || 0,
        width: response.data.width || 0,
        height: response.data.height || 0
    };

    // 添加到图库列表
    galleryState.mediaItems.unshift(newItem);
    galleryState.totalItems = galleryState.mediaItems.length;
    fetchGalleryMediaList();
    showMessage('图片上传成功', 'success');
    setTimeout(() => {
        galleryElements.uploadModal.classList.add('hidden');
        galleryElements.uploadModal.classList.remove('flex');
        galleryElements.uploadResults.innerHTML = '';
        const fileInput = document.getElementById('gallery-upload-input');
        if (fileInput) {
            fileInput.value = '';
        }
    }, 2000);
}

// 处理上传失败
function galleryHandleUploadError(file, message, progressItem) {
    progressItem.innerHTML = `
        <div class="flex justify-between items-start mb-1">
            <span class="font-medium text-red-800 text-sm">${file.name}</span>
            <span class="text-red-600 text-xs">失败</span>
        </div>
        <p class="text-red-700 text-sm">${message}</p>
        <button data-action="retry-upload"
            class="mt-2 text-xs px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
            重试
        </button>
    `;
}

// 预览图片
function galleryPreviewFile(url) {
    galleryElements.previewImage.src = url;
    galleryElements.previewImage.alt = '图片预览';
    galleryElements.previewModal.classList.remove('hidden');
    galleryElements.previewModal.classList.add('flex');
}

// 复制到剪贴板
function galleryCopyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showMessage('链接已复制', 'success');
    }).catch(err => {
        console.error('无法复制文本: ', err);
        showMessage('复制失败，请手动复制', 'error');
    });
}


// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', initGallery);

// 暴露全局函数（如果需要）
window.fetchGalleryMediaList = fetchGalleryMediaList;
</script>