<?php
namespace App\Controllers\Web;

use \App\Core\WebBaseController;

class HomeController extends WebBaseController {
public function index() {
    include __DIR__ . '/../../Views/Web/index.php';
/*    echo '
    <div style="min-height: 100vh; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; padding: 1rem; box-sizing: border-box;">
        <div style="max-width: 28rem; width: 100%; background-color: #ffffff; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 1.5rem; sm:padding: 2rem;">
                <div style="width: 4rem; height: 4rem; background-color: rgba(59, 130, 246, 0.1); border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <i class="fas fa-home" style="color: #3b82f6; font-size: 2rem;"></i>
                </div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; text-align: center; margin-bottom: 1rem;">默认首页</h2>
                <p style="color: #6b7280; text-align: center; margin-bottom: 1.5rem;">
                    这是系统提供的默认首页；如果插件中需要调用到首页，可在主路由文件中删除默认路由即可！
                </p>
                <div style="display: flex; justify-content: center;">
                    <a href="/admin" style="display: inline-flex; items-center; padding: 0.5rem 1rem; border: none; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); color: #ffffff; background-color: #3b82f6; text-decoration: none; transition: background-color 0.2s ease-in-out;">
                        <i class="fas fa-puzzle-piece" style="margin-right: 0.5rem;"></i>进入后台
                    </a>
                </div>
            </div>
        </div>
    </div>';*/
}
    
    
}
