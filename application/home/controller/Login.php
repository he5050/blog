<?php
namespace app\home\controller;
//引入控制类 DB类
use app\home\model;
use think\Controller;
use think\Db;
//登录控制器
class Login extends Controller
{
     public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
    }
    /**
     * 用户登录
     * 已经登录了存在session直接跳转到后台首页
     * 没有登录跳转到登录页面
     */
    public function login()
    {
        //检查是否登录了
        if(session('u_id')>0 && session('u_status') == 1){
            //$this->success('登录成功,正在进入系统......',url('home/index/index'));exit;
            $url = url('home/index/index');
            header("Location:$url");
            exit;
        }else{
            session(null);
            //显示首页
            return $this->fetch();
        }  
    }
    /**
     * 登录验证
     */
    public function logincheck()
    {
        if(request()->isAjax()){
            //接收登录信息
            $data = array();
            //使用助手函数也可以使用$this->request->param('name');
            // 表单使用提post传递过来的使用post接收 
            $data['u_name'] = input('post.uname') ?? '';
            $data['u_psw']  = input('post.upsw') ?? '';
            $data['captcha']= input('post.ucaptcha') ?? '';
            $ip             = input('post.ip') ?? '';
            //如果没有接收到任何为空数组就返回登录页面
            if(empty($data)){
                return json(array('status'=>0,'msg'=>'用户名或密码不能空,验证码必须写!'));
                exit;
            }
            //使用验证器进行验证 验证器User 场景为login 
            $result = $this->validate($data,'User.login');
            //判断验证结果
            if($result !== true){
                //验证失败 输出错误信息;
                //$this->error($result);
                return json(array('status'=>0,'msg'=>$result));
                exit;
            }
            //验证验证码的是有效果否
            //实例化captcha类
            $captcha = new \think\captcha\Captcha();
    //        !$captcha->check($data['captcha']) ||
            if( !$captcha->check($data['captcha'])){
                //$this->error('验证码错误');
                return json(array('status'=>0,'msg'=>'验证码错误!'));
                exit;
            }else{
                //验证成功
                //取出当前登录用户的相关数据 Db:table(tp_user);
                //找到当前登录用户这个对象  然后转成数组
                $userinfo = db('user')->field('u_id,u_sname,u_salt,u_psw,u_email,u_status,u_icon')->where('u_name',$data['u_name'])->find();
                //判断用户是否存在
                if(!empty($userinfo)){
                    //判断用户账号是否正常
                    if($userinfo['u_status'] != 1){
                        //$this->error('用户账号异常,请联系管理员!');
                        return json(array('status'=>0,'msg'=>'用户账号异常,请联系管理员!'));
                        exit;
                    }
                    //验证密码是否正确
                    $psw = md5(md5($data['u_psw']).$userinfo['u_salt']);
                    if($psw === $userinfo['u_psw']){
                        //session('u_id',$userinfo['u_id']);
                        //用户信息放用session中
                        unset($userinfo['u_salt']);
                        unset($userinfo['u_psw']);
                        //查询当前登录用户对应URL
                        $url = db('comment')->field('cm_url')->where('flag',1)->where('u_id',$userinfo['u_id'])->group('u_id')->order('id desc')->find();
                        if($url){
                            $userinfo['url'] = $url['cm_url'];
                        }
                        //设置session
                        foreach($userinfo as $k=>$v){
                            session(strtolower($k),strtolower($v));
                        }
                        //设置cookie
                        unset($userinfo['u_status']);
                        //unset($userinfo['u_psw']);
                        foreach($userinfo as $k=>$v){
                            cookie(strtolower($k),strtolower($v),60*60*24*365);
                        }
                        //记录登录时间和ip
                        //$update['u_lastip'] = getIPaddress();
                        $update['u_lastip']   = $ip;
                       if($update['u_lastip'] == '127.0.0.1'){
                            $update['u_lastarea'] = '服务端登录';
                       }else{
                           $area = taobaoIP($update['u_lastip']);
                           if($area){
                              $update['u_lastarea'] = $area; 
                           } 
                       }
                        $this->model->find($userinfo['u_id'])->save($update);
                        //清除
                        unset($userinfo);
                        //$this->success('登录成功',url('home/index/index'));exit; 
                        return json(array('status'=>1,'msg'=>'成功登录！跳转到后台首页'));
                        exit;

                    }else{
                        //密码不正确
                        //$this->error('用户名或密码错误'); 
                     return json(array('status'=>0,'msg'=>'用户名或密码不能空,验证码必须写!'));
                        exit;

                    }
                }else{
                    //用户名不正确
                    //$this->error('用户名或密码错误');
                    return json(array('status'=>0,'msg'=>'用户名或密码不能空,验证码必须写!'));
                    exit;

                }
            }
        }
    }
    /**
     * 用户退出
     */
    public function logout()
    {
        session(null);
        cookie('u_id',null);
        //$this->success('退出登录成功,跳转中......','home/login/login');
        $url = url('home/login/login');
        header("Location:$url");
        exit;
    }
}