<?php
namespace app\home\controller;
use think\Controller;
use think\Auth;
use think\Model;
// 这个无效 可以删掉的
//权限验证
class MyAuth extends Controller
{
    //初始化方法
    protected function _initialize()
    {
         //判断用户id是否存在
        if(session('u_id')>0){
            //有来路就记录一下，没有就进去后台
            $http_referer = $_SERVER['HTTP_REFERER'] ?? 'home/index/index';
            session('http_referer',$http_referer);
            //当session存在的时候,不需要验证的权限有
            $not_check = array('Index/index','Index/logout','Index/main');
            //当前操作的请求   模块名/方法
            $controller = $this->request->controller();
            $action     = $this->request->action();
            $check      = $controller.'/'.$action;
            //echo $check;
            //exit;
            if(in_array($check,$not_check)){
                return true;
            }
            //echo $check;
            //动态判断权限
           
            
            //判断是不是超级管理员
            if(session('u_id') ==1 ){
                 $auth = new Auth();
                //不是 超级管理员
                // 第一个参数是规则名称,第二个参数是用户UID 
                if(!$auth->check($check,session('u_id'))){
                    $this->error('访问错误');
                }
            }else{
                return true;
            }
        }else{
            //跳转到登录界面
            $this->error('登录超时,请重新登录','home/login/login');
        }
    }
}