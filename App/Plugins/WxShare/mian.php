<?php
/**
 * Plugin Name: WxShare
 * Description: 微信分享卡片的创建。可动态调态分享后的卡片跳转的地址。
 * Version: 1.0.0
 * Author: JuheDev
 * Plugin URL: https://plugins.juhe.me/wxshare
 */

return [

    'menus' => [
        [
            'title' => '微信分享',
            'icon' => 'fa fa-share-alt',
            'path' => '/admin/wxshare/',

        ],
    ],

    'route_group' => [
        [
            'prefix' => '/wxshare',
            'namespace' => 'Plugins\WxShare\Controllers\Web',
            'routes' => [
                ['GET', '/{code}', 'WxShareController@index'],
            ],
        ],
        [
            'prefix' => '/admin/wxshare',
            'namespace' => 'Plugins\WxShare\Controllers\Admin',
            'routes' => [
                ['GET', '/', 'WxShareController@index'],
                ['GET', '/get/{id}', 'WxShareController@get'],
                ['GET', '/list', 'WxShareController@list'],
                ['POST', '/delete/{id}', 'WxShareController@delete'],
                ['POST', '/update', 'WxShareController@update'],
                ['GET|POST', '/settings', 'WxShareController@settings'],
            ],
        ],
    ],
    'tables' => ['wxshare_list', 'wxshare_settings'],
    'init' => function () {},

    'activate' => function ($db) {
        $db->query("
            CREATE TABLE IF NOT EXISTS `wxshare_list` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(100) NOT NULL COMMENT '名称',
                `code` varchar(20) NOT NULL COMMENT '分享编码',
                `views` int(11) NOT NULL DEFAULT 0 COMMENT '访问次数',
                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1-启用，0-禁用',
                `share_title` varchar(255) DEFAULT NULL COMMENT '分享标题',
                `share_desc` varchar(255) DEFAULT NULL COMMENT '分享描述',
                `share_img` varchar(500) DEFAULT NULL COMMENT '分享封面图URL',
                `share_link` text NOT NULL COMMENT '跳转地址',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信分享链接管理表';
        ");


        $db->query("
            CREATE TABLE IF NOT EXISTS `wxshare_settings` (
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