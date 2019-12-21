<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\index\controller;
use think\Controller;
use think\Url;
use think\Route;

/**
 * 前台父类
 * Class Index
 */
class Base extends Controller
{
    protected function _initialize(){
        /* 读取数据库中的配置 */
        $config =   cache('DB_CONFIG_DATA_INDEX');
        if(!$config){
            $config =   get_db_config(1);
            cache('DB_CONFIG_DATA_INDEX',$config);
        }

        config($config); //添加配置
        //兼容模板标签 include
        define('TPL',config('WEB_TEMPLATE_PATH'));

        //判断是否关闭站点。
        if(!config('WEB_SITE_CLOSE')){
            $this->error('网站暂时关闭！');
        }
        //判断是否开启了伪静态
        if(!config('WEB_REWRITE')){
            Url::root('/index.php');
        }
    }
}