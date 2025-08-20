<?php
namespace App\Controllers\Admin;
use App\Core\AdminBaseController;
use Db\Database;

class UserController extends AdminBaseController {
    /**
     * 构造函数 - 验证管理员权限
     */
    public function __construct() {
       $this->checkLogin();
    }

    
    /**
     * 用户列表页面
     */
    public function index() {
         $this->checkAdmin();
        $db = new Database();
        $users = $db->select('users', '*', [
            'ORDER' => ['id' => 'DESC']
        ]);
        $this->render('Admin/user.php', [
            'users' => $users,
            'title' => '用户管理'
        ]);
    }

    /**
     * 获取单个用户数据（用于编辑和详情）
     */
    public function get($id) {
        header('Content-Type: application/json');
        
        if (empty($id) || !is_numeric($id)) {
            echo json_encode([
                'success' => false,
                'message' => '无效的用户ID'
            ]);
            return;
        }

        $db = new Database();
        $user = $db->get('users', '*', [
            'id' => $id
        ]);

        if ($user) {
            // 移除密码字段，避免泄露
            unset($user['password']);
            
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '用户不存在'
            ]);
        }
    }

    public function update() {
        header('Content-Type: application/json');

        $data = $_POST;
        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => '未接收到数据'
            ]);
            return;
        }

        $db = new Database();
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $isEditMode = $id > 0;

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = $data['role'] ?? 'user';
        $status = isset($data['status']) ? (int)$data['status'] : 0;

        if (empty($username) || empty($email)) {
            echo json_encode([
                'success' => false,
                'message' => '用户名和邮箱不能为空'
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => '邮箱格式不正确'
            ]);
            return;
        }

        // 统一查重（排除当前 ID）
        $exists = $db->get('users', '*', [
            'AND' => [
                'OR' => [
                    'username' => $username,
                    'email' => $email
                ],
                'id[!]' => $id
            ]
        ]);

        if ($exists) {
            if ($exists['username'] === $username) {
                $msg = '用户名已存在';
            } elseif ($exists['email'] === $email) {
                $msg = '邮箱已被使用';
            } else {
                $msg = '用户名或邮箱已被占用';
            }
            echo json_encode([
                'success' => false,
                'message' => $msg
            ]);
            return;
        }

        // 准备数据
        $userData = [
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($isEditMode) {
                // 如果密码不为空，更新密码
                if (!empty($data['password'])) {
                    if (strlen($data['password']) < 8) {
                        echo json_encode([
                            'success' => false,
                            'message' => '密码长度至少8位'
                        ]);
                        return;
                    }
                    $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }

                $db->update('users', $userData, ['id' => $id]);
                $message = '用户更新成功';
            } else {
                // 新增时必须有密码
                if (empty($data['password']) || strlen($data['password']) < 8) {
                    echo json_encode([
                        'success' => false,
                        'message' => '密码长度至少8位'
                    ]);
                    return;
                }
                $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $userData['created_at'] = date('Y-m-d H:i:s');

                $id = $db->insert('users', $userData);
                $message = '用户创建成功';
            }

            echo json_encode([
                'success' => true,
                'message' => $message,
                'data' => ['id' => $id]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '操作失败：' . $e->getMessage()
            ]);
        }
    }


    /**
     * 删除用户
     */
    public function delete($id) {
        header('Content-Type: application/json');
        
        if (empty($id) || !is_numeric($id)) {
            echo json_encode([
                'success' => false,
                'message' => '无效的用户ID'
            ]);
            return;
        }

        // 禁止删除自己
        session_start();
        if ($id == $_SESSION['user_id']) {
            echo json_encode([
                'success' => false,
                'message' => '不能删除当前登录用户'
            ]);
            return;
        }

        $db = new Database();
        $userExists = $db->get('users', 'id', [
            'id' => $id
        ]);

        if (!$userExists) {
            echo json_encode([
                'success' => false,
                'message' => '用户不存在'
            ]);
            return;
        }

        try {
            $db->delete('users', [
                'id' => $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => '用户已删除'
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '删除失败：' . $e->getMessage()
            ]);
        }
    }

}
