<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>管家详细</title>
	<style>
		.tab-list li {
			width: 50%;
		}
		.tab-list li.pick {
			border-top: 0;
			border-bottom: 4px solid #51aee3;
		}
		.product-list {
			padding-top: 0px;
		}
	</style>
</head>
<body>
	<div>
		<header>
			<div class="header">
				<h1>管家详细</h1>
				<a onclick="history.go(-1)" class="return" style="top: 25%;z-index: 10"><i class="icon-16"></i></a>
			</div>
		</header>	
		<div style="height: 2.5rem;"></div>
	</div>
	<!-- 我的社区 Myassociation -->
	<div class="Myassociation">
		<!-- 社区头部信息 -->
		<div class="mya-head">
			<div class="mya-logoTxt">
				@if(!empty($houseKeeper_detail['avatar']))
					<img src="{!! $houseKeeper_detail['avatar'] !!}">
				@else
					<img src="{!! Theme::asset()->url('images/wxRepair/dt4.jpg') !!}">
				@endif

				<div class="mya-txt">
					<h1>{!! $houseKeeper_detail['nickname'] !!}</h1>
					<p>{!! $houseKeeper_detail['star'] !!}星</p>
					<p>{!! $houseKeeper_detail['city'] !!}&nbsp;&nbsp;&nbsp;{!! $houseKeeper_detail['experience'] !!}工作经验</p>
					<p>{!! $houseKeeper_detail['address'] !!}</p>
					<p><span style="color: red">{!! $houseKeeper_detail['pageviews'] !!}</span>人浏览&nbsp;&nbsp;&nbsp;<span style="color: red">{!! $houseKeeper_detail['employee_num'] !!}</span>人成交</p>
				</div>
				<div class="cl"></div>
			</div>

			{{--<ul class="mya-sum">--}}
				{{--<li>欢迎下载！共筑辉煌！<div class="bar"></div></li>--}}
				{{--<li>人数&nbsp;&nbsp;<span>13人</span></li>--}}
			{{--</ul>--}}
			{{--<a href="tel:13535800058" class="compile-btn myaTel-btn"><i class="icon-3"></i></a>--}}
		</div>
		<!-- 社区头部信息 end-->
		<!-- 文化理念，服务范畴，联系我们，视频 -->
		<div class="mya-content">
			<ul class="datum-list">
				<li class="dropdown">
					<h1 data-toggle="dropdown" class="vtitle">
						<i class="icon-19"></i>
						<b>自我介绍<i style="color: red;font-size: 0.5rem">（点击展开或收起）</i></b>
						<span class="icon-uniE926"></span>
					</h1>
					<div class="dropdown-menu">
						<div class="M-concept">
							{!! $houseKeeper_detail['introduce'] !!}
						</div>
					</div>
				</li>
			</ul>
			<div class="firmD-tab">
				<!-- tab nav -->
				<ul id="tab_btn" class="tab-list">
					<li class="pick">工地</li>
					<li><span class="bar"></span>案例</li>
					{{--<li><span class="bar"></span>评价</li>--}}

				</ul>
				<!-- tab nav end-->
				<ul>
					<li class="tab_content show">
						<div class="firmD-menu">
							<ul class="vconlist">

								@foreach($houseKeeper_detail['tasks_detail'] as $key => $value)
									<li class="firmD-img" style="padding: 0.5rem;position: relative;">
										<div style="height: 30px;position: absolute;color: white;font-size: 1.6rem;text-align: center;width: 100%;top: calc( 50% - 15px );">{!! $value['project_position'] !!}</div>
										<img src="{!! Theme::asset()->url('images/wxRepair/demo2.jpg') !!}">
									</li>
								@endforeach
								<div class="cl"></div>
							</ul>
						</div>
					</li>

					<li class="tab_content">
						<ul class="product-list">
							@foreach($houseKeeper_detail['goods_list'] as $key => $value)
								<li class="firmD-img" style="padding: 0.5rem;position: relative;">
									<div style="height: 30px;position: absolute;color: white;font-size: 1.6rem;text-align: center;width: 100%;top: calc( 50% - 15px );">{!! $value['goods_address'] !!}</div>
									@if(!empty($value['cover']))
										<img src="{!! $value['cover'] !!}">
									@else
										<img src="{!! Theme::asset()->url('images/wxRepair/demo2.jpg') !!}">
									@endif

								</li>
							@endforeach
						</ul>
					</li>

					{{--<li class="tab_content">--}}
						{{--<ul class="vconlist supply bio-supply">--}}
							{{--<li>--}}
								{{--<a href="supplyDetails.html">--}}
									{{--<label><span class="btn btn-green">供应</span></label>--}}
									{{--<div class="vcon-content">--}}
										{{--<span class="supply-text"> 10吨黑美人西瓜待销售 </span>--}}
									{{--<span class="supply-time">--}}
										{{--2016-04-25--}}
										{{--<span class="icon-uniE926"></span>--}}
									{{--</span>--}}
									{{--</div>--}}
								{{--</a>--}}
							{{--</li>--}}
							{{--<li>--}}
								{{--<a href="#">--}}
									{{--<label><span class="btn btn-red">求购</span></label>--}}
									{{--<div class="vcon-content">--}}
										{{--<span class="supply-text"> 需要10吨黑美人西瓜 </span>--}}
									{{--<span class="supply-time">--}}
										{{--2016-04-25--}}
										{{--<span class="icon-uniE926"></span>--}}
									{{--</span>--}}
									{{--</div>--}}
								{{--</a>--}}
							{{--</li>--}}
							{{--<li>--}}
								{{--<a href="#">--}}
									{{--<label><span class="btn btn-red">求购</span></label>--}}
									{{--<div class="vcon-content">--}}
										{{--<span class="supply-text"> 需要5吨进口凤梨 </span>--}}
									{{--<span class="supply-time">--}}
										{{--2016-04-25--}}
										{{--<span class="icon-uniE926"></span>--}}
									{{--</span>--}}
									{{--</div>--}}
								{{--</a>--}}
							{{--</li>--}}
							{{--<li>--}}
								{{--<a href="#">--}}
									{{--<label><span class="btn btn-green">供应</span></label>--}}
									{{--<div class="vcon-content">--}}
										{{--<span class="supply-text"> 5吨进口凤梨待销售 </span>--}}
									{{--<span class="supply-time">--}}
										{{--2016-04-25--}}
										{{--<span class="icon-uniE926"></span>--}}
									{{--</span>--}}
									{{--</div>--}}
								{{--</a>--}}
							{{--</li>--}}
							{{--<div class="cl"></div>--}}
						{{--</ul>--}}
					{{--</li>--}}
				</ul>
			</div>
		</div>
		<!-- 文化理念，服务范畴，联系我们，视频 end-->
	</div>

	<!-- 引用的js文件 -->
	<!-- 引入js资源 -->
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_renovation_custom','js/wxRepair/custom.js') !!}

	<script type="text/javascript">
	//TAB切换
	var tab_Btns_box=document.getElementById('tab_btn');
	var tab_Btns=tab_Btns_box.getElementsByTagName('li');
	var tab_contents=document.getElementsByClassName('tab_content');
	for(var i=0;i<tab_Btns.length;i++){
		tab_Btns[i].index=i;

		tab_Btns[i].onclick=function(){
			for(var i=0;i<tab_Btns.length;i++){
				if(i!==this.index){
					tab_Btns[i].classList.remove('pick')
				}
			}
			tab_Btns[this.index].className="pick";

			for(var j=0;j<tab_contents.length;j++){
				if(j!==this.index){
					tab_contents[j].classList.remove('show');
				}
				tab_contents[this.index].classList.add('show');
			}
		}
	}
</script>

</body>
</html>