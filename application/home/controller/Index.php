<?php
namespace app\home\controller;
use think\Db;
use app\common\controller\MyAuth;
//后面首页
class Index extends MyAuth
{
    public function index()
    {
        //echo '<pre>';
        //获取当前登录的ID
        $uid = $_SESSION['u_id'];
        //目录设置只能是管理员才能进入
        if($uid > 2 || session('u_status') != 1){
            session(null);
            $this->redirect('home/login/login');
            exit;
        }
        //查询所在的用户组
        $group_id = Db::name('auth_group_access')->where("uid=$uid")->column('group_id');
        //拆分成字符串
        $group_id = implode(',', $group_id);
        //获取用户的权限显示对应的菜单
        //获取用户所在组的 权限id
        $auth_id = Db::name('auth_group')->where("id in($group_id)")->column('rules');
        //拆分成字符串
        $auth_id = implode(',', $auth_id);
        //获取一级权限列表
        $auth_ONE = Db::name('auth_rule')->where("id in($auth_id) and isshow = 1")->field('id,name,title,p_id,isparent,icon')->order('sort')->select();
        $auth_ONE = getSort($auth_ONE);
        //var_dump($auth_ONE);
        //exit;
        $this->assign('auth_ONE',$auth_ONE);
        
        return $this->fetch();
        //return view('index');
    }
    public function main(){
        $server = array(
            '操作系统'              => PHP_OS,
            'PHP运行版本'           => PHP_VERSION,
            'PHP路径'              => $_SERVER['PHPRC'],
            '服务器解译引擎'         => $_SERVER['SERVER_SOFTWARE'],
            '服务器域名/IP地址'      => $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '语言'                 => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            'ThinkPHP版本'         => THINK_VERSION,
            '文档目录'           => $_SERVER['DOCUMENT_ROOT'],
            '上传附件限制'          => ini_get('upload_max_filesize'),
            '执行时间限制'          =>ini_get('max_execution_time').'秒',
            '北京时间'              =>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '剩余空间'              =>round((disk_free_space(".")/(1024*1024)),2).'M',
        );
        //查询更新日志并显示
        $updateData = Db::name('update')->where('up_ison = 1')->limit('10')->order('up_id desc')->select();
        $this->assign('updateData',$updateData);
        $this->assign('server',$server);
        return $this->fetch();
    }
    //测试
    public function test()
    {   
        //取得盐
        $salt = uniqid();
        $salt = substr($salt,3);
        echo $salt;
        echo '<br>';
        $str = 'admin';
        $str = md5(md5($str).$salt);
        echo $str;
    }
}