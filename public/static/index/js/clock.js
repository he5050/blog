/**
 *html5时钟侧边栏
 */
$(function () {
    var clock = $('#utility-clock');
    utilityClock(clock);
    function utilityClock(container) {
        //侧边栏无法显示时不执行时钟代码
        if($('body').width() <= 960) {
            $('.widget_custom_clock').hide().remove();
            return;
        }
        //检测是否支持html5 不支持则remove元素后return
        if(typeof requestAnimationFrame !=='function') {
            $('.widget_custom_clock').hide().remove();
            return;
        }
        var dynamic       = $(container).find('.dynamic').get(0);
        var hourElement   = $(container).find('.hour').get(0);
        var minuteElement = $(container).find('.minute').get(0);
        var secondElement = $(container).find('.second').get(0);
        var minute = function(n) {
            return n % 5 == 0 ? minuteText(n) : minuteLine(n);
        }
        var minuteText = function(n) {
            var element = document.createElement('div');
            element.className = 'minute-text';
            element.innerHTML = (n < 10 ? '0' : '') + n;
            position(element, n / 60, 135);
            dynamic.appendChild(element);
        }
        var minuteLine = function(n) {
            var anchor = document.createElement('div');
            anchor.className = 'anchor';
            var element = document.createElement('div');
            element.className = 'element minute-line';
            rotate(anchor, n);
            anchor.appendChild(element);
            dynamic.appendChild(anchor);
        }
        var hour = function(n) {
            var element = document.createElement('div');
            element.className = 'hour-text hour-' + n;
            element.innerHTML = n;
            position(element, n / 12, 105);
            dynamic.appendChild(element);
        }
        var position = function(element, phase, r) {
            var theta = phase * 2 * Math.PI;
            element.style.top = (-r * Math.cos(theta)).toFixed(1) + 'px';
            element.style.left = (r * Math.sin(theta)).toFixed(1) + 'px';
        }
        var rotate = function(element, second) {
            element.style.transform = element.style.webkitTransform = 'rotate(' + (second * 6) + 'deg)';
        }
        //以服务器时间为准进行计算
        var isSetTimer = false;
        var _timer     = new Date();
        var bTimer     = _timer.getTime();
        var animate = function() {
            var now  = new Date();
            //处理Date对象，以服务器时间为准
            if(!isSetTimer && _info.timer) {
                isSetTimer = true;
                now.setTime(_info.timer);
            }else if(_info.timer) {
                var nowTimer = _info.timer + now.getTime() - bTimer;
                now.setTime(nowTimer);
            }
            // console.log(now.getTime());
            var time = now.getHours() * 3600 +
                       now.getMinutes() * 60 +
                       now.getSeconds() * 1 +
                       now.getMilliseconds() / 1000;
            rotate(secondElement, time);
            rotate(minuteElement, time / 60);
            rotate(hourElement, time / 60 / 12);
            requestAnimationFrame(animate);
        };
        for (var i = 1; i <= 60; i ++) minute(i);
        for (var i = 1; i <= 12; i ++) hour(i);
        animate();
    }
});