<?php
//定义为公共的
namespace app\common\validate;
//引入自动验证
use think\Validate;
//用户表
class Comment extends Validate
{
    //验证规则
    protected $rule = [
        'cm_name|用户名' => 'require',
        'cm_email|用户邮箱' => 'require|email',
    ];
    //错误消息
    protected $message = [
        'cm_name.require'  => '用户昵称必须填写!',
        'cm_email.require' => '用户邮箱必须填写!',
        'cm_email.email'  => '邮箱表格式错误!',
    ];
    //定义验证场景与验证字段
    protected $scene = [
       'add' =>['cm_name','cm_email'],
    ];
}