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
class Index extends Base
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

    public function ver()
    {
        echo 123456;die;
    }
}