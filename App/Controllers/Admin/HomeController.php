<?php
namespace App\Controllers\Admin;

use App\Core\AdminBaseController;
use Db\Database;
use App\Core\PluginManager;

class HomeController extends AdminBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->checkLogin();
    }

    public function index() {
        $plugins = $this->pluginManager->getPluginStatusList();

        $this->render('Admin/dashboard.php', [
            'plugins' => $plugins,
            'title' => '控制台'
        ]);
    }

}