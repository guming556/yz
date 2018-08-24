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
<form class="form-horizontal registerform" role="form" action="{!! url('manage/subGoodsEdit') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border">
        <input type="hidden" name="edit_id" value="{!! $id !!}">


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="user-avatar"> 材料图片：</p>
            <div class="container col-sm-3" id="crop-avatar">
                <div class="avatar-view text-left" title="修改材料图片" >
                    @if( !empty($goods) && !empty($goods->img) )
                        <img src="{!! $goods->img !!}" style="height: 100%" alt="Avatar">
                    @else
                        <img src="/themes/default/assets/images/default_avatar.png" style="height: 100%" alt="Avatar">
                    @endif
                </div>
                <input type="hidden" name="user-avatar" id="user-avatar" value="@if(!empty($goods)){!! $goods->img_2 !!}@endif" />
            </div>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 材料名称：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="name" id="name"  class="col-xs-10 col-sm-5" value="@if(!empty($goods)){!! $goods->name !!}@endif">
                {{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="cate_id"> 材料分类：</p>
            <p class="col-sm-2">
                <select name="cate_id" id="cate_id" class="form-control validform-select Validform_error" >
                    <option value="">请选择分类</option>
                    @foreach($cates as $item)
                        <option  value="{!! $item->id !!}" @if(!empty($goods) && ($item->id == $goods->cate_id)) selected="true" @endif >{!! $item->name !!}</option>
                    @endforeach
                </select>
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="model_name"> 材料型号：</p>
            <p class="col-sm-5">
                <input type="text" id="model_name"  class="col-xs-10 col-sm-5" name="model_name" datatype="*" value="@if(!empty($goods)){!! $goods->model_name !!}@endif">
                <!-- <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span> -->
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="specifications"> 材料规格：</p>
            <p class="col-sm-5">
                <input type="text" id="specifications"  class="col-xs-10 col-sm-5" name="specifications" datatype="*" value="@if(!empty($goods)){!! $goods->specifications !!}@endif">
                <!-- <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span> -->
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="brand_name"> 材料品牌：</p>
            <p class="col-sm-4">
                <input type="text" name="brand_name" id="brand_name"  class="col-xs-10 col-sm-5" value="@if(!empty($goods)){!! $goods->brand_name !!}@endif">
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
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('crop-css', 'css/crop/cropper.min.css') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('crop-main-css', 'css/crop/main.css') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-js', 'js/crop/cropper.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('crop-main-js', 'js/crop/main.js') !!}