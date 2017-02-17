<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
class Base extends Controller
{
    //初始化 该类是一个基础类 index下面的所有类都继承该类
    public function _initialize()
    {
    	$this->right();
    	$this->top();
        $this->tags();
        $this->comment();
        $controller = $this->request->controller();
        //如果访问的会员中心的话需要查询
        if($controller == 'Center'){
            if(!session('u_id') || session('u_id') <1){
                 session(null);
                $this->redirect(url('index/index/login')); 
            }
        }
    }
    //顶部菜单数据
    public function top()
    {
        //查询分类信息
        $cates = db('category')->order('c_sort')->select();
        $cates = getSort($cates);
        //查询最新的图片
        $this->assign(array(
            'cates'  => $cates,
            'auto'   => 2,
        ));
    }
    //右侧数据
    public function right(){
        //var_dump($city);
    	//查询最新文章且带图片的
        $img = db('file')->field('f_model_id,f_location')->where('f_type','img')->where('f_ison','1')->where('f_model','article')->order('f_createtime desc')->find();
        //查询推荐文章
        $recommends = db('article')->where('ac_recommend',1)->order('ac_createtime desc')->limit(5)->select();
        $this->assign(array(
            'img' => $img,
            'recommends' => $recommends,
        )); 
    }
    //标签云 文章信息
    public function tags()
    {   
        $tags = db('article')->column('ac_tags');
        $tags = getArr($tags);
        //查询文章数
        $info['acount'] = db('article')->count();
        $info['lasttime'] = db('article')->field('ac_createtime')->order('ac_id desc')->find()['ac_createtime'];
        $info['createtime'] = db('article')->field('ac_createtime')->order('ac_id')->find()['ac_createtime'];
        $info['tag'] = count($tags);
        $info['comment'] = db('comment')->count();
        $info['twitter'] = db('twitter')->count();
        $this->assign('info',$info);
        $this->assign('tags',$tags);
    }
    //查询最新评论
    public function comment()
    {
        //视图查询
        $comms = Db::view('comment','id,cm_name,cm_content,p_id,cm_createtime,cm_email,cm_ison,cm_url,cm_icon,ac_id,u_id,flag')
            ->view('article','ac_title,ac_subhead,ac_imgthumb','comment.ac_id = article.ac_id')
            ->view('category','c_name,c_thumbimg','category.id = article.cate_id')
            ->order('comment.id desc')
            ->where('comment.cm_ison = 1')
            ->limit(5)
            ->select();
        $this->assign('comms',$comms);
    }
//    public function weiyu()
//    {
//        $t = time();
//        $ti = $t-1;
//        $url = "http://blog.jjonline.cn/motto.php?http://blog.jjonline.cn/motto.php?callback=jQuery19103998644621249128_".$ti."&type=random&_=".$t;
//        $weiyu = file_get_contents($url);
//        $weiyu = str_replace("(",'',$weiyu);
//        $weiyu = str_replace(")",'',$weiyu);
//        $weiyu = str_replace("jsonpName",'',$weiyu);
//        $weiyu = json_decode($weiyu);
//        //$data['motto']
//        $data['mt_id'] = $weiyu->id;
//        $data['mt_time'] = strtotime($weiyu->time);
//        $data['mt_type'] = $weiyu->type;
//        $data['mt_content'] = $weiyu->content;
//        $data['mt_source'] = $weiyu->source;
//        $data['mt_click'] = $weiyu->click;
//        $res = db('motto')->where('mt_id',$data['mt_id'])->find();
//        if(empty($res)){
//           db('motto')->insert($data);
//            return '1';
//        }else{
//            return '0';
//        }
//    }
//    public function mo()
//    {
//        ignore_user_abort();//关闭浏览器仍然执行
//        set_time_limit(0);//让程序一直执行下去
//        $interval=3;//每隔一定时间运行
//        $i = 0;
//        do{
//            $msg=date("Y-m-d H:i:s");
//            $res = $this->weiyu();
//            if($res == 1){
//                $i++;
//                echo " ---------- 执行了".$i."次<br>";
//             }
//            sleep($interval);//等待时间，进行下一次操作。
//            }while($i< 5);
//    }
    //返回格言
    public function motto()
    {
        if(request()->isAjax()){
           if(input('post.type' == 'random')){
               $all = db('motto')->count();
                $id = mt_rand(1,$all);
                $res = db('motto')->where('mt_id',$id)->find();
                $res['status'] = 1;
                return json($res);
           }
        }else{
            $res = array('status'=>0);
            return json($res);
        }
    }
    public function weatherInfo()
    {
        if(request()->isAjax()){
//            $infocity = @file_get_contents("http://pv.sohu.com/cityjson?ie=utf-8");
//            $infocity = str_replace('var returnCitySN = ','',$infocity);
//            $infocity = str_replace(';','',$infocity);
//            $ip= json_decode($infocity)->cip;
            $ip = checkInput(input('post.ipinfo'),'125.86.83.177');
            $info = taobaoIP($ip,0);
            //var_dump($info);
            if($info){
                $city = $info->data->city;
                $city = str_replace('市','',$city);
                //echo $city;
                //查询重庆的编码
                $code = db('weather')->field('cityCode')->where('cityName',$city)->find()['cityCode'];
                //echo $code;
                //$url = "http://wthrcdn.etouch.cn/weather_mini?city=".$city;
                //$url = "http://www.weather.com.cn/data/cityinfo/".$code.'.html';
                $key = 'mQDd72BfaBvjKE23HGViFAuRK8x9N9ds';
                $url = "http://api.map.baidu.com/telematics/v3/weather?location=".urlencode($city)."&output=json&ak=".$key;
                $weather = @file_get_contents($url);
                $results = json_decode($weather,true)['results'][0];
                $results = $results ?? 0;
    //            $pm = $results['pm25'];
    //            $cCity = $results['currentCity'];
    //            $weatherData = $results['weather_data'];
                $resInfo['status'] = 1;
                $resInfo['info'] = $results;
                return json($resInfo);
            }else{
                $resInfo['status'] = 0;
                return json($resInfo);
            }
        }
    }
}