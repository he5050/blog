<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
class Test  extends Controller
{
    public function tab1()
    {
        //最简单的方法
        $all = db('article')->order('ar_createtime desc')->limit(5)->select();
        $zhaoping = db('article')->where('cateid',2)->order('ar_createtime desc')->limit(5)->select();
        $mianshi = db('article')->where('cateid',3)->order('ar_createtime desc')->limit(5)->select();
        $this->assign('all',$all);
        $this->assign('zhaoping',$zhaoping);
        $this->assign('mianshi',$mianshi);
        return $this->fetch();
    }
    public function tab()
    {
        //查询分类
        $cates = db('category')->field('id,c_name')->where('c_ison',1)->order('c_sort')->select();
        $sql = "select a.ac_title,a.cate_id,a.ac_createtime,c.c_name,c_sort from tp_article a join tp_category c on c.id = a.cate_id where (select count(*) from tp_article where cate_id = a.cate_id and ac_createtime > a.ac_createtime ) < ? ORDER BY c.c_sort,cate_id desc,ac_createtime desc";
        //设置每个分类显示的记录数
        $artis = Db::query($sql,[2]);
        $this->assign('artis',$artis);
        //var_dump($artis);
        $this->assign('cates',$cates);
        return $this->fetch();
        //print_r($all);
    }
    public function progress(){
        return $this->fetch();
    }
    public function range()
    {
        return $this->fetch();
    }
    public function drag()
    {
        return $this->fetch();
    }
    public function canvas()
    {
        return $this->fetch();
    }
    public function storage()
    {
        return view();
    }
    public function style(){
    	return view();
    }
    public function cataract(){
    	return view();
    }
    public function layout(){
    	return view();
    }
    public function ldd(){
    	
    }
}