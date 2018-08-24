
{{--<div class="page-header pay-api">--}}
    {{--<ul class="nav nav-pills nav-justified">--}}
        {{--<li role="presentation" class="active"><a href="{!! url('manage/payConfig') !!}" title="">支付配置</a></li>--}}
        {{--<li role="presentation"><a href="{!! url('manage/thirdPay') !!}" title="">第三方支付平台接口</a></li>--}}
    {{--</ul>--}}
{{--</div>--}}
<style>
    .hide{
        display: none;
    }
</style>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/advanceConfig') !!}" title="">预约金设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/payConfig') !!}" title="">支付设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/thirdPay') !!}" title="">第三方支付平台接口</a>
            </li>
            <li class="active">
                <a href="{!! url('manage/orderConfig') !!}" title="">接单设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/designConfig') !!}" title="">设计费用设置</a>
            </li>
        </ul>
    </div>
</div>
<!--  /.page-header -->
<form class="form-horizontal" role="form" method="post" action="{!! url('manage/orderConfig') !!}">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border interface">
        <!-- PAGE CONTENT BEGINS -->
            <div class="space-8 col-xs-12"></div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">接单时间限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_public_time[hour]"
                           value="@if(isset($order_public_time['hour'])){!! $order_public_time['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_public_time[minute]"
                           value="@if(isset($order_public_time['minute'])){!! $order_public_time['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_public_time[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ ($order_public_time['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">服务者发起线下约谈限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_server_line_time[hour]"
                           value="@if(isset($order_server_line_time['hour'])){!! $order_server_line_time['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_server_line_time[minute]"
                           value="@if(isset($order_server_line_time['minute'])){!! $order_server_line_time['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_server_line_time[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_server_line_time['status']) && $order_server_line_time['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">业主确认线下约谈时间限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_line_time[hour]"
                           value="@if(isset($order_owner_line_time['hour'])){!! $order_owner_line_time['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_line_time[minute]"
                           value="@if(isset($order_owner_line_time['minute'])){!! $order_owner_line_time['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_owner_line_time[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_owner_line_time['status']) &&  $order_owner_line_time['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">业主选择服务者时间限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_chose_time[hour]"
                           value="@if(isset($order_owner_chose_time['hour'])){!! $order_owner_chose_time['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_chose_time[minute]"
                           value="@if(isset($order_owner_chose_time['minute'])){!! $order_owner_chose_time['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_owner_chose_time[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_owner_chose_time['status']) && $order_owner_chose_time['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">接单后未上门</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-10 col-sm-4" name="order_server_not_come[day]"
                           value="@if(isset($order_server_not_come['day'])){!! $order_server_not_come['day'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">天</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_server_not_come[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_server_not_come['status']) && $order_server_not_come['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-6">服务者拒绝接单</label>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">不能接单时间限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_server_refuse[hour]"
                           value="@if(isset($order_server_refuse['hour'])){!! $order_server_refuse['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_server_refuse[minute]"
                           value="@if(isset($order_server_refuse['minute'])){!! $order_server_refuse['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_server_refuse[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_server_refuse['status']) && $order_server_refuse['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">信用评分下降</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-10 col-sm-4" name="order_server_credit_scoring[score]" value="@if(isset($order_server_credit_scoring['score'])){!! $order_server_credit_scoring['score'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_server_credit_scoring[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_server_credit_scoring['status']) && $order_server_credit_scoring['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-6">业主取消订单接单（服务者接单后）</label>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">扣除预约金</label>
                <div class="col-sm-5">
					
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_owner_cancel" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_owner_cancel) && $order_owner_cancel == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
			<div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">不能预约服务时间限制</label>
                <div class="col-sm-5">
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_refuse[hour]"
                           value="@if(isset($order_owner_refuse['hour'])){!! $order_owner_refuse['hour'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">时</span>
                    </span>
                    <input type="number" class="col-xs-1 col-sm-1" name="order_owner_refuse[minute]"
                           value="@if(isset($order_owner_refuse['minute'])){!! $order_owner_refuse['minute'] !!}@endif">
                    <span class="help-inline col-xs-1 col-sm-1">
                        <span class="middle">分</span>
                    </span>
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_owner_refuse[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_owner_refuse['status']) && $order_owner_refuse['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">同时进行订单上限</label>
                <div class="col-sm-5">
                	<input type="number" id="spinner3" class="col-xs-10 col-sm-4" name="order_server_get_max[num]" value="@if(isset($order_server_get_max['num'])){!! $order_server_get_max['num'] !!}@endif"  maxlength="3">
                	<span class="" style="margin-left: 12px;">个</span>
                    
                </div>
                <div class="col-sm-4">
	            	<div class="widget-toolbar">
                        <label>
                            开关<input name="order_server_get_max[status]" type="checkbox" class="ace ace-switch ace-switch-6 change_sys_help" value="1" {{ (isset($order_server_get_max['status']) && $order_server_get_max['status'] == 1)?'checked':'' }} />
                            <span class="lbl middle"></span>
                        </label>
                    </div>
	            </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">订单提出比例</label>
                <div class="col-sm-5">
					<input type="number" id="assa" class="col-xs-10 col-sm-4" name="order_percentage" value="@if(isset($order_percentage)){!! $order_percentage !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">%</span>
                    </span>
                </div>
            </div>

	        <div class="col-xs-12">
	            <div class="clearfix row bg-backf5 padding20 mg-margin12">
	                <div class="col-xs-12">
	                    <div class="col-sm-1 text-right"></div>
	                    <div class="col-sm-10"><button type="submit" class="btn btn-sm btn-primary">提交</button></div>
	                </div>
	            </div>
	        </div>
	</div>
</form>
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('bootstrap-datetimepicker.css', 'plugins/ace/css/bootstrap-datetimepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('fuelux.spinner.min.js', 'plugins/ace/js/fuelux/fuelux.spinner.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('moment', 'plugins/ace/js/date-time/moment.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepickertime-js', 'plugins/ace/js/date-time/bootstrap-datetimepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('datefuelux-js', 'js/doc/datefuelux.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('ad-js', 'js/doc/ad.js') !!}