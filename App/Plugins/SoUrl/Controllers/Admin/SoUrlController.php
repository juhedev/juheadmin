<?php

namespace Plugins\SoUrl\Controllers\Admin;

use App\Core\PluginBaseController;

class SoUrlController extends PluginBaseController {
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
        $settings = $this->db->get('sourl_settings', '*', [
            "ORDER" => ["id" => "ASC"],
            "LIMIT" => 1
        ]) ?: []; // 如果没有数据，返回空数组

        // 处理域名逻辑
        $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
        $useDomain = !empty($settings['domain']) ? $settings['domain'] : $currentDomain;
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $fullDomain = "{$protocol}://{$useDomain}";

        // 传数据给视图（统一封装在data中）
        $this->renderPluginView('SoUrl', 'Admin/index.php', [
            'data' => [
                'settings' => $settings,       // 配置信息
                'domain' => $fullDomain   // 带协议的完整域名
            ],
            'title' => '短链管理中心'
        ]);
    }

    public function list() {
        $this->checkLogin(); // 登录保护

        // 获取分页参数
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pageSize = isset($_GET['page_size']) ? max(1, min(100, intval($_GET['page_size']))) : 10;
        $offset = ($page - 1) * $pageSize;

        // 1. 获取总记录数
        $totalItems = $this->db->count('sourl_list', '*');
        
        // 2. 获取当前页数据
        $shortlinks = $this->db->select('sourl_list', '*', [
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
        
        $row = $this->db->get('sourl_list', '*', ['id' => $id]);

        if ($row) {
            // 成功响应，包含状态和数据
            echo json_encode([
                'success' => true,
                'data' => $row
            ]);
        } else {
            
            echo json_encode([
                'success' => false,
                'message' => '无效的ID或短链不存在'
            ]);
        }
        exit;
    }

    public function update() {
        // 设置JSON响应头
        header('Content-Type: application/json');
        
        $this->checkLogin();

        // 获取表单数据
        $id = intval($_POST['id'] ?? 0);
        $url = trim($_POST['url'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;


        try {
            // 有ID则更新
            if ($id > 0) {
                $data = [
                    'url' => $url,
                    'name' => $name,
                    'description' => $description,
                    'is_active' => $isActive,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                

                $result = $this->db->update('sourl_list', $data, ['id' => $id]);
                
                if ($result) {
                    $qrcode = $this->db->get('sourl_list', '*', ['id' => $id]);
                    echo json_encode([
                        'success' => true,
                        'message' => '短链更新成功',
                        'data' => $qrcode,
                        'shortUrl' => '/so/' . $qrcode['code']
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => '更新失败，请稍后重试'
                    ]);
                }
            } else {

                $insertId = $this->db->insert('sourl_list', [
                    'code' => $code,
                    'url' => $url,
                    'name' => $name,
                    'description' => $description,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                if ($insertId) {
                    $shortUrl = '/so/' . $code;
                    echo json_encode([
                        'success' => true,
                        'message' => '短链创建成功',
                        'data' => [
                            'id' => $insertId,
                            'code' => $code,
                            'url' => $url,
                            'name' => $name,
                            'description' => $description,
                            'is_active' => $isActive
                        ],
                        'shortUrl' => $shortUrl
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
                $row = $this->db->get('sourl_settings', '*', [
                    "ORDER" => ["id" => "ASC"]
                ]);

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
                $domain   = trim($_POST['domain'] ?? '');
                $isActive = isset($_POST['is_active']) ? 1 : 0;

/*                if (empty($domain)) {
                    echo json_encode([
                        'success' => false,
                        'message' => '域名不能为空'
                    ]);
                    exit;
                }*/

                if ($id > 0) {
                    // 更新
                    $data = [
                        'domain' => $domain,
                        'is_active' => $isActive,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $result = $this->db->update('sourl_settings', $data, ['id' => $id]);

                    if ($result->rowCount() > 0) {
                        $setting = $this->db->get('sourl_settings', '*', ['id' => $id]);
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
                    $insertId = $this->db->insert('sourl_settings', [
                        'domain' => $domain,
                        'is_active' =>1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    if ($insertId) {
                        $setting = $this->db->get('sourl_settings', '*');
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

    public function deletelink($id) {
        $this->checkLogin();
        header('Content-Type: application/json');

        try {
            // 检查是否存在
            $row = $this->db->get('sourl_list', '*', ['id' => $id]);
            if (!$row) {
                echo json_encode([
                    'success' => false,
                    'message' => '要删除的短链不存在'
                ]);
                exit;
            }

            // 执行删除
            $result = $this->db->delete('sourl_list', ['id' => $id]);

            if ($result->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => '短链已删除'
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
