<div class="well">
    <h4 >编辑业主资料</h4>
</div>
<div class="alert-danger">
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
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
<div class="row">
    <div class="col-md-12">

        <form class="form-horizontal registerform" role="form" action="{!! url('manage/userAdd') !!}" method="post">
            {!! csrf_field() !!}

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 头像</p>
                <div class="container col-sm-3" id="crop-avatar">
                    <div class="avatar-view text-left" title="Change the avatar" >

                        <img src="/themes/default/assets/images/default_avatar.png" style="height: 100%" alt="Avatar">

                    </div>
                    <input type="hidden" name="user-avatar" id="user-avatar" value="" />
                </div>
            </div>

            <div class="form-group basic-form-bottom">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 登录账户：</label>
                <div class="col-sm-4">
                    {{--<input type="text" name="name" id="form-field-1" class="col-xs-10 col-sm-5"  ajaxurl="{!! url('manage/checkUserName') !!}" datatype="*4-15" nullmsg="请输入用户名" errormsg="用户名长度为4到15位字符">--}}
                    <input type="text" name="name" id="form-field-1" class="col-xs-10 col-sm-5"    maxlength="11" placeholder="请输入手机号" value="{{old('name')}}">
                    <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>
                </div>
            </div>

            <div class="form-group basic-form-bottom">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 昵称：</label>
                <div class="col-sm-4">
                    <input type="text" name="nickname" id="form-field-1"  class="col-xs-10 col-sm-5" value="">
                </div>
            </div>
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 密码：</label>--}}
                {{--<div class="col-sm-4">--}}
                    {{--<input type="password" name="password" id="form-field-1"  class="col-xs-10 col-sm-5" >--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 手机号码：</label>--}}
                {{--<div class="col-sm-4">--}}
                    {{--<input type="text" name="mobile" id="form-field-1"   class="col-xs-10 col-sm-5" >--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label class="col-sm-1 control-label no-padding-left" for="form-field-1"> QQ号码：</label>--}}
                {{--<div class="col-sm-4">--}}
                    {{--<input type="text" name="qq" id="form-field-1"  class="col-xs-10 col-sm-5" >--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 电子邮箱：</label>--}}
                {{--<div class="col-sm-4">--}}
                    {{--<input type="text" name="email" id="form-field-1"  class="col-xs-10 col-sm-5"  ajaxurl="{!! url('manage/checkEmail') !!}" datatype="e" nullmsg="请输入邮箱帐号" errormsg="邮箱地址格式不对！">--}}
                    {{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label  class="col-sm-1 control-label no-padding-left">所在地：</label>--}}
                {{--<div class="col-sm-5">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-sm-6">--}}
                            {{--<select name="province" id="province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'city');">--}}
                                {{--<option value="">请选择省份</option>--}}
                                {{--@foreach($province as $item)--}}
                                    {{--<option value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-6">--}}
                            {{--<select class="form-control  validform-select" name="city" id="city" onchange="getZone(this.value, 'area');">--}}
                                {{--<option value="">请选择城市</option>--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="col-sm-3">--}}
                    {{--<select class="form-control  validform-select" name="area" id="area">--}}
                        {{--<option value="">请选择区域</option>--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group basic-form-bottom">--}}
                {{--<label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 出生日期：</label>--}}
                {{--<div class="col-sm-4">--}}
                    {{--<div class="input-group input-group-sm col-xs-10 col-sm-5">--}}
                        {{--<input type="text" id="datepicker" class="form-control hasDatepicker">--}}
				{{--<span class="input-group-addon">--}}
					{{--<i class="ace-icon fa fa-calendar"></i>--}}
				{{--</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group basic-form-bottom">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"> 密&nbsp;&nbsp;码：</label>
                <div class="col-sm-4">
                    <input type="password" id="form-field-1"  class="col-xs-10 col-sm-5" name="password" value="{{old('password')}}" datatype="*6-16" placeholder="请输入密码">
                    <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i>（提示：更改此密码不会修改用户的支付密码）</span>
                </div>
            </div>
            <div class="form-group text-center">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"></label>
                <div class="col-sm-2 text-center">
                    <button class="btn btn-primary btn-sm">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('validform-css', 'plugins/jquery/validform/css/style.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('crop-css', 'css/crop/cropper.min.css') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('crop-main-css', 'css/crop/main.css') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-js', 'js/crop/cropper.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-main-js', 'js/crop/main.js') !!}