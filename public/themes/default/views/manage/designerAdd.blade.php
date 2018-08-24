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
            {{--<li class="">--}}
                {{--<a href="{!! url('manage/userList') !!}" title="">用户列表</a>--}}
            {{--</li>--}}
            <li class="active">
                <a title="">添加设计师</a>
            </li>
        </ul>
    </div>
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
<form class="form-horizontal registerform" role="form" action="{!! url('manage/designerAdd') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border">
        <input type="hidden" name="uid" value="">
        {{--<tr>--}}
            {{--<td class="text-right">主图：</td>--}}
            {{--<td>--}}
                {{--<div class="memberdiv pull-left">--}}
                    {{--<div class="position-relative">--}}
                        {{--<input multiple="" type="file" name="main_file"  id="id-input-file-3" />--}}
                        {{--@if($adInfo[0]['ad_file'])--}}
                        {{--<img src="{!! url($adInfo[0]['ad_file']) !!}" width="152" height="126">--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</td>--}}
        {{--</tr>--}}

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="head_img"> 头像：</p>
            <div class="container col-sm-3" id="crop-avatar">
                <div class="avatar-view text-left" title="Change the avatar" >
                    <img src="/themes/default/assets/images/default_avatar.png" style="height: 100%" alt="Avatar">
                </div>
                <input type="hidden" name="user-avatar" id="user-avatar" value="" />
            </div>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="name"> 登录账户：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="手机号码" name="name" id="name"  class="col-xs-10 col-sm-5"value="{{ old('name') }}">
                {{--<span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span>--}}
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="password"> 密&nbsp;&nbsp;码：</p>
            <p class="col-sm-5">
                <input type="password" id="password"  class="col-xs-10 col-sm-5" name="password" datatype="*" value="{{ old('password') }}">
                <!-- <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span> -->
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="cost_of_design"> 面积单价：</p>
            <p class="col-sm-5">
                <input type="text" id="cost_of_design"  class="col-xs-10 col-sm-5" name="cost_of_design" datatype="*" value="{{ old('cost_of_design') }}">
                <!-- <span class="help-inline col-xs-12 col-sm-7"><i class="light-red ace-icon fa fa-asterisk"></i></span> -->
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 真实姓名：</p>
            <p class="col-sm-4">
                <input type="text" name="realname" id="realname"  class="col-xs-10 col-sm-5" value="{{ old('realname') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="user_age"> 年龄：</p>
            <p class="col-sm-4">
                <input type="text" name="user_age" id="user_age"  class="col-xs-10 col-sm-5" value="{{ old('user_age') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="native_place"> 籍贯：</p>
            <p class="col-sm-4">
                <input type="text" name="native_place" id="native_place"  class="col-xs-10 col-sm-5"value="{{ old('native_place') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">身份证号码：</p>
            <p class="col-sm-4">
                <input type="text" name="card_number" id="card_number"  class="col-xs-12 col-sm-12" value="{{ old('card_number') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 开户银行：</p>
            <p class="col-sm-4">
                <input type="text" name="deposit_name" id="deposit_name"  class="col-xs-10 col-sm-5" value="{{ old('deposit_name') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 银行卡号：</p>
            <p class="col-sm-4">
                <input type="text" name="bank_account" id="bank_account"  class="col-xs-10 col-sm-5" value="{{ old('bank_account') }}">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 简介：</p>
            <p class="col-sm-4">
                <textarea type="text" name="introduce" id="introduce"  class="col-xs-10 col-sm-5" >{{ old('introduce') }}</textarea>
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 个人特长<span style="color: red">（请用中文逗号分隔）</span>：</p>
            <p class="col-sm-4">
                <textarea type="text" name="tag" id="tag"  class="col-xs-10 col-sm-5">{{ old('tag') }}</textarea>
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 客户评价<span style="color: red">（请用中文逗号分隔）</span>：</p>
            <p class="col-sm-4">
                <textarea type="text" name="sign" id="sign"  class="col-xs-10 col-sm-5" >{{ old('sign') }}</textarea>
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 样板案例<span style="color: red">（请用中文逗号分隔）</span>：</p>
            <p class="col-sm-4">
                <textarea type="text" name="demo" id="demo"  class="col-xs-10 col-sm-5" >{{ old('demo') }}</textarea>
            </p>
        </div>
        {{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
            {{--<p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 联系手机：</p>--}}
            {{--<p class="col-sm-4">--}}
                {{--<input type="text" name="mobile" id="form-field-1"   class="col-xs-10 col-sm-5" value="">--}}
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
                                <option  value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>
                            @endforeach
                        </select>
                    </p>
                    <p class="col-sm-4">
                        <select class="form-control  validform-select" name="city" id="city" onchange="getZone(this.value, 'area');">
                                <option value="">请选择城市</option>
                        </select>
                    </p>
                    {{--<p class="col-sm-4">--}}
                        {{--<select class="form-control  validform-select" name="area" id="area">--}}
                                {{--<option value="">请选择区域</option>--}}
                        {{--</select>--}}
                    {{--</p>--}}
                </div>
            </div>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="detail_address"> 详细地址：</p>
            <p class="col-sm-4">
                <input type="text" name="detail_address" id="detail_address"  class="col-xs-12 col-sm-12" value="{{old('detail_address')}}">
            </p>
            <p class="col-sm-4">
                <button type="button" style="padding: 5px 10px;background: #4d98dd;color: white;border: none" id="searchLatAndLng">检索经纬度</button>
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 住址经纬度：</p>

            <p class="col-sm-4">
                <input type="text" name="lat"  id="lat"  class="col-xs-10 col-sm-5" value="" readonly="true" style="margin-right: 1rem">
                <input type="text" name="lng"  id="lng"  class="col-xs-10 col-sm-5" value="" readonly="true">
            </p>

            <div id="allmap" class="col-xs-8 allmap" style="height: 400px;margin-left: 8%">

            </div>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">服务区域：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        <select name="serve_province" id="serve_province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'serve_city');">
                            <option value="">请选择省份</option>
                            @foreach($province as $item)
                                <option  value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>
                            @endforeach
                        </select>
                    </p>
                    <p class="col-sm-4">
                        <select class="form-control  validform-select" name="serve_city" id="serve_city" >
                            <option value="">请选择城市</option>
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
                            <option value="{!! $i !!}">{!! $i !!}年</option>
                           @endfor
                        </select>
                    </p>
                </div>
            </div>
        </div>


        @if(!empty($type) && $type!=2)
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">星级：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        <select name="workStar" id="experience" class="form-control validform-select Validform_error">
                            @for($i=1;$i<=5;$i++)
                            <option value="{!! $i !!}}">{!! $i !!}星</option>
                           @endfor
                        </select>
                    </p>
                </div>
            </div>
        </div>
        @endif





        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_front_side"> 身份证正面：</p>
            <p class="col-sm-5">
                <input multiple="" type="file" name="card_front_side" id="card_front_side" />
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_back_dside"> 身份证反面：</p>
            <p class="col-sm-5">
                <input multiple="" type="file" name="card_back_dside" id="card_back_dside" />
            </p>
        </div>
        {{--<div class="form-group text-center">
                <label class="col-sm-1 control-label no-padding-left" for="form-field-1"></label>
                <div class="col-sm-3 text-left">
                    　<button class="btn btn-primary btn-sm">提交</button>
                </div>
        </div>--}}

        {{--<div class="bankAuth-bottom clearfix col-xs-12">--}}
            {{--<p  class="col-sm-1 control-label no-padding-left">用户类型：</p>--}}
            {{--<div class="col-sm-5">--}}
                {{--<div class="row">--}}
                    {{--<p class="col-sm-6">--}}
                        {{--<select name="user_type" id="user_type" class="form-control validform-select Validform_error">--}}
                            {{--<option value="0">请选择用户类型</option>--}}
                            {{--@foreach( $user_type_list as $index => $_type)--}}
                                {{--<option @if($info['user_type'] == intval($index)) selected="selected" @endif value="{!! $index !!}">{!! $_type !!}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</p>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}


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