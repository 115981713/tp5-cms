<?php
// +----------------------------------------------------------------------
// | 网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\validate\C1 as C1Validate;
use think\Db;


class C1 extends Base
{
    /**
     * 抽奖人员首页
     * @return none
     */
    public function users(){
        $name = trim(input('get.name'));
        $map = [];
        if ($name) {
            $map['name'] = array('like', "%$name%");
        }
        $lists = db('c1_user') ->where($map)-> paginate(config('LIST_ROWS'),false,['query' => request()->param()]);
        $this->ifPageNoData($lists);
        $page = $lists -> render();

        $this -> assign('lists', $lists);
        $this -> assign('page', $page);
        
        return $this->fetch();
    }    

    /**
     * 抽奖列表首页
     * @return none
     */
    public function win(){
        $name = trim(input('get.level_name'));
        $map = [];
        if ($name) {
            $map['level_name'] = array('like', "%$name%");
        }
        $lists = db('c1_win_level') ->where($map)->order('sort desc')-> paginate(config('LIST_ROWS'),false,['query' => request()->param()]);
        $this->ifPageNoData($lists);
        $page = $lists -> render();

        $this -> assign('lists', $lists);
        $this -> assign('page', $page);
        
        return $this->fetch();
    }

    /**
     * 新增人员
     */
    public function add() {
        if ( request() -> isPost()) {
            $data = $_POST;
            //验证
            $Validate = new c1Validate();
            if (!$Validate -> check($data)) {
                $this -> error($Validate -> getError());
            }
            $info=db('c1_user')->where('name',$data['name'])->find();
            if ($info) {
                $this->error('该姓名已添加！');
            }
            //添加到banner表$Data
            $Data = array();

            $Data['name'] = $data['name'];
            $Data['type'] = $data['type'];
            $Data['company'] = $data['company'];
            
            $re1 = db('c1_user') -> insertGetId($Data);
            if ($re1) {
                $this -> success('新增成功');
            } else {
                $this -> error('新增失败');
            }
        } else {
            return $this -> fetch();
        }
    }    

    /**
     * 新增抽奖
     */
    public function add_win() {
        if ( request() -> isPost()) {
            $data = $_POST;
            //验证
            $Validate = new c1Validate();
            if (!$Validate -> check($data)) {
                $this -> error($Validate -> getError());
            }
            //添加到banner表$Data
            $Data = array();

            $Data['level_name'] = $data['level_name'];
            $Data['num'] = $data['num'];
            $Data['show_time'] = $data['show_time'];
            $Data['sort'] = $data['sort'];
            
            $re1 = db('c1_win_level') -> insertGetId($Data);
            if ($re1) {
                $this -> success('新增成功');
            } else {
                $this -> error('新增失败');
            }
        } else {
            return $this -> fetch();
        }
    }    

    /**
     * 编辑抽奖
     */
    public function edit_win($id=0) {
        $info=db('c1_win_level')->find($id);
        if(!$info){
            $this->error('该记录不存在或已删除！');
        }
        if ( request() -> isPost()) {
            $data = $_POST;
            //验证
            $Validate = new c1Validate();
            if (!$Validate -> check($data)) {
                $this -> error($Validate -> getError());
            }
            //添加到banner表$Data
            $Data = array();

            $Data['id'] = $id;
            $Data['level_name'] = $data['level_name'];
            $Data['num'] = $data['num'];
            $Data['show_time'] = $data['show_time'];
            $Data['sort'] = $data['sort'];
            
            $re1 = db('c1_win_level')->update($Data);
            if ($re1) {
                $this -> success('编辑成功');
            } else {
                $this -> error('编辑失败');
            }
        } else {
            $this->assign('id',$id);
            
            $this->assign('info',$info);
            return $this -> fetch();
        }
    }

    /**
     * 编辑人员
     */
    public function edit($id = 0){
    	$info=db('c1_user')->find($id);
		if(!$info){
			$this->error('该记录不存在或已删除！');
		}
        if(request()->isPost()){
            $data=$_POST;
            $Validate=new c1Validate();
            if (!$Validate->check($data)) {
                $this->error($Validate->getError());
            }

            $info = db('c1_user')->where('id','<>',$id)->where('name',$data['name'])->find();
            if ($info) {
                $this->error('该姓名已添加！');
            }
			$DataArr = array();

            $DataArr['id'] = $id;
            $DataArr['name'] = $data['name'];
            $DataArr['type'] = $data['type'];
            $DataArr['company'] = $data['company'];
           
            $re=db('c1_user')->update($DataArr);
            if($re){
                $this->success('编辑成功','users');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $this->assign('id',$id);
            
            $this->assign('info',$info);
            $this->meta_title = '编辑banner';
            return $this->fetch();
        }
    }    

    /**
     * 抽奖首页
     */
    public function title(){
        $info=db('setting')->where('k','c1_title')->find();
        if(!$info){
            $this->error('该记录不存在或已删除！');
        }
        if(request()->isPost()){
            $data=$_POST;
            $DataArr = array();

            $DataArr['id'] = $data['id'];
            $DataArr['value'] = $data['value'];
           
            $re=db('setting')->update($DataArr);
            if($re){
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $this->assign('id',$info['id']);
            
            $this->assign('info',$info);
            return $this->fetch();
        }
    }    

    /**
     * 抽奖首页解释
     */
    public function top_title(){
        $info=db('setting')->where('k','c1_top_title')->find();
        if(!$info){
            $this->error('该记录不存在或已删除！');
        }
        if(request()->isPost()){
            $data=$_POST;
            $DataArr = array();

            $DataArr['id'] = $data['id'];
            $DataArr['value'] = $data['value'];
           
            $re=db('setting')->update($DataArr);
            if($re){
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $this->assign('id',$info['id']);
            
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**
     * 删除抽奖
     */
    public function del_win(){
        $id = input('id');
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        if(db('c1_win_level')->delete($id)){
            //添加行为记录
            action_log("c1_win_del","c1",$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }    

    /**
     * 删除user
     */
    public function del(){
        $id = input('id/a');
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        if(db('c1_user')->delete($id)){
            //添加行为记录
            action_log("c1_del","c1",$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 设置为未抽奖
     */
    public function status_one(){
        $id = input('id');
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $win = db('c1_win')->where('user_id',$id)->find();

        if(db('c1_win')->delete($win['id'])){
            db('c1_user')->where('id',$id)->update(['status'=>0]);
            //添加行为记录
            // action_log("c1_del","c1",$id,UID);
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * 菜单排序
     */
    public function sort(){
        if (request()->isPost()){
            $data['id']=input('id');
            $data['sort']=input('sort');

            $BannerValidate=new BannerValidate();
            if (!$BannerValidate->scene('sort')->check($data)) {
                $this->error($BannerValidate->getError());
            }
            $res=db('admin_menu')->update($data);
            if($res){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log("banner_sort","admin_menu",$data['id'],UID);
                $this->success('排序修改成功！');
            }else{
                $this->error('排序修改失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

    /**
     * 显示隐藏菜单
     */
    public function hide(){
        if (request()->isPost()){
            $data['id']=input('id');
            $data['hide']=input('val');

            if($data['hide']==1){
				//隐藏
                $banner_status="banner_status_yin";
            }
            if($data['hide']==0){
				//显示
                $banner_status="banner_status_xian";
            }

            $res=db('admin_menu')->update($data);

            if($res){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log($banner_status,"admin_menu",$data['id'],UID);
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
