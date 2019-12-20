<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = config('CONFIG_GROUP_LIST');
    return $group?(isset($list[$group])?$list[$group]:''):'';
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = config('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function zz_ucenter_md5($str, $key = 'ZzUserKey'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 灼灼文化 http://www.zhuopro.com
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 数据列表转换为数据树
 */
function list_to_tree($lists,$pid=0,$level=0) {
    $treeList=[];
    foreach ($lists as $item){
        if($item['pid']==$pid){
            $item['level']=$level;
            $childItem=list_to_tree($lists,$item['id'],$level+1);
            if($childItem){
                $item['child']=$childItem;
            }
            array_push($treeList,$item);
        }
    }
    return $treeList;
}

/**
 * 分类转换为数据树（字符数据树）
 */
function list_to_char_tree($lists) {
	if(!is_array($lists)){
		var_dump($lists);
		exit;
	}
	$treeList=[];
	foreach($lists as $key=>$item){
		if($item['level']>0){
			$item['title']="├─".$item['title'];
		}
		
		for($x=1;$x<$item['level'];$x++){
			$item['title']="│  ".$item['title'];
		}
		
		if(isset($item['child'])){
            $treeItem=list_to_char_tree($item['child']);
			unset($item['child']);
			array_push($treeList,$item);
            $treeList=array_merge($treeList,$treeItem);
        }
		else{
			array_push($treeList,$item);
		}
	}
    return $treeList;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的表名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null,$remark=''){

    //参数检查
    if(empty($action) || empty($model) ){
        return '参数不能为空';
    }

    //查询行为,判断是否执行
    $action_info = db('admin_action')->where('name',$action)->find();

    if($action_info['status'] != 1){
        return '该行为被禁用或删除';
    }
	
	if(!is_array($record_id)){
		$record_id=[$record_id];
	}
	
	foreach($record_id as $item){
		//插入行为日志
	    $data['action_id']      =   $action_info['id'];
	    $data['user_id']        =   $user_id;
	    $data['action_ip']      =   request()->ip();
	    $data['model']          =   $model;
	    $data['record_id']      =   $item;
	    $data['create_time']    =   time();
	
	    if($remark){
	        $data['remark'] =$remark;
	    }
	    db('admin_action_log')->insert($data);
	}
}