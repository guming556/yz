{{--<div class="well">
    <h4 >修改菜单</h4>
</div>--}}

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
                <a href="{!! url('manage/businessInfo') !!}" title="">商家信息</a>
            </li>
            <li class="active">
                <a title="">@if(!empty($MerchantDetail['id']))商家信息编辑@else商家信息添加 @endif</a>
            </li>

        </ul>
    </div>
</div>


{{--<h3 class="header smaller lighter blue mg-top12 mg-bottom20">
    @if(!empty($MerchantDetail['id']))商家信息编辑@else商家信息添加 @endif

</h3>--}}
<div class="">
    <div class="g-backrealdetails clearfix bor-border">

        <div class="alert-danger">
            @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            @endif
        </div>

        <form class="form-horizontal clearfix registerform" role="form" action="/manage/saveBusinessInfo" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            @if(!empty($MerchantDetail['id']))
            <input type="hidden" name="id" value="{{ $MerchantDetail['id'] }}"/>
            @endif



            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> logo：</p>
                <p class="col-sm-4">
                    <input type="file" name="brand_logo" id="brand_logo" multiple="" ><span style="color: red">提交的logo图片必须为300*300像素
                        @if(!empty($MerchantDetail['id']))(编辑时如果未选择,将以原图为准) @endif
                    </span>
                </p>
            </div>


            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 推广图片：</p>
                <p class="col-sm-4">
                    <input type="file" name="popular_img" id="popular_img" multiple="" ><span style="color: red">提交的推广图片必须为600*300像素
                        @if(!empty($MerchantDetail['id']))(编辑时如果未选择,将以原图为准) @endif
                    </span>
                </p>
            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1">品牌名称：</p>
                <p class="col-sm-4">
                    <input type="text" id="form-field-1"   name="brand_name" value="@if(isset($MerchantDetail['brand_name'])){{ $MerchantDetail['brand_name'] }}@endif">
                </p>
            </div>
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1">广告语：</p>
                <p class="col-sm-4">
                    <textarea name="ad_slogan">@if(isset($MerchantDetail['ad_slogan'])){{ $MerchantDetail['ad_slogan'] }}@endif</textarea>
                </p>
            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 联系人：</p>
                <p class="col-sm-4">
                    <input type="text" id="form-field-1"  class="col-xs-10 col-sm-5" name="name" value="@if(isset($MerchantDetail['name'])){{ $MerchantDetail['name'] }} @endif" />
                </p>
            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 联系电话：</p>
                <p class="col-sm-4">
                    <input type="text" id="form-field-1"  class="col-xs-10 col-sm-5" name="mobile" value="@if(isset($MerchantDetail['mobile'])){{ $MerchantDetail['mobile'] }} @endif" />
                </p>
            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="address"> 地址：</p>
                <p class="col-sm-6">

                    <input type="text" id="address"  class="col-xs-10 col-sm-5" name="address" value="@if(isset($MerchantDetail['address'])){{ $MerchantDetail['address'] }}@endif" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<button type="button" style="padding: 5px 10px;background: #4d98dd;color: white;border: none" id="searchLatAndLng">检索经纬度</button>
                </p>



            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-left" for="form-field-1"> 住址经纬度：</p>

                <p class="col-sm-4">
                    <input type="text" name="lat"  id="lat"  class="col-xs-10 col-sm-5" value="@if(isset($MerchantDetail['lat'])){{ $MerchantDetail['lat'] }}@endif" readonly="true" style="margin-right: 1rem">
                    <input type="text" name="lng"  id="lng"  class="col-xs-10 col-sm-5" value="@if(isset($MerchantDetail['lng'])){{ $MerchantDetail['lng'] }}@endif" readonly="true">
                </p>

                <div id="allmap" class="col-xs-8 allmap" style="height: 400px;margin-left: 8%">

                </div>
            </div>
            <div class="col-xs-12">
                <div class="clearfix row bg-backf5 padding20 mg-margin12">
                    <div class="col-xs-12">
                        <div class="col-md-1 text-right"></div>
                        <div class="col-md-10">
                            <button class="btn btn-primary btn-sm" type="submit" >提交</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>





{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}

<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=ZQEAQICL6vg3MLfqP9yEYz3X"></script>
<script type="text/javascript">
    // 百度地图API功能

    var lng = "{!! empty($MerchantDetail['lat'])?114.02597366:$MerchantDetail['lng'] !!}";
    var lat = "{!! empty($MerchantDetail['lng'])?22.54605355:$MerchantDetail['lat'] !!}";
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

        var detail_address = $("#address").val();
console.log(detail_address)
        myGeo.getPoint(detail_address, function(point){
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