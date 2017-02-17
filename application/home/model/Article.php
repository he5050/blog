<?php
namespace app\home\model;
use think\Model;
class Article extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'ac_createtime';
    protected $updateTime = 'ac_updatetime';
    //设置类型自动转换
    protected $type = array(
        'ac_createtime' => 'timestamp:Y-m-d H:i:s',
        'ac_updatetime' => 'timestamp:Y-m-d H:i:s',
    );
    //修改器
    protected function getAcIsonAttr($value,$data)
    {
        $status = [0=>'删除',1=>'正常'];
        return $status[$data['ac_ison']];
    }
    protected function getAcIsshowAttr($value,$data)
    {
        $status = [0=>'不公开',1=>'公开'];
        return $status[$data['ac_isshow']];
    }
     //修改器
    protected function getAcStatusAttr($value,$data)
    {
        $status = [1=>'精华',2=>'热帖',3=>'美图',4=>'优秀',5=>'置顶',6=>'推荐',7=>'原创',8=>'爆料'];
        $str = '';
        if($value != ''){
            $value = explode(',',$value);
            foreach($value as $v){
                $str .= $status[$v].','; 
            }
            $str = trim($str,',');
        }
        return $str;
    }
    protected function getAcRecommendAttr($value,$data)
    {
        $status = [0=>'不推荐',1=>'推荐'];
        return $status[$value];
    }
}