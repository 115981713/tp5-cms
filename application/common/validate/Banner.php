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

class Banner extends Validate {

    protected $rule =   [
        'name' => 'require|max:25',
        'name' => 'max:25'
    ];

    protected $message  =   [
        'title.require' => '请输入banner名称!',
        'name.max' => '名称最多输入25个字符'
    ];
    //更新排序
    protected $scene = [
        
    ];

}
