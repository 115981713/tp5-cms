<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\common\validate;
use think\Validate;

/**
 * 后台菜单验证器
 */

class Chitu extends Validate {

    protected $rule =   [
        'name' => 'require|max:20',
        'name' => 'max:20',
        'type' => 'require|max:20',
        'type' => 'max:20'
    ];

    protected $message  =   [
        'name.require' => '请输入姓名!',
        'name.max' => '姓名最多输入20个字符',
        'type.require' => '请输入称呼!',
        'type.max' => '称呼最多输入30个字符'
    ];
    //更新排序
    protected $scene = [
        
    ];

}
