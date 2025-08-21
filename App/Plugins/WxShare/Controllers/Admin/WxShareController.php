<?php
namespace Plugins\WxShare\Controllers\Admin;
use App\Core\PluginBaseController;

class WxShareController  extends PluginBaseController {
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
        $settings = $this->db->get('wxshare_settings', '*') ?: []; // 如果没有数据，返回空数组



        // 传数据给视图（统一封装在data中）
        $this->renderPluginView('WxShare', 'Admin/index.php', [
            'data' => [
                'settings' => '',       // 配置信息
                'domain' => ''   // 带协议的完整域名
            ],
            'title' => '微信卡片分享管理中心'
        ]);
    }

    public function list() {
        $this->checkLogin(); // 登录保护

        // 获取分页参数
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pageSize = isset($_GET['page_size']) ? max(1, min(100, intval($_GET['page_size']))) : 10;
        $offset = ($page - 1) * $pageSize;

        // 1. 获取总记录数
        $totalItems = $this->db->count('wxshare_list', '*');
        
        // 2. 获取当前页数据
        $shortlinks = $this->db->select('wxshare_list', '*', [
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
        
        $row = $this->db->get('wxshare_list', '*', ['id' => $id]);

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

    // 获取表单数据（使用表中定义的字段）
    $id = intval($_POST['id'] ?? 0);
    $share_link = trim($_POST['share_link'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $share_title = trim($_POST['share_title'] ?? '');
    $share_desc = trim($_POST['share_desc'] ?? '');
    $share_img = trim($_POST['share_img'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    // 验证参数
    $errors = [];
    if (!$share_title) $errors[] = '分享标题不能为空';
    if (!$share_link) $errors[] = '分享链接不能为空';
    if (!$code) $errors[] = 'code不能为空';
    if (!$share_desc) $errors[] = '分享描述不能为空';
    if (!$share_img) $errors[] = '分享封面图不能为空';

    if (!empty($errors)) {
        // 返回 JSON 错误信息
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => implode('; ', $errors)
        ]);
        exit;
    }

    try {
        // 有ID则更新
        if ($id > 0) {
            $data = [
                'share_link' => $share_link,  // 原url改为share_link
                'name' => $share_link,
                'code' => $code,
                'share_title' => $share_title, // 新增字段
                'share_desc' => $share_desc,   // 原description改为share_desc
                'share_img' => $share_img,     // 新增字段
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            

            $result = $this->db->update('wxshare_list', $data, ['id' => $id]);
            
            if ($result) {
                $qrcode = $this->db->get('wxshare_list', '*', ['id' => $id]);
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
            $insertId = $this->db->insert('wxshare_list', [
                'share_link' => $share_link,   // 原url改为share_link
                'name' => $share_link,
                'code' => $code,
                'share_title' => $share_title, // 新增字段
                'share_desc' => $share_desc,   // 原description改为share_desc
                'share_img' => $share_img,     // 新增字段
                'status' => 1,
                'views' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($insertId) {
                echo json_encode([
                    'success' => true,
                    'message' => '创建成功',
                    'data' => [
                        'id' => $insertId,
                        'share_link' => $share_link,
                        'name' => $share_link,
                        'code' => $code,
                        'share_title' => $share_title,
                        'share_desc' => $share_desc,
                        'share_img' => $share_img,
                        'status' => $status,
                        'views' => 0
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
                $row = $this->db->get('wxshare_settings', '*');

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

                    $result = $this->db->update('wxshare_settings', $data, ['id' => $id]);

                    if ($result) {
                        $setting = $this->db->get('wxshare_settings', '*', ['id' => $id]);
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
                    $insertId = $this->db->insert('wxshare_settings', [
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
                        $setting = $this->db->get('wxshare_settings', '*');
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
            $row = $this->db->get('wxshare_list', '*', ['id' => $id]);
            if (!$row) {
                echo json_encode([
                    'success' => false,
                    'message' => '要删除的记录不存在'
                ]);
                exit;
            }

            // 执行删除
            $result = $this->db->delete('wxshare_list', ['id' => $id]);

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
