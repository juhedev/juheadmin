<?php
namespace Db;
require_once __DIR__ . '/Medoo.php';

use Medoo\Medoo;

class Database {
    public $medoo;

    public function __construct() {
        $configFile = __DIR__ . '/config.php';

        if (!file_exists($configFile)) {
            // 配置文件不存在，不报错
            $this->medoo = null;
            return;
        }

        $config = require $configFile;

        $this->medoo = new Medoo([
            'type' => 'mysql',
            'database' => $config['db_name'],
            'server' => $config['db_host'],
            'username' => $config['db_user'],
            'password' => $config['db_pass'],
            'charset' => 'utf8mb4',
        ]);
    }

    public function __call($method, $args) {
        if ($this->medoo === null) {
            throw new \Exception('Database not initialized. Please run the installer.');
        }
        return $this->medoo->$method(...$args);
    }
}
