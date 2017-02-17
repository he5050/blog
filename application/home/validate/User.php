<?php
//定义为公共的
namespace app\common\validate;
//引入自动验证
use think\Validate;
//用户表
class User extends Validate
{
    //验证规则
    protected $rule = [
        //'u_name|用户名'    => 'require|max:12|min:5|unique:user',
        'u_name|用户名'    => 'require|max:12|min:5',
        'u_psw|密码'     => 'require|min:5',
        'u_rpws|重复密码'    => 'require|confirm:u_psw',
        'u_salt|盐'    => 'require',
        'u_email|邮件'    => 'email',
        'u_mobile|电话'  => ['regex' =>'/^1(3[0-9]|4[57]|5[0-35-9]|7[01678]|8[0-9])\\d{8}$/'],
        'u_card|身份证'    => ['regex' =>'/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/'],
        'captcha|验证码'   => 'require',
    ];
    //错误消息
    protected $message = [
        'u_name.require'    => '登录名必须填写',
        'u_name.unique'     => '用户名已存在',
        'u_name.max'        => '登录名最大长度为12',
        'u_name.min'        => '登录名最小长度为6',
        'u_psw.require'     => '密码必须填写',
        'u_psw.min'         => '密码不能少于6位',
    ];
    //定义验证场景与验证字段
    protected $scene = [
        'login'             => ['u_name','u_psw','captcha'],
        //'reg'               => ['u_name','u_psw','u_salt','u_email','u_mobile'],
        'reg'               => ['u_name','u_psw','u_email','u_mobile'],
    ];
}