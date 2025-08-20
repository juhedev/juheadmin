<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-dark">插件管理</h1>
        <p class="text-gray-500 mt-1">管理、安装和卸载系统插件</p>
    </div>
    <div class="relative">
        <button  class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 btn-effect">
            <a target="_blank" href="https://plugins.juhe.me" >
                <i class="fas fa-th-large"></i>
                <span>插件库</span>
            </a>
        </button>
        <div id="uploadOverlay" class="hidden fixed inset-0 bg-black/50 z-40 transition-opacity duration-300"></div>
        <div id="uploadForm" class="hidden fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-80 bg-white rounded-lg shadow-lg p-4 z-50 border border-gray-200 transition-all duration-300 scale-95 opacity-0">
            <h3 class="font-medium mb-3">上传插件</h3>
            <div class="mb-3">
                <label class="block text-sm text-gray-600 mb-1">选择插件包 (.zip)</label>
                <input type="file" id="pluginZip" accept=".zip" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">支持的格式: .zip</p>
            </div>
            <div class="flex gap-2">
                <button type="button" id="submitUpload" class="flex-1 bg-primary text-white px-3 py-2 rounded-md text-sm btn-effect">
                    确认上传
                </button>
                <button type="button" id="cancelUpload" class="px-3 py-2 border border-gray-300 rounded-md text-sm btn-effect">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<!-- 插件统计卡片 -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 card-shadow hover-lift">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">总插件数</p>
                <h3 class="text-2xl font-bold mt-1">
                    <?=count($plugins) ?>
                </h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <i class="fas fa-puzzle-piece text-primary"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 card-shadow hover-lift">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">已启用</p>
                <h3 class="text-2xl font-bold mt-1">
                    <?=count(array_filter($plugins, function($p) { return $p['status']; })) ?>
                </h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center">
                <i class="fas fa-check text-success"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 card-shadow hover-lift">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">未启用</p>
                <h3 class="text-2xl font-bold mt-1">
                    <?=count(array_filter($plugins, function($p) { return !$p['status']; })) ?>
                </h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-medium/30 flex items-center justify-center">
                <i class="fas fa-times text-gray-medium"></i>
            </div>
        </div>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-lg font-semibold">插件列表</h2>
        <button id="uploadBtn" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg 
            flex items-center gap-2 btn-effect text-sm">
            <i class="fas fa-upload"></i>
            <span>上传插件</span>
        </button>
    </div>
    <?php if (empty($plugins)): ?>
    <div class="p-10 text-center border-b border-gray-200">
        <i class="fas fa-puzzle-piece text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium mb-2">暂无插件</h3>
        <p class="text-gray-500 mb-6">请上传并安装插件来扩展系统功能</p>
        <button id="emptyStateUploadBtn" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 btn-effect mx-auto">
            <i class="fas fa-upload"></i>
            <span>上传插件</span>
        </button>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-200">
        <?php foreach ($plugins as $plugin): ?>
        <div class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex flex-wrap md:flex-nowrap justify-between items-start gap-4">
                <!-- 插件信息 -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="<?= htmlspecialchars($plugin['icon']) ?> text-primary text-xl"></i>
                        <h3 class="font-semibold text-gray-900 truncate">
                            <?= htmlspecialchars($plugin['title']) ?>
                            <span class="text-sm font-normal text-gray-500 ml-2">v<?= htmlspecialchars($plugin['version']) ?></span>
                        </h3>
                        <?php if ($plugin['status']): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            已启用
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <?= $plugin['installed'] ? '已禁用' : '未安装' ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-2">
                        <span class="font-medium">简介:</span>
                        <?= htmlspecialchars($plugin['description']) ?>
                    </p>
                    <p class="text-gray-600 text-sm">
                        <span class="font-medium">URL:</span> 
                        <a href="<?= htmlspecialchars($plugin['url']) ?>" class="text-primary hover:underline">
                            <?= htmlspecialchars($plugin['url']) ?>
                        </a>
                    </p>
                </div>
                
                <!-- 操作按钮 -->
                <div class="flex gap-2 shrink-0">
                    <?php if ($plugin['installed']): ?>
                    <button class="px-3 py-1.5 rounded border text-sm transition-colors btn-effect 
                        <?= $plugin['status'] ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border-green-200 bg-green-50 text-green-700 hover:bg-green-100' ?>" 
                        data-action="toggle" 
                        data-url="/admin/plugins/toggle/<?= $plugin['name'] ?>">
                        <?= $plugin['status'] ? '禁用' : '启用' ?>
                    </button>
                    <button class="px-3 py-1.5 rounded border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm transition-colors btn-effect" 
                        data-action="uninstall" 
                        data-url="/admin/plugins/uninstall/<?= $plugin['name'] ?>" 
                        data-confirm="确定要卸载此插件吗？卸载此插件将会删除所有和此插件有关的数据。此操作不可恢复！">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <?php else: ?>
                    <button class="px-3 py-1.5 rounded border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm transition-colors btn-effect" 
                        data-action="install" 
                        data-url="/admin/plugins/install/<?= $plugin['name'] ?>">
                        安装
                    </button>
                    <button class="px-3 py-1.5 rounded border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm transition-colors btn-effect" 
                        data-action="delete" 
                        data-url="/admin/plugins/delete/<?= $plugin['name'] ?>" 
                        data-confirm="确定要删除此插件安装包吗？">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
    
<div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-5">
    <div class="flex">
        <i class="fas fa-info-circle text-primary mt-0.5 mr-3"></i>
        <div>
            <h3 class="font-medium text-primary mb-2">插件管理说明</h3>
            <ul class="text-sm text-gray-700 space-y-1">
                <li><i class="fas fa-angle-right mr-1 text-primary/70"></i> 插件以ZIP格式上传，系统会自动解压并安装</li>
                <li><i class="fas fa-angle-right mr-1 text-primary/70"></i> 禁用插件不会删除数据，卸载插件将清除所有相关数据</li>
                <li><i class="fas fa-angle-right mr-1 text-primary/70"></i> 未安装的插件可以直接删除安装包，不会影响系统</li>
                <li><i class="fas fa-angle-right mr-1 text-primary/70"></i> 请只安装来自可信来源的插件，以确保系统安全</li>
            </ul>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const url = btn.dataset.url;
            const confirmMsg = btn.dataset.confirm;
            if (confirmMsg && !confirm(confirmMsg)) return;

            try {
                const res = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('请求失败');

                const json = await res.json();
                if (json.success) {
                    showMessage(json.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage(json.message, 'error');
                }
            } catch (e) {
                showMessage(e.message, 'warning');
            }
        });
    });

    // ========= 插件上传弹窗功能 =========
    const uploadBtn = document.getElementById('uploadBtn');
    const emptyStateUploadBtn = document.getElementById('emptyStateUploadBtn');
    const uploadForm = document.getElementById('uploadForm');
    const uploadOverlay = document.getElementById('uploadOverlay');
    const cancelUpload = document.getElementById('cancelUpload');
    const submitUpload = document.getElementById('submitUpload');
    const pluginZip = document.getElementById('pluginZip');

    const showUploadForm = () => {
        uploadOverlay.classList.remove('hidden');
        uploadForm.classList.remove('hidden');
        setTimeout(() => {
            uploadOverlay.classList.add('opacity-100');
            uploadForm.classList.remove('scale-95', 'opacity-0');
            uploadForm.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    };

    const hideUploadForm = () => {
        uploadOverlay.classList.remove('opacity-100');
        uploadForm.classList.remove('scale-100', 'opacity-100');
        uploadForm.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            uploadOverlay.classList.add('hidden');
            uploadForm.classList.add('hidden');
            document.body.style.overflow = '';
            pluginZip.value = '';
        }, 300);
    };

    const handleUpload = () => {
        if (!pluginZip.files.length) return;
        const file = pluginZip.files[0];
        if (!file.name.endsWith('.zip')) return;

        submitUpload.disabled = true;
        submitUpload.textContent = '上传中...';

        const formData = new FormData();
        formData.append('plugin_zip', file);

        fetch('/admin/plugins/upload', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert(data.message || '安装失败');
                    submitUpload.disabled = false;
                    submitUpload.textContent = '安装插件';
                }
            })
            .catch(() => {
                alert('网络错误');
                submitUpload.disabled = false;
                submitUpload.textContent = '安装插件';
            });
    };

    if (uploadBtn) uploadBtn.addEventListener('click', showUploadForm);
    if (emptyStateUploadBtn) emptyStateUploadBtn.addEventListener('click', showUploadForm);
    if (cancelUpload) cancelUpload.addEventListener('click', hideUploadForm);
    if (submitUpload) submitUpload.addEventListener('click', handleUpload);
    if (uploadOverlay) uploadOverlay.addEventListener('click', hideUploadForm);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !uploadForm.classList.contains('hidden')) {
            hideUploadForm();
        }
    });
});
</script>
