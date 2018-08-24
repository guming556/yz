<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>楼盘列表</title>
<style type="text/css">
body {background-color:#FBFBFB;}
    .font-color {
        color: red;
    }
</style>
</head>
<body>
<div class="firmL-nav">
    <a href="/wxRepairIndex"  class="return" style="z-index: 10;top: 25%;"><i class="icon-16"></i></a>
    <ul class="firmL-nav-U myassociation-nav" style="text-align: center;">
        <li style="width: 100%">
            <span>{!! $current_province !!}<i class="icon-23"></i></span>
            <div class="firmLBox">
                <div class="firmLText" style="color: #444444;text-align: left;">
                    <p>请选择城市</p>
                    @foreach($province as $key => $value)
                        <label>
                            <a href="/buildingList?id={!! $value->id !!}"  @if($value->id == $id)class="font-color" @endif style="margin-left: 20px;">{!! $value->name !!}</a>
                        </label>
                    @endforeach

                </div>
                <div class="firmLBg"></div>
            </div>
            <div class="bar"></div>
        </li>


        <!-- 	<div class="assInput-search">
                <input type="search" placeholder="协会关键字" class="associationList-search">
                <i class="icon-icon-1460187631756 icon-search-association"></i>
            </div>
  -->

    </ul>
</div>
			<div>
                <header>
                    <div class="header">
                        <h1>请选择楼盘</h1>
                        <a href="/wxRepairIndex" class="return"><i class="icon-16"></i></a>
                    </div>
                </header>
                <div style="height: 2.5rem;"></div>
            </div>
            <div class="expert-type">
                <ul>
                    @foreach($building as $key => $value)
                        <li><a href="/renovation?id={!! $value->id !!}"><i class="icon-uniE926"></i>{!! $value->building_name !!}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- 引入js资源 -->
            {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
            {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
            {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}

{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_kslider','js/wxRepair/zepto.kslider.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_renovation_init','js/wxRepair/renovation.init.js') !!}
</body>
</html>
