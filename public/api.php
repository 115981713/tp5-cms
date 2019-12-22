<?php
header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
// +----------------------------------------------------------------------
// | CMS 网站管理系统
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

define('__ROOT__',  '');

// 定义配置文件目录和应用目录同级
define('CONF_PATH', __DIR__.'/../config/');
//绑定模块
define('BIND_MODULE','api');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
