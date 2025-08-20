<?php
namespace App\Controllers\admin;
use \Db\Database;
class AuthController {
    public function loginPage($error = '') {
        include __DIR__ . '/../../Views/Admin/login.php';
    }

    public function loginSubmit() {
        header('Content-Type: application/json');
        
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        // 检查 JSON 格式
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'success' => false,
                'message' => '提交的数据格式有误'
            ]);
            return;
        }

        // 提取输入参数
        $usernameOrEmail = trim($data['username_or_email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($usernameOrEmail) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => '请输入用户名/邮箱和密码'
            ]);
            return;
        }

        $db = new Database();

        // 查询用户（匹配用户名或邮箱，忽略邮箱大小写）
        $user = $db->get('users', '*', [
            'OR' => [
                'username' => $usernameOrEmail,
                'email' => strtolower($usernameOrEmail)
            ]
        ]);

        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => '用户不存在'
            ]);
            return;
        }

        // 检查是否禁用（假设 status=0 表示禁用）
        if (isset($user['status']) && $user['status'] == 0) {
            echo json_encode([
                'success' => false,
                'message' => '该账户未启用，请联系管理员'
            ]);
            return;
        }

        // 验证密码
        if (!password_verify($password, $user['password'])) {
            echo json_encode([
                'success' => false,
                'message' => '密码错误'
            ]);
            return;
        }

        // 设置会话
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];

        echo json_encode([
            'success' => true,
            'message' => '登录成功，正在跳转...',
            'redirect' => '/admin/dashboard'
        ]);
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    public function registerSubmit() {
        header('Content-Type: application/json');
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $username = trim($data['username'] ?? '');
        $email = strtolower(trim($data['email'] ?? '')); // 统一转小写
        $password = $data['password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';

        if (!$username || !$email || !$password || !$confirm) {
            echo json_encode([
                'success' => false,
                'message' => '请完整填写注册信息'
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

        if ($password !== $confirm) {
            echo json_encode([
                'success' => false,
                'message' => '两次密码输入不一致'
            ]);
            return;
        }

        $db = new Database();

        // 检查用户名或邮箱是否已存在（邮箱忽略大小写）
        $existing = $db->get('users', '*', [
            'OR' => [
                'username' => $username,
                'email' => $email
            ]
        ]);

        if ($existing) {
            if ($existing['username'] === $username) {
                $msg = '用户名已存在';
            } elseif (strtolower($existing['email']) === $email) {
                $msg = '邮箱已存在';
            } else {
                $msg = '用户名或邮箱已存在';
            }
            echo json_encode([
                'success' => false,
                'message' => $msg
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertResult = $db->insert('users', [
            'username' => $username,
            'email' => $email, // 已转小写
            'password' => $hashedPassword,
            'role' => 'user',  // 默认角色
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($insertResult) {
            echo json_encode([
                'success' => true,
                'message' => '注册成功，请登录',
                'redirect' => '/admin/login'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '注册失败，请稍后重试'
            ]);
        }
    }



}
