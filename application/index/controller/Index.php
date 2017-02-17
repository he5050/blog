<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;
class Index extends Base
{
    //初始页面
    public function index()
    {
         //查询最新的文章信息
        $artcs = db('article')->alias('ar')->field("ar.ac_id,ar.ac_title,ar.ac_subhead,ar.ac_auth,ar.ac_imgthumb,ar.ac_tags,ar.ac_pv,ar.ac_comment,ar.ac_status,ar.cate_id,ar.ac_createtime,ca.c_name,ca.c_thumbimg,ca.id")->join('category ca','ar.cate_id = ca.id')->order('ac_createtime desc')->paginate(config('arti.page'));
        //查询焦点图
        $jds = db('jd')->field('jd_img,jd_content,jd_url')->where('jd_ison',1)->order('jd_sort,jd_id desc')->limit(5)->select();
        $this->assign('jds',$jds);
        $this->assign('artcs',$artcs);
        return $this->fetch();
    }
    //分类文章显示
    public function cate()
    {
        //获取传传递过来的分类ID号
        $id = request()->param('c') ?? 1;
        // id 10 为twitter 本来计划在后期 直接读取配置文件的值
        if($id != 5){
             //查询当前分类的名称同时获取该分类下面的子类
            $cate = db('category')->field('c_name,p_id,c_info')->where('id',$id)->find();
            //如果当前分类为顶级分类则查询出该分类下面的所有子分类的文章
            $ids = db('category')->where('p_id',$id)->column('id');
            //判断是否为空
            if(!empty($ids)){
                //查询该分类以及子类中的文章
                $ids = implode(',',$ids).','.$id;
                $artcs = db('article')->alias('ar')->field("ar.ac_id,ar.ac_title,ar.ac_subhead,ar.ac_auth,ar.ac_imgthumb,ar.ac_tags,ar.ac_pv,ar.ac_comment,ar.ac_status,ar.cate_id,ar.ac_createtime,ca.c_name,ca.c_thumbimg,ca.id")->join('category ca','ar.cate_id = ca.id')->order('ac_createtime desc,ca.id')->where("ca.id in($ids)")->paginate(config('arti.page'));
            }else{
                //查询该分类下面的文章
                 $artcs = db('article')->alias('ar')->field("ar.ac_id,ar.ac_title,ar.ac_subhead,ar.ac_auth,ar.ac_imgthumb,ar.ac_tags,ar.ac_pv,ar.ac_comment,ar.ac_status,ar.cate_id,ar.ac_createtime,ca.c_name,ca.c_thumbimg,ca.id")->join('category ca','ar.cate_id = ca.id')->order('ac_createtime desc')->where('ca.id',$id)->paginate(config('arti.page'));
            }          
            //查询父类信息
            if($cate['p_id'] != 0){
                $catep = db('category')->field('id,c_name')->where('id',$cate['p_id'])->find();
                $this->assign('catep',$catep);
            }
            $this->assign(array(
                'artcs' => $artcs,
                'cate' => $cate,
            ));
            return $this->fetch();
        }else{
            $twitter = db('category')->field('c_name,c_info')->where('id',$id)->find();
            //查询所有twitter信息
            //$twitters = db('twitter')->order('tw_id desc')->select();
            $twitters = Db::view('twitter','tw_id,tw_content,tw_comment,tw_createtime,tw_img')
                ->view('user','u_sname,u_icon','twitter.tw_uid = user.u_id')
                ->order('tw_id desc')
                ->paginate(config('arti.page'));
            $this->assign('twitters',$twitters);
            $this->assign('twitter',$twitter);
            return $this->fetch('twitter');
        }
    }
    //内容详细页
    public function arti()
    {
        //获取传传递过来的分类ID号
        $id = request()->param('a') ?? 1;
        //查询该文章的信息
        $arts = db('article')->alias('ar')->field("ar.ac_id,ar.ac_title,ar.ac_subhead,ar.ac_auth,ar.ac_content,ar.ac_tags,ar.ac_pv,ar.ac_comment,ar.ac_status,ar.cate_id,ar.ac_createtime,ca.c_name,ca.id")->join('category ca','ar.cate_id = ca.id')->order('ac_createtime desc')->where('ar.ac_id',$id)->find();
        //查询当前文章所属分类的上级分类
        if($arts['id'] != 0){
            //查询该文章分类的上级分类信息
            $pid = db('category')->where('id',$arts['id'])->value('p_id');
            $catep = db('category')->field('id,c_name')->where('id',$pid)->find();
            $this->assign('catep',$catep);
        }
        //查询该文章所属分类中的其他文章
        $otherArts = db('article')->field('ac_id,ac_createtime,ac_title')->where("ac_id != $id")->where('cate_id',$arts['cate_id'])->limit(5)->select();
        //查询该文章的前一篇文章 与后一篇文章
        $where = ($id-1).','.($id+1) ;
        $pageArts = db('article')->field('ac_id,ac_title')->where("ac_id in ($where)")->order('ac_id')->select();
        $pageArts = versaArr($pageArts);
        //查询该文章下面的所有评论信息 评论ID可以设置写入配置文件
        //$comm = db('comment')->where('ac_id',$id)->select();
        $comm = db('comment')->where('ac_id',$id)->paginate(config('comm.pageszie'));
        $count = count($comm);
        if(!empty($comm)){
            $this->assign('comm1',$comm);
            $comm = F_comment($comm);
            $this->assign('comm',$comm);
        }
        //更新阅读 PV
        db('article')->where('ac_id',$id)->setInc('ac_pv');
        $this->assign('pageArts',$pageArts);
        $this->assign('arts',$arts);
        $this->assign('otherArts',$otherArts);
        return $this->fetch();
    }
    //评论提交
    public function comm()
    {
        if(request()->isAjax()){
            $data['ac_id'] = input('post.gid') ?? '';
            $data['p_id']  = input('post.pid') ?? 0 ;
            $data['cm_name'] = input('post.comname') ?? (session('u_sname') ?? '');
            $data['cm_email'] = input('post.commail') ?? (session('u_email') ?? '');
            $data['cm_url']   =  input('post.comurl') ?? (session('url') ?? '');
            $uid     = input('post.uid')   ?? '';
            $f = input('post.f') ?? '';
            $flag =  input('post.flag')   ?? ''; // 0表示游客,1表示注册用户
            //定义返回信息
            $info = array();
            //判断 在点击回复时验证是否回复的自己
            if($f == 111){
                //查询当前的要回复的pid的评论是不是自己发的
                if($uid != ''){
                    $id = db('comment')->field('u_id')->where('flag',$flag)->where('id',$data['p_id'])->find();
                    if($id['u_id'] == $uid){
                        $info['status'] = 0;
                        $info['msg']   = '您不能自己回复自己啊!';
                        return json($info);
                    }else{
                        $info['status'] = 1;
                        $info['msg']   = 'success';
                        return json($info);
                    }
                }else{
                    $info['status'] = 1;
                    $info['msg']   = 'success';
                    return json($info);
                }
                
                exit;
            }
            if($data['cm_url'] == ''){
                $data['cm_url'] = "javascript:;";
            }
            $data['cm_content'] = input('post.comment') ?? '';
            $com = model('comment');
            
            $res = $com->validate($data,'Comment.add');  
            //验证没有通过
            if(!$res){
                $info['status'] = 0;
                $info['msg']    = $res;
                return  json($info);
            }
            //如果验证通过
            if($res){
                if($flag == 0){
                    //查询当前当前用户 是否评论过
                    $uData = db('comment')->field('u_id,cm_icon')->where('u_id',$uid)->where('flag',$flag)->group('u_id')->order('id desc')->find();
                    //如果没有评论过
                    if(empty($uData)){
                        // 查询当前最后评论的uid
                        $maxUid = db('comment')->field('u_id')->order('u_id desc')->find();
                        //如果还没有一次评论
                        if(empty($maxUid)){
                            $uid = 1;
                        }else{
                            $uid = $maxUid['u_id']+1;
                        }
                        //随机头像
                        $data['cm_icon'] = 'images/head/tig'.rand(1,53).'.gif';
                    }else{
                        $data['cm_icon'] = $uData['cm_icon'];
                    }                    
                }else{
                    //注册用户评论
                    $uData = db('comment')->field('cm_icon,cm_url')->where('u_id',$uid)->where('flag',$flag)->group('u_id')->order('id desc')->find();
                    //表示注册用户已评论过
                    if(!empty($uData)){
                       $data['cm_icon'] = $uData['cm_icon'];
                        //如果传进来的url为空的话
                        if($data['cm_url']){
                            //判断用户url是否存在
                            if(!session('cm_url')){
                                session('cm_url',$data['cm_url']);
                            }else{
                                //如果session url 存在 则判断两条记录是否相等
                                if($data['cm_url'] != session('cm_url')){
                                    session('cm_url',$data['cm_url']);
                                }
                            }
                        }
                    }else{
                        $icon = db('user')->field('u_icon')->where('u_id',$uid)->find();
                        //如果用户没有头像
                        if(empty($icon['u_icon'])){
                            //随机头像
                            $data['cm_icon'] = 'images/head/tig'.rand(1,53).'.gif';
                            db('user')->where('u_id',$uid)->update(array('u_icon'=>$data['cm_icon']));
                            if(!session('cm_icon')){
                                session('cm_icon',$data['cm_icon']);
                            }
                        }else{
                            $data['cm_icon'] = $icon['u_icon'];
                        }
                        //表示用户是第一次评论
                        if(!session('cm_url')){
                            session('cm_url',$data['cm_url']);
                        }
                        //
                    }
                    $data['u_id'] = $uid;
                }
                $data['flag'] = $flag;
                $data['u_id'] = $uid;
                $data['cm_createtime'] = time();
                if($com->insert($data)){
                       $info['status'] = 1;
                       $info['msg']    = 'success';
                       $info['uid']   = $data['u_id'];
                       $info['u_sname'] = $data['cm_name'];
                       $info['u_icon'] = $data['cm_icon'];
                        //更新文章表中该文章中的评论数自增  这里设计 数据库的时候有问题 没有考虑好
                        db('article')->where('ac_id',$data['ac_id'])->setInc('ac_comment');
                    }else{
                        $info['status'] = 0;
                        $info['msg']    = 'sorry,您的评论太刺激了,有点接受不了!';
                    }
                return json($info);
            }
        }
    }
    //查询标签
    public function tag()
    {
        $tag = request()->param('t') ?? '';
        $artcs = db('article')->alias('ar')->field("ar.ac_id,ar.ac_title,ar.ac_subhead,ar.ac_auth,ar.ac_imgthumb,ar.ac_tags,ar.ac_pv,ar.ac_comment,ar.ac_status,ar.cate_id,ar.ac_createtime,ca.c_name,ca.c_thumbimg,ca.id")->join('category ca','ar.cate_id = ca.id')->order('ac_createtime desc')->where("ac_tags like '%$tag%'")->paginate(config('arti.page'));
        $this->assign('tag',$tag);
        $this->assign('artcs',$artcs);
        return $this->fetch();
    }
    public function reg()
    {
        if(checkSession()){
            //$this->success('您已登录成功!跳转到用户中心......',url('index/center/center'));
            $url = url('index/center/center');
            header("Location:$url");
            exit;
        }
        // 提交注册
        if(request()->isAjax()){
            $info = array('code'=>400,'info'=>"");
            $data['u_sname'] = input('post.sname') ?? '';
            $data['u_email'] = input('post.email') ?? '';
            $data['u_regip'] = input('post.ip') ?? '';
            $data['captcha'] = input('post.capt') ?? '';
            if($data['u_sname'] == '' || empty($data['u_sname'])){
                $info['info'] = '用户名或密码不能为空!';
                return json($info);
            }
            if($data['u_email'] == '' || empty($data['u_sname'])){
                $info['info'] = '邮箱不能空!';
                return json($info);
            }
            $captcha = new \think\captcha\Captcha();
            if(!$captcha->check($data['captcha'])){
                $info['code'] = 500;
                $info['info'] = '验证码填写错误!';
                return json($info);
            }
            $flag = db('user')->where('u_email',$data['u_email'])->find();
            if(!empty($flag)){
                $info['info'] = '邮箱已注册过可以直接登录!';
                return json($info);
            }
            $pwd = input('post.pwd') ?? '';
            $rpwd = input('post.rpwd') ?? '';
            if($pwd != $rpwd){
                $info['info'] = '用户名或密码出错!';
                return json($info);
            }else{
                $data['u_salt'] = substr(uniqid(),3);
                $data['u_psw']  = md5(md5($pwd).$data['u_salt']);
            }
            if($data['u_regip'] != ''){
                $areaInfo = taobaoIP($data['u_regip'],2);
                if($areaInfo){
                    if($areaInfo->code == 0 && !empty($areaInfo->data)){
                        $country = $areaInfo->data->country;
                        if($country == '中国'){
                                $data['u_countrie'] = 0;
                        }else{
                                $data['u_countrie'] = 1;
                        }
                            $data['u_province'] = $areaInfo->data->region;
                            $data['u_city']     = $areaInfo->data->city;
                            $data['u_from']     = $areaInfo->data->area;
                            $county = $areaInfo->data->county;
                    }
                        $data['u_area'] = $country.','.$data['u_from'].','.$data['u_province'].','.$data['u_city'].','.$data['u_from'];
                }
            }
            $data['u_regtime'] = time();
            unset($data['captcha']);
            $res = db('user')->insert($data);
            if($res){
                $info['code'] = 200;
                $info['info'] = 'success';
                cookie('u_email',$data['email']);
                cookie('u_id',null);
                cookie('u_psw',null);
                cookie('url',null);
                cookie('u_icon','null');
                return json($info);
            }else{
                $info['code'] = 500;
                $info['info'] = '好像出了点问题,可能需要重新注册!';
                return json($info);
            }
        }
        return $this->fetch();
    }
    public function login()
    {
        if(checkSession()){
            //$this->success('登录成功!跳转到用户中心......',url('index/center/center'));
            $url = url('index/center/center');
            header("Location:$url");
            exit;
        }
        if(request()->isAjax()){
             //接收登录信息
        $data = array();
        $info = array('code'=>400,'info'=>'');    
        // 表单使用提post传递过来的使用post接收 
        $data['u_email'] = input('post.user') ?? '';
        $data['u_psw']   = input('post.pwd') ?? '';
        $data['captcha'] = input('post.capt') ?? '';
        $ip              = input('post.ip') ?? '';
        //如果没有接收到任何为空数组就返回登录页面
        if(empty($data)){
            $info['info'] = '用户名或密码不能空,验证码必须写!';
            return json($info);
        }
        $captcha = new \think\captcha\Captcha();
        if(!$captcha->check($data['captcha'])){
            $info['code'] = 500;
            $info['info'] = '验证码填写错误!';
            return json($info);
        }else{
            $userinfo = db('user')->field('u_id,u_sname,u_salt,u_psw,u_email,u_status,u_icon')->where('u_email',$data['u_email'])->find();
            //判断用户是否存在
            if(!empty($userinfo)){
                //判断用户账号是否正常
                if($userinfo['u_status'] == 0 || $userinfo['u_status'] == -1){
                    $info['info'] = '账号异常,请联系管理员!';
                    return json($info);
                }
                //验证密码是否正确
                $psw = md5(md5($data['u_psw']).$userinfo['u_salt']);
                if($psw === $userinfo['u_psw']){
                    unset($userinfo['u_salt']);
                    unset($userinfo['u_psw']);
                    //查询当前登录用户对应URL
                    $url = db('comment')->field('cm_url')->where('flag',1)->where('u_id',$userinfo['u_id'])->group('u_id')->order('id desc')->find();
                    if($url){
                        $userinfo['url'] = $url['cm_url'];
                    }
                    //设置session
                    foreach($userinfo as $k=>$v){
                        session(strtolower($k),strtolower($v));
                    }
                    //设置cookie
                    unset($userinfo['u_status']);
                    foreach($userinfo as $k=>$v){
                        cookie(strtolower($k),strtolower($v));
                    }
                    cookie('u_psw',null);
                    $update['u_lastip']   = $ip;
                   if($update['u_lastip'] == '127.0.0.1'){
                        $update['u_lastarea'] = '服务端登录';
                   }else{
                       $area = taobaoIP($update['u_lastip']);
                       if($area){
                          $update['u_lastarea'] = $area; 
                       }
                   }
                    $update['u_lastlogintime'] = time();
                    db('user')->where('u_email',$data['u_email'])->update($update);
                    //清除
                    unset($userinfo); 
                    $info['code'] = 200;
                    $info['info'] = 'success';
                    return json($info);
                    
                }else{
                    //密码不正确
                    $info['info'] = '用户名或密码错误!';
                    return json($info);
                }
            }else{
                //用户名不正确
                $info['info'] = '好像网络出了点问题!';
                return json($info);
            }
        }
            //return json(array(1,2,3,4,5,6,7));
        }
        return $this->fetch();
    }
    public function out()
    {
        session(null);
        cookie('u_id',null);
        //$this->redirect(url('index/index/login'));
         $url = url('index/index/login');
        header("Location:$url");
        exit;
    }
    public function twi()
    {
        //评论
        if(input('post.rname')){
            $info['u_sname'] = checkInput(input('post.rname'));
            $info['tw_id'] = checkInput(input('post.tid'));
            $info['tc_comment'] = checkInput(input('post.r'));
            $info['u_id']  = session('u_id');
            $info['tc_createtime'] = time();
            if(db('twcomm')->insert($info)){
                //增加评论数
                db('twitter')->where('tw_id', $info['tw_id'])->setInc('tw_comment');
                $str = "<li>";
                $str .= "<span class='name'>".$info['u_sname']."</span> ".$info['tc_comment']."<span class='time'>0 秒前</span>";
                $c = "'@".$info['u_sname'].":'";
                $str .= '<em><a href="javascript:re('.$c.','.$info['tw_id'].');">回复</a></em>';
                $str .= "</li>";
            }else{
                $str = 'err1';
            }  
        }else{
            if(input('post.tid')){
                $tid = checkInput(input('post.tid'));
                $res = db('twcomm')->where('tw_id',$tid)->select();
                if(session('u_id')>0){
                    $str = '';
                }else{
                    $str = '<h2>登录之后可以发表评论!</h2>';
                }
                if($res){
                    foreach($res as $v){
                        $str .= "<li>";
                        $str .= "<span class='name'>".$v['u_sname']."</span> ".$v['tc_comment']."<span class='time'>".timeago($v['tc_createtime'])."</span>";
                        $c = "'@".$v['u_sname'].":'";
                        $str .= '<em><a href="javascript:re('.$c.','.$v['tw_id'].');">回复</a></em>';
                        $str .= "</li>";
                    }
                }else{
                    $str = '<h2>目前还没有人评论过!!</h2>';
                }
            }else{
                $str = '<h2>登录之后可以发表评论!</h2>';
            }
        }
        return $str;
    }
}
