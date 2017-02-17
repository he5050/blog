<?php
namespace app\home\model;
use think\Model;
class Comment extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'cm_createtime';
    //设置类型自动转换
    protected $type = array(
        'cm_createtime' => 'timestamp:Y-m-d H:i:s',
    );
    //修改器
    protected function getCmIsonAttr($value,$data)
    {
        $status = [0=>'删除',1=>'正常'];
        return $status[$value];
    }
}