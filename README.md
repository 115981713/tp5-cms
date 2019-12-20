HulaCWMS V1.4.6
===============

>为了网站更加安全，请将网站根目录设置为本系统文件夹下的/public的文件夹。

>hula_data.sql为数据库文件，请手动新建数据导入该文件。

>/config/database.php 为数据库链接配置文件，可以在此填写您的数据库链接配置信息。
      
HulaCWMS(呼啦企业网站管理系统)是基于ThinkPHP5框架开发，包括ThinkPHP5的所有特性。并在此基础上开发了如下功能：

 + 文章管理
 + 分类管理
 + 友情链接
 + 留言管理
 + 权限管理
 + 行为日志
 + 配置管理
 + 数据库管理
 + SEO友好
 + 多模板机制


> HulaCWMS的运行环境要求PHP5.4以上。

详细可访问我司官方网站 [灼灼文化](http://www.zhuopro.com)

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─admin              后台模块目录
│  └─index              前台模块目录
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─config                配置目录
│  ├─config.php         网站配置
│  └─database.php           数据库配置
│ 
│ 
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
|─template              前台模板目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

> router.php用于php自带webserver支持，可用于快速测试
> 切换到public目录后，启动命令：php -S localhost:8888  router.php
> 上面的目录结构和名称是可以改变的，这取决于你的入口文件和配置参数。

## 版权信息

HulaCWMS遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2019 by 灼灼文化 (http://www.zhuopro.com)

All rights reserved。
