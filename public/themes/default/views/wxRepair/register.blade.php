<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>注册</title>
</head>
<body>
	<div>
		<header>
			<div class="header">
				<h1>注册账户</h1>
				<a href="/wxLogin" class="return" style="top: 25%"><i class="icon-16"></i></a>
			</div>
		</header>
		<div style="height: 2.5rem;"></div>
	</div>
	<!-- 注册 register-->
	<div class="register">
		<form class="input-group register-group">
			<div class="input-row">
				<label><i class="icon-uniE938"></i></label>
				<input type="text" placeholder="手机号码" required pattern="^(13[0-9]|15[0|1|3|6|7|8|9]|18[8|9])\d{8}$">
				<div class="form_hint">请填写有效的手机号码</div>
			</div>
			{{--<div class="input-row">--}}
				{{--<label><i class="icon-uniE92B"></i></label>--}}
				{{--<div class="group-T">--}}
					{{--<input type="radio" class="radio-la" name="n" id="check-1" hidden><label for="check-1" class="group-T-l icon-uniE940">个人</label>--}}
					{{--<input type="radio" class="radio-la" name="n" id="check-2" hidden><label for="check-2" class="group-T-l icon-uniE940">企业</label>--}}
				{{--</div>--}}
			{{--</div>--}}
			<div class="input-row">
				<label><i class="icon-uniE937"></i></label>
				<input type="password" placeholder="密码" required>
				<div class="form_hint">请设置密码</div>
			</div>
			<div class="input-row">
				<label><i class="icon-uniE937"></i></label>
				<input type="password" placeholder="确认密码" required>
				<div class="form_hint">请输入一致的密码</div>
			</div>
			<div class="input-row">
				<label></label>
				<input class="code-in" type="text" placeholder="验证码">
				<a class="code-a" href="#" class="btn">获取验证码</a>
			</div>
			<button type="button" class="btn cy-btn btn-red" onclick="return false;">提交</button>
			<div class="clause-box">
				注册即视为同意<a href="#" class="clause-t">《使用条款和隐私政策》</a>
				<a href="/wxLogin" class="fr clause-t">登录</a>
			</div>
		</form>

	</div> 
	<!-- 引用的js文件 -->
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_custom','js/wxRepair/custom.js') !!}

	<script type="text/javascript">
		/*注册选择协会时的显示隐藏*/
		function showAss() {
		    if (document.getElementById('select-association').style.display == 'none')
		        document.getElementById('select-association').style.display = 'block';
		    else
		        document.getElementById('select-association').style.display = 'block'
		}

		/*选择协会时赋值*/
//		var select = document.getElementById("select-association").getElementsByTagName("input");
//		var selecttext = document.getElementById("selectText");
//		var submit = document.getElementById("submit");
//		submit.onclick = function(){
//		    for(var i=0;i<select.length;i++){
//			    if(select[i].checked){
//					selecttext.value = select[i].value;
//				}
//			}
//			if (document.getElementById('select-association').style.display == 'block')
//		        document.getElementById('select-association').style.display = 'none';
//		}
	</script>
</body>
</html>