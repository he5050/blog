/*
 *
 */
/**image lazy load plugin**/
(function(a){a.fn.scrollLoading=function(b){var c={attr:"data-src",container:a(window),callback:a.noop};var d=a.extend({},c,b||{});d.cache=[];a(this).each(function(){var h=this.nodeName.toLowerCase(),g=a(this).attr(d.attr);var i={obj:a(this),tag:h,url:g};d.cache.push(i)});var f=function(g){if(a.isFunction(d.callback)){d.callback.call(g.get(0))}};var e=function(){var g=d.container.height();if(a(window).get(0)===window){contop=a(window).scrollTop()}else{contop=d.container.offset().top}a.each(d.cache,function(m,n){var p=n.obj,j=n.tag,k=n.url,l,h;if(p){l=p.offset().top-contop,l+p.height();if((l>=0&&l<g)||(h>0&&h<=g)){if(k){if(j==="img"){f(p.attr("src",k))}else{p.load(k,{},function(){f(p)})}}else{f(p)}n.obj=null}}})};e();d.container.bind("scroll",e)}})(jQuery);

/**Base Func**/
$(function () {
    var Bod = $("body");
    /**导航条**/
    var pointer = $('.pointer'),
        poinetNode = $('.nav_list .item'),
        _pos = pointer.attr('data-now'),
        pos = ((_pos-1)*102-5);
        $.each(poinetNode,function (i,n) {
            if($(n).hasClass('current')) {return true;}
            if(i>7) {return false;}
            var _left = (i*102-5);
            $(n).hover(
                function () {
                    pointer.stop().animate({left:_left}, "slow");
                },
                function () {
                    pointer.stop().animate({left:pos}, "slow");
                }
            );
        });
    /**文章评论 当开始评论设置了全局,并存在文章ID的时候才可以进行评论**/  
    if(_info && _info.logid && _info.isOpenComment) {
        //定义表情
        var face = [{ "img": "images\/emoji\/e056.png", "title": "[\u7b11cry]" }, { "img": "images\/emoji\/e057.png", "title": "[\u5f00\u5fc3]" }, { "img": "images\/emoji\/e414.png", "title": "[\u53d1\u5446]" }, { "img": "images\/emoji\/e402.png", "title": "[\u5978\u7b11]" }, { "img": "images\/emoji\/e106.png", "title": "[\u8272]" }, { "img": "images\/emoji\/e417.png", "title": "[\u4eb2\u4eb2]" }, { "img": "images\/emoji\/e108.png", "title": "[\u6d41\u6c57]" }, { "img": "images\/emoji\/e403.png", "title": "[\u60c6\u6005]" }, { "img": "images\/emoji\/e058.png", "title": "[\u4f24\u5fc3]" }, { "img": "images\/emoji\/e40b.png", "title": "[\u8870]" }, { "img": "images\/emoji\/e411.png", "title": "[\u5927\u54ed]" }, { "img": "images\/emoji\/e410.png", "title": "[\u6df7\u4e71]" }, { "img": "images\/emoji\/e107.png", "title": "[\u6050\u6016]" }, { "img": "images\/emoji\/e059.png", "title": "[\u751f\u6c14]" }, { "img": "images\/emoji\/e408.png", "title": "[\u778c\u7761]" }, { "img": "images\/emoji\/e10c.png", "title": "[\u5916\u661f\u4eba]" }, { "img": "images\/emoji\/e022.png", "title": "[\u7231\u5fc3]" }, { "img": "images\/emoji\/e00e.png", "title": "[\u5f3a\u608d]" }, { "img": "images\/emoji\/e421.png", "title": "[\u9119\u89c6]" }, { "img": "images\/emoji\/e011.png", "title": "[\u80dc\u5229]" }];
        var _PostNameNode    = $('#comname'),//comname 评论昵称
            _PostMailNode    = $('#commail'),//commail 评论者的邮箱
            _PostUrlNode     = $('#comurl'),//comurl   评论者的URL
            _PostCodeNode    = $('.comment_verfiy_code'),//验证码
            _PostGidNode     = $('#comment-gid'),//gid   文章的ID号
            _PostPidNode     = $('#comment-pid'),//pid   上级评论的ID号
            _PostCommentNode = $('#comment'),//comment   评论的内容     
            _PostFormNode    = $('#commentform'), //评论提交的表单
            _PostInfoNode    = $('.comment_info'),// 评论的提交提示出，用于显示错误,帮助信息
            _PostSubBtnNode  = $('#comment_submit'),//评论提交
            _PostComUidNode  = $('#comment-uid');//评论者的id(如果是游客在第一次发表评论后会生成一个UID并保存到cookie中)
        //设置默认评论人的信息从cookie中读取
        if(!_info.isLogin){
            _PostNameNode.val(J.cookie('u_sname'));
            _PostMailNode.val(J.cookie('u_email'));
            _PostUrlNode.val(J.cookie('cm_url'));
            _PostComUidNode.val(J.cookie('u_id'));
        }else{
            //登录的情况一下 清除cookie
            J.cookie('u_sname',null);
            J.cookie('u_email',null);
            J.cookie('cm_url',null);
            J.cookie('u_id',null);
        }
        //定义 处理表情的方法,文本中的 [惆怅][混乱][流汗][外星人] 替换成对应的图标
        function HandlComment(str) {
            //使用J命名空间调用 html_encode方法把 < , > 等符号转成html符号
            var ct = J.html_encode(str),
                //定义一个空文本
                content = '';
            //遍历表情对象
            $.each(face, function(i, n) {
                //进行正则匹配
                var regxe = new RegExp('\\' + n.title.toString(), 'gm');
                if (i == 0) {
                    content = ct.replace(regxe, '<img src="' + _info.tpl + n.img + '">');
                } else {
                    content = content.replace(regxe, '<img src="' + _info.tpl + n.img + '">');
                }
            });
            return content;
        };
        /** 修改提交按钮的状态 **/
        function DisabledBtn(isDisabled,time) {
            if(isDisabled) {
                _PostSubBtnNode.addClass('subDisabled').attr('disabled',true).fadeTo('slow',0.3);
            }else {
                _PostSubBtnNode.removeClass('subDisabled').attr('disabled',false).fadeTo('slow',1);
            }
            if(time) {  //定时取消禁用状态
                setTimeout(function () {
                    _PostSubBtnNode.removeClass('subDisabled').attr('disabled',false).fadeTo('slow',1);
                },time*1000);
            }
        };
        _PostCommentNode.focus(function () {
            _PostInfoNode.empty().append('Ctrl+Enter快速提交').css('color','#555');
        });
        /**提交成功后用于处理返回信息**/
        /**
         * @param retStr ajax返回html
         * @return array['status':boolean,'info':text]
         */
        function handleCommentReturn(retStr) {
            //去除字符串中看不到的\r\n回车符号 去除字符串中所有空白
            var ret = J.trimAll(J.trimEnter(retStr));
            //匹配 评论是否需要审核
            var isPcre = ret.match(/<divclass=\"main\"><p>[^<]+<\/p>/);
            if(isPcre) {
                //评论成功 需要审核
                var text = isPcre[0].match(/[\u4E00-\u9FA5]+，[\u4E00-\u9FA5]+/gi);
                if(text) {
                    return {'status':true,'info':'success'};
                }
                var text = isPcre[0].match(/[\u4E00-\u9FA5]+：[\u4E00-\u9FA5]+/gi);
                if(text) {
                    return {'status':false,'info':text[0]};
                }
                return true;
            }else {
                return {'status':true,'info':'评论成功'};
            }
            //意外情况
            return false;
        }
        /**表单提交事件**/
        _PostFormNode.submit(function () {
            var uid = _PostComUidNode.val();
            if(uid == ''|| uid == 0){
                uid = J.cookie('uid');
            }
            var url = _PostUrlNode.val();
            if(url == '' || url == undefined){
                url = J.cookie('cm_url');
            }
            var name = _PostNameNode.val();
            if(name == '' || name == undefined){
                name = J.cookie('u_sname');
            }
            var email = _PostMailNode.val();
            if(email == '' || email == undefined){
                email = J.cookie('u_email');
            }
            var flag = _info.isLogin ? 1 : 0;
            //定义要发送的数据
            var _postData = {
                    'gid':_PostGidNode.val(),//文章的ID号
                    'pid':_PostPidNode.val(),//评论的上级ID号
                    'comname':name, //评论昵称
                    'commail':email, //评论邮箱
                    'comurl':url,   //评论者的url
                    'imgcode':_PostCodeNode.val(), //验证码
                    'comment':_PostCommentNode.val(), //内容
                    'uid' : uid,
                    'flag' : flag
                };
//            //获取当前表单提交的action 页面
                _Url     = _info.url+_PostFormNode.attr('action');
//                //判断如果不存在提交页面 则使用如
                _Url     = _Url ? _Url : _info.url+'/comm.html';
            //效验内容
            if(_postData.comment.length==0) {
                _PostInfoNode.empty().append('请输入评论').css('color','#CC0033');
                //_PostCommentNode.focus();                
                return false;
            }
            //前端显示1024个字符
            if(_postData.comment.length>1024) {
                _PostInfoNode.empty().append('评论字数过多').css('color','#CC0033');
                //_PostCommentNode.focus();                
                return false;
            }
            //未登录状态下的各种效验
            if(!_info.isLogin) {
                //验证码如果有
                if(_info.isCommentCode && _postData.imgcode.length<=1) {
                    _PostInfoNode.empty().append('验证码格式错误').css('color','#CC0033');
                    _PostCodeNode.focus();                
                    return false;
                }
                //昵称
                if(_postData.comname.length==0) {
                    _PostInfoNode.empty().append('昵称格式错误').css('color','#CC0033');
                    _PostNameNode.focus();                
                    return false;
                }
                //邮箱
                if(!J.isMail(_postData.commail)) {
                    _PostInfoNode.empty().append('邮箱格式错误').css('color','#CC0033');
                    _PostMailNode.focus();                
                    return false;
                }
                //url 如果有 http:// https://
                if(_postData.comurl.length>0 && !J.isUrl(_postData.comurl)) {
                    _PostInfoNode.empty().append('网址格式错误').css('color','#CC0033');
                    _PostUrlNode.focus();                
                    return false;
                }
            }
            //如果验证都 修改submit可以提交
            DisabledBtn(true);
            //清空当前提示,帮助信息,重新添加进度信息
            _PostInfoNode.empty().append('正在提交...').css('color','#555');
            $.ajax({
                type: "POST",
                url: _Url,
                dataType:'html',
                data: _postData,
                success: function (msg) {
                    var result = handleCommentReturn(msg),_tips;
                    if(result.status) {
                        _PostCommentNode.val('');
                        //验证码处理
                        if(_info.isCommentCode) {
                            _PostCodeNode.val('');
                            var _imgNode = $('.comment_verfiy_container img'),
                                _src     = _imgNode.attr('src');
                            if(!_imgNode.attr('data-src')) {
                                _imgNode.attr('data-src',_src);
                            }
                            var _src = _imgNode.attr('data-src')+'?_rmd='+new Date().getTime();
                            _imgNode.attr('src',_src);                         
                        }
                        //不需要评论审核
                        if(!_info.isCommentCheck) {
                            var _nickName = _postData.comname ? _postData.comname : JSON.parse(msg).u_sname,
                                _tips     =  '评论成功';
                            //判断评论的上级ID 如果存在上级ID表示为回复别的人评论
                            if(_postData.pid>0) {
                                //子评论包裹容器处理 <ul class="children"></ul>
                                var _newNode = [
                                    '<li class="comment comment_children" style="background:#FBFCE7;">',
                                    '<div class="avatar"><img src="'+_info.tpl+'images/noAvator.jpg"></div>',
                                    '<div class="comment-info">',
                                        '<div class="comment-content"><p>'+HandlComment(_postData.comment)+'</p></div>',
                                        '<div class="comment-meata">',
                                            '<span class="comment-poster">'+_nickName+' </span>',
                                            '<span class="comment-time">刚刚</span>',
                                        '</div>',
                                    '</div>',
                                    '</li>'
                                ].join('');
                                //抢沙发和评论次数切换
                                //获取当前评论次数
                                if($('#comments h3 span').text()) {
                                    $('#comments h3 span').remove();
                                    $('#comments h3').append('<strong>1</strong>');
                                }else {
                                    var num = parseInt($('#comments h3 strong').text())+1;
                                    $('#comments h3 strong').empty().text(num);
                                }
                                if($('#comment-'+_postData.pid).find('.children').text()) {
                                    //回复子评论
                                    $('#comment-'+_postData.pid).find('.children:first').append(_newNode);
                                }else {
                                    //回复盖楼
                                    $('#comment-'+_postData.pid).append('<ul class="children">'+_newNode+'</ul>');
                                }                                
                            }else {
                                //新楼层评论
                                var _newNode  = [
                                    '<li class="comment dpt_line" style="background:#FBFCE7;">',
                                        '<span class="comment_floor">#新盖楼</span>',
                                        '<div class="avatar"><img src="'+_info.tpl+'images/noAvator.jpg"></div>',
                                        '<div class="comment-info">',
                                            '<div class="comment-content"><p>'+HandlComment(_postData.comment)+'</p></div>',
                                            '<div class="comment-meata"><span class="comment-poster">'+_nickName+' </span> <span class="comment-time">刚刚</span></div>',
                                        '</div>',
                                    '</li>'
                                ].join('');
                                //抢沙发和评论次数切换
                                if($('#comments h3 span').text()) {
                                    $('#comments h3 span').remove();
                                    $('#comments h3').append('<strong>1</strong>');
                                }else {
                                    var num = parseInt($('#comments h3 strong').text())+1;
                                    $('#comments h3 strong').empty().text(num);
                                }
                                $('.comment_views .parents').prepend(_newNode);                               
                            }
                            //在没有登录的情况下
                            if(!_info.isLogin){
                                //设置cookie
                                J.cookie('u_sname',_postData.comname);
                                J.cookie('u_email',_postData.commail);
                                J.cookie('cm_url',_postData.comurl);
                                J.cookie('u_id',JSON.parse(msg).uid);
                                J.cookie('u_icon',JSON.parse(msg).u_icon);
                            }
                        }else {
                            _tips   =  '评论成功，管理员审核通过后方可显示';
                        }                        
                        _PostInfoNode.empty().append(_tips).css('color','#99CC33');
                    }else {
                        _PostInfoNode.empty().append(result.info).css('color','#CC0033'); 
                    }
                    DisabledBtn(false);
                    return false; 
                },
                error:function () {
                    _PostInfoNode.empty().append('网络异常，请刷新页面后再试').css('color','#CC0033');
                    DisabledBtn(false);
                    return false;
                }
            });
            return false;
        });
        //绑定 Ctrl+Enter submit
        _PostCommentNode.keypress(function(event){
            if(event.ctrlKey && event.keyCode == 13 || event.which == 10) {
                if(_PostSubBtnNode.hasClass('subDisabled')){return false;}else{$(this).submit();}
            }
        });
        //表情包 _FaceBtnNode 按钮  _FaceInsertNode 评论内容的div
        var _FaceBtnNode = $('.comment_face_btn'),_FaceInsertNode = $('.form_textarea');
        //点击表情按钮的时候 出现表情包
        _FaceBtnNode.click(function () {
            //判断表情包是否显示出来了
            if($(this).hasClass('readyState')) {
                $('#Face').slideToggle();
            }else {
                //表情包显示出来
                $(this).addClass('readyState')
                //定义显示的元素
                var _FaceString = ['<div id="Face" class="faceContainer"><p>'];
                //遍历每一个表情包
                $.each(face,function(i,n) {
                    var _value = '<a href="javascript:;" title="'+n.title+'" data-title="'+n.title+'"><img src="'+_info.tpl + n.img+'"></a>';
                    //把每一个表情包放到前面定义的_FaceString数组当中
                    _FaceString.push(_value);
                });
                _FaceString.push('</p></div>');
                //拆分 成string 相当于php 中的implode()方法 把_FaceString数组转成string
                _FaceString    = _FaceString.join('');
                //在评论操作模块后面添加表情
                _FaceInsertNode.after(_FaceString);
                //给每一个表情绑定一个click事件,在单击的时候把当前的表情追加到评论内容后面
                $('#Face a').bind({
                    'click':function () {
                        //取得当前表情的title
                        var _FaceString = $(this).attr('data-title'),
                            //获取评论的的dom对象
                            obj         = $("#comment").get(0);
                        //IE下面 当前激活选中区，即高亮文本块，或文档中用户可执行某些操作的其它元素。
                        if (document.selection) {
                            //获取焦点
                            obj.focus();
                            //创建选定区 获取 TextRange 对象 文本对象 也就是当前光标所有位子
                            var sel = document.selection.createRange();
                            //添加选中的表情
                            sel.text = _FaceString;
                        } else if (typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number') {
                            // selectionStart 输入性元素selection起点的位置   selectionStart 输入性元素selection结束点的位置
                            //比如 我输入 我爱你  我当标选中了 爱你 就会把爱你替换 所选的表情
                            //获取焦点
                            obj.focus();
                            //获取焦点的起始位置
                            var startPos = obj.selectionStart;
                            //获取焦点的结束位置
                            var endPos = obj.selectionEnd;
                            //获取当文本的值 就相当于前面的我爱你
                            var tmpStr = obj.value;
                            //替换文本 内容
                            obj.value = tmpStr.substring(0, startPos) + _FaceString + tmpStr.substring(endPos, tmpStr.length);
                        } else {
                            //直接添加
                            obj.value += _FaceString;
                        }
                        $('#Face').slideToggle();
                    }
                });
            }
        });
    };
    /**twiter ajax**/
    if(_info && _info.isOpenTwitter && _info.isPageTwiter && _info.isLogin) {
        var _tokenNode       = $('#token'),//token
            _twiterNode      = $('#addTwiter'),//t
            _PostSubTBtnNode = $('.addTwiterBtn'),//Tbtn
            _infoNode        = $('.addTwiterInfo'),
            _twiterForm      = $('.addTwiterForm');
        _twiterNode.focus(function () {
            _infoNode.empty().append('Ctrl+Enter快速提交').css('color','#555');
        });
        /**control btn status**/
        function DisabledTBtn(isDisabled,time) {
            if(isDisabled) {
                _PostSubTBtnNode.addClass('subDisabled').attr('disabled',true).fadeTo('slow',0.3);
            }else {
                _PostSubTBtnNode.removeClass('subDisabled').attr('disabled',false).fadeTo('slow',1);
            }
            if(time) {  //定时取消禁用状态
                setTimeout(function () {
                    _PostSubTBtnNode.removeClass('subDisabled').attr('disabled',false).fadeTo('slow',1);
                },time*1000);
            }
        };
        /**handle status ajax return**/
        /**
         * @param retStr ajax返回html
         * @return boolean
         */
        function handleTwiterReturn(retStr) {
            var ret = J.trimAll(J.trimEnter(retStr));
            if(/<spanclass=\"actived\">/.test(ret)) {
                return true;
            }
            if(/<spanclass=\"error\">/.test(ret)) {
                return false;
            }
            //意外情况
            return false;
        }
        _twiterForm.submit(function () {
            var _Data   = {'t':$.trim(_twiterNode.val()),'token':_tokenNode.val()},
                _Url    = _twiterForm.attr('action'),
                _Url    = _Url?_Url:_info.url+'admin/twitter.php?action=post';
            if(_Data.t.length==0) {
                _infoNode.empty().append('请输入碎语').css('color','#CC0033');
                //_twiterNode.blur();
                return false;
            }
            if(_Data.t.length>140) {
                _infoNode.empty().append('碎语不得多于140字').css('color','#CC0033');
                //_twiterNode.blur();
                return false;
            }
            DisabledTBtn(true);
            _infoNode.empty().append('正在提交...').css('color','#555');
            $.ajax({
                type: "POST",
                url: _Url,
                dataType:'html',
                data: _Data,
                success: function (msg) {
                    if(handleTwiterReturn(msg)) {
                        _twiterNode.val('');
                        var _newNode = [
                            '<li class="twiter_list" style="background:#FBFCE7;">',
                                '<img src="'+$('.twiter_list img').attr('src')+'" alt="'+$('.twiter_list img').attr('alt')+'" class="twiter_avatar" />',
                                '<p class="twiter_content">'+J.html_encode(_Data.t)+'</p>',
                                '<p class="twiter_info"><span class="twiter_author">'+$('.twiter_list img').attr('alt')+'</span><span class="twiter_time"><i class="fa fa-clock-o"></i> 刚刚</span></p>',
                            '</li>'
                        ].join('');
                        $('.twiter').prepend(_newNode);
                        _infoNode.empty().append('碎语发布成功').css('color','#99CC33');
                    }else {
                        _infoNode.empty().append('碎语发布失败，请刷新页面后再试').css('color','#CC0033'); 
                    }
                    DisabledTBtn(false);
                    return false;                   
                },
                error:function (XMLHttpRequest,textStatus,errorThrown) {
                    _infoNode.empty().append('网络异常，请刷新页面后再试').css('color','#CC0033');
                    DisabledTBtn(false);
                    return false;
                }
            });
            // prevent default event
            return false;
        });
        //bind Ctrl+Enter submit
        _twiterNode.keypress(function(event){
            if(event.ctrlKey && event.keyCode == 13 || event.which == 10) {
                if(_PostSubTBtnNode.hasClass('subDisabled')){return false;}else{$(this).submit();}
            }
        });
    }
    /**change vcode**/
    $('.comment_verfiy_container img').click(function () {
        var src = $(this).attr('src');
        if(!$(this).attr('data-src')) {
            $(this).attr('data-src',src);
        }
        var _src = $(this).attr('data-src')+'?_rmd='+new Date().getTime();
        $(this).attr('src',_src);
    });
    $('.twiter_reply_ipt_code img').click(function () {
        var src = $(this).attr('src');
        if(!$(this).attr('data-src')) {
            $(this).attr('data-src',src);
        }
        var _src = $(this).attr('data-src')+'&_rmd='+new Date().getTime();
        $(this).attr('src',_src);
    });
    
    /**lazy load**/
    if($('.thumbnail img:first').data('src')) {
       $.each($('.thumbnail img'),function (i,n) {$(n).scrollLoading();}); 
    }
    if($('.article_exp_img img:first').data('src')) {
       $.each($('.article_exp_img img'),function (i,n) {$(n).scrollLoading();});
    }
    if($('.article_content img:first').data('src')) {
        $.each($('.article_content img'),function (i,n) {$(n).scrollLoading();});
    }
    if($('.avatar img:first').data('src')) {
       $.each($('.avatar img'),function (i,n) {$(n).scrollLoading();}); 
    }
    if($('.page_reader_avatar img:first').data('src')) {
       $.each($('.page_reader_avatar img'),function (i,n) {$(n).scrollLoading();}); 
    }
    /**back to top**/
    function scrollTop() {
        var scroller  = $('.scrollTop'),
            windoInfo = J.windowSize(),
            _right    = (windoInfo.width-_info.maxWidth+20)/2-42;
            if(_right>0) {
                scroller.css('right',_right+'px');
            }else {
                scroller.css('right','10px');
            }
        document.documentElement.scrollTop+document.body.scrollTop>200?scroller.fadeIn():scroller.fadeOut();
    };
    //init
    scrollTop();
    //event
    $(window).resize(function () {scrollTop();});
    $(window).scroll(function(){
        scrollTop();
    });
    $('.scrollTop').click(function(){
        $('html,body').animate({scrollTop:0},300);
    }); 
    /**widget_custom scroll Fixed**/
    var _Fixed = $('.widget_custom_fixed');
    if(typeof _Fixed.html()!='undefined') {
        var _FixedParent   = _Fixed.parents('.widget_custom'),
            _top           = _FixedParent.offset().top,
            documentHeight = $(document).height(),
            isIE6          = window.ActiveXObject && !window.XMLHttpRequest;
        function Fixed() {
            var sideBarHeight  = _FixedParent.height();
                if($(window).scrollTop() > _top) {
                    var newPosition = $(window).scrollTop() - _top;
                    var maxPosition = documentHeight - sideBarHeight;
                    if (newPosition > maxPosition) {
                        newPosition = maxPosition;
                    }
                    if(isIE6) {
                        _FixedParent.stop().animate({
                            marginTop: newPosition + 10+'px'
                        });
                    }else {
                        _FixedParent.css({'position':'fixed','top':'10px'});
                    }
                }else {
                    if(isIE6) {
                        _FixedParent.stop().animate({
                            marginTop: 0
                        });
                    }else {
                        _FixedParent.css({'position':'static','top':0});
                    }
                };
        };
        Fixed();
        $(window).scroll(function () {Fixed();});
    }
    /**百度分享 开发地址 http://share.baidu.com/code/advance**/
    if(_info.logid && $(window).width()>480) {
        window._bd_share_config = {
            common: {
                "bdText": $('title').text()+'--'+$('.article_header h1 a').text(),
                "bdMini": "2",
                "bdUrl": _info.url+$('.article_header h1 a').attr('href')+'#J_share',
                "bdDesc": '很不错的文章，分享给大家！',
                "bdMiniList": false,
                "bdPic": $('.article_content img:first') ? _info.url+$('.article_content img:first').attr('src') : '',
                "bdStyle": "0",
                "bdSize": "24"
            },
            share: [{
                bdCustomStyle: _info.tpl + 'css/share.css'
            }],
            image: {
                tag: 'bdshare',
                "viewList": ["qzone", "tsina", "weixin", "tqq", "sqq", "renren", "douban"],
                "viewText": " ",
                "viewSize": "16"
            }
        };
        with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
    }
    
    /*滚动条*/
    var toolTips = function (JqueryObj,title) {
        $('.tooltip').remove();
        JqueryObj.removeAttr('title');
        var title=title || JqueryObj.attr("data-title"),
            offset = JqueryObj.offset(),
            str = '<div class="tooltip"><div class="tooltipstext">'+title+'</div><div class="tooltipsArrow"><i></i></div></div>';
            $('body').append(str),
            obj = $('.tooltip');
        var tipsw =JqueryObj.innerWidth();
        var w = obj.innerWidth(),h = obj.innerHeight()+3,top=offset.top-h,left=offset.left+tipsw/2-w/2;
        obj.css('top',top+'px');
        obj.css('left',left+'px');
        return obj;
    };
    var useToolTips = $('.useTooltips');
    $.each(useToolTips,function (i,n) {
        var obj;
        $(n).bind({
            mouseover:function() {obj = toolTips($(n));obj.show();},
            mouseout:function() {obj.remove();}
        });
    });
    /*格言*/
    if(_info.openMotto) {
        $.post('/motto.html',{'type':'random'},function(msg){
            if(msg.status == 1){
                var motto = '<div class="container container_section"><div class="content_wrap motto"><p>'+msg.mt_content+'By-'+msg.mt_source+'</p></div></div>';
                $(".header").after(motto);
            }
        },'json');
    }
    /**手持设备版显示导航栏**/
    var navBtn = $('.mini_nav_btn');
    navBtn.click(function () {
        Bod.toggleClass('m_nav');
    });
    
});