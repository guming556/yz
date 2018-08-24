{{--<div class="well">
	<h4 >编辑资料</h4>
</div>--}}
{{--<h3 class="header smaller lighter blue mg-top12 mg-bottom20">编辑资料</h3>--}}
<div class="widget-header mg-bottom20 mg-top12 widget-well">
	<div class="widget-toolbar no-border pull-left no-padding">
		<ul class="nav nav-tabs">
			<li class="">
				<a href="{!! url('manage/userList') !!}" title="">用户列表</a>
			</li>
			<li class="active">
				<a title="">充值资料填写</a>
			</li>
		</ul>
	</div>
</div>

<form class="form-horizontal registerform " role="form" action="{!! url('manage/postUserRecharge') !!}" method="post" enctype="multipart/form-data">
	{!! csrf_field() !!}
	<div class="g-backrealdetails clearfix bor-border">
		<input type="hidden" name="uid" value="{!! $uid !!}">

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 充值账号：</p>
			<p class="col-sm-4">
				<input type="text"   class="col-xs-10 col-sm-5 " value="{!! $name->name !!}" readonly="true">
			</p>
		</div>

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="bank_transaction"> 银行流水号：</p>
			<p class="col-sm-4">
				<input type="text" name="bank_transaction" id="bank_transaction"  class="col-xs-10 col-sm-5" value="">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="bankname"> 充值银行：</p>
			<p class="col-sm-4">
				<input type="text" name="bankname" id="bankname"  class="col-xs-10 col-sm-5" value="">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="price"> 充值金额：</p>
			<p class="col-sm-4">
				<input type="text" name="price" id="price"  class="col-xs-10 col-sm-5" value="">
			</p>
		</div>

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="price">邮箱验证码：</p>
			<p class="col-sm-4">
				<input type="text" name="email_code" id="email_code"  class="col-xs-10 col-sm-5" value="">

				<input id="sendMess" type="button" style="margin: 5px;" value="发送验证码"  class="col-xs-10 col-sm-6" /></p>
			</p>

		</div>



		<div class="col-xs-12">
			<div class="clearfix row bg-backf5 padding20 mg-margin12">
				<div class="col-xs-12">
					<div class="col-md-1 text-right"></div>
					<div class="col-md-10">
						<button class="btn btn-primary btn-sm" type="submit">提交</button>
					</div>
				</div>
			</div>
		</div>
		<div class="space col-xs-12"></div>


		</div>
	</div>
</form>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('validform-css', 'plugins/jquery/validform/css/style.css') !!}
{{--{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}

<script type="text/javascript">
	$.ajaxSetup({
		headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
	});
</script>

<script type="text/javascript">

	var InterValObj; //timer变量，控制时间
	var count = 20; //间隔函数，1秒执行
	var curCount;//当前剩余秒数

	$(function(){

		$("#sendMess").click(function () {
			curCount = count;
			$("#sendMess").attr("disabled", "true");
			$("#sendMess").val("请在" + curCount + "秒内输入");
			InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
			var uid = $('input[name=uid]').val();
			var price = $('input[name=price]').val();

			$.ajax({
				type: "get",
				url: "/manage/sendRechargeEmail",
				data: {uid: uid,price:price},
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

