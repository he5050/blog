// Weather Config
var Weather = true;

if (typeof Weather === "boolean") {
    var timestamp = remoteTimestamp * 1000;
    function dateTime() {
        var now = new Date(timestamp);
        timestamp += 1000;
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
    document.getElementById("nowTime").innerHTML = dateTime() + "　";
    setInterval(function () {
        document.getElementById("nowTime").innerHTML = dateTime() + "　";
    },1000);
    document.getElementById("address").innerHTML = "<a href=\"//www.Chaidu.com/App/Web/IP/\" target=\"_blank\">" + remoteIpInfo.results[0].address + "</a>　";
    document.getElementById("todayWeather").innerHTML = remoteWeatherInfo.results[0].weather + "　" + remoteWeatherInfo.results[0].wind + "　" + remoteWeatherInfo.results[0].temperature;
    document.getElementById("tomorrowWeather").innerHTML = remoteWeatherInfo.results[1].weather + "　" + remoteWeatherInfo.results[1].wind + "　" + remoteWeatherInfo.results[1].temperature;
    $("#weatherBox").fadeIn("slow");
}