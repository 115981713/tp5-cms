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

        // 'level_name'  => 'require|max:30',
        // 'num'   => 'require|number|between:1,100',
        // 'sort'   => 'require|number|between:1,1000',
        // 'show_time'   => 'require|max:30'
    ];

    protected $message  =   [
        'name.require' => '请输入姓名!',
        'name.max' => '姓名最多输入20个字符！',
        'type.require' => '请输入称呼!',
        'type.max' => '称呼最多输入30个字符！'

        // 'level_name.require' => '请输入等级名称!',
        // 'level_name.max' => '等级名称最多30个字符!',
        // 'num.require' => '请输入奖品名额！',
        // 'num.number' => '奖品名额必须是数字！',
        // 'num.between' => '奖品名额必须在1-100之间！',
        // 'sort.require' => '请输入排序！',
        // 'sort.number' => '排序必须是数字！',
        // 'sort.between' => '排序必须在1-1000之间！',

        // 'show_time.require' => '请输入时间！',
        // 'show_time.max' => '称呼最多输入30个字符！'
    ];
    //更新排序
    protected $scene = [
        
    ];

}
