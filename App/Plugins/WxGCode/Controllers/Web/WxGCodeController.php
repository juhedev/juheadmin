<?php
namespace Plugins\WxGCode\Controllers\Web;
use App\Core\WebBaseController;

class WxGCodeController extends WebBaseController {
    protected $pluginManager;
    protected $db;

    public function __construct() {
        global $pluginManager;
        $this->pluginManager = $pluginManager;
        $this->db = $this->pluginManager->getDB();
    }

   public function index($code) {

        $code = $code;
         include __DIR__ . '/../../Views/Web/index.php';
    }
    public function get($code) {
        // 设置响应内容类型为JSON
        header('Content-Type: application/json');
        
        // 验证code格式 (假设code是字母数字组合)
        if (empty($code) || !preg_match('/^[A-Za-z0-9]+$/', $code)) {
            echo json_encode([
                'success' => false,
                'message' => '无效的活码编码'
            ]);
            exit;
        }
        
        // 查询有效的活码记录 (只查询启用状态的)
        $row = $this->db->get('wxgcode_list', '*', [
            'AND' => [
                'code' => $code,
                'status' => 1 // 只显示启用状态的活码
            ]
        ]);

        if ($row) {
            // 记录访问量
            $this->increaseViewCount($row['id']);
            
            // 检查是否需要切换到备用活码 (如果当前活码达到最大扫码次数)
            if ($row['max_scans'] > 0 && $row['total_scans'] >= $row['max_scans'] && !empty($row['backup_id'])) {
                $backupRow = $this->db->get('wxgcode_list', '*', [
                    'AND' => [
                        'id' => $row['backup_id'],
                        'status' => 1
                    ]
                ]);
                if ($backupRow) {
                    $row = $backupRow;
                }
            }
            
            // 格式化数据
            if (isset($row['created_at'])) {
                $row['created_at'] = date('Y-m-d', strtotime($row['created_at']));
            }
            
            // 返回活码信息
            echo json_encode([
                'success' => true,
                'data' => $row
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '活码不存在或已被禁用'
            ]);
        }

        exit;
    }
    /**
     * 增加活码访问量
     * @param int $id 活码ID
     */
    protected function increaseViewCount($id) {
        // 增加扫码次数
        $this->db->update('wxgcode_list', [
            'total_views[+]' => 1
        ], ['id' => $id]);
    }
    
    /**
     * 刷新二维码
     * @param string $code 活码编码
     */
    public function refreshQrcode($code) {
        header('Content-Type: application/json');
        
        $row = $this->db->get('wxgcode_list', ['id', 'qrcode_url', 'code'], [
            'AND' => [
                'code' => $code,
                'status' => 1
            ]
        ]);
        
        if ($row) {
            // 这里可以添加调用微信接口生成新二维码的逻辑
            // 示例：$newQrcodeUrl = $this->generateNewQrcode($row['id']);
            
            // 简单模拟刷新（实际项目中应替换为真实逻辑）
            $newQrcodeUrl = $row['qrcode_url'] . '?t=' . time();
            
            // 更新数据库中的二维码URL
            $this->db->update('wxgcode_list', [
                'qrcode_url' => $newQrcodeUrl,
                'update_time' => date('Y-m-d H:i:s')
            ], ['id' => $row['id']]);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'qrcode_url' => $newQrcodeUrl
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '刷新失败，活码不存在或已被禁用'
            ]);
        }
        exit;
    }
}
