{{--<div class="space-2 pay-api"></div>
<div class="page-header">
    <h1>
        预约金设置
    </h1>
</div> <!--  /.page-header -->--}}
<h3 class="header smaller lighter blue mg-bottom20 mg-top12">预约金设置</h3>
<form class="form-horizontal alipay-edit" role="form" method="post" action="{!! url('manage/advanceConfigUpdate') !!}">

    <div class="g-backrealdetails clearfix bor-border">
        <!-- PAGE CONTENT BEGINS -->

            {!! csrf_field() !!}
            <input type="hidden" name="id" value="{!! $data['id'] !!}">
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-right" for="form-field-1">服务名称：</p>

                <p class="col-sm-10">
                    <span class=" col-xs-12 col-sm-12 alipy-edit-show">
                        <span class="middle">{!! $data['title'] !!}</span>
                    </span>
                </p>
            </div>
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-right" for="form-field-1">上次编辑时间：</p>

                <p class="col-sm-10">
                    <span class=" col-xs-12 col-sm-12 alipy-edit-show">
                        <span class="middle">{!! $data['rule']['updatetime'] !!}</span>
                    </span>
                </p>
            </div>
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-right" for="form-field-1">上次编辑人：</p>

                <p class="col-sm-10">
                    <span class=" col-xs-12 col-sm-12 alipy-edit-show">
                        <span class="middle">{!! $data['rule']['editor'] !!}</span>
                    </span>
                </p>
            </div>

            
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-1 control-label no-padding-right" for="form-field-1">金额：</p>

                <p class="col-sm-10">
                    <input type="number" id="form-field-1" class="col-xs-10 col-sm-4" name="money" value="{!! $data['rule']['money'] !!}">
                    <span class="help-inline col-xs-12 col-sm-8">
                        （单位：元）
                    </span>
                </p>
            </div>

            <div class="col-xs-12">
                <div class="clearfix row bg-backf5 padding20 mg-margin12">
                    <div class="col-xs-12">
                        <div class="col-md-1 text-right"></div>
                        <div class="col-md-10"><button type="submit" class="btn btn-primary btn-sm">提交</button></div>
                    </div>
                </div>
            </div>
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->

</form>
</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}