<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;
class Info extends Base
{
    public function doc()
    {
        $allArtis = db('article')->field('ac_title,ac_createtime,ac_id')->order('ac_id desc')->select();
        $allArtis = getDoc($allArtis,'ac_createtime');
        $this->assign('allArtis',$allArtis);
        return $this->fetch();
    }
    public function link()
    {
        $links = db('link')->select();
        $this->assign('links',$links);
        return $this->fetch();
    }
    public function reader()
    {
        $reader = db('comment')->field('cm_name,cm_email,cm_url,cm_icon,count(id) as num')->group('u_id')->order('num desc')->select();
        $this->assign('reader',$reader);
        return $this->fetch();
    }
    public function me()
    {
        return $this->fetch();
    }
    public function copyright()
    {
        return $this->fetch();
    }
    public function private()
    {
        return $this->fetch();
    }
}