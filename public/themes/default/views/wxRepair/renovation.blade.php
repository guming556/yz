<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<title>易装</title>
</head>
<style>
	.nav-acvity{
		color: red;
	}
</style>
<body>


<div>
	<header>
		<div class="header">
			<h1>装修报装</h1>
			<a href="/buildingList" class="return" style="    top: 25%;"><i class="icon-16"></i></a>
		</div>
	</header>
	<div style="height: 2.5rem;"></div>
</div>
          <div class="m-slider" id="slider" >
		    <div class="ks_dbox ks_ts">

				@foreach($ad as $key => $value)
					<div class="ks_wrap">
						<img src="{!! $value->ad_file !!}">
					</div>
				@endforeach
		    </div>
		    <div class="ks-circles"><ul class="ks_wt ks_wt_1"></ul></div>
	    </div>
	<!-- 我的协会列表 associationList-- >
	<div class="firmList associationList">
		<!-- 头部选择项 -->


		<!-- 头部选择项 end-->
		<ul class="firmL-U" style="margin-top:0 ">
			@foreach($housekeeper as $key => $value)
				<li>
					<a href="/housekeeperDetail?id={!! $value->id !!}">
						@if(!empty($value->avatar))
							<img src="{!! $value->avatar !!}">
						@else
							<img src="{!! Theme::asset()->url('images/wxRepair/dt4.jpg') !!}">
						@endif
						<div class="firm-text">
							<h1>{!! $value->realname !!}</h1>
							<p>评价：暂无评价</p>
							<div class="firm-span">
								<span>收费：{!! $value->cost_of_design !!}</span>
								{{--<span>推荐：广东省协会</span>--}}
								<br/>
								<span style="width: 100%;">星级：好评（{!! $value->good !!}） 一般（{!! $value->commonly !!}） 差评价（{!! $value->bad !!}）</span>
								<!-- 		<span>电话:+86-(027)5133855</span> -->
							</div>
						</div>
						<i class="icon-uniE926"></i>
					</a>
				</li>
			@endforeach

		</ul>

		 <nav class="nav-bar nav-bar-tab" style="margin-top: 10px">
            <a href="/userRenovation?id={!! $id !!}" class="nav-tab-item" style="background: #51aee3;
    color: white;">
                <!-- <span class="nav-icon icon-uniE90A"></span> -->
                <span class="nav-tab-label">我要报装</span>
            </a>
            <a href="/myRenovationRecord" class="nav-tab-item">
                <!-- <span class="nav-icon icon-uniE919"></span> -->
                <span class="nav-tab-label">我的报装</span>
            </a>
        </nav>

	</div>
</body>
<!-- 引入js资源 -->
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}


{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_kslider','js/wxRepair/zepto.kslider.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_renovation_init','js/wxRepair/renovation.init.js') !!}
<!-- 引入js资源 -->

</html>