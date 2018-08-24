<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<title>工人列表</title>
</head>
<style>
	.nav-acvity{
		color: red;
	}
</style>
<body>

	<!-- 我的协会列表 associationList-- >
	<div class="firmList associationList">
		<!-- 头部选择项 -->

		<div class="firmL-nav" >
			<a href="/wxRepairIndex"  class="return" style="z-index: 10;top: 25%;"><i class="icon-16"></i></a>
			<ul class="firmL-nav-U myassociation-nav" style="text-align: center;">
				<li style="width: 100%">
					<span>楼盘<i class="icon-23"></i></span>
					<div class="firmLBox">
						<div class="firmLText" style="color: #444444;text-align: left;"> 
                   				<p>楼盘列表</p>
							@foreach($building as $key => $value)
								<label>
									<a href="/findWorker?id={!! $value->id !!}"  style="margin-left: 20px;@if($id==$value->id)color:red @endif">{!! $value->building_name !!}</a>
								</label>
							@endforeach

						</div>
						<div class="firmLBg"></div>
					</div>
					<div class="bar"></div>
				</li>
                
			</ul>
		</div>
		<!-- 头部选择项 end-->
		<ul class="firmL-U" style="margin-top:45px ">
			@foreach($worker as $key => $value)
				<li>
					<a href="#">
						@if(!empty($value->avatar))
							<img src="{!! $value->avatar !!}">
						@else
							<img src="{!! Theme::asset()->url('images/wxRepair/dt4.jpg') !!}">
						@endif
						<div class="firm-text">
							<h1>{!! $value->realname !!}（{!! $value->native_place !!}）</h1>
							<p>评价：暂无评价</p>
							<div class="firm-span">
								<span>收费：{!! $value->cost_of_design !!}</span>
								<span style="width: 100%;">工种：{!! $value->work_type !!}</span>
								{{--<span>推荐：广东省协会</span>--}}
								{{--<br/>--}}
								<span style="width: 100%;">星级：{!! $value->star !!}星</span>
								<span style="width: 100%;">评价：好评（{!! $value->good !!}） 一般（{!! $value->commonly !!}） 差评价（{!! $value->bad !!}）</span>
								<!-- 		<span>电话:+86-(027)5133855</span> -->
							</div>
						</div>
						<i class="icon-uniE926"></i>
					</a>
				</li>
			@endforeach

		</ul>


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