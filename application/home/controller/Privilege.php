<?php
namespace app\home\controller;
use think\Db;
use app\common\controller\MyAuth;

class Privilege extends MyAuth
{
    protected $auth;
      //自动实例化模型
    public function _initialize()
    {
       $this->auth = Db::name('auth_group');
    }
    //显示权限列表
    public function listAuth(){
        $authdata = Db::name('auth_rule')->order('sort')->select();
        $authdata = getSort($authdata);
        $this->assign('authdata',$authdata);
        return $this->fetch();
    }
    /***************************************************************************************************************************************
     *权限的操作
    ****************************************************************************************************************************************/
    //添加权限
    public function addAuth()
    {
        //添加权限
        if(ajax()){
            //权限名称
            $data['title'] = input('post.title') ?? '';
            //模块名
            $model = input('post.controller') ?? '';
            //方法名
            $action = input('post.action') ?? '';
            //图标
            $data['icon'] = input('post.icon') ?? '';
            //排序
            $data['sort'] = input('post.sort') ?? '';
            //组成name 
            if($action == '' || empty($action)){
                $data['name'] = $model;
            }else{
                $data['name'] = $model.'/'.$action;
            }
            $authR = Db::name('auth_rule');
            //是否显示
            $data['isshow'] = input('post.isshow');
            //所输分类
            $data['p_id']  = input('post.p_id'); 
            $result =$authR->insert($data);
            //添加成功
            if($result){
                //如果父ID不为0则设置 其父ID的 isparent 为 1
                if($data['p_id'] != 0){
                    $preuslt = $authR->where('id',$data['p_id'])->update(array('isparent'=>1));
                }
                 $info['status'] = 1;
                 $info['msg']    = '新增权限成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '新增权限失败!';
            }
            return json($info);
            exit;
        }
        $authdata = Db::name('auth_rule')->field('id,title,p_id')->order('sort')->select();
        $authdata = getSort($authdata);
        $this->assign('authdata',$authdata);
        return $this->fetch();
    }
    //修改权限
    public function editAuth()
    {
        //修改权限
        if(ajax()){
            //接收要修改有ID
            $id = input('post.id');
             $data['title'] = input('post.title') ?? '';
            //模块名
            $model = input('post.controller') ?? '';
            //方法名
            $action = input('post.action') ?? '';
            //是否显示成菜单
            $data['isshow'] = input('post.isshow') ?? '';
            //父亲id
            $data['p_id'] = input('post.p_id') ?? '';
            //图标
            $data['icon'] = input('post.icon') ?? '';
            //排序
            $data['sort'] = input('post.sort')  ?? '';
            //组成name 
            if($action == '' || empty($action)){
                $data['name'] = $model;
            }else{
                $data['name'] = $model.'/'.$action;
            }
            $authR = Db::name('auth_rule');
            $result =$authR->where('id',$id)->update($data);
            if($result){
                if($data['p_id'] != 0){
                    //设置父id isparent为1
                    $authR->where('id',$data['p_id'])->update(array('isparent'=>1));
                }else{
                    //如果 p_id 等0 设置本身
                    $authR->where('id',$id)->update(array('isparent'=>1,'isshow'=>1));
                }
                 $info['status'] = 1;
                 $info['msg']    = '修改权限成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '修改权限失败!';
            }
            return json($info);
            exit;
        }
        $id = request()->param('id');
        $authR = Db::name('auth_rule')->where('id',$id)->find();
        $authdata = Db::name('auth_rule')->field('id,title,p_id')->select();
        $authdata = getSort($authdata);
        $this->assign('authdata',$authdata);
        $this->assign('authR',$authR);
        return $this->fetch();
    }
     //删除权限
    public function delAuth()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $result = Db::name('auth_rule')->where("id = $id")->update(array('ison'=>'-1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '删除权限成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除权限失败!';
            }
            return json($info);
            exit;
        }
    }
     //禁用权限
    public function lockAuth()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $result = Db::name('auth_rule')->where("id = $id")->update(array('ison'=>'0'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '禁用权限成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '禁用权限失败!';
            }
            return json($info);
            exit;
        }
    }
     //启用权限
    public function unlockAuth()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $result = Db::name('auth_rule')->where("id = $id")->update(array('ison'=>'1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '恢复权限成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复权限失败!';
            }
            return json($info);
            exit;
        }
        
    }
    /***************************************************************************************************************************************
     *用户组的操作
    ****************************************************************************************************************************************/
    //显示用户组
    public function listGroup()
    {
        $data = $this->auth->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    //添加权限组
    public function addGroup()
    {
        //使用用手函数判断是否有提交
        if(ajax()){
            $data['title'] = input('post.title') ?? '';
            $data['title'] = trim($data['title']);
            $rules = input('post.')['rules'];
            $data['rules'] = implode(',',$rules);
            $result = $this->auth->insert($data);
            if($result){
                $info['status'] = 1;
                $info['msg']    = '新增用户组成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '新增用户组失败!';
            }
            return json($info);
            exit;
        }
        //查询现在用户组信息
        $groupdata = $this->auth->select();
        //var_dump($groupdata);
        //查询现有权限规则
        $authdata = Db::name('auth_rule')->field('id,title,p_id')->select();
        $authdata = getSort($authdata);
        $this->assign('groupdata',$groupdata);
        $this->assign('authdata',$authdata);
        return $this->fetch();
    }
    //修改
    public function editGroup()
    {
        //修改用户组
        if(ajax()){
            $id = input('post.id') ?? '';
            $data['title'] = input('post.title') ?? '';
            $data['title'] = trim($data['title']);
            $rules = input('post.')['rules'];
            $data['rules'] = implode(',',$rules);
            $result = $this->auth->where('id',$id)->update($data);
            if($result){
                $info['status'] = 1;
                $info['msg']    = '修改用户组成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '修改用户组失败!';
            }
            return json($info);
            exit;
        }
         //查询现在用户组信息
        $id = request()->param('id') ?? '';
        $group = $this->auth->where('id',$id)->find();
        $this->assign('group',$group);
        $groupdata = $this->auth->select();
         //查询现有权限规则
        $authdata = Db::name('auth_rule')->field('id,title,p_id')->select();
        $authdata = getSort($authdata);
        $this->assign('groupdata',$groupdata);
        $this->assign('authdata',$authdata);
        return $this->fetch();
    }
    //删除用户组
    public function delGroup()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $result = $this->auth->where("id = $id")->update(array('ison'=>'-1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '删除用户组成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除用户组失败!';
            }
            return json($info);
            exit;
        }
        
    }
     //禁用用户组
    public function lockGroup()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $result = $this->auth->where("id = $id")->update(array('ison'=>'0'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '禁用用户组成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '禁用用户组失败!';
            } 
            return json($info);
            exit;
        }
    }
     //启用用户组
    public function unlockGroup()
    {
        if(ajax()){
           $id = request()->param('id') ?? '';
            $result = $this->auth->where("id = $id")->update(array('ison'=>'1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '恢复用户组成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复用户组失败!';
            } 
        }
        return json($info);
        exit; 
    }
}