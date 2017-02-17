<?php
//定义为公共的
namespace app\common\validate;
//引入自动验证
use think\Validate;
//用户表
class Category extends Validate
{
    //验证规则
    protected $rule = [
        'c_name|分类名'    => 'require',
        'p_id|分类父ID'    => 'require',
    ];
    //错误消息
    protected $message = [
        'c_name.require'    => '分类名必须选择',
        'p_id.require'      => '父类必须选择',
    ];
    //定义验证场景与验证字段
    protected $scene = [
        'add'             => ['c_name','p_id'],
        'edit'             => ['c_name','p_id'],
    ];
}