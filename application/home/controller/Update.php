<?php
namespace app\home\controller;
use think\Db;
use app\common\controller\MyAuth;
class Update extends MyAuth
{
    //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Update');
    }
    //添加更新日志
    public function add()
    {
       //ajax提交
        if(ajax()){
            $data['up_version'] = input('post.version') ?? '';
            $data['up_main']    = input('post.maincontent') ?? '';
            $data['up_content'] = input('post.content');
            //进行验证
            $result = $this->validate($data,'Update.add');
            $info = array();
            if(!$result){
                $info = array(
                    'res' => '0',
                    'info' => $result,
                );
                return json($info);
            }
            //验证通过
            //添加到数据库中
            $res = $this->model->save($data);
            if($res){
                $info = array(
                    'res' => '1',
                    'info' => '增加数据成功!',
                );
                return json($info);    
            }else{
                $info = array(
                    'res' => '0',
                    'info' => '增加数据失败!',
                );
                 return json($info);
            }
        }
        //查询出当前的系统中最新的版本号
        $ver = $this->model->order('up_id desc')->value('up_version');
        $this->assign('ver',$ver);
        return $this->fetch();
    }
    //更新日志列表
    public function list()
    {
        //查询出当前系统中的数据
        $updatedata = $this->model->select();
        $this->assign('updatedata',$updatedata);
        return $this->fetch();
    }
    //修改更新日志 
    public function edit()
    {
        //提交更新
        if(ajax()){
            $up_id = input('post.id') ?? '';
            $data['up_version'] = input('post.version') ?? '';
            $data['up_main']    = input('post.maincontent') ?? '';
            $data['up_content'] = input('post.content');
            //进行验证
            $flag = $this->validate($data,'Update.edit');
            $info = array();
            if(!$flag){
                $info = array(
                    'res' => '0',
                    'info' => $flag,
                );
                return json($info);
            }
            //验证通过 更新数据
            $result = $this->model->get($up_id)->save($data);
             
            if($result){
                $info = array(
                    'res' => '1',
                    'info' => '修改数据成功!',
                );
                return json($info);    
            }else{
                $info = array(
                    'res' => '0',
                    'info' => '修改数据失败!',
                );
                 return json($info);
            }
        }
        $id = request()->param('id') ?? '';
        $res = $this->model->get($id)->toarray();
        $this->assign('res',$res);
        return $this->fetch();
    }
    //删除
    public function del()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $res = $this->model->where('up_id',$id)->update(array('up_ison'=> '0'));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '删除日志成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除日志失败!';
            }
            return json($info);
        }
    }
    //取消删除
     public function unlock()
    {
        if(ajax()){
           $id = request()->param('id') ?? '';
            $res = $this->model->where('up_id',$id)->update(array('up_ison'=> '1'));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '恢复日志成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复日志失败!';
            } 
            return json($info);
        }
    }
}