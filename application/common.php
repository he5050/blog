<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
 /**
     * 递归操作
     * @param  array $arr      要操作的数据
     * @param  int   [$p_id=0]  父ID
     * @param  int   [$lev=0]  层级
     */
 function getSort($arr,$p_id=0,$lev=0){
    static $list = array();
    foreach($arr as $v){
        if($v['p_id'] == $p_id){
            //添加一个元素到数组中
            $v['lev'] = $lev;
            //把当前的数据添加到list当中
            $list[] = $v;
            //递归操作
            getSort($arr,$v['id'],$lev+1);
        } 
    }
    $result = $list;
    unset($list);
    $list = '';
    return $result;
}
function get_onlineip() {
    $ch = curl_init('http://www.ip138.com/ip2city.asp');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $a  = curl_exec($ch);
    preg_match('/[(.*)]/', $a, $ip);
    return $ip[1];
 }
/**
 * 获取用户登录ip
 * 
 */
function getIPaddress(){
        $IPaddress='';
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $IPaddress = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $IPaddress = getenv("HTTP_CLIENT_IP");
            } else {
                $IPaddress = getenv("REMOTE_ADDR");
            }
        }
        return $IPaddress;
    }
function getClientIP()  
{  
    global $ip;  
    if (getenv("HTTP_CLIENT_IP"))  
        $ip = getenv("HTTP_CLIENT_IP");  
    else if(getenv("HTTP_X_FORWARDED_FOR"))  
        $ip = getenv("HTTP_X_FORWARDED_FOR");  
    else if(getenv("REMOTE_ADDR"))  
        $ip = getenv("REMOTE_ADDR");  
    else $ip = "Unknow";  
    return $ip;  
}  
/**
 * 调用淘宝ip库 获取登录地点
 * @param  取得客户端登录ip
 * @return $data 城市信息
 */
function taobaoIP($clientIP,$info = 1){
    //超时设置处理
       $timeout = array(
            'http' => array(
                'timeout' => 5 //设置一个超时时间，单位为秒
            )
        );
        $ctx = stream_context_create($timeout);
        $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$clientIP;
        $taobao = @file_get_contents($taobaoIP,0,$ctx);
        if($taobao){
            $IPinfo = json_decode($taobao);
            if($IPinfo->code == 0){
               $country = $IPinfo->data->country;
                $area    = $IPinfo->data->area;
                $province = $IPinfo->data->region;
                $city = $IPinfo->data->city;
                $county = $IPinfo->data->county;
                $data = "$country,$area,$province,$city,$county";
                if($info == 1){
                    return $data;
                }else{
                    return $IPinfo;
                } 
            }else{
                return false;
            }
            
        }else{
            return false;
        } 
    }
/**
 * 遍历目录
 * @param  要遍历的目录
 * @return 返回的结果
 */
function read_all_dir($dir){
    //定义接收的数组
    $result = array();
    //打开目录
    $handle = opendir($dir);
    if($handle){
        //开始循环
        while(($file = readdir($handle)) !== false ){
            //如果不是 . ..执行后面的循环
            if($file != '.' && $file != '..'){
                $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                //判断如是要路径接着循环 不是路径是文件就保存
                if(is_dir($cur_path)){
                        $result['dir'][$cur_path] = read_all_dir ( $cur_path );
                    }else{
                        $result['file'][] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
}
/**
 * 获取所有文件
 * @param  传入已获取到的所有目录信息与文件信息
 * @return 返回文件信息
 */
function getFile($arr){
    static $result = array();
    if(is_array($arr)){
        foreach($arr as $k=>$v){
            if($k == 'file'){
                //提取所有文件
                foreach($v as $all){
                    $result[] = $all;
                }
            }else{
                getFile($v);
            }
        }
    }
    //我现在环境是windos所要替换路径
    return repArr($result);
}
/**
 * 数组的回调函数
 * @param 要替换的数组
 * @return 返回处理之后的数组
 */
function repArr($arr){
    $result = array();
    foreach($arr as $k=>$v){
        //查找要截取的位
        $result[$k] = str_replace('\\','/',$v);
    }
    return $result;
}
/**
 * 添加完整路径
 * @param  要添加的数组
 * @return 返回添加完成物理路径的数组
 */
function addArr($arr){
    $result = array();
    foreach($arr as $k=>$v){
        $result[$k] = ROOT_PATH.'public'.$v;
    }
    return repArr($result);
}
/**
 * 专门用于处理文章标签获取所有标签 然后统一返回一个一维数组
 * @param  array $arr
 * @return 返回一维数组
 */
function getArr($arr){
    $result = array();
    $arr = rArr($arr);
    foreach($arr as $k=>$v){
        if($v != ''){
            $result[] = explode(',',$v);
        }
    }
    $res = array();
    if(count($result) >= 1){
        foreach($result as $value){
            if(is_array($value)){
                foreach($value as $v1){
                    $res[] = $v1;
                }
            }
        }
    }
    $res = array_unique($res);
    $res = setUrl($res);
    return $res;
}
/**
 * 给标签设置一个url
 * 
 */
function setUrl($arr){
    $result = array();
    foreach($arr as $v){
        $url = url('index/index/tag',['t'=>$v]);
        $result[] = array('tag'=>$v,'url'=>$url);
    }
    return $result;
}
/**
 * 用于处理中文的逗号 全都替换成英文的逗号
 * @param array $arr
 * @return array  返回替换之后数组
 */
function rArr($arr){
    $result = array();
    if(count($arr) >= 1){
        foreach($arr as $k=>$v){
            $result[$k] = str_replace('，',',',$v);
        }
    }else{
        $result = $arr;
    }
    return $result;
}
/**
 * 把一个二维数组的键值变成与数组里面的键值一样
 */
function addK($arr){
    $result = array();
    foreach($arr as $v){
        $result[$v['id']] = $v;
    }
    return $result;
}
/**
 * 获取评论
 * 接收传递进行来的评论数据
 */
function getComment($arr){
    if(count($arr)<= 1){
        return $arr;
    }
    $arr = addK($arr);
    $tree = array();
    foreach($arr as $v){
        if(isset($arr[$v['p_id']]) && $v['p_id'] != 0){
            $arr[$v['p_id']]['son'][] = &$arr[$v['id']];
        }else{
            $tree[] = &$arr[$v['id']];
        }
    }
    return $tree;
}

/**
 * 顶级评论
 */
function F_comment($arr){
    $arr = getComment($arr);
    $str = '';
    $i = 1;
    foreach($arr as $v){
        if($v['flag'] == 1){
            if($v['u_id'] == 1){
               $visiter = "c_admin"; 
            }else{
                $visiter = "c_user";
            }
        }else{
            $visiter = "c_visiter";
        }
        //顶级
        $str .=  "<li class='comment dpt_line' id='comment-".$v['id']."'>";
        //楼层
        $str .=  "<span class='comment_floor'>".$i++."楼</span>";             
        $str .=  "<a name='".$v['id']."'></a>";
        //头像
        $str .=  "<div class='avatar'><img lay-src='__INDEX__".$v['cm_icon']."' data-src='__INDEX__".$v['cm_icon']."'></div>";
        $str .=  "<div class='comment-info'>";
        $str .=  "<div class='comment-content'><p>".$v['cm_content']."</p></div>";
        $str .=  "<div class='comment-meata'><span class='comment-poster ".$visiter."' title='".$v['cm_name']."'><a href='".$v['cm_url']."' target='_blank' rel='external nofollow'>".$v['cm_name']."</a></span><span class='comment-time'>".timeago($v['cm_createtime'])."</span><a href='#comment-".$v['id']."' onclick='commentReply(".$v['id'].",this)' class='comment-reply-btn'>回复</a></div></div></li>";
        //如果存在子评论
        if(isset($v['son'])){
            $str .= S_comment($v['son'],$v['cm_name']);
        }
    }
    //
    $str = str_replace('\'',"\"",$str);
    //替换表情
    $str = comment2emoji($str);
    return $str;
}
/**
 * 获取子评论
 */
function S_comment($arr,$name,$i=1){
    
    $str  =  '';
    $str .=  "<ul class='children'>";
    foreach($arr as $v){
        if($v['flag'] == 1){
            if($v['u_id'] == 1){
               $visiter = "c_admin"; 
            }else{
                $visiter = "c_user";
            }
        }else{
            $visiter = "c_visiter";
        }
    $str .=  "<li class='comment comment_children' id='comment-".$v['id']."'>";
    $str .=  "<span class='comment_floor'>".$i++."#</span>";
    $str .=  "<a name='".$v['id']."'></a>";
    $str .=  "<div class='avatar'><img lay-src='__INDEX__".$v['cm_icon']."' data-src='__INDEX__".$v['cm_icon']."'></div>";
    $str .=  "<div class='comment-info'><div class='comment-content'><p><span>@</span><a href='#comment-".$v['p_id']."'><strong>".$name."</strong></a>:".$v['cm_content']."</p></div>";
    $str .=  "<div class='comment-meata'><span class='comment-poster ".$visiter."' title='".$v['cm_name']."'><a href='".$v['cm_url']."' target='_blank' rel='external nofollow'>".$v['cm_name']."</a></span><span class='comment-time'>".timeago($v['cm_createtime'])."</span><a href='#comment-".$v['id']."' onclick='commentReply(".$v['id'].",this)' class='comment-reply-btn'>回复</a> </div></div>";
        if(isset($v['son'])){
            $str .= S_comment($v['son'],$v['cm_name'],$i);
        }
        $i--;
    $str .= "</li>";
    }

    $str .= "</ul>";
    return $str;
}
function timeago($unixtime) {
	$etime = time() - $unixtime;
    if ($etime < 1) return '刚刚';    
    $interval = array (         
        12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $unixtime).')',
        30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $unixtime).')',
        7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $unixtime).')',
        24 * 60 * 60            =>  '天前',
        60 * 60                 =>  '小时前',
        60                      =>  '分钟前',
        1                       =>  '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}
/**
 * @des emoji 标签处理评论并输出
 * @param $str 评论数据
 * @return string
 */
function comment2emoji($str) {
	$data = array(
		array(
			'img'=>'__INDEX__'.'images/emoji/e056.png',
			'title'=>'可爱'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e057.png',
			'title'=>'开心'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e414.png',
			'title'=>'发呆'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e402.png',
			'title'=>'奸笑'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e106.png',
			'title'=>'色'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e417.png',
			'title'=>'亲亲'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e108.png',
			'title'=>'流汗'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e403.png',
			'title'=>'惆怅'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e058.png',
			'title'=>'伤心'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e40b.png',
			'title'=>'衰'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e411.png',
			'title'=>'大哭'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e410.png',
			'title'=>'混乱'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e107.png',
			'title'=>'恐怖'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e059.png',
			'title'=>'生气'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e408.png',
			'title'=>'瞌睡'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e10c.png',
			'title'=>'外星人'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e022.png',
			'title'=>'爱心'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e00e.png',
			'title'=>'强悍'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e421.png',
			'title'=>'鄙视'
		),
		array(
			'img'=>'__INDEX__'.'images/emoji/e011.png',
			'title'=>'胜利'
		),
	);
	foreach($data as $key=>$value) {
		$str = str_replace('['.$value['title'].']','<img src="'.$value['img'].'" title="'.$value['title'].'">',$str);
	}
	return $str;
}
/**
 * 用于处理上下页
 * 如果就是第一篇文章的时候
 */
function versaArr($arr){
    $result = array();
    if(empty($arr)){
        $result[0] = array('ac_id'=>'javascript:;','ac_title'=>'没了');
        $result[1] = array('ac_id'=>'javascript:;','ac_title'=>'没了');
    }else{
       if(count($arr) == 1){
            //表示当前查询到了第一篇或是最后一篇文章
            if($arr[0]['ac_id']>2){
                //表示最前为最后一篇
                $result[0] = $arr[0];
                $result[1] = array('ac_id'=>'javascript:;','ac_title'=>'没了');
            }else{
                //表示最前为第一篇
                $result[0] = array('ac_id'=>'javascript:;','ac_title'=>'没了');
                $result[1] = $arr[0];
            }
            return $result;
        }else{
            $arr[0]['ac_id'] = url('index/index/arti',['a'=>$arr[0]['ac_id']]);
            $arr[1]['ac_id'] = url('index/index/arti',['a'=>$arr[1]['ac_id']]);
            return $arr;
        } 
    }
    
    
}
/**
 * 获取状态
 * 
 */
function getStatus($status)
{
    $arr = array('1'=>'正常','0'=>'禁用','-1'=>'删除');
    return $arr[$status];
}
/**
 * 全局的ajax判断
 */
function ajax()
{
    if(request()->isAjax() && session('u_id')>0 && session('u_status') == 1){
        return true;
    }else{
        return false;
    }
}
/**
 * 用户获取评论跳转分页
 * 文章id与评论id
 */
function getPage($ac_id,$co_id)
{
    //取得当前文章的所有评论
    $ids = count(db('comment')->where('ac_id',$ac_id)->column('id'));
    $res = ceil($ids/(config('comm.pagesize')));
    if($res > 1){
       return '?page='.$res; 
    }
}
//ajax上传检查
function checkSession()
{
    if(session('u_id') >0 && (session('u_status') ==1 || session('u_status') == 2)){
        return true;
    }else{
        return false;
    }
}
//检查输入
function checkInput($input,$value=''){
    if(!is_array($input)){
        $input = trim($input,'');
        $input = trim($input,' ');
        $res = isset($input) ? $input : $value;
    }else{
        foreach($input as $k=>$v){
            $res[$k] = trim(trim($v,' '),'');
            $res[$k] = isset($res[$k]) ? $res[$k] : $value;
        }
    }
    return $res;
}
function getParam($param,$type='',$default=''){
    if($type == ''){
        
    }
}
function pregIP($test){  
        /** 
        匹配ip 
        规则： 
            **1.**2.**3.**4 
            **1可以是一位的 1-9，两位的01-99，三位的001-255 
            **2和**3可以是一位的0-9，两位的00-99,三位的000-255 
            **4可以是一位的 1-9，两位的01-99，三位的001-255 
            四个参数必须存在 
        */  
        $rule = '/^((([1-9])|((0[1-9])|([1-9][0-9]))|((00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))))\.)((([0-9]{1,2})|(([0-1][0-9]{2})|(2[0-4][0-9])|(25[0-5])))\.){2}(([1-9])|((0[1-9])|([1-9][0-9]))|(00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5])))$/';  
        preg_match($rule,$test,$result);  
        return $result;  
}
//文章归档
function getDoc($arr,$key){
    $res = array();
    foreach($arr as $k=>$v){
        $time = date('F Y',$v[$key]);
        $res[$time][] = $v;
    }
    return $res;
}
    //使用curl库
    function getJosn($url){
        $ch = curl_init();
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }