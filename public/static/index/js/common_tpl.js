function focusEle(ele){
	try {document.getElementById(ele).focus();}
	catch(e){}
}
function updateEle(ele,content){
	document.getElementById(ele).innerHTML = content;
}
function timestamp(){
	return new Date().getTime();
}
var XMLHttp = {  
	_objPool: [],
	_getInstance: function () {
		for (var i = 0; i < this._objPool.length; i ++) {
			if (this._objPool[i].readyState == 0 || this._objPool[i].readyState == 4) {
				return this._objPool[i];
			}
		}
		this._objPool[this._objPool.length] = this._createObj();
		return this._objPool[this._objPool.length - 1];
	},
	_createObj: function(){
		if (window.XMLHttpRequest){
			var objXMLHttp = new XMLHttpRequest();
		} else {
			var MSXML = ['MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
			for(var n = 0; n < MSXML.length; n ++){
				try{
					var objXMLHttp = new ActiveXObject(MSXML[n]);
					break;
				}catch(e){}
			}
		}
		if (objXMLHttp.readyState == null){
			objXMLHttp.readyState = 0;
			objXMLHttp.addEventListener('load',function(){
				objXMLHttp.readyState = 4;
				if (typeof objXMLHttp.onreadystatechange == "function") {  
					objXMLHttp.onreadystatechange();
				}
			}, false);
		}
		return objXMLHttp;
	},
	sendReq: function(method, url, data, callback){
		var objXMLHttp = this._getInstance();
		with(objXMLHttp){
			try {
				if (url.indexOf("?") > 0) {
					url += "&randnum=" + Math.random();
				} else {
					url += "?randnum=" + Math.random();
				}
				open(method, url, true);
				setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
				send(data);
				onreadystatechange = function () {  
					if (objXMLHttp.readyState == 4 && (objXMLHttp.status == 200 || objXMLHttp.status == 304)) {  
						callback(objXMLHttp);
					}
				}
			} catch(e) {
				alert('emria:error');
			}
		}
	}
	}
function sendinfo(url,node){
	updateEle(node,"<div><span style=\"background-color:#FFFFE5; color:#666666;\">加载中...</span></div>");
	XMLHttp.sendReq('GET',url,'',function(obj){updateEle(node,obj.responseText);});
}
//展示评论
function loadr(tid){
        url = _info.url+'/twi.html';
        var r=document.getElementById("r_"+tid);
        var rp=document.getElementById("rp_"+tid);
        var data = "tid="+tid;
        if (r.style.display=="block"){
            r.style.display="none";
            rp.style.display="none";
        } else {
            r.style.display="block";
            r.innerHTML = '<span style=\"background-color:#FFFFE5;text-align:center;font-size:12px;color:#666666;\">加载中...</span>';
            XMLHttp.sendReq('POST',url,data,function(obj){r.innerHTML = obj.responseText;rp.style.display="block";});
        }
}
//提交评论
function reply(tid){
    if(_info.isLogin){
        url = _info.url+'/twi.html';
        var rtext=document.getElementById("rtext_"+tid).value;
        var rname=document.getElementById("rname_"+tid).value;
        var rmsg=document.getElementById("rmsg_"+tid);
        var rn=document.getElementById("rn_"+tid);
        var r=document.getElementById("r_"+tid);
        //var data = {'r':rtext,'rname':rname,'tid':tid}
        var data = "r="+rtext+"&rname="+rname+"&tid="+tid;
        XMLHttp.sendReq('POST',url,data,function(obj){
            if(obj.responseText == 'err1'){rmsg.innerHTML = '(回复长度需在140个字内)';
            }else if(obj.responseText == 'err2'){rmsg.innerHTML = '(昵称不能为空)';
            }else if(obj.responseText == 'err3'){rmsg.innerHTML = '(验证码错误)';
            }else if(obj.responseText == 'err4'){rmsg.innerHTML = '(不允许使用该昵称)';
            }else if(obj.responseText == 'err5'){rmsg.innerHTML = '(已存在该回复)';
            }else if(obj.responseText == 'err0'){rmsg.innerHTML = '(禁止回复)';
            }else if(obj.responseText == 'succ1'){rmsg.innerHTML = '(回复成功，等待管理员审核)';
            }else{r.innerHTML += obj.responseText;rn.innerHTML = Number(rn.innerHTML)+1;rmsg.innerHTML=''}});
    }
}
//回复谁
function re(rp,tid){
    if(_info.isLogin){
      var rtext=document.getElementById("rtext_"+tid).value = rp;
	   focusEle("rtext_"+tid);  
    }
	
}
//文章评论
function commentReply(pid,c){
    _Url = _info.url+'/comm.html';
    //_Url = 'comm.html';
            var uid = $('#comment-uid').val();
            if(uid == '' || uid == 0){
                uid = J.cookie('uid');
            }
    var fff;
    if(_info.isLogin){
        fff = 1;
    }else{
        fff = 0;
    }
    $.ajax({
                type: "POST",
                url: _Url,
                dataType:'html',
                data: {'pid':pid,'uid':uid,'flag':fff,'f':111},
                success: function (msg) {
                    var re = JSON.parse(msg);
                    if(re.status == 1){
                        var response = document.getElementById('comment-post');
                        document.getElementById('comment-pid').value = pid;
                        document.getElementById('cancel-reply').style.display = '';
                        c.parentNode.parentNode.appendChild(response);
                    }else{
                        console.log(re.msg);
                    }
                },
                error : function(){
                    console.log('好像网络出了点问题!');
                },
    });
        
}
function cancelReply(){
	var commentPlace = document.getElementById('comment-place'),response = document.getElementById('comment-post');
	document.getElementById('comment-pid').value = 0;
	document.getElementById('cancel-reply').style.display = 'none';
	commentPlace.appendChild(response);
}
function addScript(src) {
    var script = document.createElement("script");
    script.src = src;
    document.head.appendChild(script);
}
function dateTime() {
        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth() + 1;
        var date = now.getDate();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();
        var weeknum = now.getDay();
        return year + "-" + two(month) + "-" + two(date) + " " + two(hours) + ":" + two(minutes) + ":" + two(seconds) + "　" + getWeek(weeknum);
    }
function two(Obj) {
        if (Obj < 10) {
            return "0" + "" + Obj;
        } else {
            return Obj;
        }
    }
function getWeek(weeknum) {
        if (weeknum == 0) week = "星期日";
        if (weeknum == 1) week = "星期一";
        if (weeknum == 2) week = "星期二";
        if (weeknum == 3) week = "星期三";
        if (weeknum == 4) week = "星期四";
        if (weeknum == 5) week = "星期五";
        if (weeknum == 6) week = "星期六";
        return week;
    }
$.fn.typing = function(n){
	var options = {
		speed : 100,
		range : 0,
		repeat : true,
		flashback : true,
		flicker : false //是否清空
	};
	$.extend(options,n);//读取参数
	var _this = $(this);
	var str = $(this).text().split(''); //获取当前的文本信息并转为数组
	var index = 0;//起始位置
	var direction = 1; //方向
	$(str).each(function(i,k){
		str[i] = (str[i-1] ? str[i-1] : '') + str[i];//组建不同的文本信息 博,博客,博客不 .....
	});
	_this.css('border-right','1px solid #000');//设置样式
	setTimeout(init,options.speed);//用计时器执行
	function init(){
		_this.text(str[index]);
		if(index >= (str.length-1) && options.repeat){
			if(options.flashback){
				direction = -1;
			}else{
				index = 0;
			}
			if(options.flicker){
				_this.delay(200).fadeOut(1).delay(400).fadeIn(1).delay(200).fadeOut(1).delay(400).fadeIn(1);
			}
			setTimeout(init,2000);
		}else if( index >= (str.length-1) && !options.repeat ){
			if(options.flicker){
			_this.delay(200).fadeOut(1).delay(400).fadeIn(1).delay(200).fadeOut(1).delay(400).fadeIn(1);
			}
			_this.css('border-right','');
		}else if(index < 0 ){
			index = 0;
			direction = 1;
			setTimeout(init,Math.random()*options.range + options.speed);
		}else{
			setTimeout(init,Math.random()*options.range + options.speed);
		}
		index += direction;
	}
};
$(".desinfo span").typing({
	range:200,
	repeat:true
});
