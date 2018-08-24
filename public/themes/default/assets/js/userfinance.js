/**
 * Created by kuke on 2016/5/6.
 */

$('.input-daterange').datepicker({autoclose:true});

//   TODO
/*var financeExport = function(paras){
/!*    var start = $("input[name = 'start']").val();
    var end = $("input[name = 'end']").val();
    var param = 'start=' + dateToTimestamp(start) + '&end=' + dateToTimestamp(end);*!/
/!*    alert(2222)

    var start = $("input[name = 'start']").val();
    var end = $("input[name = 'end']").val();
    var phone_num = $("input[name = 'phone_num']").val();
    var order_num = $("input[name = 'order_num']").val();
    var fund_state = $("#fund_state").val();
    var param = 'start=' + dateToTimestamp(start) + '&end=' + dateToTimestamp(end)  + '&phone_num=' + phone_num + '&order_num=' + order_num +  '&fund_state=' + fund_state;
    //修改前  404错误
    //var url = document.domain + '/manage/financeListExport/' + escape(param);
    // window.open('http://' + url);
    var url = '/manage/financeListExport/' + escape(param);
    window.open(url);*!/
    var url = location.href;
    var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
    var paraObj = {}
    for (i=0; j=paraString[i]; i++){
        paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
    }
    var returnValue = paraObj[paras.toLowerCase()];
    if(typeof(returnValue)=="undefined"){
        return "";
    }else{
        return returnValue;
    }
}*/

var userFinanceExport = function(){
    var start = $("input[name = 'start']").val();
    var end = $("input[name = 'end']").val();
    var username = $("input[name = 'username']").val();
    var action = $("#action").val();
    var param = 'start=' + dateToTimestamp(start) + '&end=' + dateToTimestamp(end) + '&uid=' + uid + '&username=' + username + '&order=' + order + '&by=' + by + '&action=' + action;
    var url = document.domain + '/manage/userFinanceListExport/' + escape(param);
    window.open('http://' + url);
}

function dateToTimestamp(date)
{
    return new Date(date).getTime()
}

//工地统计
// TODO
var projectpositionExport = function(){
    var start = $("input[name = 'start']").val();
    var end = $("input[name = 'end']").val();
    var param = 'start=' + dateToTimestamp(start) + '&end=' + dateToTimestamp(end);
    var url = '/manage/projectpositionListExport/' + escape(param);
    window.open('http://localhost:8000/' + url);
}
