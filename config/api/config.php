<?php
return [
    /* 模板相关配置 */
    'view_replace_str' => array(
        '__STATIC__' => __ROOT__ . '/static',
        '__LIB__'    => __ROOT__ . '/theme/admin/lib',
        '__IMG__'    => __ROOT__ . '/theme/admin/img',
        '__CSS__'    => __ROOT__ . '/theme/admin/css',
        '__JS__'     => __ROOT__ . '/theme/admin/js',
    ),
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'admin_dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'admin_dispatch_jump.tpl',

    //分页配置
    'paginate'               => [
        'type'      => 'think\paginator\driver\Layer',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'maxSize'  => 2*1024*1024, //上传的文件大小限制
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'rootPath' => 'uploads/picture', //保存根路径
    ),
    /* 视频上传相关配置 */
    'VIDEO_UPLOAD' => array(
        'maxSize'  => 500*1024*1024, //上传的文件大小限制
        'exts'     => 'mp4,ogg,webm', //允许上传的文件后缀
        'rootPath' => 'uploads/video', //保存根路径
    ),
    /* 文件上传相关配置 */
    'FILE_UPLOAD' => array(
        'maxSize'  => 500*1024*1024, //上传的文件大小限制
        'exts'     => 'jpg,gif,png,jpeg,txt,pdf,doc,docx,xls,xlsx,zip,rar,ppt,pptx', //允许上传的文件后缀
        'rootPath' => 'uploads/file', //保存根路径
    ),

    //用户密码加密字符串
    'UC_AUTH_KEY' => 'Kx"X![4(W+n?;OdD:/%_BF3r1w0fmGyc{8JtHQlM',
    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'zz_admin',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
];
