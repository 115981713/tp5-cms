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
 * 中文字符截取
 */
function cn_substr($str,$len){
    return mb_substr($str,0,$len,'utf-8');
}
/**
 * 时间戳格式化
 */
function MyDate($ft,$data){
    return date($ft,$data);
}
/**
 * 过滤html标签
 */
function html2text($str){
    return strip_tags($str);
}
/**
 * 获取文章分类的内容
 */
function get_type_content($id){
    $dc=db('document_category_content')->find($id);
    if(!$dc){
        return false;
    }
    return $dc['content'];
}
/**
 * 获取文章分类
 */
function get_document_category_list(){
    //缓存文章菜单
    $docuemtCategory=cache('DATA_DOCUMENT_CATEGORY_LIST');
    if($docuemtCategory===false){
        $docuemtCategoryList=db('document_category')->where('status',1)->order('sort asc')->select();

        //转换，让id作为数组的键
        $docuemtCategory=[];
        foreach ($docuemtCategoryList as $key=>$item){
            //根据栏目类型，生成栏目url
            if((int)$item['type']==0){
                $item['url']=url('article/lists?id='.$item['id']);
            }
            elseif((int)$item['type']==1){
                $item['url']=url('article/lists?id='.$item['id']);
            }
            elseif((int)$item['type']==2){
                $item['url']=$item['link_str'];
            }
            $docuemtCategory[$key]=$item;
        }
        cache('DATA_DOCUMENT_CATEGORY_LIST',$docuemtCategory);
    }
    return $docuemtCategory;
}


/**
 * 获取一个文章分类
 */
function get_document_category($x,$field=false){
    if(!$x){
        return false;
    }

    //获取缓存的文章菜单
    $docuemtCategoryList=get_document_category_list();
	
	$docuemtCategory=false;
	foreach($docuemtCategoryList as $item){
		if($item['id']==$x){
			$docuemtCategory=$item;
			break;
		}
	}

    if($docuemtCategory&&$field){
        return $docuemtCategory[$field];
    }
    else{
        return $docuemtCategory;
    }
}

/**
 * 模板-获取文章分类
 */
function tpl_get_channel($type,$typeid,$row){
    $docmentCategoryList=get_document_category_list();
    $x=1;
    switch($type){
        case 'top':
            //顶级栏目
            $tempArr=[];
            foreach ($docmentCategoryList as $item){
                if($x>$row){
                    break;
                }
                if($item['pid']==0&&$item['display']==1){
                    $x=$x+1;
                    array_push($tempArr,$item);
                }
            }
            return $tempArr;
            break;
        case 'son':
            //子级栏目
            if($typeid){
                $tempArr=[];
                foreach ($docmentCategoryList as $item){
                    if($x>$row){
                        break;
                    }
                    if($item['pid']==$typeid&&$item['display']==1){
                        $x=$x+1;
                        array_push($tempArr,$item);
                    }
                }
                return $tempArr;
            }
            else{
                //如果没有指定栏目id
                //获取当前action 是lists还是detail
                $id=input('id');
                if($id&&request()->action()=="detail"){
                    $id=db('document')->field('category_id')->find($id);
                    $id=$id['category_id'];
                }

                if($id){
                    $dc=get_document_category($id);
                }
                else{
                    $dcName=input('name');
                    if($dcName){
                        return false;
                    }
                    $dclist=get_document_category_list();

                    foreach ($dclist as $item){
                        if($item['name']==$dcName){
                            $dc=$item;
                            break;
                        }
                    }
                }

                //如果没有找到分类，返回
                if(!$dc){
                    return false;
                }

                //读取该栏目上一级栏目下的所有子级
                $mapPid=(int)$dc['pid']===0?$dc['id']:$dc['pid'];
                $tempArr=[];
                foreach ($docmentCategoryList as $item){
                    if($x>$row){
                        break;
                    }
                    if($item['pid']==$mapPid&&$item['display']==1){
                        $x=$x+1;
                        array_push($tempArr,$item);
                    }
                }
                return $tempArr;

            }
            break;
        case 'self':
            $typeid=$typeid?$typeid:input('id');
            $dc=get_document_category($typeid);

            $tempArr=[];
            foreach ($docmentCategoryList as $item){
                if($x>$row){
                    break;
                }
                if($item['pid']==$dc['pid']&&$item['display']==1){
                    $x=$x+1;
                    array_push($tempArr,$item);
                }
            }
            return $tempArr;
            break;

        case 'detail':
            if(request()->action()!="detail"){
                return false;
            }
            $category_id=db('document')->field('category_id')->find(input('id'));
            $dc=get_document_category($category_id);


            $tempArr=[];
            foreach ($docmentCategoryList as $item){
                if($x>$row){
                    break;
                }
                if($item['pid']==$dc['pid']&&$item['display']==1){
                    $x=$x+1;
                    array_push($tempArr,$item);
                }
            }
            return $tempArr;
            break;
    }
}

/**
 * 模板-获取上一篇和下一篇
 */
function tpl_get_prenext($get,$id=false){
    if(!$id){
        $id=input('id');
    }

    if(!$get){
        $get='get';
    }
    //读取当前id的分类栏目
    $curDocument=db('document')->field('id,category_id')->find($id);

    $document=db('document')->where('display',1)->where('status',1)
    ->where($get=='pre'?"id<$id":"id>$id")
    ->where("category_id",$curDocument['category_id'])
    ->field('id,title')->order($get=='pre'?'id desc':'id asc')->find();
	
    if($document){
        $document['url']=url('article/detail?id='.$document['id']);
    }
    else{
        $document['id']=false;
        $document['url']='javascript:void(0)';
        $document['title']='没有了';
    }

    return $document;
}

/**
 * 模板-获取文章列表
 */
function tpl_get_list($orderby,$pagesize,$id,$type,$table='article'){
    //解析orderby
    $orderbyArr=explode(',',$orderby);
    foreach ($orderbyArr as $key=>$item){
        if(!strpos($item,'b.')){
            $orderbyArr[$key]='a.'.$item;
        }
        else{
            $orderbyArr[$key]=$item;
        }
    }
    $orderby=implode(',',$orderbyArr);


    $docmentListModel=db('document')
        ->alias('a')
        ->join(config('database.prefix').'document_category b','a.category_id=b.id','LEFT')
        ->where('a.status',1)->where('a.display',1)->where('b.status',1);

    $selectField='a.*,b.title as category_title';
    if($table){
        $docmentListModel=$docmentListModel->join(config('database.prefix')."document_$table c",'a.id=c.id','LEFT')->where("a.type='$table'");
        $selectField='a.*,b.title as category_title,c.*';
    }

    switch ($type){
        case 'son':
            //根据栏目来检索
            $dc=get_document_category($id);
            $child=$dc['child'];
            if($child){
                $docmentListModel=$docmentListModel->where('a.category_id','in',"$id,$child");
            }
            else{
                $docmentListModel=$docmentListModel->where('a.category_id',$id);
            }

            break;
        case 'self':
            $docmentListModel=$docmentListModel->where('a.category_id',$id);
            break;
        case 'search':
            //搜索
            $kw=input('kw'); //搜索关键词
            $tid=input('cid');//文章分类Id
            if($kw){
                $docmentListModel=$docmentListModel->where('a.title','like',"%$kw%");
            }
            if($tid){
                $docmentListModel=$docmentListModel->where('a.category_id',$tid);
            }
            break;
    }


	
    $docmentListModel=$docmentListModel->field($selectField)->order($orderby);
	$query=request()->query();
	if($query){
		$docmentListModel=$docmentListModel->paginate($pagesize,false,['query' => request()->param()]);
	}
	else{
		$docmentListModel=$docmentListModel->paginate($pagesize);
	}
    $lists=[];
    foreach ($docmentListModel as $key=>$item){
        //根据栏目类型，生成栏目url
        if((int)$item['type']==0){
            $item['url']=url('article/detail?id='.$item['id']);
        }
        elseif((int)$item['type']==1){
            $item['url']=url('article/detail?id='.$item['id']);
        }
        elseif((int)$item['type']==2){
            $item['url']=$item['link_str'];
        }
        $lists[$key]=$item;
    }

    $re=[
        'model'=>$docmentListModel,
        'lists'=>$lists
    ];

    return $re;
}

/**
 * 模板-栏目列表，用于读取列表栏目下的文章
 */
function tpl_get_channel_article_list($typeid,$row,$orderby){
    $map='';
    if(is_numeric($typeid)){
        $map="pid=$typeid";
    }
    else{
        $map="id in($typeid)";
    }
    $dcList=db('document_category')
        ->where('status',1)
        ->where('display',1)
        ->where($map)
        ->limit($row)
        ->order($orderby)
        ->select();

    foreach ($dcList as $key=>$item){
        //根据栏目类型，生成栏目url
        if((int)$item['type']==0){
            $dcList[$key]['url']=url('article/lists?id='.$item['id']);
        }
        elseif((int)$item['type']==1){
            $dcList[$key]['url']=url('article/lists?id='.$item['id']);
        }
        elseif((int)$item['type']==2){
            $dcList[$key]['url']=$item['link_str'];
        }
    }
    return $dcList;
}

/**
 * 模板-友情链接
 */
function tpl_get_friend_link($type,$row){
    $flinkList=cache('DATA_FRIEND_LINK');

    if($flinkList===false){
        $flinkList=db('friend_link')->where('status',1)->order('sort asc')->limit($row)->select();
        cache('DATA_FRIEND_LINK',$flinkList);
    }
    if($type===0){
        return $flinkList;
    }
    $flinkListTemp=[];
    foreach ($flinkList as $key=>$item){
        if($item['image']){
            array_push($flinkListTemp,$item);
        }
    }
    return $flinkListTemp;
}

/**
 * 模板-根据指定的栏目id获取文章列表
 */
function tpl_get_article_list($typeid,$row,$orderby,$table='article'){

    //解析orderby
    $orderbyArr=explode(',',$orderby);
    foreach ($orderbyArr as $key=>$item){
        if(!strpos($item,'b.')){
            $orderbyArr[$key]='a.'.$item;
        }
        else{
            $orderbyArr[$key]=$item;
        }
    }
    $orderby=implode(',',$orderbyArr);

    $docmentListModel=db('document')
        ->alias('a')
        ->join(config('database.prefix').'document_category b','a.category_id=b.id','LEFT')
        ->join(config('database.prefix')."document_$table c",'a.id=c.id','LEFT')
        ->where('a.status',1)->where('a.display',1)->where('b.status',1)->where("a.type='$table'");

    if(is_numeric($typeid)){
        //读取分类，获取其下级
        $dc=get_document_category($typeid);

        if($dc['child']){
            $typeid=$typeid.','.$dc['child'];
        }
    }

    $docmentListModel=$docmentListModel->where('a.category_id','in',$typeid);

    $docmentListModel=$docmentListModel->field('a.*,b.title as category_title,c.*')->limit($row)->order($orderby);

    $dlList=$docmentListModel->select();

    foreach ($dlList as $key=>$item){
        if($item['link_str']){
            $dlList[$key]['url']=$item['link_str'];
        }
        else{
            $dlList[$key]['url']=url('article/detail?id='.$item['id']);
        }
    }

    return $dlList;
}

/**
 * 模板-根据指定的栏目id获取产品列表
 */
function tpl_get_product_list($typeid,$row,$orderby){
    return tpl_get_article_list($typeid,$row,$orderby,'product');
}

/**
 * 模板-根据指定的栏目id获取文章列表
 */
function tpl_get_article($id){


    $docmentModel=db('document')
        ->alias('a')
        ->join(config('database.prefix').'document_category b','a.category_id=b.id','LEFT')
        ->join(config('database.prefix').'document_article c','a.id=c.id','LEFT')
        ->where('a.status',1)->where('a.display',1)->where('a.id',$id)->where("a.type='' or a.type='article'")
        ->field('a.*,b.title as category_title,c.content');

    $doc=$docmentModel->find();

    if(!$doc){
        return false;
    }

    if($doc['link_str']){
        $doc['url']=$doc['link_str'];
    }
    else{
        $doc['url']=url('article/detail?id='.$doc['id']);
    }

    return $doc;
}

/**
 * 模板-获取页面当前位置position
 */
function tpl_get_position($dc){

    //添加当前位置position
    $dclist=get_document_category_list();

    $dcListCount=count($dclist);
    $positionList=[$dc];

    for ($x=$dcListCount-1;$x>=0;$x--){
        $lastDc=$positionList[count($positionList)-1];

        if($lastDc['pid']==0){
            break;
        }
        if($lastDc['pid']==$dclist[$x]['id']){
            array_push($positionList,$dclist[$x]);
        }
    }
    $htmlstr='<a href="/">首页</a>';
    $positionListCount=count($positionList);
    for ($x=$positionListCount-1;$x>=0;$x--){
        $htmlstr=$htmlstr.'><a href="'.$positionList[$x]['url'].'">'.$positionList[$x]['title'].'</a>';
    }
    return $htmlstr;
}

//获取顶级栏目名
function GetTopTypename($id=false)
{
    $id=$id?$id:input('id');
    $dc=get_document_category($id);
    if((int)$dc['pid']===0){
        return $dc['title'];
    }

    return GetTopTypename($dc['pid']);
}

//获取顶级栏目图片
function GetTopTypeimg($id=false)
{
    $id=$id?$id:input('id');
    $dc=get_document_category($id);
    if((int)$dc['pid']===0){
        return $dc['icon'];
    }
    return GetTopTypeimg($dc['pid']);
}
//获取顶级栏目描述
function GetTopDescription($id=false)
{
    $id=$id?$id:input('id');
    $dc=get_document_category($id);
    if((int)$dc['pid']===0){
        return $dc['description'];
    }

    return GetTopDescription($dc['pid']);
}
//获取顶级英文名称
function GetTopTypenameen($id=false)
{
    $id=$id?$id:input('id');
    $dc=get_document_category($id);
    if((int)$dc['pid']===0){
        return $dc['name'];
    }

    return GetTopTypenameen($dc['pid']);
}