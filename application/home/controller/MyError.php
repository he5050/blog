<?php
namespace app\home\controller;
use think\Controller;
use think\Request;

class MyError extends Controller
{
    public function _empty()
    {
        session(null);
        return $this->redirect(url('home/login/login'));
    }
}