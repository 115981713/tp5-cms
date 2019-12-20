<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    /* 模板相关配置 */
    'view_replace_str' => array(
        '__LIB__'    => __ROOT__ . '/theme/index/lib',
        '__IMG__'    => __ROOT__ . '/theme/index/img',
        '__CSS__'    => __ROOT__ . '/theme/index/css',
        '__JS__'     => __ROOT__ . '/theme/index/js',
        '__TPL__' => '../template/',
    ),
    'template'               => [
        // 预先加载的标签库
        'taglib_pre_load'     =>    'app\common\taglib\Zz'
    ],
    'http_exception_template'    =>  [
        // 定义404错误的重定向页面地址
        404 =>  APP_PATH . 'index/view/404.html'
    ]
];
