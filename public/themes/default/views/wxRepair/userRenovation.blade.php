<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>装修报装</title>
	<style>
		.purchase .input-row label {
			width: 6rem;
		}
		.purchase .group-T, .purchase .input-row input, .purchase .input-row .form_hint {
			padding-left: 6rem;
		}
		#cadImage li {
			/*float: left;*/
			width: 30%;
			margin-left: 2px;
		}
		.LUploader:not(#cadImage){
			float: left;
		}
		/*:not(#cadImage>.LUploader){*/
			/*float: left;*/
		/*}*/
	</style>
</head>
<body>
    <div>
	    <header>
		    <div class="header">
			    <h1>装修报装</h1>
			    <a href="/renovation" class="return" style="    top: 25%;"><i class="icon-16"></i></a>
		    </div>
	    </header>
	    <div style="height: 2.5rem;"></div>
    </div>
    <!--新增采购信息-->
    <div class="purchase">
		<form class="input-group">
			{{--<input id="_token" name="_token" value="{{ csrf_token() }}"/>--}}
			<h1 style="font-size: 1rem">基本信息</h1>
		    <div class="input-row">
			    <label><i class="icon-113"></i>&nbsp;楼盘</label>
			    <input type="text" placeholder="请输入主题内容" readonly value="万科天誉" />
		    </div>
            <div class="input-row">
			    <label><i class="icon-uniE969"></i>&nbsp;类型</label>
		        <div class="group-T">
			        <input type="radio" class="radio-la" name="purchase-type" id="check-1" hidden>
			        <label for="check-1" class="group-T-l icon-uniE940">全房装修</label>
			        <input type="radio" class="radio-la" name="purchase-type" id="check-2" hidden>
			        <label for="check-2" class="group-T-l icon-uniE940">局部装修</label>
				</div>
		    </div>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;地址</label>
				<input type="text" placeholder="请输入报装地址" required>
				<div class="form_hint">请填写报装地址</div>
			</div>
		    <div class="input-row">
			    <label><i class="icon-uniE953"></i>&nbsp;联系人</label>
			    <input type="text" placeholder="请输入联系人" required>
			    <div class="form_hint">请填写联系人</div>
		    </div>
			<div class="input-row">
			    <label><i class="icon-14"></i>&nbsp;电话</label>
			    <input type="text" placeholder="填写手机号码" required pattern="^(13[0-9]|15[0|1|3|6|7|8|9]|18[8|9])\d{8}$">
			    <div class="form_hint">请填写11位数的手机号码</div>
			</div>
			<div class="input-row">
				<label style="width: 100%"><i class="icon-uniE953"></i>报装人身份证</label>
				<div style="margin-top: 35px">
					<div class="LUploader" id="userCardImage_1">
						<div class="LUploader-container">
							<input data-LUploader='userCardImage_1' data-form-file='basestr' data-upload-type='front' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>正面</p>
						</div>
						<div style="display: none;" id="userCardImage_1_path_list" >

						</div>
					</div>
					<div class="LUploader" id="userCardImage_2">
						<div class="LUploader-container">
							<input data-LUploader='userCardImage_2' data-form-file='basestr' data-upload-type='back' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>反面</p>
						</div>
						<div style="display: none;" id="userCardImage_2_path_list" >

						</div>
					</div>
				</div>
			</div>

			<div class="input-row">
				<label style="width: 100%"><i class="icon-uniE953"></i>装修公司营业执照</label>
				<div style="margin-top: 35px">
					<div class="LUploader" id="businessLicense">
						<div class="LUploader-container">
							<input data-LUploader='businessLicense' data-form-file='basestr' data-upload-type='front' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>营业执照</p>
						</div>
						<div style="display: none;" id="businessLicense_path_list" >

						</div>
					</div>
				</div>
			</div>
			<div class="input-row">
				<label style="width: 100%"><i class="icon-uniE953"></i>施工CAD图纸（最多9张）</label>
				<div style="margin-top: 35px">
					<div class="LUploader" id="cadImage" style="clear: left;width: 97%">
						<div class="LUploader-container">
							<input data-LUploader='cadImage' data-form-file='basestr' data-upload-type='front' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list" style="text-align: left"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>CAD图纸</p>
						</div>
						<div style="display: none;" id="cadImage_path_list" >

						</div>
					</div>
				</div>
			</div>

<h1 style="margin-top: 1rem;font-size: 1rem">负责人信息</h1>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;负责人姓名</label>
				<input type="text" placeholder="请输入负责人姓名" required>
				<div class="form_hint">请填写负责人姓名</div>
			</div>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;负责人电话</label>
				<input type="text" placeholder="请输入负责人电话" required>
				<div class="form_hint">请填写负责人电话</div>
			</div>
			<div class="input-row">
				<label style="width: 100%"><i class="icon-uniE953"></i>负责人身份证</label>
				<div style="margin-top: 35px">
					<div class="LUploader" id="housekeeperIdCard_1">
						<div class="LUploader-container">
							<input data-LUploader='housekeeperIdCard_1' data-form-file='basestr' data-upload-type='front' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>正面</p>
						</div>
						<div style="display: none;" id="housekeeperIdCard_1_path_list" >

						</div>
					</div>
					<div class="LUploader" id="housekeeperIdCard_2">
						<div class="LUploader-container">
							<input data-LUploader='housekeeperIdCard_2' data-form-file='basestr' data-upload-type='back' data-_token="{{ csrf_token() }}" type="file" />
							<ul class="LUploader-list"></ul>
						</div>
						<div>
							<div class="icon icon-camera font20"></div>
							<p>反面</p>
						</div>
						<div style="display: none;" id="housekeeperIdCard_2_path_list" >

						</div>
					</div>
				</div>
			</div>



			<h1 style="margin-top: 1rem;font-size: 1rem">工人人信息</h1>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;工人姓名</label>
				<input type="text" placeholder="请输入工人姓名" required>
				<div class="form_hint">请填写工人姓名</div>
			</div>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;工人电话</label>
				<input type="text" placeholder="请输入工人电话" required>
				<div class="form_hint">请填写工人电话</div>
			</div>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>&nbsp;选择工种</label>
				<!--<input type="text" placeholder="请输入工人工种" required>-->
				<select style="padding-left: 6rem;">
					<option>拆除工</option>
					<option>泥水工</option>
					<option>水电工</option>
					<option>木工</option>
					<option>油漆工</option>
					<!--<option>拆除工</option>-->
				</select>
				<div class="form_hint">请选择工人工种</div>
			</div>
			<div class="input-row">
				<label><i class="icon-uniE953"></i>工人身份证图片</label>
				<div style="margin-top: 35px">
				<div class="LUploader" id="workerIdCard_1">
					<div class="LUploader-container">
						<input data-LUploader='workerIdCard_1' data-form-file='basestr' data-upload-type='front' data-_token="{{ csrf_token() }}" type="file" />
						<ul class="LUploader-list"></ul>
					</div>
					<div>
						<div class="icon icon-camera font20"></div>
						<p>正面</p>
					</div>
					<div style="display: none;" id="workerIdCard_1_path_list" >

					</div>
				</div>
				<div class="LUploader" id="workerIdCard_2">
					<div class="LUploader-container">
						<input data-LUploader='workerIdCard_2' data-form-file='basestr' data-upload-type='back' data-_token="{{ csrf_token() }}" type="file" />
						<ul class="LUploader-list"></ul>
					</div>
					<div>
						<div class="icon icon-camera font20"></div>
						<p>反面</p>
					</div>
					<div style="display: none;" id="workerIdCard_2_path_list" >

					</div>
				</div>
				</div>
			</div>
			<!--<button type="button" onclick="return false;" >增加工人</button>-->
			<!--2196f3-->
            <button type="button" style="background: #2196f3" onclick="subData()" >提交</button>
        </form>
	</div>
	<!--新增采购信息end-->




	<!-- 引入js资源 -->
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}
	{!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_luploader','css/wxRepair/LUploader.css') !!}

	{{--{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}--}}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/jquery.min.js') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_luploaderjs','js/wxRepair/LUploader.js') !!}
	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_exif_js','js/exif/exif.js') !!}

	{!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_renovation_init','js/wxRepair/userRenovation.init.js') !!}
	<script>
		function subData(){

		}

	</script>


</body>
</html>






















