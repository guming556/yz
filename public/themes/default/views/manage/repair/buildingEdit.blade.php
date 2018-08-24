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

<form class="form-horizontal registerform" role="form" action="{!! url('manage/subBuildingEdit') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border">
        <input type="hidden" name="edit_id" value="{!! $building_id !!}">

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 省：</p>
            <p class="col-sm-4">
                <select class="form-control" id="province" name="province" onchange="getZoneByCoordinate(this.value,'city')">
                    <option value="0">请选择省份</option>
                    @foreach($province as $key => $value)
                        <option @if(!empty($building) && $building->province_id == $value['id'])selected="selected"@endif value="{!! $value['id'] !!}">{!! $value['name'] !!}</option>
                    @endforeach
                </select>
            </p>

        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 市：</p>
            <p class="col-sm-4">
                <select class="form-control" name="city" id="city" onchange="getZoneByCoordinate(this.value,'area')">
                    @if(empty($city))
                        <option value="0">请选择城市</option>
                    @else
                        <option selected="selected" value="{!! $city->id !!}">{!! $city->name !!}</option>
                    @endif
                </select>
            </p>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 区：</p>
            <p class="col-sm-4">
                <select class="form-control" name="area" id="area" >
                    @if(empty($area))
                        <option value="0">请选择区域</option>
                    @else
                        <option selected="selected" value="{!! $area->id !!}">{!! $area->name !!}</option>
                    @endif
                </select>
            </p>
        </div>



        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 楼盘名称：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="building_name" id="building_name"  class="col-xs-10 col-sm-5" value="@if(!empty($building)){!! $building->building_name !!}@endif">
                {{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="cate_id"> 楼盘物管：</p>
            <p class="col-sm-2">
                <select name="property_id" id="property_id" class="form-control validform-select Validform_error" >
                    @foreach($property as $item)
                    <option value="{!! $item->id !!}" @if(!empty($building) && $building->property_id == $item->id)selected="selected"@endif >{!! $item->name !!}</option>
                    @endforeach
                </select>
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
