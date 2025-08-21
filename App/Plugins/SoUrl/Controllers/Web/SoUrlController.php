<?php

namespace Plugins\SoUrl\Controllers\Web;

use App\Core\WebBaseController;

class SoUrlController extends WebBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->db = $this->pluginManager->getDB();
    }


public function redirect($code) {
    $row = $this->db->get('sourl_list', '*', ['code' => $code]);

    if ($row) {
        // 检查链接是否处于激活状态
        if ($row['is_active'] != 1) {
            $this->showError($code . ' 此链接已被停用！');
            exit;
        }

        // 若激活，则更新访问量并跳转
        $update = $this->db->update('sourl_list', ['views[+]' => 1 ], ['id' => $row['id'] ]);
        if ($update->rowCount() > 0) {
            header("Location: " . $row['url']);
            exit;
        } 
    } else {
      $this->showError( $code . ' 此链接不存在！');
    }
}

    
}
