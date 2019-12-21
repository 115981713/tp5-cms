<?php
// +----------------------------------------------------------------------
// | 网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\validate\AdminMenu as AdminMenuValidate;
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
            $documentValidate = new DocumentValidate();
            if (!$documentValidate -> check($data)) {
                $this -> error($documentValidate -> getError());
            }

            //添加到产品表$documentData；添加到产品附表$dcdata
            $documentData = array();
            $documentExtData = array();

            
            //是否推荐
            $documentData['isrecommend'] = isset($data['isrecommend']) ? 1 : 0;
            //              是否置顶
            $documentData['istop'] = isset($data['istop']) ? 1 : 0;
            //              是否可见
            $documentData['display'] = isset($data['display']) ? 1 : 0;

            $documentData['uid'] = UID;
            $documentData['type'] = 'product';
            $documentData['title'] = $data['title'];
            $documentData['writer'] = db('admin_member') -> where('id', UID) -> value('username');
            $documentData['category_id'] = $data['category_id'];
            $documentData['keywords'] = $data['keywords'];
            $documentData['link_str'] = $data['link_str'];
            if ($data['piclist']) {
                //首图做封面
                $piclistArr = explode(',', $data['piclist']);
                $documentData['cover_path'] = $piclistArr[0];
            }

            $documentData['sort'] = $data['sort'];
            $documentData['description'] = $data['description'];
            $documentData['create_time'] = time();
            $documentData['update_time'] = time();
            $documentData['status'] = 1;
            $re1 = db('document') -> insertGetId($documentData);
            if ($re1) {
                //附表添加数据
                $documentExtData['id'] = $re1;
                //产品附加表数据
                $documentExtData['content'] = $data['content'];
                $documentExtData['piclist'] = $data['piclist'];
                $documentExtData['price'] = $data['price'];
                $documentExtData['market_price'] = $data['market_price'];
                db('document_product') -> insert($documentExtData);
                action_log("document_product_add", "document_article", $re1, UID);
                $this -> success('新增成功', 'index');
            } else {
                $this -> error('新增失败');
            }
        } else {
            //查询产品分类列表
            $whereDocument['status'] = 1;
            $document_category = db('document_category') -> where($whereDocument) -> field('id,title,pid') -> select();
            $document_category=list_to_tree($document_category);
            $document_category=list_to_char_tree($document_category);
            $this -> assign('dclist', $document_category);
            $this -> meta_title = '新增产品';
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
            $adminMenuValidate=new AdminMenuValidate();
            if (!$adminMenuValidate->check($data)) {
                $this->error($adminMenuValidate->getError());
            }
			$data['hide']=isset($data['hide'])?1:0;
            $re=db('admin_menu')->update($data);
            if($re){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log("adminmenu_edit","admin_menu",$data['id'],UID);
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
            action_log("adminmenu_del","admin_menu",$ids,UID);
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

            $adminMenuValidate=new AdminMenuValidate();
            if (!$adminMenuValidate->scene('sort')->check($data)) {
                $this->error($adminMenuValidate->getError());
            }
            $res=db('admin_menu')->update($data);
            if($res){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log("adminmenu_sort","admin_menu",$data['id'],UID);
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
                $adminmenu_status="adminmenu_status_yin";
            }
            if($data['hide']==0){
				//显示
                $adminmenu_status="adminmenu_status_xian";
            }

            $res=db('admin_menu')->update($data);

            if($res){
                session('ADMIN_MENU_LIST',null);
                //                添加行为记录
                action_log($adminmenu_status,"admin_menu",$data['id'],UID);
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
