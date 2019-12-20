<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\common\taglib;
use think\template\TagLib;
class Zz extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'channel'      => ['attr' => 'type,typeid,row,void', 'close' => 1],
        'arclist'=> ['attr' => 'typeid,titlelen,orderby,row,model,void', 'close' => 1],
        'type'=> ['attr' => 'typeid', 'close' => 1],
        'channelartlist'=> ['attr' => 'typeid,row,void', 'close' => 1],
        'list'=> ['attr' => 'orderby,pagesize,model,void', 'close' => 1],
        'prenext'=> ['attr' => 'get,void', 'close' => 1],
        'flink'=> ['attr' => 'type,row,void', 'close' => 1],
        'sql'=> ['attr' => 'sql', 'close' => 1],
        'article'=> ['attr' => 'id,void', 'close' => 1],
        'prolist'=> ['attr' => 'typeid,titlelen,orderby,row,void', 'close' => 1],

    ];


    /**
     * 栏目列表
     */
    public function tagChannel($tag,$content)
    {
        $type=isset($tag['type'])?$tag['type']:'son';
        $typeid=isset($tag['typeid'])?$tag['typeid']:'false';
        $row=isset($tag['row'])?$tag['row']:100;
        $void=isset($tag['void'])?$tag['void']:'field';

        $parse = '<?php ';
        $parse .= '$__LIST__ = '."tpl_get_channel('$type',$typeid,$row);";
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 文章列表
     */
    public function tagArclist($tag,$content)
    {
        $typeid = isset($tag['typeid'])?$tag['typeid']:'$channel[\'id\']';

        if(is_numeric($typeid)){
            $typeid=$typeid;
        }
        //判断是否指定多个栏目id
        elseif(strpos($typeid,',')){
            $typeid="'$typeid'";
        }

        $orderby=isset($tag['orderby'])?$tag['orderby']:'sort asc,create_time desc';
        $row=isset($tag['row'])?$tag['row']:'100';
        $void=isset($tag['void'])?$tag['void']:'field';
        $limit=isset($tag['limit'])?$tag['limit']:false;
        $model=isset($tag['model'])?$tag['model']:'article';

        $row=$limit?$limit:$row;

        $parse = '<?php ';
        $parse .= '$__LIST__ = '."tpl_get_article_list($typeid,'$row','$orderby','$model');";;
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 栏目列表，用于读取列表栏目下的文章
     */
    public function tagChannelartlist ($tag,$content)
    {
        if(!isset($tag['typeid'])){
            return '';
        }
        $typeid = $tag['typeid'];

        $orderby=isset($tag['orderby'])?$tag['orderby']:'sort asc,create_time desc';
        $row=isset($tag['row'])?$tag['row']:'100';
        $void=isset($tag['void'])?$tag['void']:'channel';


        $parse = '<?php ';
        $parse .= '$__LIST__ = '."tpl_get_channel_article_list('$typeid',$row,'$orderby');";
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagType($tag,$content)
    {
        if(!isset($tag['typeid'])){
            return '';
        }
        $typeid = $tag['typeid'];

        $parse = '<?php ';
        $parse .= '$__LIST__ =[];array_push($__LIST__,get_document_category('.$typeid.'));';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="field"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }


    /**
     * 列表分页
     */
    public function tagList($tag,$content)
    {
        $orderby=isset($tag['orderby'])?$tag['orderby']:'sort asc,create_time desc';
        $pagesize=isset($tag['pagesize'])?$tag['pagesize']:15;
        $type=isset($tag['type'])?$tag['type']:'son';
        $typeid=isset($tag['typeid'])?$tag['typeid']:'$id';
        $void=isset($tag['void'])?$tag['void']:'field';
        $model=isset($tag['model'])?$tag['model']:'';


        $parse = '<?php ';
        $parse .= '$__FUN__ ='."tpl_get_list('$orderby',$pagesize,$typeid,".'isset($LIST_TYPE_SEARCH)?"search":'."'$type','$model');";
        $parse .= '$__LIST__ =$__FUN__["lists"];$pager = $__FUN__["model"]->render();';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 详情页 上一篇 下一篇
     */
    public function tagPrenext($tag,$content)
    {
        $get=isset($tag['get'])?$tag['get']:'pre';
        $id=isset($tag['id'])?$tag['id']:'false';
        $void=isset($tag['void'])?$tag['void']:'field';

        $parse = '<?php ';
        $parse .= '$__LIST__ =[];array_push($__LIST__,'."tpl_get_prenext('$get',$id));";
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 友情链接
     */
    public function tagFlink($tag,$content)
    {
        $type=isset($tag['type'])?$tag['type']:'text';
        $type=$type=='text'?0:1;
        $row=isset($tag['row'])?$tag['row']:100;
        $void=isset($tag['void'])?$tag['void']:'field';

        $parse = '<?php ';
        $parse .= '$__LIST__ ='."tpl_get_friend_link($type,$row);";
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 执行SQL
     */
    public function tagSql($tag,$content)
    {
        if(!isset($tag['sql'])){
            return '';
        }
        $sql=$tag['sql'];
        $parse = '<?php ';
        $parse .= '$__LIST__ ='."db()->query(\"$sql\");";
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="field"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 获取单篇文章
     */
    public function tagArticle($tag,$content)
    {
        if(!isset($tag['id'])){
            return '';
        }
        $void=isset($tag['void'])?$tag['void']:'field';
        $parse = '<?php ';
        $parse .= '$__LIST__ =[];array_push($__LIST__,tpl_get_article('.$tag['id'].'));';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 文章列表
     */
    public function tagProlist($tag,$content)
    {
        $typeid = isset($tag['typeid'])?$tag['typeid']:'';

        if(is_numeric($typeid)){
            $typeid=$typeid;
        }
        //判断是否指定多个栏目id
        elseif(strpos($typeid,',')){
            $typeid="'$typeid'";
        }
        else{
            $typeid='$channel[\'id\']';
        }

        $orderby=isset($tag['orderby'])?$tag['orderby']:'sort asc,create_time desc';
        $row=isset($tag['row'])?$tag['row']:'100';
        $void=isset($tag['void'])?$tag['void']:'field';
        $limit=isset($tag['limit'])?$tag['limit']:false;

        $row=$limit?$limit:$row;

        $parse = '<?php ';
        $parse .= '$__LIST__ = '."tpl_get_product_list($typeid,'$row','$orderby');";;
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$void.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
}