<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>Document</title>
    <script src="/themes/default/assets/js/jquery-1.10.2.js"></script>
    <script type="text/javascript">
        $(function(){
            if($('.alertmsg').length){
                alert($('.alertmsg').html());
            }
        })
    </script>
</head>
<body>


@if ($errors->has('messageOfStatus'))
    <div class="alertmsg hide">{{ $errors->first('messageOfStatus') }}</div>
@endif
<form action="/api/postRegisterByCode" method="post">
    <input type="hidden" name="code" id="" value="{{$code}}">
    姓  名:<input type="text" name="name" placeholder="请输入姓名"><br>
    手机号:<input type="text" name="tel" placeholder="请输入手机号"><br>
    密  码:<input  type="text" id="password" name="password" placeholder="请输入密码"><br>
    验证码:<input  type="text" id="verify_code" name="verify_code" placeholder="请输入验证码">    <input id="sendMess" type="button" style="margin: 5px;" value="发送验证码"  class="col-xs-10 col-sm-6" /><br>
    <input type="submit" value="提交">
</form>
</body>
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>

<script>

    var InterValObj; //timer变量，控制时间
    var count = 20; //间隔函数，1秒执行
    var curCount;//当前剩余秒数
    $(function(){

        $("#sendMess").click(function () {
            curCount = count;
            $("#sendMess").attr("disabled", "true");
            $("#sendMess").val("请在" + curCount + "秒内输入");
            InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
            var tel = $('input[name=tel]').val();

            $.ajax({
                type: "post",
                data: {code_type: 'reg',tel:tel},
                dataType:'json',
                url: "/api/sendRegCode",
                success: function (data) {
                    alert(data.msg)
                }
            });
        });
    });


    //timer处理函数
    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $("#sendMess").removeAttr("disabled");//启用按钮
            $("#sendMess").val("重新发送验证码");
        }
        else {
            curCount--;
            $("#sendMess").val("请在" + curCount + "秒内输入");
        }
    }
</script>
</html>