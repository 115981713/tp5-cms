<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\api\controller;
use think\Controller;

/**
 * 应用入口
 * Class Index
 * @package app\api\controller
 */
class Article extends Base
{
    public function lists()
    {
        $dc=false;
        $id=input('id');
        if($id){

            //获取分类信息
            $dc=get_document_category($id);
        }

        //接收name字段,当name不为空的时候，通过name查询分类，一般name会用于伪静态
        $name=input('name');
        if(!$id&&$name){
            $dclist=get_document_category_list();

            foreach ($dclist as $item){
                if($item['name']==$name){
                    $dc=$item;
                    break;
                }
            }
        }

        if(!$dc){
            $this->error('栏目不存在或已删除！');
        }
        $id=$dc['id'];

        //读取列表页模板
        $listTmp='';
        if($dc['type']==0){
            $listTmp=$dc['template_lists'];
        }
        elseif($dc['type']==1){
            $listTmp=$dc['template_index'];
            //如果是单篇栏目，加载内容
            $dcContent=db('document_category_content')->find($id);
            $dc['content']=$dcContent['content'];
        }
        if(!$listTmp){
            $this->error('请指定栏目页模板！');
        }
        trace('列表页模板路径：'.TPL.$listTmp,'debug');
        //文章兼容字段
        $dc['category_id']=$dc['id'];

        //判断seo标题是否存在
        $dc['meta_title']=$dc['meta_title']?$dc['meta_title']:$dc['title'];

        //添加当前页面的位置信息
        $dc['position']=tpl_get_position($dc);

        //输出文章分类
        $this->assign('zzField',$dc);
        $this->assign('id',$id);
        return $this->fetch(config('WEB_TEMPLATE_PATH').$listTmp);
    }
    public function detail($id)
    {
        //获取该文章
        $article=db('document')->where('status',1)->where('id',$id)->find();
        if(!$article){
            $this->error('文章不存在或已删除！');
        }

        //获取该文章内容
        //根据文章类型，加载不同的内容。
        $articleType=$article['type']?$article['type']:'article';
        $articleExt=db('document_'.$articleType)->where('id',$id)->find();
        if(!$articleExt){
            $this->error('文章不存在或已删除！');
        }
        $article=array_merge($article,$articleExt);

        //根据分类id找分类信息
        $dc=get_document_category($article['category_id']);
        if(!$dc){
            $this->error('栏目不存在或已删除！');
        }

        //添加当前页面的位置信息
        $article['position']=tpl_get_position($dc);

        //更新浏览次数
        db('document')->where('id', $article['id'])->setInc('view', 1);

        //读取详情页模板
        $detailTmp=$dc['template_detail'];
        if(!$detailTmp){
            $detailTmp='detail_article.htm';//默认模板名字
        }
        $article['category_title']=$dc['title'];

        //输出文章内容
        $this->assign('zzField',$article);
        $this->assign('id',$id);


        //设置文章的url
        if($article['link_str']){
            $article['url']=$article['link_str'];
        }
        else{
            $article['url']=url('article/detail?id='.$article['id']);
        }


        trace('详情页模板路径：'.TPL.$detailTmp,'debug');
        return $this->fetch(TPL.$detailTmp);
    }

    public function search()
    {
        $kw=input('kw');
        if(!trim($kw)){
            $this->error('请输入搜索关键词');
        }
        $zzField['id']='0';
        $zzField['title']='搜索';
        $zzField['id']='0';
        $zzField['meta_title']='搜索';
        $zzField['keywords']=config('WEB_SITE_KEYWORD');
        $zzField['description']=config('WEB_SITE_DESCRIPTION');
        $zzField['position']='<a href="/">首页</a> > <a>搜索</a>';
        $this->assign('zzField',$zzField);
        $this->assign('LIST_TYPE_SEARCH',true);
        $this->assign('id',0);
        return $this->fetch(TPL.'search.htm');
    }
}