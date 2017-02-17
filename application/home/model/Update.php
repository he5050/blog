<?php
namespace app\home\model;
use think\Model;
class Update extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'up_createtime';
    protected $updateTime = 'up_updatetime';
    //设置类型自动转换
    protected $type = array(
        'up_createtime' => 'timestamp:Y-m-d H:i:s',
        'up_updatetime' => 'timestamp:Y-m-d H:i:s',
    );
    //修改器
    protected function getUpIsonAttr($value,$data)
    {
        $status = [0=>'删除',1=>'正常'];
        return $status[$data['up_ison']];
    }
}