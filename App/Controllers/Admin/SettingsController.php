<?php
namespace App\Controllers\admin;

use App\Core\AdminBaseController;

class SettingsController extends AdminBaseController {
    public function index() {
        session_start();

        $this->render('Admin/settings.php', [
            'title' => '系统设置'
        ]);
    }


}
