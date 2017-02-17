<?php
namespace app\common\controller;
use think\Controller;
use think\Auth;
use think\Model;
use think\Request;

//权限验证
class MyAuth extends Controller
{
    //初始化方法
    protected function _initialize()
    {
         //判断用户id是否存在
        if(session('u_id')>0 && session('u_status') == 1){
            //有来路就记录一下，没有就进去后台
            //不需要验证的权限有
            $not_check = array('Index/index','Index/logout','Index/main','Index/login');
            //当前操作的请求   模块名/方法
            $controller = $this->request->controller();
            $action     = $this->request->action();
            $check      = $controller.'/'.$action;
            if(in_array($check,$not_check)){
                return true;
            }
            //echo $check;
            //动态判断权限
            //判断是不是超级管理员
            if(session('u_id') !=1 ){
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
            //$this->error('登录超时,请重新登录','home/login/login');
            $url = url('index/index/index');
            header("Location:$url");
            exit;
        }
    }
    //空方法
    public function _empty()
    {
        //空方法
        session(null);
        return $this->redirect(url('home/login/login'));
    }
}