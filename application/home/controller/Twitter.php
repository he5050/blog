<?php
namespace app\home\controller;
use think\Db;
use app\home\model;
use app\common\controller\MyAuth;


//纸条操作模块
class Twitter extends MyAuth
{
     //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Twitter');
    }
    public function list()
    {
        $twitter = $this->model->select();
        $this->assign('twitter',$twitter);
        return $this->fetch();
    }
    public function add(){
        if(ajax()){
            $data['tw_content'] = input('post.content') ?? '';
            $data['tw_img']     = input('post.images') ?? '';
            $data['tw_uid'] = session('u_id');
            $res = $this->model->save($data);
            if($res){
                $id = $this->model->tw_id;
                $file = array('f_model'=>'twitter','f_model_id'=>$id,'f_location'=>$data['tw_img'],'f_type'=>'img');
                model('file')->save($file);
                return json(array('status'=>1));
            }else{
                return json(array('status'=>0));
            }
            exit;
        }
        return $this->fetch();
    }
    public function del()
    {
        if(ajax()){
            $id = input('post.id') ?? '';
            $res = db('twitter')->where('tw_id',$id)->update(array('tw_status'=>0));
            $info = array('status'=>0,'msg'=>'没有什么信息');
            if($res){
                $info['status'] = 1;
                $info['msg']    = '删除信息成功!';
            }else{
                $info['status'] = 0;
                $info['msg'] = '删除失败请联系管理员!';
            }
            return json($info);
        }
    }
    public function unlock()
    {
        if(ajax()){
            $id = input('post.id') ?? '';
            $res = db('twitter')->where('tw_id',$id)->update(array('tw_status'=>1));
            $info = array('status'=>0,'msg'=>'没有什么信息');
            if($res){
                $info['status'] = 1;
                $info['msg']    = '启用成功!';
            }else{
                $info['status'] = 0;
                $info['msg'] = '启用失败请联系管理员!';
            }
            return json($info);
        }
    }
    public function edit()
    {
        if(ajax()){
            $data['tw_content'] = input('post.content') ?? '';
            $data['tw_img']     = input('post.images') ?? '';
            $id = input('post.id') ?? '';
            $res = $this->model->where('tw_id',$id)->where('tw_uid',session('u_id'))->update($data);
            if($res){
                $file = array('f_model'=>'twitter','f_model_id'=>$id,'f_location'=>$data['tw_img'],'f_type'=>'img');
                model('file')->save($file);
                return json(array('status'=>1));
            }else{
               return json(array('status'=>0)); 
            }
            exit;
        }
        $id = request()->param('id') ?? '';
        $uid = session('u_id');
        $info = db('twitter')->where('tw_id',$id)->where('tw_uid',$uid)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function upload()
    { 
    	/* 返回标准数据 */
        if(ajax()){
            $savePath = '/upload/twitter/';
            $return = array();
            $file = request()->file('images');
            $info = $file->move(ROOT_PATH.'/public'.$savePath);
            if($info){
                $res['status'] = 1;
                $res['url'] = str_replace('\\','/',$savePath.$info->getSaveName());
            }else{
                $res['status'] = 0;
                $res['url'] = '上传失败!';
            }
            return json($res);
        }else{
            return json(array('status'=>0,'url'=>'请确认您的操作正确'));
        }
//		if ($info) {
//			$data = array();
//			$data['f_type']     = 'ueditor';
//			$data['f_savepath'] = $this->rootPath.$info->getSaveName();
//			$data['f_name']     = $info->getFilename();
//			$data['f_savename'] = $info->getSaveName();
//			$data['f_size']     = $info->getSize();
//			$data['f_mime']     = $info->getExtension();
//			$data['f_extension']= $info->getExtension();
//			$data['f_hash']     = $info->hash('md5');
//			$data['f_time']     = date("Y-m-d H:i:s");
//			$data['u_id']       = '';//$_SESSION['uid'];
//			$data['web_id']     = '';//$_SESSION['activewebid'];			
//			//$fid = Db::name('file')->insert($data);
//			//-----------------------
//			$ret = array();
//            $ret['filename'] = $this->rootPath.$info->getSaveName();
//            $ret['imgurl']	 = $this->rootPath.$info->getSaveName();
//            $ret['hash']	 = $info->hash('md5');
//            $ret['fid']		 = '';//$fid;                                        
//            array_push($return, $ret);
//		}
    }
}