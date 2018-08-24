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
            <li class="">
                <a href="{!! url('manage/orderConfig') !!}" title="">接单设置</a>
            </li>
            <li class="active">
                <a href="{!! url('manage/designConfig') !!}" title="">设计费用设置</a>
            </li>
        </ul>
    </div>
</div>
<!--  /.page-header -->
<form class="form-horizontal" role="form" method="post" action="{!! url('manage/designConfig') !!}">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border interface">
        <!-- PAGE CONTENT BEGINS -->
            <div class="space-8 col-xs-12"></div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">总比例</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[total_percentage]"
                           value="@if(isset($data['total_percentage'])){!! $data['total_percentage'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）（下面的比例将按照需要按照总比例进行设置，否则会导致设置失败）</span>
                    </span>
                </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">上门费</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[door_fee]"
                            value="@if(isset($data['door_fee'])){!! $data['door_fee'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">量房费</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[amount_room]"
                           value="@if(isset($data['amount_room'])){!! $data['amount_room'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">平面图</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[floor_plan]"
                           value="@if(isset($data['floor_plan'])){!! $data['floor_plan'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
            <div class="form-group interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">效果图</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[rendering]"
                           value="@if(isset($data['rendering'])){!! $data['rendering'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
            <div class=" interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">施工图</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[construction_drawing]" value="@if(isset($data['construction_drawing'])){!! $data['construction_drawing'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
            <div class=" interface-bottom col-xs-12">
                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">施工指导费</label>
                <div class="col-sm-9">
                    <input type="text" id="form-field-1" class="col-xs-10 col-sm-4" name="design_config[construction_fee]" value="@if(isset($data['construction_fee'])){!! $data['construction_fee'] !!}@endif">
                    <span class="help-inline col-xs-12 col-sm-8">
                        <span class="middle">（单位：%）</span>
                    </span>
                </div>
            </div>
        <!-- <div class="col-sm-12">
            <div class="widget-box">
                <div class="widget-header widget-header-flat">
                    <h5 class="widget-title">设计费用设置</h5>
                </div>
        
                <div class="widget-body">
                    <div class="widget-main row paddingTop">
                        <div class="table-responsive">
                            <table class="table table-hover mg-bottom0">
                                <tbody>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">总比例：</td>
                                        <td> <input type="text" name="39" value="0" class="change_ids"> %（下面的比例将按照。。。）</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">上门费：</td>
                                        <td> <input type="text" name="40" value="8" class="change_ids"> %</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">量房费：</td>
                                        <td> <input type="text" name="41" value="3" class="change_ids"> %</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">平面图：</td>
                                        <td> <input type="text" name="42" value="3" class="change_ids"> %</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">效果图：</td>
                                        <td> <input type="text" name="43" value="3" class="change_ids"> %</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">施工图：</td>
                                        <td> <input type="text" name="36" value="10" class="change_ids"> %</td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2 flow-money text-right">施工指导费：</td>
                                        <td> <input type="text" name="36" value="10" class="change_ids"> %</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
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