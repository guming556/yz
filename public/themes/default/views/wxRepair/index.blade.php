<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<title>易装</title>
</head>
<body>
	<div>
		<header>
			<div class="header">
				<a href="index.html" class="returnA"><h1 class="htitle">易装</h1></a>
				<div class="hmap" id="gomap"><span class="icon-uniE902"></span>深圳</div>
				<div class="head-input-row head-search">
					<input id="search" type="search" placeholder="搜索信息" >
						<span class=" head-icon-left icon-uniE90F"></span>
						<span class=" head-icon-right icon-uniE939"></span>
				</div>
			</div>
		</header>
		<div style="height: 2.5rem;"></div>
	</div>
	<nav class="nav-bar nav-bar-tab">
	    <a class="nav-tab-item nav-active">
	        <span class="nav-icon icon-uniE90A"></span>
	        <span class="nav-tab-label">首页</span>
	    </a>
	    <a href="/findWorker" class="nav-tab-item">
	        <span class="nav-icon icon-uniE919"></span>
	        <span class="nav-tab-label">找工人</span>
	    </a>
	    <a href="/findHousekeeper" class="nav-tab-item">
	        <span class="nav-icon icon-uniE903"></span>
	        <span class="nav-tab-label">找管家</span>
	    </a>
	     <a href="/wxLogin" class="nav-tab-item">
	        <span class="nav-icon icon-uniE90B"></span>
	        <span class="nav-tab-label">我的</span>
	    </a>
	</nav>
	<div class="all_box">
		<!-- 轮播图 -->
	    <div class="m-slider" id="slider">
		    <div class="ks_dbox ks_ts">
				@foreach($ad as $key => $value)
					<div class="ks_wrap">
						<img src="{!! $value->ad_file !!}">
					</div>
				@endforeach

		    </div>
		    <div class="ks-circles"><ul class="ks_wt ks_wt_1"></ul></div>
	    </div>
	    <!-- 轮播图 end-->
	    <!-- 功能板块 -->
	    <div class="big_button">
	    	<div class="big_left">
	    		<div class="big_left_p">
	    			<a href="/buildingList" style="height: 100%">
	    				<span class="icon-uniE92C" style="    padding: 40px 0;"></span>
	    				<h3>装修报装</h3>
	    			</a>
	    		</div>
	    		<div class="ind-bar"></div>
	    	</div>
	    	<div class="big_right">
				<div class="big_rigt_p plate_a fl">
					<a href="/findWorker"><span class="icon-17"></span><h3>找装修工人</h3></a>
					<div class="ind-bar2"></div>
				</div>
				<div class="big_rigt_p plate_b fr">
					<a href="#"><span class="icon--01"></span><h3>发布需求</h3></a>
					<div class="ind-bar3"></div>
				</div>
				<div class="big_rigt_p plate_c fl" style="width: 100%">
					<a href="/findHousekeeper"><span class="icon-uniE932"></span><h3>找装修管家</h3></a>
					<div class="ind-bar2"></div>
				</div>
			
				<div class="cl"></div>
			</div>
	    </div>
	    <!-- 功能板块 end-->
	 <!-- 轮播图 -->
	    <div class="m-slider" id="slider2" >
		    <div class="ks_dbox ks_ts">
				@foreach($ad as $key => $value)
					<div class="ks_wrap">
						<img src="{!! $value->ad_file !!}">
					</div>
				@endforeach

		    </div>
		    <div class="ks-circles"><ul class="ks_wt ks_wt_2"></ul></div>
	    </div>

	    <!-- 轮播图 end-->
		<!-- 工地展示 -->
		 <div class="dynamic-list activity-list" style="margin-bottom: 5rem">
			<div class="dynamic-list-head">
				<i class="dynamic-i-l icon-uniE93D"></i>
				<b>工地展示</b>
			</div>
			<ul class="eventreminder">


				@foreach($construction as $key => $value)
				<li>
		    		<a href="#">
		    			<div class="A-cimg">
							@if(!empty($value['first_image']))
								<img src="{!! $value['first_image'] !!}">

							@else
								<img src="{!! Theme::asset()->url('images/wxRepair/demo.jpg') !!}">
							@endif
		    			</div>
	                    <div class="eventreminder-list">
	                        <p class="activity-h1">{!! $value['project_position'] !!}</p>
	                        <p class="activity-TimeXh"><span class="event-xh">{!! $value['region'] !!}</span></p>
	                    </div>
					</a>
				</li>
				@endforeach

	        </ul>
		</div>
	    <!-- 协会活动列表 end-->
	</div>

    <!-- 协会动态列表 end-->
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_kslider','js/wxRepair/zepto.kslider.js') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_init','js/wxRepair/init.js') !!}
	<script type="text/javascript">

	</script>
	<script>

	</script>
</body>
</html>