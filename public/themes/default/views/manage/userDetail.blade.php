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
						<img src="{!! url($info['avatar']) !!}" style="height: 100%" alt="Avatar">
					@else
						<img src="/themes/default/assets/images/default_avatar.png" style="height: 100%" alt="Avatar">
					@endif
				</div>
			</div>





			{{--</p>--}}
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 用户名：</p>
			<p class="col-sm-4">
				<input type="text" name="name" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! $info['name'] !!}" readonly="true">
				<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>
			</p>
		</div>
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 昵称：</p>
			<p class="col-sm-4">
				<input type="text" name="nickname" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! empty($info['nickname'])?'未填写':$info['nickname'] !!}">
			</p>
		</div>
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 真实姓名：</p>--}}
			{{--<p class="col-sm-4">--}}
				{{--<input type="text" name="realname" id="form-field-1"  class="col-xs-10 col-sm-5" value="{!! $info['realname'] !!}">--}}
			{{--</p>--}}
		{{--</div>--}}
		<input type="hidden" name="user-avatar" id="user-avatar" value="{!! $info['avatar'] !!}" />
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
		{{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
			{{--<p  class="col-sm-1 control-label no-padding-left">所在地：</p>--}}
			{{--<div class="col-sm-5">--}}
				{{--<div class="row">--}}
					{{--<p class="col-sm-4">--}}
						{{--<select name="province" id="province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'city');">--}}
							{{--<option value="">请选择省份</option>--}}
							{{--@foreach($province as $item)--}}
								{{--<option @if($info['province'] == $item['id'])selected="selected"@endif value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>--}}
							{{--@endforeach--}}
						{{--</select>--}}
					{{--</p>--}}
					{{--<p class="col-sm-4">--}}
						{{--<select class="form-control  validform-select" name="city" id="city" onchange="getZone(this.value, 'area');">--}}
							{{--@if(empty($city))--}}
								{{--<option value="">请选择城市</option>--}}
							{{--@else--}}
								{{--<option selected="selected" value="{!! $info['city'] !!}">{!! $city !!}</option>--}}
							{{--@endif--}}
						{{--</select>--}}
					{{--</p>--}}
					{{--<p class="col-sm-4">--}}
						{{--<select class="form-control  validform-select" name="area" id="area">--}}
							{{--@if(empty($area))--}}
								{{--<option value="">请选择区域</option>--}}
							{{--@else--}}
								{{--<option selected="selected" value="{!! $info['area'] !!}">{!! $area !!}</option>--}}
							{{--@endif--}}
						{{--</select>--}}
					{{--</p>--}}
				{{--</div>--}}
			{{--</div>--}}


		{{--</div>--}}
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
		<div class="bankAuth-bottom clearfix col-xs-12">
			<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 密&nbsp;&nbsp;码：</p>
			<p class="col-sm-5">
				<input type="password" id="form-field-1"  class="col-xs-10 col-sm-5" name="password" datatype="*" value="">
				<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i>（提示：更改此密码不会修改用户的支付密码）</span>
			</p>
		</div>
		{{--<div class="form-group text-center">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"></label>
                <div class="col-sm-3 text-left">
                    　<button class="btn btn-primary btn-sm">提交</button>
                </div>
        </div>--}}

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

