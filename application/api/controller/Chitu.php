<?php
// +----------------------------------------------------------------------
// |  网站管理系统
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\api\controller;
use think\Controller;
use think\Cache;

/**
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Chitu extends Base
{
    /**
     * 首页等级列表
     */
    public function index()
    {
        
        $list = db('chitu_win_level')
            ->order('sort asc')
            ->select();
        foreach ($list as $k=>$v) {
            $list[$k]['count'] = db('chitu_win')->where(['level_id'=>$v['id']])->count();
        }

        $this->out(200,$list);
        
    }

    // 未中奖人员列表
    public function noWinList()
    {
        
        $list = db('chitu_user')
            ->where(['status'=>0])
            ->select();
        
        $this->out(200,$list);
    }    

    // 抽中N等奖人员列表
    public function WinNList()
    {
        $id = $_POST['id'];
        $list = db('chitu_win')
            ->where(['level_id'=>$id])
            ->select();
        
        $this->out(200,$list);
    }    

    // 全部中奖人员列表
    public function WinAllList()
    {
        $id = $_POST['id'];
        $list = db('chitu_win')
            ->select();

        $arr = [];
        foreach ($list as $k=>$v) {
            $arr[$type][] = $v;
        }
        
        $this->out(200,$arr);
    }
}