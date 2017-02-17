<?php
//定义为公共的
namespace app\common\validate;
//引入自动验证
use think\Validate;
//用户表
class Update extends Validate
{
    //验证规则
    protected $rule = [
        'up_version' => 'require',
        'up_content' => 'require',
        'up_main'    => 'require',
    ];
    //错误消息
    protected $message = [
        'up_version.require' => '版本号必须填写',
        'up_main.require'           => '更新标题需要填写',
        'up_content.require'        => '主要更新内容'
    ];
    //定义验证场景与验证字段
    protected $scene = [
        //'reg'               => ['u_name','u_psw','u_salt','u_email','u_mobile'],
        //新增的时候进行验证
        'add'               => ['up_version','up_content','up_main'],
        'edit'               => ['up_version','up_content','up_main'],
    ];
}