
<h3 class="header smaller lighter blue mg-bottom20 mg-top12">作品详细</h3>
{{--/advertisement/updateInfo/{!! $ad_id !!}--}}
<form class="form-horizontal" action="/manage/handleWorkGoodsSub" method="post" enctype="multipart/form-data" >
    {{ csrf_field() }}
    {{--<input name="worker_id" value="{!! $worker_id !!}" type="hidden"/>--}}
    <input name="goods_id" value="{!! $goods_id !!}}" type="hidden"/>
    <div class="widget-body">
        <div class="">
            <div class="g-backrealdetails clearfix bor-border">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td class="text-right">主图：</td>
                        <td>
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="main_file"  id="id-input-file-3" />
                                    {{--@if($adInfo[0]['ad_file'])--}}
                                    {{--<img src="{!! url($adInfo[0]['ad_file']) !!}" width="152" height="126">--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{--{!! $adInfo[0]['ad_url'] !!}--}}
                    <tr>
                        <td class="text-right">作品名称：</td>
                        <td class="text-left">
                            <input type="text" name="goods_name" value=""  class="col-sm-6">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">作品面积：</td>
                        <td class="text-left">
                            <input type="text" name="goods_square" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">作品地址：</td>
                        <td class="text-left">
                            <input type="text" name="goods_address" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">户型：</td>
                        <td class="text-left">
                            <select name="goods_house" class="target_id">
                                <option value="0">全部</option>
                                @foreach($house as $item)
                                    <option value="{!! $item->id !!}"  >{!! $item->name !!}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr >
                        <td class="text-right">风格：</td>
                        <td class="text-left">
                            <select name="goods_style" class="target_id">
                                <option value="0">全部</option>
                                @foreach($style as $item)
                                    <option value="{!! $item->id !!}"  >{!! $item->name !!}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">版块图片：    </td>
                        <td class="text-left">
                            <input multiple="" type="file" name="section[img][]"  value="" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">版块位置名称：</td>
                        <td class="text-left">
                            <input type="text" name="section[position][]" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">版块位置描述：</td>
                        <td class="text-left">
                            <input type="text" name="section[des][]" value="">
                        </td>
                    </tr>



                    <tr id="addSection" class="hide">
                        <td class="text-right">添加版块：</td>
                        <td class="text-left">
                            <div class="ace-spinner touch-spinner" style="width: 100px;"><div class="input-group">
                                    <button type="button" class="btn btn-default btn-sm" onclick="addSection()">添加版块</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{--<tr>--}}
                    {{--<td class="text-right">是否开启：</td>--}}
                    {{--<td class="text-left">--}}
                    {{--<input type="radio" name="is_open" value="1" @if($adInfo[0]['is_open'] == '1')checked="checked"@endif>是 <input type="radio" name="is_open" value="2" @if($adInfo[0]['is_open'] == '2')checked="checked"@endif>否--}}
                    {{--</td>--}}
                    {{--</tr>--}}
                    <tr class="hide">
                        <td class="text-right"></td>
                        <td class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm">提交</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('bootstrap-datetimepicker.css', 'plugins/ace/css/bootstrap-datetimepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('fuelux.spinner.min.js', 'plugins/ace/js/fuelux/fuelux.spinner.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('moment', 'plugins/ace/js/date-time/moment.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepickertime-js', 'plugins/ace/js/date-time/bootstrap-datetimepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('datefuelux-js', 'js/doc/datefuelux.js') !!}
{{--{!! Theme::asset()->container('custom-js')->usePath()->add('ad-js', 'js/doc/ad.js') !!}--}}
<script>
    function addSection(){
        var html =  '<tr><td class="text-right">版块图片：    </td> <td class="text-left"> <input multiple="" type="file" name="section[img][]"  id="id-input-file-2" /> </td> </tr>';
        html += '<tr><td class="text-right">版块位置名称：</td> <td class="text-left"> <input type="text" name="section[position][]" value=""> </td> </tr>';
        html += '<tr><td class="text-right">版块位置描述：</td> <td class="text-left"> <input type="text" name="section[des][]" value=""> </td> </tr>';

        $("#addSection").before(html);
    }
</script>


