<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>易装：装修也能“嘀”一下，和家装公司说拜拜！</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <!-- Link Swiper's CSS -->
    {{--{!! Theme::asset()->container('custom-css')->usePath()->add('swiper-css', 'css/reg/swiper.min.css') !!}--}}
    {{--<link rel="stylesheet" href="/themes/default/assets/css/reg/swiper.min.css">--}}
    <!-- Demo styles -->
    <style>
        html, body {
            position: relative;
            height: 100%;
        }

        body {
            background: #99e5ff;
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .swiper-container {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            /*-webkit-box-pack: center;*/
            /*-ms-flex-pack: center;*/
            /*-webkit-justify-content: center;*/
            /*justify-content: center;*/
            /*-webkit-box-align: center;*/
            /*-ms-flex-align: center;*/
            /*-webkit-align-items: center;*/
            /*align-items: center;*/
        }

        .first-input {
            /*position: absolute;*/
            /*bottom: 35%;*/
            height: 2%;
            border: none;
            width: 76%;
            border-radius: 7px;
            padding: 0.8rem;
            margin: 0 calc(12% - 0.8rem);
            margin-top: 103%;
        }

        .second-input {
            position: absolute;
            /*bottom: 26%;*/
            height: 2%;
            border: none;
            width: 76%;
            border-radius: 7px;
            padding: 0.8rem;
            margin: 0 calc(12% - 0.8rem);
            margin-top: 118%;
        }

        .third-input {
            /*position: absolute;*/
            /*bottom: 17%;*/
            height: 2%;
            border: none;
            width: 38%;
            border-radius: 7px;
            padding: 0.8rem;
            margin-left: calc(12% - 0.8rem);
            margin-top: 133%;
        }

        .send {
            /*position: absolute;*/
            background: #ff6600;
            color: white;
            border-radius: 6px;
            height: 2.2rem;
            width: 26%;
            text-align: center;
            /*bottom: 17%;*/
            border-radius: 5px;
            border: none;

        }

        .btn-submit {
            position: absolute;
            background: #ff6600;
            color: white;
            width: 30%;
            padding: 0.6rem 1rem;
            text-align: center;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
            left: 32.5%;
            top: 84%;
        }
    </style>
</head>
<script src="/themes/default/assets/plugins/jquery/jquery.min.js"></script>
{{--<script src="/themes/default/assets/js/swiper/swiper.js"></script>--}}
<body>


<div class="swiper-container">
    <div class="swiper-wrapper">

        {{--<div class="swiper-slide">--}}
            {{--<img src="{{ Theme::asset()->url('images/mobile_reg/1.jpg') }}" style="width: 100%">--}}
        {{--</div>--}}

        {{--<div class="swiper-slide">--}}
            <div style="width: 100%;position: relative">
                <img src="{{ Theme::asset()->url('images/mobile_reg/2.jpg') }}" style="width: 100%">
                <input type="hidden" name="code_invite" id="" value="{{$code}}">


                        <input value="" placeholder="请输入电话号码" maxlength="11" name="tel"
                               style="width: 76%; height: 2.2rem;padding: 0 0.8rem;border: none;border-radius: 8px;left: calc( 12% - 0.8rem );top: 57%;position: absolute;" />

                        <input value="" placeholder="请输入6到16位密码" name="password"
                               style="width: 76%;height: 2.2rem;padding: 0 0.8rem; border: none;border-radius: 8px;position: absolute;top: 63.5%;left: calc( 12% - 0.8rem );" type="password"/>

                        <input value="" placeholder="请输入验证码" name="verify_code"
                               style="width: 40%;height: 2.2rem;padding: 0 0.8rem;border: none;border-radius: 8px;left: calc( 12% - 0.8rem );position: absolute;top: 70%;"/>
                        {{--<div style="line-height:2.2rem;background: #ff6600;color: white;height: 2.2rem; width: 26%;text-align: center;border-radius: 5px; border: none;position: absolute;top: 70%;right: calc( 12% - 0.8rem );" onclick="sendCode()" id="code"  type="text"/>验证码</div>--}}


                <input style="line-height:2.2rem;background: #ff6600;color: white;height: 2.2rem; width: 26%;text-align: center;border-radius: 5px; border: none;position: absolute;top: 70%;right: calc( 12% - 0.8rem );" onclick="sendCode()" id="code"  type="text" value="验证码" readonly/>

                        <div style=" position: absolute;top: 77%;left: calc( 12% - 0.8rem );">
                            <input id="agree" checked="checked" type="checkbox" style="height: 1rem;margin: 0.5rem 0;float: left"/>
                            <div style="margin-top: 0.5rem;float: left;">我已阅读并同意<a href="https://app.52hom.com/bre/agree/register">《用户协议》</a></div>
                        </div>
                        <div  id="btn-submit" class="btn-submit" style="margin-top: 0.4rem"/>提交</div>
                    </div>

                {{--<div style="height: 8rem;display: none" id="place"></div>--}}
            </div>

            {{--<input class="send" onclick="sendCode()" id="code" value="验证码" type="text"/>--}}
        {{--</div>--}}
        {{--<div class="swiper-slide">--}}
            <img src="{{ Theme::asset()->url('images/mobile_reg/3.jpg') }}" style="width: 100%">
        {{--</div>--}}

    </div>
    <!-- Add Pagination -->
    {{--<div class="swiper-pagination"></div>--}}
</div>


<div class="alert-danger">
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
</div>

</body>

<script>
//    var swiper = new Swiper('.swiper-container', {
//        pagination: '.swiper-pagination',
//        paginationClickable: true,
//        direction: 'vertical'
//    });


//    var u = navigator.userAgent;
//    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    //    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

//    if(isAndroid){
//        //  这里是获取焦点时的事件
//        $("input").focus(function(){
//            $("#first-input").css('margin-top','25%')
//        })
//
////  这里是失去焦点时的事件
//        $("input").blur(function(){
//            $("#first-input").css('margin-top','103%')
//        })
//    }




    var InterValObj; //timer变量，控制时间
    var count = 30; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    //timer处理函数
    function SetRemainTime() {
        if (curCount == 1) {
            window.clearInterval(InterValObj);//停止计时器
            document.getElementById("code").disabled = false;//启用按钮
            document.getElementById("code").value = "重新发送";
            document.getElementById("code").style.backgroundColor = "#ff6600";
        }
        else {
            curCount--;
            document.getElementById("code").value = curCount + "s";
        }
    }

    function sendCode() {
        var tel = $('input[name=tel]').val();
        if (tel.length != 11) {
            alert('请输入有效的手机号码');
            return;
        }
        curCount = count;
        document.getElementById("code").disabled = true;
        document.getElementById("code").value = curCount + "s";
        document.getElementById("code").style.backgroundColor = "grey";
        InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
//        var tel = $('input[name=tel]').val();
        $.ajax({
            type: "post",
            data: {code_type: 'reg', tel: tel},
            dataType: 'json',
            url: "/api/sendRegCode",
            success: function (data) {
                alert(data.msg)
            }
        });
    }


    $('#btn-submit').click(function () {
        var agree = $("#agree").is(':checked')

        if(!agree){
            alert('请确认已同意用户协议');return;
        }

        var tel = $('input[name=tel]').val();
        var password = $('input[name=password]').val();
        var verify_code = $('input[name=verify_code]').val();
        var code_invite = $('input[name=code_invite]').val();

        $.ajax({
            type: "post",
            data: {password: password, verify_code: verify_code, tel: tel, code_invite: code_invite},
            dataType: 'json',
            url: "/api/postRegisterByCode",
            success: function (data) {
                location.href = 'http://www.yizhuanghome.com/'
            },
            error: function (msg) {
                var json = JSON.parse(msg.responseText);
                if (typeof(json.password) !== "undefined") {
                    alert(json.password);return;
                }
                if (typeof(json.verify_code) !== "undefined") {
                    alert(json.verify_code);return;
                }
                if (typeof(json.tel) !== "undefined") {
                    alert(json.tel);return;
                }
                if (typeof(json.error) !== "undefined") {
                    alert(json.error);return;
                }
            },
        });
    })


</script>
</html>