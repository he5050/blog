<?php
//定义为公共的
namespace app\common\validate;
//引入自动验证
use think\Validate;
//用户表
class Article extends Validate
{
    //验证规则
    protected $rule = [
        'ac_id|文章ID'         => 'require',
        'ac_title|文章标题'    => 'require',
        'ac_subhead|文章副标题'    => 'require',
        'ac_content|文章内容'    => 'require',
        'cate_id|文章分类'      => 'require',
    ];
    //错误消息
    protected $message = [
        'ac_title.require'    => '文章标题必须填写!',
        'ac_subhead.require'      => '文章副标题必须填写!',
        'ac_content.require'     => '文章内容不能空!',
        'cate_id.require'       => '文章分类不能空!',
        'ac_id.require'         => '文章号不正确',
    ];
    //定义验证场景与验证字段
    protected $scene = [
        'add'             => ['ac_title','cate_id'],
        'edit'             => ['ac_title','cate_id','ac_id'],
    ];
    //自定义验证方法
    protected function checkStr($str){
        if(strpos($str,' ')){
            return true;
        }else{
            return false;
        }
    }
}