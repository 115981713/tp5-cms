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

    /**
     * 奖项详情
     */
    public function level_detail()
    {
        $id = $_POST['id'];
        $list = db('chitu_win_level')
            ->where('id',$id)
            ->find();

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

    // 保存中奖人员
    public function WinNList()
    {
        $id = $_POST['id'];
        $list = $_POST['list'];

        // 判断是否抽取过
        $is_win = db('chitu_win_level')->where('id',$id)->find();
        if ($is_win) {
            $type = $is_win['type'];
            $time = time();
            if (!$list) {
                $this->out(400,'中奖人员不存在，请重试！');
            }

            if ($is_win['type'] == 1) {
                // 已经抽取过
                $this->out(401,'奖项已经结束抽取！');

            } else if($is_win['type'] == 0) {
                // 没有抽取过
                $data = [];
                $user_ids = [];
                foreach ($list as $k=>$v) {
                    $user_ids[] = $v['id'];
                    $data[] = [
                        'user_id' => $v['id'],
                        'type' => $type,
                        'time' => $time,
                        'level_id'=>$id
                    ];
                }

                $res = db('chitu_win')->insertAll($data);
                if ($res) {
                    $this->out(200,'抽奖成功！');
                } else {
                    $this->out(400,'结果保存失败，请重新抽取！');
                }

            }
        } else {
            $this->out(400,'奖项不存在，请刷新重试！');
        }
    }    

    // 覆盖保存中奖人员（重新抽奖）
    public function ReWinNList()
    {
        $id = $_POST['id'];
        $list = $_POST['list'];

        // 判断是否抽取过
        $is_win = db('chitu_win_level')->where('id',$id)->find();
        if ($is_win) {
            $type = $is_win['type'];
            $time = time();
            if (!$list) {
                $this->out(400,'中奖人员不存在，请重试！');
            }
            // 重新抽奖
            $data = [];
            $user_ids = [];
            foreach ($list as $k=>$v) {
                $user_ids[] = $v['id'];
                $data[] = [
                    'user_id' => $v['id'],
                    'type' => $type,
                    'time' => $time,
                    'level_id'=>$id

                ];
            }

            $res = db('chitu_win')->insertAll($data);
            if ($res) {
                $this->out(200,'抽奖成功！');
            } else {
                $this->out(400,'结果保存失败，请重新抽取！');
            }

            
        } else {
            $this->out(400,'奖项不存在，请刷新重试！');
        }
    }
}