<?php
namespace app\index\model;
use think\Model;
class Comment extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $updateTime = false;
    protected $createTime = 'cm_createtime';
}