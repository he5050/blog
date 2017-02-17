<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;
class Center extends Base
{
    public function center(){
        if(checkSession()){
            return $this->fetch();
        }else{
           session(null);
            //$this->redirect(url('index/index/login')); 
            $url = url('index/index/login');
            header("Location:$url");
            exit;
        }
    }
    public function commen()
    {
        if(checkSession()){
            $uid = session('u_id');
            //查询uid所的评论信息
            //$comms = db('comment')->where('u_id',$uid)->where('flag',1)->paginate(5);
            $comms = Db::view('comment','id,cm_createtime,cm_content')->view('article','ac_id,ac_title,ac_comment','comment.ac_id = article.ac_id')->where("comment.u_id = $uid")->where('comment.flag',1)->paginate(5);
            $this->assign('comms',$comms);
            return $this->fetch();
        }
    }
    public function info()
    {
        if(checkSession()){
            $uid = session('u_id');
            if(request()->isAjax()){
                $info = array('code'=>400,'info'=>'');
                $data['u_sname'] = input('post.nick') ?? '';
                $data['u_email'] = input('post.mail') ?? '';
                $data['u_url'] = input('post.url') ?? '';
                $data['u_mobile'] = input('post.phone') ?? '';
                $data['u_gender'] = input('post.sex') ?? '';
                $data['u_motto'] = input('post.motto') ?? '';
                if(db('user')->where('u_id',$uid)->update($data)){
                    $info['code'] = 200;
                    $info['info'] = '修改成功!';
                }else{
                    $info['info'] = '修改失败!';
                }
                return $info;
            }
            $info = db('user')->field('u_regtime,u_gender,u_mobile,u_motto')->where('u_id',$uid)->find();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }
    public function edit()
    {
        if(checkSession()){
               if(request()->isAjax()){
               $info = array('code'=>400,'info'=>'');
                $pwd = input('post.pwd') ?? '';
                $rpwd = input('post.rpwd') ?? '';
                $opwd  = input('post.opwd') ?? '';
                if($pwd == '' || empty($pwd)){
                    $info['info'] = '密码不能为空!';
                }
                if($rpwd == '' || empty($rpwd)){
                    $info['info'] = '密码不能为空!';
                }
                if($opwd == '' || empty($opwd)){
                    $info['info'] = '密码不能为空!';
                }
                if($pwd == $rpwd){
                    if($pwd != $opwd){
                        $uid = session('u_id');
                         //查询当前用户的密码与盐
                        $userinfo = db('user')->field('u_psw,u_salt')->where('u_id',$uid)->find();
                        if(md5(md5($opwd).$userinfo['u_salt']) == $userinfo['u_psw']){
                            //重新生成盐
                            $data = array();
                            $data['u_salt'] = substr(uniqid(),3);
                            $data['u_psw']  = md5(md5($pwd).$data['u_salt']);
                            $res = db('user')->where('u_id',$uid)->update($data);
                            if($res){
                                $info['code'] = 200;
                                $info['info'] = '修改成功!';
                            }else{
                                $info['info'] = '修改失败!';
                            }
                        }else{
                            $info['info'] = '当前密码输入错误';
                        }
                    }else{
                        $info['info'] = '不能与旧密码相同';
                    }
                }else{
                    $info['info'] = '两次输入密码不一致';
                }
                return json($info);
            } 
            return $this->fetch();
        }
    }
    public function list()
    {
        if(checkSession()){
            return $this->fetch();
        }
    }
    public function add()
    {
        if(checkSession()){
            return $this->fetch();
        }
    }
}