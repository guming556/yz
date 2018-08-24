<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>登录</title>
</head>
<body>
    <div class="login-box">
        <div class="head-login">
        	<div class="logo">
            	<img src="{!! Theme::asset()->url('images/wxRepair/logo-yizhuang.png') !!}">
                <p>易装</p>
        	</div>
        </div>
        <div class="login">
          	<form id="login" action="" method="post" class="input-group register-group" >
                <div class="input-row">
        			<label><i class="icon-uniE938"></i></label>
        			<input type="text" placeholder="手机号码">
        		</div>
        		<div class="input-row">
        			<label><i class="icon-uniE937"></i></label>
        			<input type="password" placeholder="输入密码">
        		</div>
                <div class="toolTip-box" style="display: none;">
                    <div class="toolTip-flop">
                        <i class="icon-uniE931 hint"></i>
                        <b>登录失败</b>
                        <p>用户名或密码不正确</p>
                        <div class="btnbox">
                            <a href="#" class="btn btn-b hidePopup">确定</a>
                        </div>
                        
                    </div>
                </div>
                <button type="button" class="btn cy-btn btn-red">登录</button>
        	</form>
        	<div class="login-reg">
                <a class="reg-left" href="/wxRepairIndex">返回首页</a>
                <a class="reg-right" href="/wxReg">注册账号</a>
            </div>
        </div>
    </div>
</body>
<!-- 引入js资源 -->
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}

<script language="javascript">  
//    $(document).ready(function(){
//        $(".hidePopup").click(function(){
//            $(".toolTip-box").hide();
//        });
//        $(".cy-btn").click(function(){
//            $(".toolTip-box").show();
//        });
//    });
</script>

</html>




















