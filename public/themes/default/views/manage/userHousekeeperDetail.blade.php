{{--<div class="well">
	<h4 >编辑资料</h4>
</div>--}}
{{--<h3 class="header smaller lighter blue mg-top12 mg-bottom20">编辑资料</h3>--}}
<style>
	.allmap img {
		width: 38px;
		height: 25px;
	}
</style>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
	<div class="widget-toolbar no-border pull-left no-padding">
		<ul class="nav nav-tabs">
			<li class="">
				<a href="{!! url('manage/userList') !!}" title="">用户列表</a>
			</li>
			<li class="active">
				<a title="">编辑资料</a>
			</li>
		</ul>
	</div>
</div>
<div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form class="avatar-form" action="{!! url('manage/crop') !!}" enctype="multipart/form-data" method="post">
				{!! csrf_field() !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
				</div>
				<div class="modal-body">
					<div class="avatar-body">

						<!-- Upload image and data -->
						<div class="avatar-upload">
							<input type="hidden" class="avatar-src" name="avatar_src">
							<input type="hidden" class="avatar-data" name="avatar_data">
							<label for="avatarInput">Local upload</label>
							<input type="file" class="avatar-input" id="avatarInput" name="avatar_file">
						</div>

						<!-- Crop and preview -->
						<div class="row">
							<div class="col-md-9">
								<div class="avatar-wrapper"></div>
							</div>
							<div class="col-md-3">
								<div class="avatar-preview preview-lg"></div>
								<div class="avatar-preview preview-md"></div>
								<div class="avatar-preview preview-sm"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer text-center">
					<button type="submit" class="btn btn-primary btn-block avatar-save" style="width: 20%">确认</button>
                  	{{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                </div>
				{{--<div class="col-md-3">--}}
					{{--<button type="submit" class="btn btn-primary btn-block avatar-save">Done</button>--}}
				{{--</div>--}}
			</form>
		</div>
	</div>
</div><!-- /.modal -->
<form class="form-horizontal registerform " role="form" action="{!! url('manage/userEdit') !!}" method="post" enctype="multipart/form-data">
	{!! csrf_field() !!}
	<div class="g-backrealdetails clearfix bor-border">
		<input type="hidden" name="uid" value="{!! $info['id'] !!}">
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 头像</p>
			<div class="container col-sm-3" id="crop-avatar">
				<div class="avatar-view text-left" title="Change the avatar" >
					@if(!empty($info['avatar']))
						<img src="{!!  url($info['avatar']) !!}" style="height: 100%" alt="Avatar">
					@else
						<img src="/themes/default/assets/images/default_avatar.png" style="height: 100%" alt="Avatar">
					@endif
				</div>
				<input type="hidden" name="user-avatar" id="user-avatar" value="{!! $info['avatar'] !!}" />

			</div>


			{{--</p>--}}
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 登录账户：</p>
			<p class="col-sm-4">
				<input type="text" name="name" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! $info['name'] !!}" readonly="true">
				<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 真实姓名：</p>
			<p class="col-sm-4">
				<input type="text" name="realname" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! $info['realname'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 昵称：</p>
			<p class="col-sm-4">
				<input type="text" name="nickname" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! empty($info['nickname'])?'未知':$info['nickname'] !!}">
			</p>
		</div>

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 密&nbsp;&nbsp;码：</p>
			<p class="col-sm-5">
				<input type="password" id="form-field-1"  class="col-xs-10 col-sm-5" name="password" datatype="*" value="">
				<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i>（提示：更改此密码不会修改用户的支付密码）</span>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="user_age"> 年龄：</p>
			<p class="col-sm-4">
				<input type="text" name="user_age" id="user_age"  class="col-xs-10 col-sm-5" value="{!! $info['user_age'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="native_place"> 籍贯：</p>
			<p class="col-sm-4">
				<input type="text" name="native_place" id="native_place"  class="col-xs-10 col-sm-5" value="{!! $info['native_place'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="card_number">身份证号码：</p>
			<p class="col-sm-4">
				<input type="text" name="card_number" id="card_number"  class="col-xs-12 col-sm-12" value="{!! $info['card_number'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 开户银行：</p>
			<p class="col-sm-4">
				<input type="text" name="deposit_name" id="deposit_name"  class="col-xs-10 col-sm-5" value="{!! $info['deposit_name'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 银行卡号：</p>
			<p class="col-sm-4">
				<input type="text" name="bank_account" id="bank_account"  class="col-xs-10 col-sm-5" value="{!! $info['bank_account'] !!}">
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 简介：</p>
			<p class="col-sm-4">
				<textarea type="text" name="introduce" id="introduce"  class="col-xs-10 col-sm-5">{!! $info['introduce'] !!}</textarea>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 个人特长<span style="color: red">（请用中文逗号分隔）</span>：</p>
			<p class="col-sm-4">
				<textarea type="text" name="tag" id="tag"  class="col-xs-10 col-sm-5" >{!! $info['tag'] !!}</textarea>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 客户评价<span style="color: red">（请用中文逗号分隔）</span>：</p>
			<p class="col-sm-4">
				<textarea type="text" name="sign" id="sign"  class="col-xs-10 col-sm-5" >{!! $info['sign'] !!}</textarea>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="realname"> 样板案例<span style="color: red">（请用中文逗号分隔）</span>：</p>
			<p class="col-sm-4">
				<textarea type="text" name="demo" id="demo"  class="col-xs-10 col-sm-5" >{!! $info['demo'] !!}</textarea>
			</p>
		</div>
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 手机号码：</p>--}}
			{{--<p class="col-sm-4">--}}
				{{--<input type="text" name="mobile" id="form-field-1"   class="col-xs-10 col-sm-5" value="{!! $info['mobile'] !!}">--}}
			{{--</p>--}}
		{{--</div>--}}
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> QQ号码：</p>--}}
			{{--<p class="col-sm-4">--}}
				{{--<input type="text" name="qq" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! $info['qq'] !!}">--}}
			{{--</p>--}}
		{{--</div>--}}
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 电子邮箱：</p>--}}
			{{--<p class="col-sm-4">--}}
				{{--<input type="text" name="email" id="form-field-1"  class="col-xs-10 col-sm-5" datatype="e" value="{!! $info['email'] !!}">--}}
				{{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
			{{--</p>--}}
		{{--</div>--}}
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p  class="col-sm-1 control-label no-padding-left">所在地：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-4">
						<select name="province" id="province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'city');">
							<option value="">请选择省份</option>
							@foreach($province as $item)
								<option @if($info['province'] == $item['id'])selected="selected"@endif value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>
							@endforeach
						</select>
					</p>
					<p class="col-sm-4">
						<select class="form-control  validform-select" name="city" id="city" onchange="getZone(this.value, 'area');">
							@if(empty($city))
								<option value="">请选择城市</option>
							@else
								<option selected="selected" value="{!! $info['city'] !!}">{!! $city !!}</option>
							@endif
						</select>
					</p>
					{{--<p class="col-sm-4">--}}
					{{--<select class="form-control  validform-select" name="area" id="area">--}}
					{{--@if(empty($area))--}}
					{{--<option value="">请选择区域</option>--}}
					{{--@else--}}
					{{--<option selected="selected" value="{!! $info['area'] !!}">{!! $area !!}</option>--}}
					{{--@endif--}}
					{{--</select>--}}
					{{--</p>--}}
				</div>
			</div>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="detail_address"> 详细地址：</p>
			<p class="col-sm-4">
				<input type="text" name="detail_address" id="detail_address"  class="col-xs-12 col-sm-12" value="{!! $info['address'] !!}">
			</p>
			<p class="col-sm-4">
				<button type="button" style="padding: 5px 10px;background: #4d98dd;color: white;border: none" id="searchLatAndLng">检索经纬度</button>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 住址经纬度：</p>

			<p class="col-sm-4">
				<input type="text" name="lat"  id="lat"  class="col-xs-10 col-sm-5" value="{!! $info['lat'] !!}" readonly="true" style="margin-right: 1rem">
				<input type="text" name="lng"  id="lng"  class="col-xs-10 col-sm-5" value="{!! $info['lng'] !!}" readonly="true">
			</p>

			<div id="allmap" class="col-xs-8 allmap" style="height: 400px;margin-left: 8%">

			</div>
		</div>
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 出生日期：</p>--}}
			{{--<div class="col-sm-4">--}}
				{{--<p class="input-group input-group-sm col-xs-10 col-sm-5">--}}
					{{--<input type="text" id="datepicker" class="form-control hasDatepicker" value="{!! $info['created_at'] !!}">--}}
					{{--<span class="input-group-addon">--}}
						{{--<i class="ace-icon fa fa-calendar"></i>--}}
					{{--</span>--}}
				{{--</p>--}}
			{{--</div>--}}
		{{--</div>--}}

		{{--<div class="form-group text-center">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"></label>
                <div class="col-sm-3 text-left">
                    　<button class="btn btn-primary btn-sm">提交</button>
                </div>
        </div>--}}
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p  class="col-sm-1 control-label no-padding-left">服务区域：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-4">
						<select name="serve_province" id="serve_province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'serve_city');">
							<option value="">请选择省份</option>
							@foreach($province as $item)
								<option  value="{!! $item['id'] !!}" @if($serve_province == $item['id'])selected="selected"@endif>{!! $item['name'] !!}</option>
							@endforeach
						</select>
					</p>
					<p class="col-sm-4">
						<select class="form-control  validform-select" name="serve_city" id="serve_city" >
							@if(empty($serve_city))
								<option value="">请选择城市</option>
							@else
								<option selected="selected" value="{!! $info['serve_area'] !!}">{!! $serve_city !!}</option>
							@endif
						</select>
					</p>

				</div>
			</div>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p  class="col-sm-1 control-label no-padding-left">工作年限：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-4">
						<select name="experience" id="experience" class="form-control validform-select Validform_error">
							@for($i=1;$i<=40;$i++)
								<option value="{!! $i !!}" @if($i==$info['experience'])selected="selected"@endif>{!! $i !!}年</option>
							@endfor
						</select>
					</p>
				</div>
			</div>
		</div>



		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="detail_address"> 成交量：</p>
			<p class="col-sm-4">
				<input type="text" name="employee_num" id="employee_num" class="col-xs-10 col-sm-5"
					   value="{!! empty($info['employee_num'])?0:$info['employee_num'] !!}">
			</p>
		</div>

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="detail_address"> 预约量：</p>
			<p class="col-sm-4">
				<input type="text" name="receive_task_num" id="receive_task_num" class="col-xs-10 col-sm-5"
					   value="{!! empty($info['receive_task_num'])?0:$info['receive_task_num'] !!}">
			</p>
		</div>

		<div class="bankAuth-bottom clearfix col-xs-12">
			<p  class="col-sm-1 control-label no-padding-left">星级：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-4">
						<select name="star" id="star" class="form-control validform-select Validform_error" disabled="disabled">

								<option value="{!! $info['star'] !!}" @if(!empty($info['star']))selected="selected"@endif>{!! $info['star'] !!}星</option>

						</select>
					</p>
				</div>
			</div>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p  class="col-sm-1 control-label no-padding-left">用户类型：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-6">
						<select name="user_type" id="user_type" class="form-control validform-select Validform_error" disabled="disabled">
							<option value="0">请选择用户类型</option>
							@foreach( $user_type_list as $index => $_type)
								<option @if($info['user_type'] == intval($index)) selected="selected" @endif value="{!! $index !!}">{!! $_type !!}</option>
							@endforeach
						</select>
					</p>
				</div>
			</div>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12 hide">
			<p  class="col-sm-1 control-label no-padding-left">用户组：</p>
			<div class="col-sm-5">
				<div class="row">
					<p class="col-sm-6">
						<select name="role_id" id="role_id" class="form-control validform-select Validform_error">
							<option value="0">请选择用户组</option>
							@foreach( $role as $item)
								<option @if($info['roles_id'] == $item['id']) selected="selected" @endif value="{!! $item['id'] !!}">{!! $item['display_name'] !!}</option>
							@endforeach
						</select>
					</p>
				</div>
			</div>
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
		{{--<div class="col-xs-12">--}}
			{{--<div class="col-md-1 text-right"></div>--}}
			{{--<div class="col-md-10"><a href="">上一项</a>　　<a href="">下一项</a></div>--}}
		{{--</div>--}}
		{{--<div class="col-xs-12 space">--}}

		</div>
	</div>
</form>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('validform-css', 'plugins/jquery/validform/css/style.css') !!}
{{--{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}


{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}

{!! Theme::asset()->container('custom-css')->usePath()->add('crop-css', 'css/crop/cropper.min.css') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('crop-main-css', 'css/crop/main.css') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-js', 'js/crop/cropper.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-main-js', 'js/crop/main.js') !!}
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=ZQEAQICL6vg3MLfqP9yEYz3X"></script>
<script type="text/javascript">
	// 百度地图API功能

	var lng = "{!! empty($info['lat'])?114.02597366:$info['lng'] !!}";
	var lat = "{!! empty($info['lng'])?22.54605355:$info['lat'] !!}";
	var map = new BMap.Map("allmap");    // 创建Map实例
	var myGeo = new BMap.Geocoder();
	var localSearch = new BMap.LocalSearch(map);
	localSearch.enableAutoViewport(); //允许自动调节窗体大小
	initMap();
	function initMap(){
		var point = new BMap.Point(lng,lat);
		if(lng&&lat){
			map.centerAndZoom(point, 15);  // 初始化地图,设置中心点坐标和地图级别
		}else{
			map.centerAndZoom("深圳", 15);  // 初始化地图,设置中心点坐标和地图级别
		}
		var marker = new BMap.Marker(point);  // 创建标注
		map.addOverlay(marker);               // 将标注添加到地图中
		marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
	}

	//添加地图类型控件
	map.addControl(new BMap.MapTypeControl({
		mapTypes:[
			BMAP_NORMAL_MAP,
			BMAP_HYBRID_MAP
		]}));

	map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
	map.setCurrentCity("深圳");



	$("#searchLatAndLng").click(function(){

		var obj_province = document.getElementById("province");
		var obj_city = document.getElementById("city");

		var province = obj_province.options[obj_province.options.selectedIndex].text;
		var city = obj_city.options[obj_city.options.selectedIndex].text;
		var detail_address = $("#detail_address").val();

		myGeo.getPoint(province+city+detail_address, function(point){
			if (point) {
				map.clearOverlays();//清空原来的标注
				$("#lat").val(point.lat);
				$("#lng").val(point.lng)
				map.centerAndZoom(point, 15);
				map.addOverlay(new BMap.Marker(point));
				var marker = new BMap.Marker(point);  // 创建标注
				marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
			}else{
				alert("您选择地址没有解析到结果!");
			}
		});

	});
</script>