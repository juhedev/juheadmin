<?php
namespace App\Controllers\Admin;

use App\Core\AdminBaseController;
use Db\Database;
use App\Core\PluginManager;

class PluginController extends AdminBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->db = $this->pluginManager->getDB();
        $this->checkLogin();
        $this->checkAdmin();

    }

    public function manage() {
        $plugins = $this->pluginManager->getPluginStatusList();
        $icons = $this->pluginManager->getAllPluginIcons();

        foreach ($plugins as &$plugin) {
            $pluginName = $plugin['name']; // 获取插件名
            $plugin['icon'] = $icons[$pluginName] ?? 'fa fa-plug';
        }
        unset($plugin); 

        $this->render('Admin/plugins.php', [
                'plugins' => $plugins,
                'title' => '插件管理'
        ]);
    }

    public function upload() {
        header('Content-Type: application/json');

        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => '插件上传失败']);
            return;
        }

        $zipPath = $_FILES['plugin_zip']['tmp_name'];
        $fileName = basename($_FILES['plugin_zip']['name']);
        $pluginName = pathinfo($fileName, PATHINFO_FILENAME);

        $pluginDir = __DIR__ . '/../../Plugins/' . $pluginName;
        if (is_dir($pluginDir)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => '插件已存在，请先删除或改名']);
            return;
        }

        if (!mkdir($pluginDir, 0777, true) && !is_dir($pluginDir)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '无法创建插件目录']);
            return;
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($pluginDir);
            $zip->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '解压失败']);
            return;
        }

        if (!file_exists($pluginDir . '/mian.php')) {
            $this->deleteFolder($pluginDir); 
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => '无效插件包，缺少 mian.php']);
            return;
        }

        $this->pluginManager->clearCache();
        $plugins = $this->pluginManager->scanPlugins();
        if (!isset($plugins[$pluginName])) {
            $this->deleteFolder($pluginDir); 
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' =>$this->pluginManager->getLastError() ?: "插件缺少必要信息,请检查插件是否规范！"
            ]);
            return;
        }

        echo json_encode(['success' => true, 'message' => '插件上传成功']);
    }


    public function toggleStatus($name) {
        header('Content-Type: application/json');

        $installedPlugins = $this->pluginManager->getInstalledPlugins();
        if (!isset($installedPlugins[$name])) {
            echo json_encode(['success' => false, 'message' => "插件 {$name} 不存在"]);
            return;
        }

        $currentStatus = $installedPlugins[$name]['status'];
        $newStatus = $currentStatus ? 0 : 1;

        $this->db->update('plugins', ['status' => $newStatus], ['name' => $name]);

        $msg = $newStatus ? "插件 {$name} 已启用" : "插件 {$name} 已禁用";
        echo json_encode(['success' => true, 'message' => $msg]);
    }

    public function install($name) {
        header('Content-Type: application/json');

        $allPlugins = $this->pluginManager->getAllPlugins();
        if (!isset($allPlugins[$name])) {
            echo json_encode(['success' => false, 'message' => "插件 {$name} 不存在"]);
            return;
        }

        $result = $this->pluginManager->installPlugin($name);
        if ($result !== true) {
            echo json_encode(['success' => false, 'message' => $result]);
            return;
        }

        $pluginFile = $allPlugins[$name]['path'] . '/mian.php';
        if (file_exists($pluginFile)) {
            $pluginInfo = require $pluginFile;
            if (isset($pluginInfo['activate']) && is_callable($pluginInfo['activate'])) {
                call_user_func($pluginInfo['activate'], $this->db);
            }
        }

        echo json_encode(['success' => true, 'message' => "插件 {$name} 安装成功"]);
    }

    public function uninstall($name) {
        header('Content-Type: application/json');

        $allPlugins = $this->pluginManager->getAllPlugins();
        if (!isset($allPlugins[$name])) {
            echo json_encode(['success' => false, 'message' => "插件 {$name} 不存在"]);
            return;
        }

        $pluginFile = $allPlugins[$name]['path'] . '/mian.php';
        if (file_exists($pluginFile)) {
            $pluginInfo = require $pluginFile;
            if (isset($pluginInfo['deactivate']) && is_callable($pluginInfo['deactivate'])) {
                call_user_func($pluginInfo['deactivate'], $this->db);
            }
        }

        $this->pluginManager->uninstallPlugin($name);
    
        echo json_encode(['success' => true, 'message' => "插件 {$name} 已卸载。"]);
    }


    public function delete($name) {
        if (!$name) {
            return json_encode(['success' => false, 'message' => '插件名称不能为空']);
        }

        $pluginDir = PLUGIN_PATH . $name;

        // 判断插件目录是否存在
        if (!is_dir($pluginDir)) {
            return json_encode(['success' => false, 'message' => '插件目录不存在']);
        }

        // 尝试删除目录
        if ($this->deleteFolder($pluginDir)) {
            echo json_encode(['success' => true, 'message' => "插件 {$name} 已卸载并删除"]);
        } else {
            echo json_encode(['success' => false, 'message' => "插件 {$name} 删除失败，请检查权限或是否被占用"]);
        }
    }


    private function deleteFolder($dir) {
        if (!file_exists($dir)) {
            return true; // 不存在当作删除成功
        }

        // 如果是文件或符号链接，直接删除
        if (is_file($dir) || is_link($dir)) {
            return @unlink($dir);
        }

        // 扫描目录内容
        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                if (!$this->deleteFolder($path)) {
                    return false; // 子目录删除失败
                }
            } else {
                if (!@unlink($path)) {
                    return false; // 删除文件失败
                }
            }
        }

        // 删除目录本身
        return @rmdir($dir);
    }



}
