<?php
namespace app\home\model;
use think\Model;
class Option extends Model{
    // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'op_createtime';
    protected $updateTime = 'op_updatetime';
    //设置类型自动转换
    protected $type = array(
        'op_createtime' => 'timestamp:Y-m-d H:i:s',
        'op_updatetime' => 'timestamp:Y-m-d H:i:s',
    );
    //获取器
    protected function getOpIsonAttr($value,$data)
    {
        $status = [0=>'删除',1=>'启用'];
        return $status[$data['op_ison']];
    }
    //获取器
    protected function getOpTipsAttr($value,$data)
    {
        if($data['op_type'] == 'radio'){
            //拆分成数组
            $data = explode(',',$value);
            return $data;
        }else{
            return $value;
        }
    }
}