<?php
namespace app\home\controller;
use think\Db;
use app\home\model;
use app\common\controller\MyAuth;


//文章管理
class Article extends MyAuth
{
     //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Article');
    }
    /***********************************************************************************************************************
    文章管理
     ***********************************************************************************************************************/
    public function add()
    {
        //添加文章
        if(ajax()){
            //获取post过来数据
            $data['ac_title'] = input('post.title') ?? '';
            $data['ac_subhead'] = input('post.subhead') ?? '';
            $data['ac_content'] = input('post.content') ?? '';
            $data['ac_isshow'] = input('post.isshow') ?? '';
            $data['cate_id'] = input('post.cate') ?? '';
            $upimg = $data['ac_img'] = trim(input('post.imgs'),',') ?? '';
            $data['ac_down'] = trim(input('post.files'),',') ?? '';
            $data['ac_tags'] = input('post.tags') ?? '';
            $data['ac_recommend'] = input('post.recommend') ?? '';
            $data['ac_auth'] = session('u_sname');
            $water = input('post.iswater') ?? '';
            //获取状态
            $status = input('post.')['status'] ?? '';
            if($status != ''){
                $data['ac_status'] = implode(',',$status);
            }
            if($data['ac_img'] != ''){
                //生成缩略图
                $imags = ROOT_PATH.'/public/'.explode(',',$data['ac_img'])[0];
                //打开图像资源
                $img =  \think\Image::open($imags);
                //获取图像后缀名
                $type = $img ->type();
                //创建目录
                $path  = './upload/thumb/'.date('Ymd',time()).'/';
                if(!is_dir($path)){
                    $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); 
                }
                //拼接文件路径与名
                $newimg = $path.time().'.'.$type;
                $res = $img->thumb(220,150,\think\Image::THUMB_FILLED)->save($newimg);
                if($res){
                    $data['ac_imgthumb'] = ltrim($newimg,'.');
                }
                //生成水印
                if($water == 1){
                    //拆分数组
                    $allimgs = explode(',',$data['ac_img']);
                    if(is_array($allimgs)){
                        foreach($allimgs as $ai){
                            //获取到图片
                            $imgone = ROOT_PATH.'public/'.$ai;
                            //打开图片资源
                            $imgwater = \think\Image::open($imgone);
                            //生成水印
                            $imgwater->water('../static/index/images/logo.png',\think\Image::WATER_SOUTHEAST,50)->save('.'.$ai);
                        }
                    }
                }
                 //把新得到的路径信息更新到file表中
            }else{
                //删除 使用默认图片
                unset($data['ac_img']);
            }
            //验证数据是否有效
            $result = $this->validate($data,'Article.add');
            if(!$result){
                $this->error($result);
            }
            $filedata = array();
            //保存数据
            $res = $this->model->save($data);
            if($res){
                //或取更新后的字段
                $idp = $this->model->ac_id;
                //判断上传文件是否存在
                if($data['ac_down'] != ''){
                    $down = explode(',',$data['ac_down']);
                    if(is_array($down)){
                        foreach($down as $v){
                            $filedata[] = array('f_model'=>'article','f_model_id'=>$idp,'f_location'=>$v,'f_type'=>'file');
                        }
                    }else{
                        $filedata[] = array('f_model'=>'article','f_model_id'=>$idp,'f_location'=>$data['ac_down'],'f_type'=>'file');
                    }
                }
                //生成新的数据
                if($upimg != ''){
                    $filedata[] = array('f_model'=>'article','f_model_id'=>$idp,'f_location'=>$data['ac_imgthumb'],'f_type'=>'th_img');
                    //判断是否是多上传
                    $imgsAll = explode(',',$data['ac_img']);
                    if(is_array($imgsAll)){
                         foreach($imgsAll as $k){
                             $filedata[] = array('f_model'=>'article','f_model_id'=>$idp,'f_location'=>$k,'f_type'=>'img');
                         }
                    }else{
                         $filedata[] = array('f_model'=>'article','f_model_id'=>$idp,'f_location'=>$data['ac_img'],'f_type'=>'img');
                    }
                }
                //更新文件表
                model('File')->saveAll($filedata);
                $info['status'] = 1;
                $info['msg']    = '发表文章成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '发表文章失败!';
            }
            return json($info);
        }
        //查询出所的分类信息
        $cates = Db::name('category')->select();
        $cates = getSort($cates);
        $tags = db('article')->column('ac_tags');
        $tags = getArr($tags);
        $this->assign('tags',$tags);
        $this->assign('cates',$cates);
        return $this->fetch();
    }
    //文章列表
    public function list()
    {
        $arts = $this->model->select();
        $this->assign('arts',$arts);
        return $this->fetch();
    }
    //修改文章
    public function edit()
    {
        //修改文章
        if(ajax()){
            //echo '<pre>';
            //var_dump(input('post.'));
            //
            $data['ac_title'] = input('post.title') ?? '';
            $data['ac_subhead'] = input('post.subhead') ?? '';
            $data['ac_content'] = input('post.content') ?? '';
            $data['ac_isshow'] = input('post.isshow') ?? '';
            $data['cate_id'] = input('post.cate') ?? '';
            $data['ac_tags'] = input('post.tags') ?? '';
            $data['ac_auth'] = session('u_sname');
            $water = input('post.iswater') ?? '';
            $ac_id   = trim(input('post.id')) ?? '';
            $data['ac_img'] = trim(input('post.imgs'),',') ?? '';
            $data['ac_down'] = trim(input('post.files'),',') ?? '';
            $data['ac_pv'] = trim(input('post.pv'),',') ?? '';
            $data['ac_comment'] = trim(input('post.comment'),',') ?? '';
            $data['ac_recommend'] = input('post.recommend') ?? '';
            //获取状态
            $status = input('post.')['status'] ?? '';
            if($status != ''){
                $data['ac_status'] = implode(',',$status);
            }
            //更新file表的查询条件
            $where = '';
            $filedata = array();
            //判断是否有图片提交过来
            if($data['ac_img'] != ''){
                //判断图片是否进行修改操作(删除,新增加)
                //$imgold = explode(',',$this->model->get($id)->value('ac_img'));//这样获取的不到数据
                $imgOld = explode(',',$this->model->where('ac_id',$ac_id)->value('ac_img'));

                $imgNew = explode(',',$data['ac_img']);
                //查看新增加的图片
                $imgNewDif = array_diff($imgNew,$imgOld);
                //判断是否有新增加
                if(!empty($imgNewDif)){
                    //生成水印
                     if(is_array($imgNewDif)){
                            foreach($imgNewDif as $ai){
                                //取得新添加的图片资源
                                $filedata[] = array('f_model'=>'article','f_model_id'=>$ac_id,'f_location'=>$ai,'f_type'=>'img');
                                if($water == 1){
                                    //获取到图片
                                    $imgone = ROOT_PATH.'public/'.$ai;
                                    //打开图片资源
                                    $imgwater = \think\Image::open($imgone);
                                    //生成水印
                                    $imgwater->water('./logo.png',\think\Image::WATER_SOUTHEAST,50)->save('.'.$ai);
                                }
                            }
                        }
                    model('File')->saveAll($filedata);
                }
                //清空
                $filedata = array();
                //查找删除了的图片
                $imgOldDif = array_diff($imgOld,$imgNew);
                //判断是否发生了变更
                if(!empty($imgOldDif)){
                    foreach($imgOldDif as $v){
                    //判断文件存在不 删除旧的图片
                        if(file_exists(ROOT_PATH.'public'.$v)){
                            $where .= "f_location = '$v' or ";
                            @unlink(ROOT_PATH.'public'.$v);
                        }
                    }
                    //获取旧缩略图
                    $oldthumb = ltrim($this->model->where('ac_id',$ac_id)->value('ac_imgthumb'),'.');
                    $imgOldThumb = ROOT_PATH.'public'.$oldthumb;
                    //删旧的缩略图
                    if(file_exists($imgOldThumb)){
                        @unlink($imgOldThumb);
                    }
                    $where .= "f_location='$oldthumb'";
                    //更新file表
                    model('File')->where($where)->update(array('f_ison'=>0));
                    //重新生成缩略图
                    //打开图像资源
                    $imgNewTuhmb = ROOT_PATH.'public'.$imgNew[0];
                    $img =  \think\Image::open($imgNewTuhmb);
                    //获取原缩略名与路径
                    $path  = '.'.$imgNew[0];
                    $path = str_replace('image','thumb',$path);
                    //拼接文件路径与名
                    $res = $img->thumb(220,150,\think\Image::THUMB_FILLED)->save($path);
                    if($res){
                        $data['ac_imgthumb'] = ltrim($path,'.');
                    }
                    //保存新获取的图片与缩略图
                    $filedata[] = array('f_model'=>'article','f_model_id'=>$ac_id,'f_location'=>$data['ac_imgthumb'],'f_type'=>'th_img');
                    //更新文件表
                    model('File')->saveAll($filedata);                
                }
            }else{
                //文章中所的图片都被 清除了
                $arcinfo = $this->model->field('ac_img,ac_imgthumb')->where('ac_id',$ac_id)->find()->toarray();
                $imgOld = explode(',',$arcinfo['ac_img']);
                foreach($imgOld as $v){
                    //判断文件存在不
                    if(file_exists(ROOT_PATH.'public'.$v)){
                        //获取被删除的文件
                        $where .= "f_location = '$v' or ";
                        @unlink(ROOT_PATH.'public'.$v);
                    }
                }
                //$where = trim($where,'or ');
                //更新file表
                //获取旧缩略图
                $oldthumb = ltrim($arcinfo['ac_imgthumb'],'.');
                $imgOldThumb = ROOT_PATH.'public'.$oldthumb;
                //删旧的缩略图
                if(file_exists($imgOldThumb)){
                        @unlink($imgOldThumb);
                    }
                //更新file表
                $where .= "f_location='$oldthumb'";
                model('File')->where($where)->update(array('f_ison'=>0));
                $data['ac_img'] = '';
                $data['ac_imgthumb'] = '';
            }
            //附件处理
            //清空where 
            $where = '';
            $filedata = array();
            //判断附件文件
            if($data['ac_down'] != ''){
                //判断附件是否进行修改操作(删除,新增加)
                $downOld = explode(',',$this->model->where('ac_id',$ac_id)->value('ac_down'));
                $downNew = explode(',',$data['ac_down']);
                //查找删除了的附件
                $downOldDif = array_diff($downOld,$downNew);
                //删除附件
                foreach($downOldDif as $v){
                    $where .= "f_location = '$v' or ";
                   if(file_exists(ROOT_PATH.'public'.$v)){
                        @unlink(ROOT_PATH.'public'.$v);
                   }
                }
                $where = trim($where,'or ');
                //更新file表
                model('File')->where($where)->update(array('f_ison'=>0));
                //获取新增加的
                $downNewDif = array_diff($downNew,$downOld);
                //不空的时候
                if(!empty($downNewDif)){
                    foreach($downNewDif as $v){
                        $filedata[] = array('f_model'=>'article','f_model_id'=>$ac_id,'f_location'=>$v,'f_type'=>'file');
                    }
                }else{
                    //附件没有发生变动
                    $data['ac_down'] = '';
                }
                //更新文件表
                model('File')->saveAll($filedata);
            }else{
                //所有附件都被删除
                 $downOld = explode(',',$this->model->where('ac_id',$ac_id)->value('ac_down'));
                 foreach($downOld as $v){
                     $where .= "f_location = '$v' or ";
                   if(file_exists(ROOT_PATH.'public'.$v)){
                        @unlink(ROOT_PATH.'public'.$v);
                   }
                }
                $where = trim($where,'or ');
                //更新file表
                model('File')->where($where)->update(array('f_ison'=>0));
            }
            //更新操作
            $result = $this->model->get($ac_id)->save($data);
            if($result){
                $info['status'] = 1;
                $info['msg']    = '修改文章成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '修改文章失败!';
            }
            return json($info);
        }
        //接收要修改的id
        $id = request()->param('id') ?? '';
        //获取原始数据 
        $art = $this->model->get($id)->getData();
        $this->assign('art',$art);
        $tags = db('article')->column('ac_tags');
        $tags = getArr($tags);
        $this->assign('tags',$tags);
        //查询出所的分类信息
        $cates = Db::name('category')->select();
        $cates = getSort($cates);
        $this->assign('cates',$cates);
        return $this->fetch();
    }
    //删除文章
    public function del()
    {
        if(ajax()){
           //接收要修改的id
            $id = request()->param('id') ?? '';
            $res = $this->model->where('ac_id',$id)->update(array('ac_ison'=>0));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '删除文章成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除文章失败!';
            }
            return json($info);
        }
        
    }
    //恢复文章
    public function unlock()
    {
        if(ajax()){
          //接收要修改的id
            $id = request()->param('id') ?? '';
            $res = $this->model->where('ac_id',$id)->update(array('ac_ison'=>1));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '恢复文章成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '恢复文章失败!';
            }
            return json($info);
        }
    }
}