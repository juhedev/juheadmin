<?php
/**
 * Plugin Name: SoUrl
 * Description: 用来缩短网址链接或者说是跳转到新地址的插件。
 * Version: 1.0.0
 * Author: JuheDev
 * Plugin URL: https://plugins.juhe.me/sourl
 */

return [

    'menus' => [
        [
            'title' => '缩短链接',
            'icon' => 'fa fa-chain',
            'path' => '/admin/sourl',

        ],
    ],

    'route_group' => [
        [
            'prefix' => '/so',
            'namespace' => 'Plugins\SoUrl\Controllers\Web',
            'routes' => [
                ['GET', '/{code}', 'SoUrlController@redirect'],
            ],
        ],
        [
            'prefix' => '/admin/sourl',
            'namespace' => 'Plugins\SoUrl\Controllers\Admin',
            'routes' => [
                ['GET', '/', 'SoUrlController@index'],
                ['GET', '/get/{id}', 'SoUrlController@get'],
                ['GET', '/list', 'SoUrlController@list'],
                ['GET', '/delete/{id}', 'SoUrlController@deletelink'],
                ['POST', '/update', 'SoUrlController@update'],
                ['GET|POST', '/settings', 'SoUrlController@settings'],
            ],
        ],
    ],
    // 插件所需表
    'tables' => ['sourl_list', 'sourl_settings'],
    'init' => function () {
        // 插件初始化，可选
        // require_once __DIR__ . '/helpers.php';
    },

    'activate' => function ($db) {
        $db->query("
            CREATE TABLE IF NOT EXISTS `sourl_list` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `code` varchar(64) NOT NULL,
              `url` text NOT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              `description` text,
              `views` int(6) DEFAULT 0,
              `is_active` tinyint(1) NOT NULL DEFAULT '1',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
                $db->query("
            CREATE TABLE IF NOT EXISTS `sourl_settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `domain` text NOT NULL,
              `is_active` tinyint(1) NOT NULL DEFAULT '1',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    },

    'deactivate' => function ($db) {
        // 插件被停用时执行
    },

];
