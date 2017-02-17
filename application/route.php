<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//定义登录页面
//Route::rule('login','home/Login/login');
//Route::rule('home','home/Index/home');
//Route::rule('logincheck','home/Login/logincheck');
//Route::rule('center','home/Index/center');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
    //登录页面
    'login'         => 'home/login/login',
    'check'         => 'home/Login/logincheck',
    'hout'           => 'home/login/logout',
    //后台首页
    'home/test'     => 'home/index/test',
    //'home/logout'   => 'home/login/logout',
    //'home/index'    => 'home/index/index',
    //'home/main'     => 'home/index/main',
    'home'          => 'home/index/index',
    'main'          => 'home/index/main',
    //分类
    'cadd'          => 'home/category/add',
    'clist'         => 'home/category/list',
    //'cedit/:id'     => ['home/category/edit',['method'=>'get'],['id'=>'\d+']],
    //'cedit'         => ['home/category/cedit',['method'=>'post']],
    'cedit'     => 'home/category/edit',
    'cdel'      => 'home/category/del',
    'clock'     => 'home/category/lock',
    'cunlock'   => 'home/category/unlock',
    'cupload'   => 'home/category/upload',
    'cimg'      => 'home/category/img',
    //文章
    'aadd'      => 'home/article/add',
    'aedit'     => 'home/article/edit',
    'alock'     => 'home/article/lock',
    'aunlock'   => 'home/article/unlock',
    'alist'     => 'home/article/list',
    //评论
    'codel'     => 'home/comment/del',
    'counlock'  => 'home/comment/lock',
    'colist'    => 'home/comment/list',
    //配置
    'olist'     => 'home/option/list',
    'oedit'     => 'home/option/edit',
    'odel'      => 'home/option/del',
    'ologo'     => 'home/option/logo',
    'ouplogo'   => 'home/option/uplogo',
    'oclear'    => 'home/option/clear',
    'ounlock'   => 'home/option/unlock',
    'ojd'       => 'home/option/jd',
    'oupjd'     => 'home/option/upjd',
    'ouplist'   => 'home/option/jdList',
    'ojedit'    => 'home/option/jdEdit',
    'ojdel'     => 'home/option/jdDel',
    'ojunlock'  => 'home/option/jdUnlock',
    'ollist'    => 'home/option/llist',
    'oledit'    => 'home/option/ledit',
    'oladd'     => 'home/option/ladd',
    'oldel'     => 'home/option/ldel',
    'olunlock'  => 'home/option/lunlock',
    //权限
    'paddAuth'  => 'home/privilege/addAuth',
    'peditAuth' => 'home/privilege/editAuth',
    'pdelAuth'  => 'home/privilege/elAuth',
    'plockAuth' => 'home/privilege/lockAuth',
    'punlockAuth'=> 'home/privilege/unlockAuth',
    'plistAuth' => 'home/privilege/listAuth',
    'plistGroup'=> 'home/privilege/listGroup',
    'paddGroup'  => 'home/privilege/addGroup',
    'peditGroup' => 'home/privilege/editGroup',
    'plockGroup' => 'home/privilege/lockGroup',
    'punlockGroup'=> 'home/privilege/unlockGroup',
    'pdelGroup'   => 'home/privilege/delGroup',
    //twitter
    'tlist'      => 'home/twitter/list',
    'tadd'       => 'home/twitter/add',
    'tdel'       => 'home/twitter/del',
    'tunlock'    => 'home/twitter/unlock',
    'tedit'      => 'home/twitter/edit',
    'tuplod'     => 'home/twitter/upload',
    //更新日志
    'uadd'       => 'home/update/add',
    'uplist'       => 'home/update/list',
    'uedit'       => 'home/update/edit',
    'udel'       => 'home/update/del',
    'uunlock'       => 'home/update/unlock',
    //用户
    'usadd'      => 'home/user/add',
    'usdel'      => 'home/user/del',
    'usedit'      => 'home/user/edit',
    'uslock'      => 'home/user/lock',
    'usunlock'      => 'home/user/unlock',
    'useditForm'      => 'home/user/editForm',
    'ulist'      => 'home/user/list',
    //用户管理
    //'user/edit/:id'     => 'home/user/edit',
    'tab'       => 'index/test/tab',
    //前台注册 登录
    'index'   => 'index/index/index',
    'reg'          => 'index/index/reg',
    'enter'        =>  'index/index/login',
    'center'        =>  'index/center/center',
    'commen'       =>   'index/center/commen',
    'info'        =>   'index/center/info',
    'edit'         =>   'index/center/edit',
    'list'        =>    'index/center/list',
    'add'         =>   'index/center/add',
    'out'        =>  'index/index/out',
    'motto'      => 'index/index/motto',
    'c/:c'       => 'index/index/cate',
    'a/:a'   => 'index/index/arti',
    't/:t'    => 'index/index/tag',
    'comm'    => 'index/index/comm',
    'weatherInfo' => 'index/base/weatherInfo',
    //信息
    'doc'  => 'index/info/doc', 
    'link'   => 'index/info/link',
    'reader' => 'index/info/reader',
    'me'     => 'index/info/me',
    'copyright'  => 'index/info/copyright',
    'private'    => 'index/info/private',
    'twi'    => 'index/index/twi',
    //********************下面路由是一个测试路由*************************//
    'progress' =>'index/test/progress',
    'range'     => 'index/test/range',
    'drag'    => 'index/test/drag',
    'canvas'   => 'index/test/canvas',
    'storage'  => 'index/test/storage',
    'style'   => 'index/test/style',
    'cataract' => 'index/test/cataract',
    'layout'   => 'index/test/layout',
];
