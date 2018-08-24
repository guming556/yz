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

<div class="alert-danger">
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
</div>

<form class="form-horizontal registerform" role="form" action="{!! url('manage/subCategoryEdit') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border">
        <input type="hidden" name="edit_id" value="{!! $id !!}">

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 分类名称：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="name" id="name"  class="col-xs-10 col-sm-5" value="@if(!empty($cate)){!! $cate->name !!}@endif">
                {{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
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
{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
