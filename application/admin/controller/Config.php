<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\validate\Config as ConfigValiate;

class Config extends Base
{
    /**
     * 配置管理
     */
    public function index(){
        /* 查询条件初始化 */
        $map['status'] = array('gt',-1);

        $group=input('group',0);

        $title=input('title','');
        $this->assign('group_id',$group);
        $this->assign('title', $title);
        if($group){
            $map['group']   =   $group;
        }
        if($title){
            $map['name|title']    =   array('like', '%'.$title.'%');
        }

        $lists   =   db('config')->where($map)->paginate(config('LIST_ROWS'),false,['query' => request()->param()]);
		$this->ifPageNoData($lists);
        $page = $lists->render();
        $this->assign('page', $page);

        $this->assign('group',config('CONFIG_GROUP_LIST'));
        $this->assign('lists', $lists);
        $this->meta_title = '配置管理';
        return $this->fetch();
    }

    public function groups(){

        $id=input('id',1);
        $this->assign('id', $id);


        /* 查询条件初始化 */
        $map['status']  = 1;
        $map['group'] = $id;

        $lists   =   db('config')->where($map)->select();


        $this->assign('lists', $lists);
        $this->assign('group',config('CONFIG_GROUP_LIST'));
        $this->meta_title = '网站设置';
        return $this->fetch();
    }

    //网站设置批量保存设置
    public function save($config){
        if($config && is_array($config)){
            $Config = db('config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        cache('DB_CONFIG_DATA_ADMIN',null);
        cache('DB_CONFIG_DATA_INDEX',null);
        //                添加行为记录
        action_log("config_set","config",0,UID);
        $this->success('保存成功！');
    }

    /**
     * 新增配置
     */
    public function add(){
        if(request()->isPost()){
            $data=$_POST;
            $configValidate=new ConfigValiate();
            if (!$configValidate->check($data)) {
                $this->error($configValidate->getError());
            }
            //判断是否和数组库中的配置重复
            $config=db('config')->where('name',$data['name'])->find();
            if($config){
                $this->error('配置标识重复！');
            }
            $data['create_time']=time();
            $data['update_time']=$data['create_time'];
            $re=db('config')->insertGetId($data);
            if($re){
                cache('DB_CONFIG_DATA_ADMIN',null);
                cache('DB_CONFIG_DATA_INDEX',null);
                //                添加行为记录
                action_log("config_add","config",$re,UID);
                $this->success('新增成功','');
            } else {
                $this->error('新增失败');
            }
        } else {

            $this->assign('pid',input('pid'));
            $this->meta_title = '新增设置';
            return $this->fetch();
        }
    }

    /**
     * 编辑配置
     */
    public function edit($id = 0){
    	$info=db('config')->find($id);
		if(!$info){
			$this->error('配置不存在或已删除！');
		}
        if(request()->isPost()){
            $data=$_POST;
            $configValidate=new ConfigValiate();
            if (!$configValidate->check($data)) {
                $this->error($configValidate->getError());
            }
            $data['update_time']=time();
            $re=db('config')->update($data);
            if($re){
                cache('DB_CONFIG_DATA_ADMIN',null);
                cache('DB_CONFIG_DATA_INDEX',null);
                //                添加行为记录
                action_log("config_edit","config",$data['id'],UID);
                $this->success('编辑成功','');
            } else {
                $this->error('编辑失败');
            }
        } else {
        	
            $this->assign('id',$id);
            
            $this->assign('info',$info);
            $this->meta_title = '编辑设置';
            return $this->fetch();
        }
    }


    /**
     * 删除配置
     */
    public function del(){
        $ids = input('ids/a');

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        if(db('config')->delete($ids)){
            cache('DB_CONFIG_DATA_ADMIN',null);
            cache('DB_CONFIG_DATA_INDEX',null);
            //                添加行为记录
            action_log("config_del","config",$ids,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   I('get.id',1);
        $type   =   C('CONFIG_GROUP_LIST');
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }

    /**
     * 配置排序
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['group']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                cache('DB_CONFIG_DATA_ADMIN',null);
                cache('DB_CONFIG_DATA_INDEX',null);
                //                添加行为记录
                action_log("config_sort","config",$ids,UID);
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
