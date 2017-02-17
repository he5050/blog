<?php
namespace app\home\controller;
use think\Db;
use app\home\model;
use app\common\controller\MyAuth;


//文章管理
class Category extends MyAuth
{
     //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('category');
    }
    /***********************************************************************************************************************
    文章分类管理
     ***********************************************************************************************************************/
    public function add()
    {
        if(ajax()){
            $data['c_name'] = input('post.title') ?? '';
            $data['p_id']   = input('post.p_id') ?? '';
            $data['c_isshow'] = input('post.isshow') ?? '';
            $data['c_sort']  =input('post.sort') ?? '';
            $data['c_info']  =input('post.info') ?? '';
            $data['c_img']   = input('post.images') ?? '';
            //生成缩略图
            $imags = ROOT_PATH.'/public'.$data['c_img'];
            //打开图像资源
            $img =  \think\Image::open($imags);
            //获取图像后缀名
            $type = $img ->type();
            //创建目录
            $path  = './upload/cate/thumb/';
            if(!is_dir($path)){
                $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); 
            }
                //拼接文件路径与名
                $newimg = $path.time().'.'.$type;
                //$newimg = ltrim($newimg,'.');
                $res = $img->thumb(220,150,\think\Image::THUMB_FILLED)->save($newimg);
                if($res){
                    $data['c_thumbimg'] = ltrim($newimg,'.');
                }
            //进行验证
            $res = $this->validate($data,'Category.add');
            if(!$res){
                $info['status'] = 0;
                $info['msg']    = $res;
                return json($info);
            }
            //验证通过 保存信息
            $result = $this->model->save($data);
            if($result){
                //获取自增加id
                $insertId = $this->model->id;
                //构建数组
                $filedata = array(  array('f_model'=>'category','f_model_id'=>$insertId,'f_location'=>$data['c_img'],'f_type'=>'img'),  array('f_model'=>'category','f_model_id'=>$insertId,'f_location'=>$data['c_thumbimg'],'f_type'=>'thimg'),
                );
                //更新文件表
                model('File')->saveAll($filedata);
                //再新增的时候 如果父id不为0 表示不是新增加顶级分类
                if($data['p_id'] != 0){
                    $this->model->where('id',$data['p_id'])->update(array('c_isparent'=>1));
                }
                $info['status'] = 1;
                $info['msg']    = '添加分类信息成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '添加分类信息失败!';
            }
            return json($info);
        }
        //查找所有分类信息
        $cates = $this->model->select();
        $cates = getSort($cates);
        $this->assign('cates',$cates);
        return $this->fetch();
    }
    //分类列表
    public function list()
    {
        $cateData = $this->model->order('c_sort')->select();
        $cateData = getSort($cateData);
        $this->assign('cateData',$cateData);
        return $this->fetch();
    }
    //修改分类 
    public function edit()
    {
        //更新操作
        if(ajax()){
            $idp = input('post.c_id');
            $data['c_name'] = input('post.title') ?? '';
            $data['p_id']   = input('post.p_id') ?? '';
            $data['c_isshow'] = input('post.isshow') ?? '';
            $data['c_sort']  =input('post.sort') ?? '';
            $oldpid = input('post.oldpid') ?? '';
            $data['c_info']  =input('post.info') ?? '';
             //进行验证
            $res = $this->validate($data,'Category.edit');
            if(!$res){
                $this->error($res);
            }
            //验证通过 保存信息
            $result = $this->model->get($idp)->save($data);
            if($result){
                //如果新添加的父id不为0
                if($data['p_id'] != 0){
                    //设置新的父id 的c_isparent 为1
                    $this->model->where('id',$data['p_id'])->update(array('c_isparent'=>1));        
                }
                //判断旧的父id是否为0
                if($oldpid != 0){
                     //判断其旧的父id下面是否还存子类
                    $flag = $this->model->where('p_id',$oldpid)->count();
                    if($flag>=1){
                        $this->model->where('id',$oldpid)->update(array('c_isparent'=>1));
                    }else{
                        $this->model->where('id',$oldpid)->update(array('c_isparent'=>0));
                    }
                }
                //判断当前分类下面是否存在子类
                $flagself = $this->model->where('p_id',$idp)->count();
                if($flagself >=1){
                        $this->model->where('id',$idp)->update(array('c_isparent'=>1));
                }else{
                        $this->model->where('id',$idp)->update(array('c_isparent'=>0));
                }
                $info['status'] = 1;
                $info['msg']    = '修改分类信息成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '修改分类信息失败!';
            }
            return json($info);
        }
        //获取ID
        $id = request()->param('id') ?? '';
        $cateData = $this->model->get($id)->toarray();
        $cates = $this->model->select();
        $cates = getSort($cates);
        $this->assign('cates',$cates);
        $this->assign('cateData',$cateData);
        return $this->fetch();
    }
    //删除分类
    public function del()
    {
        if(ajax()){
            //获取ID
            $id = request()->param('id') ?? '';
            $result = $this->model->where('id',$id)->update(array('c_ison'=>0));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '删除分类信息成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除分类信息失败!';
            }
            return json($info);
        }
        
    }
    //启用分类
    public function unlock()
    {
        if(ajax()){
           //获取ID
            $id = request()->param('id') ?? '';
            $result = $this->model->where('id',$id)->update(array('c_ison'=>1));
            if($result){
                $info['status'] = 1;
                $info['msg']    = '恢复分类信息成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复分类信息失败!';
            }
            return json($info);
        }
        
    }
    //修改图片
    public function img()
    {
        if(ajax()){
            $idp = input('post.c_id') ?? '';
            $all = $this->model->field('c_img,c_thumbimg')->where('id',$idp)->find()->toarray();
            $img = input('post.images') ?? '';
            if($img == '' | empty($img)){
                return json(array('status'=>0,'msg'=>'您好像没有做什么修改!'));
            }
            $where = '';
                //删除以前的记录 不建议在循环中操作数组
            foreach($all as $key=>$value){
                $relpath = ROOT_PATH.'public'.$value;
                $where .= "f_location = '$value' or ";
                if(file_exists($relpath)){
                    @unlink($relpath);
                    }
            }
            $where = trim($where,'or ');
            //更新file表
            model('File')->where($where)->update(array('f_ison'=>0));
            $data['c_img'] = $img;
            //生成缩略图
            $imags = ROOT_PATH.'/public'.$data['c_img'];
            //打开图像资源
            $img =  \think\Image::open($imags);
            //获取图像后缀名
            $type = $img ->type();
            //创建目录
            $path  = './upload/cate/thumb/';
            if(!is_dir($path)){
                $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); 
            }
            //拼接文件路径与名
            $newimg = $path.time().'.'.$type;
            $resflag = $img->thumb(220,150,\think\Image::THUMB_FILLED)->save($newimg);
            if($resflag){
                $data['c_thumbimg'] = ltrim($newimg,'.');
            }
            //把新得到的路径信息更新到file表中
            $filedata = array(  array('f_model'=>'category','f_model_id'=>$idp,'f_location'=>$data['c_img'],'f_type'=>'img'),  array('f_model'=>'category','f_model_id'=>$idp,'f_location'=>$data['c_thumbimg'],'f_type'=>'thimg'));
            //更新文件表
            model('File')->saveAll($filedata);
            $result = $this->model->get($idp)->save($data);
            if($result){
                $info['status'] = 1;
                $info['msg']    = '修改分类封面成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '修改分类封面失败!';
            }
            return json($info);
        }
        //获取ID
        $id = request()->param('id') ?? '';
        $cateData = $this->model->get($id)->toarray();
        $cates = $this->model->select();
        $cates = getSort($cates);
        $this->assign('cates',$cates);
        $this->assign('cateData',$cateData);
        return $this->fetch();
    }
    public function upload()
    {
        if(ajax()){
            $savePath = '/upload/cate/self/';
            $return = array();
            $file = request()->file('images');
            $info = $file->validate(['size'=>4048920,'ext'=>'jpg,png,gif,jpeg,bmp'])->rule('uniqid')->move(ROOT_PATH.'/public'.$savePath);
            if($info){
                $res['status'] = 1;
                $res['url'] = str_replace('\\','/',$savePath.$info->getSaveName());
            }else{
                $res['status'] = 0;
                $res['url'] = '上传失败!';
            }
       }else{
           $res['status'] = 2;
           $res['url'] = '您的确认您的操作合法嘛?';
       }
        return json($res);
    }
}