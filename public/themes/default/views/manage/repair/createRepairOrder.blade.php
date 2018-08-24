{{--<div class="well">
	<h4 >编辑资料</h4>
</div>--}}
{{--<h3 class="header smaller lighter blue mg-top12 mg-bottom20">编辑资料</h3>--}}
<style>
    .allmap img {
        width: 38px;
        height: 25px;
    }
</style>

<div class="alert-danger">
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
</div>

<form class="form-horizontal registerform" role="form" action="{!! url('manage/subBuildingEdit') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="g-backrealdetails clearfix bor-border">
        {{--<input type="hidden" name="edit_id" value="{!! $building_id !!}">--}}

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="province"> 楼盘（物管）：</p>
            <p class="col-sm-3">
                <select class="form-control" id="province" name="province" >
                    @foreach($buildings as $key => $value)
                    <option value="{!! $value->id !!}">{!! $value->building_name !!} ---- 物管：{!! $value->name !!}</option>
                    @endforeach
                </select>
            </p>

        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="address"> 报修地址：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="address" id="address"  class="col-xs-10 col-sm-5" value="">
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 业主姓名：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="address" id="address"  class="col-xs-10 col-sm-5" value="">
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 业主电话：</p>
            <p class="col-sm-4">
                <input type="text" placeholder="" name="address" id="address"  class="col-xs-10 col-sm-5" value="">
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 业主身份证<span style="color: red">（正面）</span>：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"   value="">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 业主身份证<span style="color: red">（反面）</span>：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"  value="">
            </p>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 报装类型：</p>
            <p class="col-sm-4">
            <label class="radio-inline">
                <input type="radio" name="repair_type" id="repair_type" value="1" checked> 全房装修
            </label>
            <label class="radio-inline">
                <input type="radio" name="repair_type" id="repair_type"  value="2"> 局部装修
            </label>
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 选择装修负责人：</p>
            <p class="col-sm-4">
                <label class="radio-inline">
                    <input type="radio" name="housekeeperSource"  value="1" checked> 自定义
                </label>
                <label class="radio-inline">
                    <input type="radio" name="housekeeperSource"   value="2"> 平台管家
                </label>
            </p>
        </div>
        <div id="selfManager">
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-2 control-label no-padding-left" for="name"> 负责人姓名：</p>
                <p class="col-sm-4">
                    <input type="text" placeholder="" name="address" id="address"  class="col-xs-10 col-sm-5" value="">
                </p>
            </div>
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-2 control-label no-padding-left" for="name"> 负责人电话：</p>
                <p class="col-sm-4">
                    <input type="text" placeholder="" name="address" id="address"  class="col-xs-10 col-sm-5" value="">
                </p>
            </div>

            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-2 control-label no-padding-left" for="name">负责人身份证<span style="color: red">（正面）</span>：</p>
                <p class="col-sm-4">
                    <input type="file" placeholder="" name="address" id="address"   value="">
                </p>
            </div>
            <div class="bankAuth-bottom clearfix col-xs-12">
                <p class="col-sm-2 control-label no-padding-left" for="name">负责人身份证<span style="color: red">（反面）</span>：</p>
                <p class="col-sm-4">
                    <input type="file" placeholder="" name="address" id="address"  value="">
                </p>
            </div>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12 hide" id="platformManager" >
            <p class="col-sm-2 control-label no-padding-left" for="name"> 平台管家：</p>
            <p class="col-sm-4">
                <select class="form-control" name="province" >
                    @foreach($housekeepers as $key => $value)
                        <option value="{!! $value->id !!}">{!! $value->code !!} - {!! $value->realname !!}</option>
                    @endforeach
                </select>
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 装修营业执照：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"  value="">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 特种作业证照片：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"  value="">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 工程图纸<span style="color: red">（可上传多张）</span>：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"  value="" multiple="multiple">
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-2 control-label no-padding-left" for="name"> 添加工人：</p>
            <p class="col-sm-4">
                <input type="file" placeholder="" name="address" id="address"  value="" multiple="multiple">
            </p>
        </div>
        <div class="col-xs-12">
            <div class="clearfix row bg-backf5 padding20 mg-margin12">
                <div class="col-xs-12">
                    <div class="col-md-2 text-right"></div>
                    <div class="col-md-10">
                        <button class="btn btn-primary btn-sm" type="submit">提交</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="space col-xs-12"></div>


    </div>
    </div>
</form>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('validform-css', 'plugins/jquery/validform/css/style.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}

<script>
    $(function(){
        $("input[name='housekeeperSource']").change(function(){
            var source = this.value;
            if(source === '1'){
                $("#selfManager").addClass('show');
                $("#platformManager").removeClass('show');
                $("#platformManager").addClass('hide');
            }else{
                $("#selfManager").removeClass('show');
                $("#selfManager").addClass('hide');
                $("#platformManager").addClass('show');

            }
        });



    });


</script>