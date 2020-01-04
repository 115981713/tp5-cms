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
            ->order('sort desc')
            ->select();
        foreach ($list as $k=>$v) {
            $list[$k]['count'] = db('chitu_win')->where(['level_id'=>$v['id']])->count();
        }
        $user = [];
        $user['count'] = db('chitu_user')->where('id','>=',1)->count();
        $user['win_count'] = db('chitu_user')->where('status',1)->count();
        $setting_chitu_title = db('setting')->where('k','chitu_title')->find();
        $setting_chitu_top_title = db('setting')->where('k','chitu_top_title')->find();
        $user['chitu_title'] = $setting_chitu_title['value'];
        $user['chitu_top_title'] = $setting_chitu_top_title['value'];

        $this->out(200,$list,$user);
        
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
        $id = $_POST['id'];
        $level = db('chitu_win_level')->where('id',$id)->find();
        // 全部
        $list = db('chitu_user')
                ->where(['status'=>0])
                ->select();
        shuffle($list);
        // 员工
        $list_staff = db('chitu_user')
                ->where(['type'=>'员工'])
                ->where(['status'=>0])
                ->select();
        shuffle($list_staff);
        // 非员工部分
        $staff_no = [];
        if ($level['is_all'] == 3) {
            $num = $level['num'];

            $pro = 0.3;
            //非员工
            $list_staff_no = db('chitu_user')
                    ->where('type','<>','员工')
                    ->where(['status'=>0])
                    ->select();

            $staff_no_count = count($list_staff_no);
            if ($staff_no_count >0) {
                shuffle($list_staff_no);
                $fect_num = floor($pro*$staff_no_count);

                if ($fect_num < 1) {
                    $fect_num = 1;
                }

                $staff_no = array_slice($list_staff_no,0,$fect_num);

                $staff_no = array_merge($list_staff,$staff_no);
            }
        }

        $arr = [];
        $arr['list_staff'] = $list_staff;
        // 全部员工部分嘉宾
        $arr['staff_no'] = $staff_no;
        
        $this->out(200,$list,$arr);
    }    

    // 抽中N等奖人员列表
    public function WinNList()
    {
        $id = $_POST['id'];
        $level = db('chitu_win_level')->where('id',$id)->find();
        $list = db('chitu_win')
            ->alias('w')
            ->field('w.*,u.name,u.type')
            ->join('chitu_user u','w.user_id=u.id')
            ->where(['w.level_id'=>$id])
            ->select();
        $arr = [];
        $arr[$level['level_name']] = $list;
        
        $this->out(200,$arr);
    }    

    // 全部中奖人员列表
    public function WinAllList()
    {
        $level_arr = [];
        $list = db('chitu_win')
            ->alias('w')
            ->field('w.*,l.level_name,u.name,u.type,u.company')
            ->join('chitu_win_level l','w.level_id=l.id')
            ->join('chitu_user u','w.user_id=u.id')
            ->order('w.level_sort asc')
            ->select();
        foreach ($list as $k=>$v) {
            $level_arr[$v['level_name']][] = $v;
        }
        $this->out(200,$level_arr);
    }        

    // 所有抽奖数据重置
    public function reset_win()
    {
        $res = db('chitu_win')->where('id','>=',1)->delete();
        $res2 = db('chitu_win_level')->where('id','>=',1)->update(['type'=>0]);
        $res3 = db('chitu_user')->where('id','>=',1)->update(['status'=>0]);
        $this->out(200,'win:'.$res.',win_level:'.$res2.',user:'.$res3);
    }    

    // 保存中奖人员
    public function saveWinNList()
    {
        $id = $_POST['id'];
        $list = $_POST['list'];

        // 判断是否抽取过
        $is_win = db('chitu_win_level')->where('id',$id)->find();
        if ($is_win) {
            $type = $is_win['level'];
            $sort = $is_win['sort'];
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
                        'level_id'=>$id,
                        'level_sort'=>$sort,
                    ];
                }

                $res = db('chitu_win')->insertAll($data);
                if ($res) {
                    // 修改奖项状态，人员状态
                    db('chitu_win_level')->where('id',$id)->update(['type'=>1]);
                    db('chitu_user')->where('id','in',implode(',',$user_ids))->update(['status'=>1]);
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

        // 判断是否抽取过
        $is_win = db('chitu_win_level')->where('id',$id)->find();
        if ($is_win) {
            $list = db('chitu_win')->where('level_id',$id)->select();
            $flag = false;
            if ($list) {
                $win_user_list = [];
                foreach ($list as $k=>$v) {
                    $win_user_list[] = $v['user_id'];
                }

                $str_user_ids = implode(',',$win_user_list);
                $res_user = db('chitu_user')->where('id','in',$str_user_ids)->update(['status'=>0]);
                $res_level = db('chitu_win_level')->where('id',$id)->update(['type'=>0]);
                $res_win= db('chitu_win')->where('level_id',$id)->delete();

                if ($res_user && $res_level && $res_win) {
                    $flag = true;
                }

                if ($flag) {
                    $this->out(200,'重新设置成功，请重新抽选！');
                } else {
                    $this->out(400,'服务器错误，请暂停操作！');
                }

            } else {
                $this->out(400,'没有中奖人数！');
            }

        } else {
            $this->out(400,'奖项不存在，请刷新重试！');
        }
    }

    // 验证登录
    function is_login() {
        $value = $_POST['value'];
        $set = db('setting')->where('k','chitu')->find();
        $set_value = $set['value'] ? $set['value'] : '';
        if ($set_value == $value) {
            $this->out(200,'欢迎进入抽奖系统！');
        } else {
            $this->out(400,'口令错误，请重试！');
        }
    }
}