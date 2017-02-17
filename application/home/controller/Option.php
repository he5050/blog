<?php
namespace app\home\controller;
use think\Db;
use app\common\controller\MyAuth;
//操作配置项目
class Option extends MyAuth
{
    //自动实例化模型
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Option');
    }
    //修改配置项目
    public function edit()
    {
        if(ajax()){
            $data = array();
            foreach(input('post.') as $k=>$v){
                $data[$k] = $v;
            }
            $id = $data['op_id'];
            unset($data['op_id']);
            $res = $this->model->get($id)->save($data);
            if($res){
                $this->putFile();
                $info['status'] = 1;
                $info['msg']    = '更新配置成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '更新配置失败!';
            }
            return json($info);
            exit;
        }
        $id = request()->param('id') ?? '';
        $op = $this->model->get($id)->getData();
        $this->assign('op',$op);
        return $this->fetch();
    }
    //配置项目列表 保存配置
    public function list()
    {
        //保存配置项目
        if(ajax()){
            $data = array();
            $sort =input('post.')['sort'];
            $all  =input('post.');
            unset($all['sort']);
            static $i = 0;
            foreach($all as $k=>$v){
                $ke = explode(',',$k);
                $data[] = array('op_id'=>$ke[0],'op_title'=>$ke[1],'op_value'=>$v,'op_sort'=>$sort[$i]);
                $i ++;
            }
            unset($i);
            $res = $this->model->saveAll($data);
            if($res){
                $this->putFile();
                $info['status'] = 1;
                $info['msg']    = '更新配置成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '更新配置失败!';
            }
            return json($info);
            exit;
        }
        //查询出已存在配置项目
        $ops = $this->model->order('op_sort')->select();
        $this->assign('ops',$ops);
        return $this->fetch();
    }
    //锁定配置项
    public function del()
    {
         //接收要修改的id
        if(ajax()){
            $id = input('post.id') ?? '';
            $res = $this->model->where('op_id',$id)->update(array('op_ison'=>0));
            if($res){
                $info['status'] = 1;
                $info['msg']    = '删除成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '删除失败!';
            }
            return json($info);
        }
    }
    //恢复配置项目
    public function unlock()
    {
         //接收要修改的id
        if(ajax()){
            $id = input('post.id') ?? '';
            $res = $this->model->where('op_id',$id)->update(array('op_ison'=>1));
            if($res){
               $info['status'] = 1;
               $info['msg']    = '启用成功!';
            }else{
                $info['status'] = 0;
                $info['msg']    = '启用失败!';
            }
            return json($info);
        }
    }
    //生成配置文件web.php
    public function putFile() {
    	$config = $this->model->column('op_value','op_title');
    	$path = '../application/extra/web.php';
    	$str = "<?php \n return ".  var_export($config,true).'; ?>';
    	file_put_contents($path, $str);
    }
    //清除多的图片以及上传资源
    public function clear()
    {
        //查找系统上传的所的资源
        $dir = ROOT_PATH . 'public' . DS . 'upload';
        $result = getFile(read_all_dir($dir));
        //查找使用的资源文件
        //查找file表中的
        $file = addArr(db('file')->where('f_ison = 1')->column('f_location'));
        //设置差集
        $difArr = array_diff($result,$file);
        //删除无效的文件
        foreach($difArr as $v){
            @unlink($v);
        }
        //$this->success('清除成功!',url('list'));
        return json(array('status'=>1,'msg'=>'清除成功!'));
    }
    //上传logo
    public function uplogo()
    {
       if(ajax()){
            $savePath = '/static/index/images/logos/';
            $return = array();
            $file = request()->file('images');
            $info = $file->move(ROOT_PATH.'/public'.$savePath,'');
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
    //修改logo
    public function logo()
    {
        if(ajax()){
            $logs = input('post.images') ?? '';
            if(empty($logs)||$logs == ''){
                return json(array('status'=>2));
            }
            $flag = copy(ROOT_PATH.'/public'.$logs,ROOT_PATH.'/public/static/index/images/logo.png');
            if($flag){
                @unlink(ROOT_PATH.'/public'.$logs);
                $res['status'] = 1;
            }else{
                $res['status'] = 0;
            }
            return json($res);
        }
        return $this->fetch();
    }
    //焦点图上传保存到文件中
    public function upjd(){
        if(ajax()){
            $savePath = '/upload/jd/';
            $return = array();
            $file = request()->file('images');
            $info = $file->rule('uniqid')->move(ROOT_PATH.'/public'.$savePath);
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
    //焦点图与对应的连接写入数据库当中
    public function jd()
    {
        if(ajax()){
            //接收数据
            $images = checkInput(input('post.')['images']);
            $link = checkInput(input('post.')['link']);
            $content = checkInput(input('post.')['content']);
            //移动每一个最后一个数组
            array_pop($images);
            array_pop($link);
            array_pop($content);
            //var_dump($link);
            //如果图片不为空
            if(!empty($images)){
                //写入数据库
                if(is_array($images)){
                    foreach($images as $k=>$v){
                        $data[] = ['jd_img'=>$v,'jd_url'=>$link[$k],'jd_content'=>$content[$k],'jd_createtime'=>time()];
                        $file[] = ['f_model'=>'jd','f_model_id'=>0,'f_location'=>$v,'f_type'=>'img'];
                    }
                    $res = db('jd')->insertAll($data);
                    if($res){
                        model('file')->saveAll($file);
                        return json(['status'=>1,'msg'=>'上传成功,即将跳转到焦点图列表']);
                    }else{
                        return json(['status'=>0,'msg'=>'上传失败,请重新上传']);
                    }
                }
            }else{
                return json(['status'=>0,'msg'=>'上传内容为空']);
            }
        }
        return $this->fetch();
    }
    public function jdList()
    {
        $jds = db('jd')->select();
        $this->assign('jds',$jds);
        return $this->fetch();
    }
    public function jdDel()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $res = db('jd')->where('jd_id',$id)->update(array('jd_ison'=>0));
            if($res){
                return json(['status'=>1,'msg'=>'删除成功!']);
            }else{
                return json(['status'=>0,'msg'=>'删除失败!']);
            }
        }else{
            return json(['status'=>0,'msg'=>'您好像进行了非法操作!']);
        }
    }
    public function jdUnlock()
    {
        if(ajax()){
            $id = request()->param('id') ?? '';
            $res = db('jd')->where('jd_id',$id)->update(array('jd_ison'=>1));
            if($res){
                return json(['status'=>1,'msg'=>'恢复成功!']);
            }else{
                return json(['status'=>0,'msg'=>'恢复失败!']);
            }
        }else{
            return json(['status'=>0,'msg'=>'您好像进行了非法操作!']);
        }
    }
    public function jdEdit()
    {
        if(ajax()){
            $id = checkInput(input('post.id'));
            $data['jd_img'] = checkInput(input('post.image'));
            $data['jd_url'] = checkInput(input('post.link'));
            $data['jd_sort'] = checkInput(input('post.sort'));
            $data['jd_content'] = checkInput(input('post.content'));
            $data['jd_updatetime'] = time();
            $res = db('jd')->where('jd_id',$id)->update($data);
            if($res){
                $file = ['f_model'=>'jd','f_model_id'=>0,'f_location'=>$data['jd_img'],'f_type'=>'img'];
                model('file')->save($file);
                return json(['status'=>1,'msg'=>'修改成功!']);
            }else{
                return json(['status'=>0,'msg'=>'修改失败!']);
            }
        }
        $id = request()->param('id') ?? '';
        $info = db('jd')->where('jd_id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    //________________________________________________链接管理
    public function llist()
    {
        $link = db('link')->select();
        $this->assign('link',$link);
        return $this->fetch();
    }
    public function ladd()
    {
        if(ajax()){
            $data['li_title'] = checkInput(input('post.title'));
            $data['li_url']   = checkInput(input('post.url'));
            $data['li_icon'] = checkInput(input('post.icon'));
            $data['li_createtime'] = time();
            if(db('link')->insert($data)){
                return json(['status'=>1,'msg'=>'添加成功!']);
            }else{
                return json(['status'=>0,'msg'=>'添加失败!']);
            }
        }
        return $this->fetch();
    }
    public function ledit()
    {
        if(ajax()){
            $id = checkInput(input('post.id'));
            $data['li_title'] = checkInput(input('post.title'));
            $data['li_url']   = checkInput(input('post.url'));
            $data['li_icon'] = checkInput(input('post.icon'));
            $data['li_updatetime'] = time();
            if(db('link')->where('li_id',$id)->update($data)){
                return json(['status'=>1,'msg'=>'修改成功!']);
            }else{
                return json(['status'=>0,'msg'=>'修改失败!']);
            }
        }
        $id = checkInput(request()->param('id'));
        $link = db('link')->where('li_id',$id)->find();
        $this->assign('link',$link);
        return $this->fetch();
    }
    public function ldel()
    {
        if(ajax()){
            $id = checkInput(request()->param('id'));
            $res = db('link')->where('li_id',$id)->update(['li_ison'=>0]);
            if($res){
                return json(['status'=>1,'msg'=>'删除成功!']);
            }else{
                return json(['status'=>0,'msg'=>'删除失败!']);
            }
        }
    }
    public function lunlock()
    {
        if(ajax()){
            $id = checkInput(request()->param('id'));
            $res = db('link')->where('li_id',$id)->update(['li_ison'=>1]);
            if($res){
                return json(['status'=>1,'msg'=>'恢复成功!']);
            }else{
                return json(['status'=>0,'msg'=>'恢复失败!']);
            }
        }
    }
}
