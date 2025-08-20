
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl w-full text-center">
            <div class="mx-auto h-32 w-32 mb-8 flex items-center justify-center rounded-full bg-primary/10">
                <i class="fas fa-cog text-5xl text-primary"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-6">
                系统设置暂未启用
            </h2>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                系统设置页未启用相关设置。您完全可以根据自身需求，通过自定义开发创建系统设置，或在开发插件中自行设计并添加所需的设置项，让系统配置更贴合您的实际业务场景。
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/docs/extension-api" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary/90 transition-colors shadow-sm">
                    <i class="fas fa-book mr-2"></i> 查看开发文档
                </a>
                <a href="/admin/plugins" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <i class="fas fa-puzzle-piece mr-2"></i> 浏览插件
                </a>
            </div>
        </div>
    </main>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // 添加按钮悬停动画
    const buttons = document.querySelectorAll('a[class*="rounded-md"]');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.classList.add('transform', 'scale-105', 'transition-transform', 'duration-200');
        });
        button.addEventListener('mouseleave', function() {
            this.classList.remove('transform', 'scale-105', 'transition-transform', 'duration-200');
        });
    });
});
</script>
    