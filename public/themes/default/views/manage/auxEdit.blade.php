<h3 class="header smaller lighter blue mg-bottom20 mg-top12">{{empty($aux_data)?'添加辅材包':'编辑辅材包'}}</h3>
<form action="/manage/auxAdd" method="post">
    {{ csrf_field() }}
    <div class="g-backrealdetails clearfix bor-border">

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-md-1 text-right">辅材包名称：</p>
            <p class="col-md-11">
                <input type="text" name="name" id="name" value="{{empty($aux_data['name'])?'':$aux_data['name']}}">
                <input type="hidden" name="id" value="{{empty($aux_data['id'])?'':$aux_data['id']}}">
                {{ $errors->first('name') }}
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-md-1 text-right">辅材包单价：</p>
            <p class="col-md-11">
                <input type="text" name="aux_price" id="aux_price" value="{{empty($aux_data['price'])?'':$aux_data['price']}}">
                <input type="hidden" name="id" value="{{empty($aux_data['id'])?'':$aux_data['id']}}">
                {{ $errors->first('name') }}
            </p>
        </div>

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p  class="col-sm-1 control-label no-padding-left">辅材包城市：</p>
            <div class="col-sm-5">
                <div class="row">
                    <p class="col-sm-4">
                        @if(!empty($aux_data))
                            <select name="serve_province" id="serve_province" class="form-control validform-select Validform_error">

                                @foreach($province as $item)
                                    @if($aux_data['province_id']==$item['id'])
                                        <option value="{!! $item['id'] !!}" selected="selected">{!! $item['name'] !!}</option>
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
                        @if(!empty($aux_data))
                            <select class="form-control  validform-select" name="serve_city" id="serve_city">
                                @foreach($city as $k=>$v)
                                    @if($aux_data['city_id']==$v['id'])
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

        <div class="bankAuth-bottom clearfix col-xs-12">
            <p class="col-md-1 text-right">辅材包内容：</p>
            <!--编辑器-->
            <p class="clearfix col-md-8">
                <script id="editor" type="text/plain" style="width:;height:300px;" name="content">{!! htmlspecialchars_decode(empty($aux_data['content'])?'':$aux_data['content']) !!}</script>
            </p>
        </div>

        <div class="col-xs-12">
            <div class="clearfix row bg-backf5 padding20 mg-margin12">
                <div class="col-xs-12">
                    <div class="col-md-1 text-right"></div>
                    <div class="col-md-10"><button class="btn btn-primary btn-sm" type="submit">提交</button></div>
                </div>
            </div>
        </div>
        <div class="space col-xs-12"></div>
        <div class="col-xs-12">
            <div class="col-md-1 text-right"></div>
            <div class="col-md-10"><a href="javascript:history.back()">返回</a>　　<a href=""></a></div>
        </div>
        <div class="col-xs-12 space">

        </div>
    </div>
</form>


<!-- basic scripts -->
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('datepicker', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('custom', 'plugins/ace/js/jquery-ui.custom.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('touch-punch', 'plugins/ace/js/jquery.ui.touch-punch.min.js') !!}

{!! Theme::asset()->container('specific-js')->usepath()->add('chosen', 'plugins/ace/js/chosen.jquery.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('autosize', 'plugins/ace/js/jquery.autosize.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('inputlimiter', 'plugins/ace/js/jquery.inputlimiter.1.3.1.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('maskedinput', 'plugins/ace/js/jquery.maskedinput.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('hotkeys', 'plugins/ace/js/jquery.hotkeys.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('wysiwyg', 'plugins/ace/js/bootstrap-wysiwyg.min.js') !!}

{!! Theme::asset()->container('custom-js')->usepath()->add('dataTab', 'plugins/ace/js/dataTab.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('jquery_dataTables', 'plugins/ace/js/jquery.dataTables.bootstrap.js') !!}

{!! Theme::asset()->container('custom-js')->usepath()->add('addarticle', 'js/doc/addarticle.js') !!}
{!! Theme::widget('ueditor')->render() !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
