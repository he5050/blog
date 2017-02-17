<?php
namespace app\home\controller;
use think\Db;
use app\home\model;
use app\common\controller\MyAuth;


//用户管理
class User extends MyAuth
{
    //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
    }
    public function add()
    {
        if(ajax()){
            //联动菜单获取地区信息
            if(input('post.info') == 1){
                //接收地区信息
               $area = input('post.area');
               $province = Db::name('area')->field('charShortProvince,charProvince')->where("charArea = '$area'")->group('charShortProvince,charProvince')->select();
                return $province;
                exit;
            }elseif(input('post.info') == 2){
                  //接收省市信息
               $area = input('post.area');
               $province = Db::name('area')->field('charCity,charCityen')->where("charProvince = '$area'")->select();
                return $province;
                exit;
            }else{
                //获取数据
                $data['u_name'] = input('post.username') ?? '';
                $data['u_name'] = trim($data['u_name']);
                $data['u_sname'] = input('post.usersname') ?? '';
                $data['u_rel_name'] = input('post.userrname') ?? '';
                $data['u_rel_name'] = trim($data['u_rel_name']);
                $data['u_salt'] = substr(uniqid(),3);
                $data['u_psw'] = input('post.password') ?? '';
                $data['u_psw'] = trim($data['u_psw']);
                if(empty($data['u_psw'])){
                    $this->error('请重新填写');
                }
                //$data['u_psw'] = md5(md5($data['u_psw']).$data['u_salt']);
                $data['u_mobile'] = input('post.userphone') ?? '';
                $data['u_email'] = input('post.email') ?? '';
                $data['u_countrie']  = input('post.a1') ?? '';
//                if($data['u_countrie'] != '国外'){
//                    $data['u_countrie'] = '国内';
//                }
                $data['u_province'] = input('post.a2') ?? '';
                $data['u_city']   = input('post.a3') ?? '';
                $data['u_area']   = input('post.areainfo') ?? '';
                $data['u_card']   = input('post.usercard') ?? '';
                $data['u_gender'] = input('post.sex') ?? '';
                //验证数据是否有效
                $result = $this->validate($data,'User.reg');
                //判断验证结果
                if($result !== true){
                    $info['status'] = 0;
                    $info['msg']    = $result;
                    return json($info);
                }
                //添加数据
                /*if(db('user')->insert($data)){
                    $this->success('添加用户成功!','add');exit;
                }else{
                    $this->error('添加用户失败!');
                }*/
                if($this->model->save($data)){
                    //获取用户的自增ID 然后更新到权限组里面去
                    $insertID = $this->model->u_id;
                    $group = input('post.')['group'];
                    //这条写的有问题 还没有想怎么解决这个问题
                    // 启动事务
                    Db::startTrans();
                    try{
                        //Db::name('auth_group_access')->where('uid',$id)->delete();
                        if(is_array($group)){
                            foreach($group as $v){
                            Db::name('auth_group_access')->insert(array('uid'=>$insertID,'group_id'=>$v));
                            }
                        }else{
                            Db::name('auth_group_access')->insert(array('uid'=>$insertID,'group_id'=>$v));
                        }
                        // 提交事务
                        Db::commit();    
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                    }
                    $info['status'] = 1;
                    $info['msg']    = '添加用户成功!';
                }else{
                    $info['status'] = 0;
                    $info['msg']    = '添加用户失败!';
                }
                return json($info);
            }
        }
        //查询出用户权限组
        $group = Db::name('auth_group')->select();
        $this->assign('group',$group);
        //查询地区信息并分配过去
        $area = Db::name('area')->field('charAreaen,charArea')->group('charArea')->select();
        $this->assign('area',$area);
        return $this->fetch();
    }
    public function edit()
    {
        //判断提交修改
        if(ajax()){
            //获取数据
                $id = input('post.userid') ?? '';
                $data['u_name'] = input('post.username') ?? '';
                $data['u_name'] = trim($data['u_name']);
                $data['u_rel_name'] = input('post.userrname') ?? '';
                $data['u_rel_name'] = trim($data['u_rel_name']);
                $data['u_sname']   = input('post.usersname') ?? '';
                $psw1 = input('post.password') ?? '';
                $psw1 = trim($psw1);
                $psw2 = input('post.rpassword') ?? '';
                $psw2 = trim($psw2);
            //判断是否修改密码
                if($psw1 && $psw2){
                     if($psw1 == $psw2){
                         $data['u_salt'] = substr(uniqid(),3);
                         $data['u_psw'] = md5(md5($psw1.$data['u_salt']));
                     }
                }
                $data['u_mobile'] = input('post.userphone') ?? '';
                $data['u_email'] = input('post.useremail') ?? '';
                $data['u_countrie']  = input('post.a1') ?? '';
                $data['u_province'] = input('post.a2') ?? '';
                $data['u_city']   = input('post.a3') ?? '';
                $data['u_area']   = input('post.areainfo') ?? '';
                $data['u_card']   = input('post.usercard') ?? '';
                $data['u_gender'] = input('post.sex') ?? '';
                
                //不需要进行验证
                $result = $this->model->where("u_id",$id)->update($data);
                //先执行删除操作 然后在执行插入操作
               
                if($result){
                    $info['status'] = 1;
                    $info['msg']    = '修改用户成功!';
                }else{
                    $info['status'] = 0;
                    $info['msg']    = '修改用户失败!';
                }
            return json($info);
        }
        //echo '<pre>';
        //获取要修改的id号;
        $u_id = request()->param('id') ?? '';
        $userInfo = $this->model->get($u_id)->getData();
        //查询地区信息并分配过去
        $area1 = Db::name('area')->field('charAreaen,charArea')->group('charArea')->select();
        $area2 = Db::name('area')->field('charShortProvince,charProvince')->group('charShortProvince,charProvince')->select();
        $area3 = Db::name('area')->field('charCity,charCityen')->select();
        $this->assign('area1',$area1);
        $this->assign('area2',$area2);
        $this->assign('area3',$area3);
        $this->assign('userInfo',$userInfo);
        
        return $this->fetch();
    }
    //用户列表
    public function list()
    {
        //如果有提交
        $str = '1';
        $where2 = '';
        if(input('post.')){//接收查询信息
           $info = input('post.info');
            //接收查询状态
           $status = input('post.')['status'] ?? '';
            //判断是否为空
           if($status){
               $status = implode(',',$status);
                $str = $status;
                $where2 = "u_name like '%$info%' or u_rel_name like '%$info%' or u_email like '%$info%' or u_mobile like '%$info%'";
           }
        }
        //echo '<pre>';
        $where1 = "u_status in ($str)";
        $userInfo = $this->model->where($where1)->where($where2)->select(); 
        //var_dump($userInfo);
        $this->assign('userInfo',$userInfo);
        return $this->fetch();
    }
    //删除用户
    public function del()
    {
        if(ajax()){
            $u_id = request()->param('id') ?? '';
            $result = $this->model->where("u_id = $u_id")->update(array('u_status'=>'-1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '删除用户成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除用户失败!';
            }
            return json($info);
        }
    }
    //禁用用户
    public function lock()
    {
        if(ajax()){
            $u_id = request()->param('id') ?? '';
            $result = $this->model->where("u_id = $u_id")->update(array('u_status'=>'0'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '禁用用户成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '禁用用户失败!';
            }
            return json($info);
        }
    }
    public function unlock()
    {
        if(ajax()){
           $u_id = request()->param('id') ?? '';
            $result = $this->model->where("u_id = $u_id")->update(array('u_status'=>'1'));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '恢复用户成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复用户失败!';
            }
            return json($info);
        }
        
    }
    //修改用户所在权限组
    public function editForm()
    { 
         if(ajax()){
             $id = input('post.userid') ?? '';
             $group = input('post.')['group'];
                    //这条写的有问题 还没有想怎么解决这个问题
                    // 启动事务
            Db::startTrans();
            try{
                Db::name('auth_group_access')->where('uid',$id)->delete();
                    if(is_array($group)){
                        foreach($group as $v){
                            Db::name('auth_group_access')->insert(array('uid'=>$id,'group_id'=>$v));
                        }
                    }else{
                            Db::name('auth_group_access')->insert(array('uid'=>$id,'group_id'=>$v));
                    }
                        // 提交事务
                    Db::commit();
                //这里有一个bug
                   $info['status'] = 1;
                $info['msg']    = '修改用户组成功!';
                } catch (\Exception $e) {
                        // 回滚事务
                    Db::rollback();
                    //$this->success('修改用户权限组失败!',url('list'));
                    $info['status'] = 0;
                    $info['msg']    = '修改用户组失败!';
                }
             return json($info);
             exit;
         }
        //获取要修改的id号;
        $u_id = request()->param('id') ?? '';
        $userInfo = $this->model->get($u_id)->getData();
        //查询用户组信息
        $groupAll = Db::name('auth_group')->select();
        //查询当前用户所在用户组
        $groupOne = Db::name('auth_group_access')->where('uid',$u_id)->column('group_id');
        $this->assign('groupAll',$groupAll);
        $this->assign('groupOne',$groupOne);
        $this->assign('userInfo',$userInfo);
        return $this->fetch();
    }
}