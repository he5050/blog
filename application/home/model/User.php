<?php
namespace app\home\model;
use think\Model;
class User extends Model{
   // 定义时间戳字段名 在 插入更新字段的时候自动更新操作
    protected $createTime = 'u_regtime';
    protected $updateTime = 'u_lastlogintime';
    //设置类型自动转换
    protected $type = array(
        'u_regtime' => 'timestamp:Y-m-d H:i:s',
        'u_lastlogintime' => 'timestamp:Y-m-d H:i:s',
    );
    //更新完成
    protected $update = array(
        'u_zipcode' => '023',
    );
    //在插入的时候自动更新字段
    protected $insert = array(
        'u_icon'    => '中国',
    );
    //修改器 u_countrie 0 表示国内 1 表示国外
    protected function setUCountrieAttr($value)
    {
        return ($value == '国外' )? '1' : '0';    
    }
    protected function getUCountrieAttr($value)
    {
        return ($value == '1' )? '国外' : '国内';    
    }
    // u_psw的修改器密码加密处理 $data 为当前传递进来的数据组
    protected function setUPswAttr($value,$data)
    {
        return md5(md5($value).$data['u_salt']);
    }
    //用户性别获取器u_grender
    protected function getUgenderAttr($value)
    {
        return ($value == 1) ? '男' : '女';
    }
    //状态获取器 u_status
    protected function getUStatusAttr($value,$data)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'未验证'];
        return $status[$data['u_status']];
    }
    protected function getUAuthGroupIdAttr($value,$data)
    {
        //取得用户组所在组的ID号
        $group = array('1'=>'超级管理员组','9'=>'普通用户');
        $id = explode(',',$value);
        $str = '';
        foreach($id as $v){
            $str .= $group[$v].',';
        }
        return trim($str,',');
    }
}