{{--<div class="well">
	<h4 >编辑资料</h4>
</div>--}}
{{--<h3 class="header smaller lighter blue mg-top12 mg-bottom20">编辑资料</h3>--}}
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/projectListManage') !!}" title="">工程配置单管理</a>
            </li>
            <li class="active">
                @if(!empty($project_data))
                    <a title="" style="cursor: pointer">编辑配置单</a>
                @else
                    <a title="" style="cursor: pointer">添加配置单</a>
                @endif
            </li>
        </ul>
    </div>
</div>

<div class="alert-danger">
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
</div>

<form class="form-horizontal registerform " role="form" action="{!! url('manage/projectConfigureSubmit') !!}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}


    <div class="g-backrealdetails clearfix bor-border">
        <input type="hidden" name="id" value="@if(!empty($project_data)){{$project_data['id']}} @endif">

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">工程项目：</p>
            <p class="col-sm-8">
                @if(!empty($project_data))
                    <input type="text" name="card_number" id="card_number"  class="col-xs-2 col-sm-2" value="{{get_project_type($project_data['project_type'])}}" disabled="disabled">
                @else
                    <select name="project_type">
                        @if(!empty($project_name))
                            @foreach($project_name as $v)
                                <option value="{{$v['project_type']}}" >{{$v['city_name']}} - {{ $v['desc'] }} </option>
                            @endforeach
                        @endif
                    </select>
                @endif

            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">工种：</p>
            <p class="col-sm-10">
                <select name="work_type">
                    @if(!empty($project_data))
                            <option value="{{$project_data['work_type']}}" >{{ get_work_type_name($project_data['work_type']) }}</option>
                        @else
                        @if(!empty($work_type))
                            @foreach($work_type as $v)
                                <option value="{{$v['work_type']}}" >{{ get_work_type_name($v['work_type']) }}</option>
                            @endforeach
                        @endif
                    @endif
                </select>
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">名称：</p>
            <p class="col-sm-10">
                <textarea name="name" id="" cols="60" rows="1">@if(!empty($project_data)){{$project_data['name']}}@endif</textarea>

            </p>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">单位：</p>
            <p class="col-sm-4">
                <input type="text" name="unit"  class="col-xs-2 col-sm-2" value="@if(!empty($project_data)){{$project_data['unit']}}@endif">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="card_number">编号：</p>
            <p class="col-sm-8">
                <input type="text" name="cardnum"  class="col-xs-2 col-sm-2" value="@if(!empty($project_data)) {{$project_data['cardnum']}} @endif">
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 描述：</p>
            <p class="col-sm-4">
                <textarea name="desc" id="" cols="60" rows="10">@if(!empty($project_data)){{$project_data['desc']}}@endif</textarea>
            </p>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-sm-1 control-label no-padding-left" for="realname"> 单价：</p>
            <p class="col-sm-4">
                <input type="text" name="price" class="col-xs-10 col-sm-5" value="@if(!empty($project_data)){{$project_data['price']}}@endif">@if(empty($project_data))&nbsp;&nbsp;元@endif

                <span style="font-size: large;">&nbsp;&nbsp;@if(!empty($project_data))元/{{$project_data['unit']}}@endif</span>
            </p>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">城市：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        @if(!empty($project_data))
                            <select name="province" id="province" class="form-control validform-select Validform_error">

                                @foreach($province as $item)

                                    @if($project_data['provice_id']==$item['id'])

                                        <option value="{!! $item['id'] !!}"
                                                selected="selected">{!! $item['name'] !!}</option>
                                    @endif


                                @endforeach
                            </select>
                            </select>
                        @else

                            <select name="serve_province" id="serve_province" class="form-control validform-select Validform_error" onchange="getZone(this.value, 'serve_city');">
                                <option value="">请选择省份</option>
                                @foreach($province as $item)
                                    <option value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>
                                @endforeach
                            </select>
                        @endif
                    </p>
                    <p class="col-sm-4">
                        @if(!empty($project_data))
                            <select class="form-control  validform-select" name="city" id="city">
                                @foreach($city as $k=>$v)

                                    @if($project_data['city_id']==$v['id'])
                                        <option value="{{$v['id']}}" selected="selected">{{$v['name']}}</option>
                                    @endif

                                @endforeach
                            </select>
                        @else

                            <select class="form-control  validform-select" name="serve_city" id="serve_city" >
                                <option value="">请选择城市</option>
                            </select>
                        @endif

                    </p>
                    {{--<p class="col-sm-4">--}}
                    {{--<select class="form-control  validform-select" name="area" id="area">--}}
                    {{--<option value="">请选择区域</option>--}}
                    {{--</select>--}}
                    {{--</p>--}}
                </div>
            </div>
        </div>
{{--        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">工作年限：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        <select name="experience" id="experience" class="form-control validform-select Validform_error">
                            <option  selected="selected" value="">666</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12 hide">
            <p  class="col-sm-1 control-label no-padding-left">星级：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        <select name="star" id="star" class="form-control validform-select Validform_error">

                            <option  selected="selected" value="">666</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">工种：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        <select name="work_type" id="work_type" class="form-control validform-select Validform_error">
                            <option  selected="selected" value="">666</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>


        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">用户类型：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-6">
                        <select name="user_type" id="user_type" class="form-control validform-select Validform_error" disabled="disabled">
                            <option value="0">请选择用户类型</option>
                            <option  selected="selected" value="">666</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="bankAuth-bottom clearfix col-xs-12 hide">
            <p  class="col-sm-1 control-label no-padding-left">用户组：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-6">
                        <select name="role_id" id="role_id" class="form-control validform-select Validform_error">
                            <option value="0">请选择用户组</option>

                                <option  selected="selected" value="">666</option>

                        </select>
                    </p>
                </div>
            </div>
        </div>--}}

        <div class="col-xs-12">
            <div class="clearfix row bg-backf5 padding20 mg-margin12">
                <div class="col-xs-12">
                    <div class="col-md-1 text-right"></div>
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
{{--{!! Theme::asset()->container('specific-js')->usePath()->add('validform-js', 'plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}


{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userManage-js', 'js/userManage.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}



