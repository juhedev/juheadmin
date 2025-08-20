<?php
namespace App\Core;

class AdminBaseController extends BaseController
{
    public function __construct()
    {
        
    }

    // 检测是否是管理员
    protected function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => '您没有权限访问该功能'
                ]);
            } else {
                echo "<script>alert('您没有权限访问该功能');history.back();</script>";
            }
            exit;
        }
    }
}
