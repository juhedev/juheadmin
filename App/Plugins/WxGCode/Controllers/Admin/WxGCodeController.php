<?php
namespace Plugins\WxGCode\Controllers\Admin;
use App\Core\PluginBaseController;

class WxGCodeController  extends PluginBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->db = $this->pluginManager->getDB();
    }

    public function index() {
        $this->checkLogin(); // 登录保护

        // 获取配置信息（带默认值，避免空值）
        $settings = $this->db->get('wxgcode_settings', '*') ?: []; // 如果没有数据，返回空数组



        // 传数据给视图（统一封装在data中）
        $this->renderPluginView('WxGCode', 'Admin/index.php', [
            'data' => [
                'settings' => '',       // 配置信息
                'domain' => ''   // 带协议的完整域名
            ],
            'title' => '扫一扫管理中心'
        ]);
    }

    public function list() {
        $this->checkLogin(); // 登录保护

        // 获取分页参数
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pageSize = isset($_GET['page_size']) ? max(1, min(100, intval($_GET['page_size']))) : 10;
        $offset = ($page - 1) * $pageSize;

        // 1. 获取总记录数
        $totalItems = $this->db->count('wxgcode_list', '*');
        
        // 2. 获取当前页数据
        $shortlinks = $this->db->select('wxgcode_list', '*', [
            'ORDER' => ['id' => 'DESC'],
            'LIMIT' => [$offset, $pageSize]
        ]);

        // 3. 计算分页信息
        $totalPages = max(1, ceil($totalItems / $pageSize));

        // 4. 返回JSON格式数据
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'data' => [
                'items' => $shortlinks,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'page_size' => $pageSize,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function get($id) {
        $this->checkLogin();
        // 设置响应内容类型为JSON
        header('Content-Type: application/json');
        
        $row = $this->db->get('wxgcode_list', '*', ['id' => $id]);

        if ($row) {
            // 成功响应，包含状态和数据
            echo json_encode([
                'success' => true,
                'data' => $row
            ]);
        } else {
            
            echo json_encode([
                'success' => false,
                'message' => '无效的ID或记录不存在'
            ]);
        }
        exit;
    }

    public function update() {
    // 设置JSON响应头
    header('Content-Type: application/json');
    
    $this->checkLogin();

    // 获取表单数据（使用前端页面对应的字段名）
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $wx_group_name = trim($_POST['wx_group_name'] ?? '');
    $qrcode_url = trim($_POST['qrcode_url'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $max_scans = intval($_POST['max_scans'] ?? 0);
    $max_members = intval($_POST['max_members'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;


    try {
        // 数据验证
        if (empty($name)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '请输入活码名称'
            ]);
            exit;
        }
        
        if (empty($wx_group_name)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '请输入微信群名称'
            ]);
            exit;
        }
        
        if (empty($qrcode_url)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '请输入群二维码URL'
            ]);
            exit;
        }
        
        if (empty($code)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '活码编码不能为空'
            ]);
            exit;
        }

        // 有ID则更新
        if ($id > 0) {
            $data = [
                'name' => $name,
                'wx_group_name' => $wx_group_name,
                'qrcode_url' => $qrcode_url,
                'code' => $code,
                'max_scans' => $max_scans,
                'max_members' => $max_members,
                'description' => $description,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->db->update('wxgcode_list', $data, ['id' => $id]);
            
            if ($result) {
                $qrcode = $this->db->get('wxgcode_list', '*', ['id' => $id]);
                echo json_encode([
                    'success' => true,
                    'message' => '更新成功',
                    'data' => $qrcode
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => '更新失败，请稍后重试'
                ]);
            }
        } 
        // 无ID则新增
        else {
            // 检查编码是否已存在
            $exists = $this->db->has('wxgcode_list', ['code' => $code]);
            if ($exists) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => '活码编码已存在，请更换'
                ]);
                exit;
            }
            
            $insertId = $this->db->insert('wxgcode_list', [
                'name' => $name,
                'wx_group_name' => $wx_group_name,
                'qrcode_url' => $qrcode_url,
                'code' => $code,
                'max_scans' => $max_scans,
                'max_members' => $max_members,
                'description' => $description,
                'status' => $status,
                'total_views' => 0,  // 对应前端显示的访问量
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($insertId) {
                echo json_encode([
                    'success' => true,
                    'message' => '创建成功',
                    'data' => [
                        'id' => $insertId,
                        'name' => $name,
                        'wx_group_name' => $wx_group_name,
                        'qrcode_url' => $qrcode_url,
                        'code' => $code,
                        'max_scans' => $max_scans,
                        'max_members' => $max_members,
                        'description' => $description,
                        'status' => $status,
                        'total_views' => 0
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => '创建失败，请稍后重试'
                ]);
            }
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '操作失败: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

    public function settings() {
        $this->checkLogin();
        header('Content-Type: application/json');

        $id = intval($_REQUEST['id'] ?? 0);

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // 获取第一条设置记录
                $row = $this->db->get('wxgcode_settings', '*');

                if ($row) {
                    echo json_encode([
                        'success' => true,
                        'data' => $row
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => '设置不存在'
                    ]);
                }

            } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // 获取提交数据
                $wechat_name = trim($_POST['wechat_name'] ?? '');
                $wechat_account = trim($_POST['wechat_account'] ?? '');
                $appid = trim($_POST['appid'] ?? '');
                $appsecret = trim($_POST['appsecret'] ?? '');
                $token = trim($_POST['token'] ?? '');
                $encoding_aes_key = trim($_POST['encoding_aes_key'] ?? '');
                $qrcode_url = trim($_POST['qrcode_url'] ?? '');
                $wechat_type = trim($_POST['wechat_type'] ?? 'service');
                $status = isset($_POST['status']) ? 1 : 0;

                // 验证必填项
                if (empty($wechat_name) || empty($wechat_account) || empty($appid) || empty($appsecret)) {
                    echo json_encode([
                        'success' => false,
                        'message' => '公众号名称、原始ID、AppID和AppSecret为必填项'
                    ]);
                    exit;
                }

                if ($id > 0) {
                    // 更新
                    $data = [
                        'wechat_name' => $wechat_name,
                        'wechat_account' => $wechat_account,
                        'appid' => $appid,
                        'appsecret' => $appsecret,
                        'token' => $token,
                        'encoding_aes_key' => $encoding_aes_key,
                        'qrcode_url' => $qrcode_url,
                        'wechat_type' => $wechat_type,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $result = $this->db->update('wxgcode_settings', $data, ['id' => $id]);

                    if ($result) {
                        $setting = $this->db->get('wxgcode_settings', '*', ['id' => $id]);
                        echo json_encode([
                            'success' => true,
                            'message' => '设置更新成功',
                            'data' => $setting
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => '更新失败或数据无变化'
                        ]);
                    }

                } else {
                    // 新增
                    $insertId = $this->db->insert('wxgcode_settings', [
                        'wechat_name' => $wechat_name,
                        'wechat_account' => $wechat_account,
                        'appid' => $appid,
                        'appsecret' => $appsecret,
                        'token' => $token,
                        'encoding_aes_key' => $encoding_aes_key,
                        'qrcode_url' => $qrcode_url,
                        'wechat_type' => $wechat_type,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    if ($insertId) {
                        $setting = $this->db->get('wxgcode_settings', '*');
                        echo json_encode([
                            'success' => true,
                            'message' => '设置创建成功',
                            'data' => $setting
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => '创建失败，请稍后重试'
                        ]);
                    }
                }

            } else {
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'message' => '只支持 GET 和 POST 请求'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => '操作失败: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function delete($id) {
        $this->checkLogin();
        header('Content-Type: application/json');

        try {
            // 检查是否存在
            $row = $this->db->get('wxgcode_list', '*', ['id' => $id]);
            if (!$row) {
                echo json_encode([
                    'success' => false,
                    'message' => '要删除的记录不存在'
                ]);
                exit;
            }

            // 执行删除
            $result = $this->db->delete('wxgcode_list', ['id' => $id]);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => '记录已删除'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '删除失败，请稍后再试'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => '删除失败: ' . $e->getMessage()
            ]);
        }

        exit;
    }

}
