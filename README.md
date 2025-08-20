# Juheadmin 企业外贸展示站

![Logo](admin/assets/logo.png)

## 项目简介

Juheadmin 是一套轻量级后管理系统，无需额外依赖。后台初始主要页面仅用户管理、图库管理、插件管理三个页面和三张表。其它功能可通过插件实现。。

特点：

* 前后台分离，简单易用
* 自动安装，无需手动建表
* 宝塔一键部署支持静态访问

---

## 功能列表

| 模块 | 功能说明                  |
| -- | --------------------- |
| 前台 | 展示公司信息、产品列表、文章列表、联系我们 |
| 后台 | 登录后台管理产品、文章、图片、导航菜单等  |
| 通用 | 自动安装、配置数据库、支持静态资源引用   |

---

## 项目目录结构

```
juheadmin/
├── public/                # 对外访问入口（静态 + PHP）
│   ├── index.php          # 前台主页
│   ├── admin/             # 后台入口
│   │   ├── index.php
│   │   └── assets/        # 后台图片、CSS、JS
│   │       ├── logo.png
│   │       └── qr_wechat.png
│   ├── assets/            # 前台静态资源
│   └── install.php        # 自动安装
├── app/                   # ThinkPHP 核心应用目录
├── config/
├── storage/
├── LICENSE
└── README.md
```

---

## 安装部署

### 宝塔部署步骤

1. 上传整个项目到网站根目录
2. 配置伪静态：

   * ThinkPHP 可选择宝塔自带 “ThinkPHP” 伪静态模板
   * 或自定义 `.htaccess` 文件
3. 设置目录权限：

   * `Storage/`、`Db/` 目录需要可写权限
4. 访问安装：

   * 打开浏览器访问 `http://你的域名/install`，填写数据库信息即可完成安装

### 安装完成后

* 前台访问：`http://你的域名/`
* 后台访问：`http://你的域名/admin`

---

## 前台引用图片示例

前台页面中可直接引用后台图片或二维码：

```html
<header>
    <img src="admin/assets/logo.png" alt="LOGO">
    <img src="admin/assets/qr_wechat.png" alt="微信二维码">
</header>
```

说明：

* `logo.png`：后台 Logo
* `qr_wechat.png`：微信二维码

---

## 使用示例

### 后台登录

* 默认管理员账户：请在安装页面设置
* 登录后台后可管理用户、上传图片、安装插件等

### 前台页面

前面页根据你的插件自定义

---

## 截图展示

### 后台管理界面

![后台界面](admin/assets/screenshot_admin.png)

### 前台首页

![前台首页](public/assets/screenshot_home.png)

---

## 许可证

本项目使用 **MIT 许可证**。

```
Copyright (c) 2025 juhedev
```

---

## 联系方式

* 微信二维码请参考 `admin/assets/qr_wechat.png`
* 邮箱：[juhedev@example.com](mailto:juhedev@example.com)
