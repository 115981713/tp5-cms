<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

define('__ROOT__',  '');

// 定义配置文件目录和应用目录同级
define('CONF_PATH', __DIR__.'/../config/');
//绑定模块
define('BIND_MODULE','index');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
