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
 * 
 * Class Index
 * @package app\index\controller
 */
class C1 extends Base
{
    /**
     * 首页等级列表
     */
    public function index()
    {
        
        $list = db('c1_win_level')
            ->order('sort desc')
            ->select();
        foreach ($list as $k=>$v) {
            $list[$k]['count'] = db('c1_win')->where(['level_id'=>$v['id']])->count();
        }
        $data = [];
        $data['list'] = $list;
        $all = db('c1_user')->select();
        $count = 0;
        $win_count = 0;
        foreach ($all as $v) {
            if ($v['status'] ==1) {
                $win_count ++;
            }
            $count ++;
        }
        $data['count'] = $count;
        $data['win_count'] = $win_count;
        $setting = db('setting')->where('k="c1_title" OR k="c1_top_title"')->select();
        foreach ($setting as $v) {
            $data[$v['k']] = $v['value'];
        }

        $this->out(200,$data);
        
    }

    // 未中奖人员列表
    public function noWinList()
    {
        
        $list = db('c1_user')
            ->where(['status'=>0])
            ->select();
        
        $this->out(200,$list);
    }    

    // 抽中N等奖人员列表
    public function WinNList()
    {
        $id = $_POST['id'];
        $list = db('c1_win')
            ->where(['level_id'=>$id])
            ->select();
        
        $this->out(200,$list);
    }    

    // 全部中奖人员列表
    public function WinAllList()
    {
        $id = $_POST['id'];
        $list = db('c1_win')
            ->select();

        $arr = [];
        foreach ($list as $k=>$v) {
            $arr[$type][] = $v;
        }
        
        $this->out(200,$arr);
    }
}