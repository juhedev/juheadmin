<?php
/**
 * Plugin Name: WxGCode
 * Description: 用来创建微信群活码或者其它活码的插件。
 * Version: 1.0.0
 * Author: JuheDev
 * Plugin URL: https://plugins.juhe.me/wxgcode
 */

return [

    'menus' => [
        [
            'title' => '微信活码',
            'icon' => 'fa fa-qrcode',
            'path' => '/admin/wxgcode/',

        ],
    ],

    'route_group' => [
        [
            'prefix' => '/wxgcode',
            'namespace' => 'Plugins\WxGCode\Controllers\Web',
            'routes' => [
                ['GET', '/get/{code}', 'WxGCodeController@get'],
                ['POST', '/data', 'WxGCodeController@data'],
                ['GET', '/{code}', 'WxGCodeController@index'],
            ],
        ],
        [
            'prefix' => '/admin/wxgcode',
            'namespace' => 'Plugins\WxGCode\Controllers\Admin',
            'routes' => [
                ['GET', '/', 'WxGCodeController@index'],
                ['GET', '/get/{id}', 'WxGCodeController@get'],
                ['GET', '/list', 'WxGCodeController@list'],
                ['GET', '/delete/{id}', 'WxGCodeController@delete'],
                ['POST', '/update', 'WxGCodeController@update'],
                ['GET|POST', '/settings', 'WxGCodeController@settings'],
            ],
        ],
    ],
    'tables' => ['wxgcode_list', 'wxgcode_settings'],
    'init' => function () {},

    'activate' => function ($db) {
        $db->query("
            CREATE TABLE IF NOT EXISTS `wxgcode_list` (
                 `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                  `name` varchar(100) NOT NULL COMMENT '活码名称（如“技术交流群活码”）',
                  `code` varchar(30) NOT NULL COMMENT '活码唯一标识（用于生成访问链接）',
                  `qrcode_url` varchar(500) NOT NULL COMMENT '微信群二维码图片URL',
                  `wx_group_name` varchar(100) NOT NULL COMMENT '微信群名称',
                  `wx_group_id` varchar(50) DEFAULT NULL COMMENT '微信群ID（可选）',
                  `total_views` int(11) NOT NULL DEFAULT 0 COMMENT '总访问次数',
                  `total_scans` int(11) NOT NULL DEFAULT 0 COMMENT '总扫码次数',
                  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1-启用，0-禁用',
                  `max_scans` int(11) DEFAULT 0 COMMENT '最大扫码次数（0为无限制）',
                  `max_members` int(11) DEFAULT NULL COMMENT '群最大人数',
                  `current_members` int(11) DEFAULT 0 COMMENT '当前群人数',
                  `is_full` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否满人：1-是，0-否',
                  `expire_time` datetime DEFAULT NULL COMMENT '过期时间（NULL为永久有效）',
                  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序值（用于多群排序）',
                  `description` text DEFAULT NULL COMMENT '备注描述',
                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uk_code` (`code`) COMMENT '活码标识唯一索引',
                  KEY `idx_status_full` (`status`,`is_full`) COMMENT '状态和满人状态索引，用于快速筛选可用群码'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='URL管理表，存储系统中所有需要管理的URL信息';
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS `wxgcode_settings` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `wechat_name` varchar(100) NOT NULL COMMENT '公众号名称',
                `wechat_account` varchar(50) NOT NULL COMMENT '公众号原始ID(gh_xxxx格式)',
                `appid` varchar(50) NOT NULL COMMENT '公众号AppID',
                `appsecret` varchar(100) NOT NULL COMMENT '公众号AppSecret',
                `token` varchar(100) DEFAULT NULL COMMENT '接口调用Token',
                `encoding_aes_key` varchar(100) DEFAULT NULL COMMENT '消息加密密钥',
                `qrcode_url` varchar(255) DEFAULT NULL COMMENT '公众号二维码URL',
                `wechat_type` enum('subscription','service','enterprise','test') NOT NULL DEFAULT 'service' COMMENT '公众号类型',
                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1-启用，0-禁用',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_appid` (`appid`),
                UNIQUE KEY `uk_wechat_account` (`wechat_account`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号设置表，存储公众号相关ID和密钥信息';
        ");
    },

    'deactivate' => function ($db) {},


];