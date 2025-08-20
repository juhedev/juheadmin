<?php
// 系统核心路由定义，供主程序和插件管理器使用
return [
    // 前台首页
    ['GET', '/', '\App\Controllers\Web\HomeController@index'],

    // 系统安装
    ['GET|POST', '/install', '\App\Controllers\Admin\InstallController@index'],

    // 后台登录/注册/登出
    ['GET', '/admin/login', '\App\Controllers\Admin\AuthController@loginPage'],
    ['POST', '/admin/login', '\App\Controllers\Admin\AuthController@loginSubmit'],
    ['GET', '/admin/logout', '\App\Controllers\Admin\AuthController@logout'],
    ['GET', '/admin/register', '\App\Controllers\Admin\AuthController@registerPage'],
    ['POST', '/admin/register', '\App\Controllers\Admin\AuthController@registerSubmit'],

    // 后台首页与设置
    ['GET', '/admin', '\App\Controllers\Admin\HomeController@index'],
    ['GET', '/admin/dashboard', '\App\Controllers\Admin\HomeController@index'],
    ['GET|POST', '/admin/settings', '\App\Controllers\Admin\SettingsController@index'],
    ['POST', '/admin/settings', '\App\Controllers\Admin\SettingsController@save'],

    // 后台用户路由
    ['GET', '/admin/users', '\App\Controllers\Admin\UserController@index'],
    ['GET', '/admin/users/{id}', '\App\Controllers\Admin\UserController@get'],
    ['POST', '/admin/users/delete/{id}', '\App\Controllers\Admin\UserController@delete'],
    ['POST', '/admin/users/update', '\App\Controllers\Admin\UserController@update'],

    // 后台图库路由
    ['GET', '/admin/images', '\App\Controllers\Admin\ImagesController@index'],
    ['GET', '/admin/images/list', '\App\Controllers\Admin\ImagesController@list'],
    ['GET', '/admin/images/settings', '\App\Controllers\Admin\ImagesController@settings'],
    ['POST', '/admin/images/upload', '\App\Controllers\Admin\ImagesController@upload'],
    ['POST', '/admin/images/delete', '\App\Controllers\Admin\ImagesController@delete'],
    // 后台插件路由 
    ['GET', '/admin/plugins', '\App\Controllers\Admin\PluginController@manage'],
    ['GET', '/admin/plugins/{pluginName}', '\App\Controllers\Admin\PluginController@detail'],
    ['GET', '/admin/plugins/toggle/{pluginName}', '\App\Controllers\Admin\PluginController@toggleStatus'],
    ['GET', '/admin/plugins/install/{pluginName}', '\App\Controllers\Admin\PluginController@install'],
    ['GET', '/admin/plugins/uninstall/{pluginName}', '\App\Controllers\Admin\PluginController@uninstall'],
    ['GET', '/admin/plugins/delete/{name}', '\App\Controllers\Admin\PluginController@delete'],
    ['POST', '/admin/plugins/upload', '\App\Controllers\Admin\PluginController@upload'],
    ['POST', '/admin/plugins/save/{pluginName}', '\App\Controllers\Admin\PluginController@save'],
];
