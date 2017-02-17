<?php
namespace app\home\model;
use think\Model;
class Twitter extends Model{
   // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'tw_createtime';
    protected $updateTime = 'tw_updatetime';
    //设置类型自动转换
    protected $type = array(
        'tw_createtime' => 'timestamp:Y-m-d H:i:s',
        'tw_updatetime' => 'timestamp:Y-m-d H:i:s',
    );
    //状态获取器 u_status
    protected function getTwStatusAttr($value)
    {
        $status = [-1=>'删除',0=>'未启用',1=>'正常'];
        return $status[$value];
    }
    protected function getTwUidAttr($value)
    {
        $name = db('user')->where('u_id',$value)->value('u_sname');
        return $name;
    }
}