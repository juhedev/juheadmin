<?php
namespace Plugins\WxShare\Controllers\Web;
use App\Core\WebBaseController;

class WxShareController extends WebBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->db = $this->pluginManager->getDB();
    }

    public function index($code) {
        // 引入 JSSDK 类文件
        $jssdkPath = dirname(__FILE__) . '/../../../WxShare/Controllers/lib/jssdk.php';
        if (!file_exists($jssdkPath)) {
            $this->showError("JSSDK 文件不存在：{$jssdkPath}");
        }
        require_once($jssdkPath); 
            try {
                // 1. 从wxshare_settings表获取公众号配置（假设系统中只有一条配置记录）
                $settings = $this->db->get('wxshare_settings', '*');
                if (empty($settings)) {
                    $this->showError('未找到公众号配置，请先在后台完成设置');
                }

                // 2. 验证必要配置是否存在
                $requiredFields = ['appid', 'appsecret'];
                foreach ($requiredFields as $field) {
                    if (empty($settings[$field])) {
                       $this->showError("公众号配置不完整，缺少：{$field}");
                    }
                }

                // 3. 从wxshare_list表获取token与URL的映射关系
                $share = $this->db->get('wxshare_list', '*', ['code' => $code] );

                // 2. 判断是否存在该类型记录
                if (empty($share)) {
                   $this->showError('未找到' . $code . '的URL配置，请先添加');
                    exit; // 无此类型记录，停止执行
                }

                // 3. 检查该记录是否启用（status = 1）
                if ($share['status'] != 1) {
                    $this->showError('当前' . $code . '的URL未启用，请启用后再使用');
                    exit; 
                }
                if (isset($_GET['rep'])) {
                    // 更新访问次数
                    $this->db->update('wxshare_list', [
                        'views[+]' => 1  
                    ], [
                        'code' => $code,
                        'status' => 1
                    ]);
                    // 跳转
                    header("Location: " . $share['share_link']);
                    exit;
                }
                $url = $share['share_link'];
                $jssdk = new \Plugins\WxShare\Controllers\lib\JSSDK($settings['appid'], $settings['appsecret']);
                $signPackage = $jssdk->GetSignPackage();

            } catch (Exception $e) {
                echo '<script>alert("错误: '. addslashes($e->getMessage()). '"); window.close();</script>';
                exit;
            }

            // 传数据给视图
            include __DIR__ . '/../../Views/Web/index.php';
    }

    public function redirect($code) {
        $row = $this->db->get('wxshare_list', '*', ['code' => $code]);

        if ($row) {
            // 检查链接是否处于激活状态
            if ($row['is_active'] != 1) {
                $this->showError($code . ' 此链接已被停用！');
                exit;
            }

            // 若激活，则更新访问量并跳转
            $update = $this->db->update('wxshare_list', ['views[+]' => 1 ], ['id' => $row['id'] ]);
            if ($update->rowCount() > 0) {
                header("Location: " . $row['share_link']);
                exit;
            } 
        } else {
          $this->showError( $code . ' 此链接不存在！');
        }
    }

}
