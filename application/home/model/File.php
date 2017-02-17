<?php
namespace app\home\model;
use think\Model;
class File extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'f_createtime';
    protected $updateTime = 'f_updatetime';
    //设置类型自动转换
    protected $type = array(
        'f_createtime' => 'timestamp:Y-m-d H:i:s',
        'f_updatetime' => 'timestamp:Y-m-d H:i:s',
    );
}