<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\validate\Documentcategory as DocumentcategoryValidate;
use app\common\validate\Document as DocumentValidate;
use think\db;

class Documentcategory extends Base {
	/**
	 * 后台文章分类首页
	 * @return none
	 */
	public function index() {
		$map['status'] = array('gt', -1);
		$lists = db('document_category') -> where($map) -> field('id,pid,title,sort,display') -> order('sort asc,id asc') -> select();
		$lists=list_to_tree($lists);
		$this -> assign('listJson', json_encode($lists));
		$lists=$this->list_to_html_tree($lists);
		
		$addData=input('addData');
		$this -> assign('addData', $addData);
		$this -> assign('lists', $lists);
		$this -> meta_title = '文章分类';
		
		return $this -> fetch();
	}

	/**
	 * 新增子分类
	 */
	public function add_category($id = 0) {
		if ( request() -> isPost()) {
			$data = $_POST;
			//验证
			if (empty($data['title'])) {
				$this -> error('请输入分类名称！');
			}
			
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['status'] = 1;
			$content = $data['content'];
			unset($data['content']);
			unset($data['file']);
			$reid = db('document_category') -> insertGetId($data);
			if ($reid) {
				//如果有填写分类内容，将数据保存到分类附表
				$contentData['id'] = $reid;
				$contentData['content'] = $content;
				db('document_category_content') -> insert($contentData);
				
				if((int)$data['pid']!=0){
					//更新上级分类child_id
					$this->edit_category_child_item($data['pid']);
				}
				
				//删除分类缓存
				cache('DATA_DOCUMENT_CATEGORY_LIST', null);
				
				//添加行为记录
				action_log("documentcategory_add", "document_category", $reid, UID);
				$categoryInfo=db('document_category')->field('id,pid')->find($reid);
				cookie('add_docuemtn_category',json_encode($categoryInfo));
				$this -> success('新增成功', 'index');
			} else {
				$this -> error('新增失败');
			}
		} else {
			$categorylist = db('document_category') -> where('status',1) -> field('id,title,status,pid') -> select();
			$categorylist=list_to_tree($categorylist);
			$categorylist=list_to_char_tree($categorylist);
			$this -> assign('dclist', $categorylist);
			$this -> assign('cid', $id);
			$this -> meta_title = '新增子分类';
			return $this -> fetch();
		}
	}

	/**
	 * 编辑文章分类
	 */
	public function edit_category($id) {
		//判断是否为顶级分类
		$categoryInfo = db('document_category') -> where('id', $id) -> find();
		if (!$categoryInfo) {
			$this -> error('分类不存在或已删除！');
		}
		if ( request() -> isPost()) {
			$data = $_POST;
			//验证
			if (empty($data['title'])) {
				$this -> error('请输入分类名称！');
			}

			$data['update_time'] = time();
			$content = $data['content'];
			unset($data['content']);
			unset($data['file']);
			$re = db('document_category') -> update($data);
			if ($re) {
				
				//如果有填写分类内容，将数据保存到分类附表
				//获取内容，看是否存在
				$dcContent = db('document_category_content') -> find($data['id']);
				$contentData['id'] = $data['id'];
				$contentData['content'] = $content;
				if ($dcContent) {
					db('document_category_content') -> update($contentData);
				} else {
					db('document_category_content') -> insert($contentData);
				}
				
				//判断是否更改了上级分类
				if($categoryInfo['pid']!=$data['pid']){
					if((int)$categoryInfo['pid']!=0){
						//更新原分类树
						$this->edit_category_child_item($categoryInfo['pid']);
					}
					
					if((int)$data['pid']!=0){
						//更新现分类树
						$this->edit_category_child_item($data['pid']);
					}
					$categoryEditInfo=['id'=>$categoryInfo['id'],'opid'=>$categoryInfo['pid'],'pid'=>$data['pid']];
					cookie('edit_docuemtn_category',json_encode($categoryEditInfo));
				}
				
				//删除分类缓存
				cache('DATA_DOCUMENT_CATEGORY_LIST', null);
				//添加行为记录
				action_log("documentcategory_edit", "document_category", $id, UID);
				$this -> success('编辑成功', 'index');
			} else {
				$this -> error('编辑失败');
			}
		} else {
			//获取分类列表，这里去除它自己
			$categorylist = db('document_category') -> where('status','gt',-1)->where('id','neq',$id) -> field('id,title,status,pid') -> select();
			$categorylist=list_to_tree($categorylist);
			$categorylist=list_to_char_tree($categorylist);
			$this -> assign('dclist', $categorylist);
			//获取分类信息
			$content = db('document_category_content') -> where('id', $id) -> field('content') -> find();
			$categoryInfo['content'] = $content ? $content['content'] : '';
			$this -> assign('info', $categoryInfo);
			$this -> assign('id', $id);

			$this -> meta_title = '编辑分类';
			return $this -> fetch();
		}
	}

	
	/**
	 * 排序
	 */
	public function sort() {
		if ( request() -> isPost()) {
			$data['id'] = input('id');
			$data['sort'] = input('sort');
			//验证
			$documentcategoryValidate = new DocumentcategoryValidate();
			if (!$documentcategoryValidate -> scene('sort') -> check($data)) {
				$this -> error($documentcategoryValidate -> getError());
			}
			$res = db('document_category') -> update($data);
			if ($res) {
				cache('DATA_DOCUMENT_CATEGORY_LIST', null);
				//添加行为记录
				action_log("documentcategory_sort", "document_category", $data['id'], UID);
				$this -> success('排序修改成功！');
			} else {
				$this -> error('排序修改失败！');
			}
		} else {
			$this -> error('非法请求！');
		}
	}

	/**
	 * 删除分类
	 */
	public function del_category() {

		$ids = input('ids/d');
		if (empty($ids)) {
			$this -> error('请选择要操作的数据!');
		}
		//判断该分类下有没有子分类，有则不允许删除
		$child = db('document_category') -> where(array('pid' => $ids)) -> where('status','gt',-1) -> field('id') -> find();
		if (!empty($child)) {
			$this -> error('请先删除该分类下的子分类');
		}
		//判断该分类下有没有文章
		$categoryWhere['category_id'] = $ids;
		$categoryWhere['status'] = 1;
		$document_list = db('document') -> where($categoryWhere) -> field('id') -> find();
		if (!empty($document_list)) {
			$this -> error('请先删除该分类下的文章');
		}

		$res = db('document_category') -> delete($ids);
		if ($res) {
			//删除分类附表 内容
			db('document_category_content') -> delete($ids);
			//更新上级child_id
			$this->del_category_child_item($ids);
			//清除缓存
			cache('DATA_DOCUMENT_CATEGORY_LIST', null);
			//添加行为记录
			action_log("documentcategory_del", "document_category", $ids, UID);
			$this -> success('删除成功');
		} else {
			$this -> error('删除失败！');
		}
	}

	/**
	 * 显示隐藏文章
	 */
	public function set_display() {
		if ( request() -> isPost()) {
			$data['id'] = input('id');
			$data['display'] = input('val');
			if ($data['display'] == 1) {
				//隐藏
				$setDisplay = "document_category_display_xian";
			}
			if ($data['display'] == 0) {
				//显示
				$setDisplay = "document_category_display_yin";
			}
			$res = db('document_category') -> update($data);
			if ($res) {
				cache('DATA_DOCUMENT_CATEGORY_LIST', null);
				action_log($setDisplay, "document_category", $data['id'], UID);
				$this -> success('操作成功！');
			} else {
				$this -> error('操作失败！');
			}
		} else {
			$this -> error('非法请求！');
		}
	}
	
	
	private function del_category_child_item($cid){
		//获取所有上级
		$lists = db('document_category') -> where("CONCAT(',',child,',') like '%,$cid,%'") -> field('id,child') -> select();
		$sql='';
		$prefix=config('database.prefix');
		foreach($lists as $item){
			$child=explode(',',$item['child']);
			$child=array_diff($child,[$cid]);
			$child=implode(',',$child);
			$id=$item['id'];
			$sql="update ".$prefix."document_category set child='$child' where id=$id;";
			Db::execute($sql);
		}
	}
	
	private function edit_category_child_item($pid){
		//添加一个分类到上级分类中
		$lists = db('document_category') -> where("CONCAT(',',child,',') like '%,$pid,%'")->whereOr('id',$pid) -> field('id,child')->order('pid asc') -> select();
		
		//由上级id排序，可得数组第一个数据为顶级分类
		$topCategory=$lists[0];
		
		//由顶级分类获取其下级的所有id；
		$childLists = db('document_category') -> where("id",'in',$topCategory['child'])->whereOr('pid',$pid) -> field('id,pid') -> select();
		
		$sql='';
		$prefix=config('database.prefix');
		foreach($lists as $item){
			$child=$this->get_category_child_id($childLists,$item['id']);
			$child=implode(',',$child);
			$sql="update ".$prefix."document_category set child='$child' where id=".$item['id'].";";
			Db::execute($sql);
		}
	}
	
	//循环获取列表中每个元素的子元素id
	private function get_category_child_id($lists,$pid){
		$treeList=[];
	    foreach ($lists as $item){
	        if($item['pid']==$pid){
	        	array_push($treeList,$item['id']);

	            $childItem=$this->get_category_child_id($lists,$item['id']);
	            if($childItem){
	                $treeList=array_merge($treeList,$childItem);
	            }
	        }
	    }
	    return $treeList;
	}
	
	/**
	 * 分类转换为数据树（栏目分类页面）
	 */
	private function list_to_html_tree($lists) {
	
		$treeList=[];
		foreach($lists as $key=>$item){
			$item['line']='';
			
			for($x=0;$x<$item['level'];$x++){
				$item['line']='<span class="zz-tree-item-line"></span>'.$item['line'];
			}
	
			if(isset($item['child'])){
				$item['icon']='<span class="zz-tree-icon '.($item['level']>0?'zz-tree-after-line':'').'"><i class="layui-icon zz-tree-ctrl layui-icon-subtraction"></i></span>';
	            $treeItem=$this->list_to_html_tree($item['child']);
				unset($item['child']);
				array_push($treeList,$item);
	            $treeList=array_merge($treeList,$treeItem);
	        }
			else{
				$item['icon']='<span class="zz-tree-icon '.($item['level']>0?'zz-tree-after-line':'').'"><i class="layui-icon zz-tree-sigle layui-icon-file"></i></span>';
				array_push($treeList,$item);
			}
		}
	    return $treeList;
	}
}
