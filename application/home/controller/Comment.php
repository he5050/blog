<?php
namespace app\home\controller;
use think\Db;
use app\home\model;
use app\common\controller\MyAuth;


//评论管理
class Comment extends MyAuth
{
    //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Comment');
    }
    public function list()
    {
        $comms = $this->model->view('comment','id,cm_name,cm_content,p_id,cm_createtime,cm_email,cm_ison')
            ->view('article','ac_title,ac_subhead','comment.ac_id = article.ac_id')
            ->view('category','c_name','category.id = article.cate_id')
            ->order('comment.id desc')
            ->select();
        $comms = getSort($comms);
        $this->assign('comms',$comms);
        return $this->fetch();
    }
    public function del()
    {
         //接收要修改的id
        if(ajax()){
            $id = request()->param('id') ?? '';
            $res = $this->model->where('id',$id)->update(array('cm_ison'=>0));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '删除评论成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除评论失败!';
            }
            return json($info);
        }
    }
    public function unlock()
    {
         //接收要修改的id
        $id = request()->param('id') ?? '';
        $res = $this->model->where('id',$id)->update(array('cm_ison'=>1));
        if($res){
            $info['status'] = 1;
            $info['msg']    = '恢复评论成功!';
        }else{
            $info['status'] = 0;
            $info['msg']    = '恢复评论失败!';
        }
        return json($info);
    }
}