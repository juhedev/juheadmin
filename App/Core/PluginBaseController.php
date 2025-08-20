<?php
namespace App\Core;

class PluginBaseController extends BaseController {
    /**
     * 渲染插件视图文件
     *
     * @param string $pluginName 插件名称（必须与插件目录一致）
     * @param string $viewFile 视图相对路径，如 'Admin/list.php'
     * @param array $data 传递给视图的变量数组
     */
    protected function renderPluginView($pluginName, $viewFile, $data = []) {
        $viewPath = PLUGIN_PATH . $pluginName . '/Views/' . $viewFile;
        // 调用基类的渲染方法
        $this->render($viewPath, $data);
    }


    
}
