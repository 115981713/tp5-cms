<?php
// +----------------------------------------------------------------------
// | 网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\validate\Banner as BannerValidate;
use think\db;


class Banner extends Base
{
    /**
     * banner首页
     * @return none
     */
    public function index(){
        $lists   =   db('banner')->order('status desc,updated_at desc')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 新增banner
     */
    public function add() {
        if ( request() -> isPost()) {
            $data = $_POST;
            //验证
            $Validate = new DocumentValidate();
            if (!$Validate -> check($data)) {
                $this -> error($Validate -> getError());
            }

            //添加到banner表$Data
            $Data = array();

            $Data['name'] = $data['name'];
            //是否显示
            $Data['status'] = isset($data['status']) ? 1 : 0;
 

            if ($data['img']) {
                //首图做封面
                $piclistArr = explode(',', $data['img']);
                $Data['img'] = $piclistArr[0];
            }

            $Data['update_time'] = time();

            $re1 = db('banner') -> insertGetId($Data);
            if ($re1) {
                action_log("banner_add", "banner", $re1, UID);
                $this -> success('新增成功', 'index');
            } else {
                $this -> error('新增失败');
            }
        } else {
            return $this -> fetch();
        }
    }

    /**
     * 编辑后台菜单
     */
    public function edit($id = 0){
    	$info=db('admin_menu')->find($id);
		if(!$info){
			$this->error('后台菜单不存在或已删除！');
		}
        if(request()->isPost()){
            $data=$_POST;
            $BannerValidate=new BannerValidate();
            if (!$BannerValidate->check($data)) {
                $this->error($BannerValidate->getError());
            }
			$data['hide']=isset($data['hide'])?1:0;
            $re=db('admin_menu')->update($data);
            if($re){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log("banner_edit","admin_menu",$data['id'],UID);
                $this->success('编辑成功','');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $this->assign('id',$id);
            
            $this->assign('info',$info);
            $this->meta_title = '编辑菜单';
            return $this->fetch();
        }
    }

    /**
     * 删除后台菜单
     */
    public function del(){
        $ids = input('ids/a');

        //判断要删除的数据，是否有子菜单。
        foreach ($ids as $item){
            $child=db('admin_menu')->where('pid',$item)->find();
            if($child){
                $this->error('检测到要删除菜单下，存在子菜单。请删除子菜单后，再执行删除命令!');
                return;
            }
        }

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        if(db('admin_menu')->delete($ids)){
            session('ADMIN_MENU_LIST',null);
            //                添加行为记录
            action_log("banner_del","admin_menu",$ids,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
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
