<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\api\controller;
use think\Controller;
use think\Cache;

/**
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Banner extends Base
{
    /**
     * 入口跳转链接
     */
    public function index()
    {
        
        $list = db('banner')
            ->field('id,name,img')
            ->where(['status'=>1])
            ->order('updated_at desc')
            ->select();
           
        $this->out(200,$list);
    }

    public function list()
    {
        $list = Cache::get('banner_list');
        if (!$list) {
            $list = db('banner')
                ->field('id,name,img')
                ->where(['status'=>1])
                ->order('updated_at desc')
                ->select();
            Cache::set('banner_list',$list,3600);
        }
        $this->out(200,$list);
    }
}