<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Index extends Base
{
    /**
     * 后台首页
     */
    public function index()
    {

        $member=db('admin_member')->find(UID);
        $this->assign('member',$member);
        return $this->fetch();
    }
    /**
     * 控制中心
     */
    public function main()
    {
        $where['status']=1;
//        已添加文章
        $articleCount= db('document_article')->count();
        $this->assign('articleCount',$articleCount);

//        已添加文章分类
        $categoryCount= db('document_category')->where($where)->count();
        $this->assign('categoryCount',$categoryCount);
//       后台管理员
        $memberCount= db('admin_member')->where($where)->count();
        $this->assign('memberCount',$memberCount);

//        行为日志
        $actionlogCount= db('admin_action_log')->where($where)->count();
        $this->assign('actionlogCount',$actionlogCount);
        return $this->fetch();
    }
}
