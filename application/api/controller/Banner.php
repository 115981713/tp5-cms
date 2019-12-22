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
        $lists = db('document')
            ->order('create_time desc')
            ->select();
        $data = [
            'code' => '200',
            'list' => $lists
        ];
        echo json_encode($data);die;
    }

    public function list()
    {
        $list = db('banner')
            ->field('id,name,img')
            ->where(['status'=>1])
            ->order('updated_at desc')
            ->select();
        $this->out(200,$list);
    }
}