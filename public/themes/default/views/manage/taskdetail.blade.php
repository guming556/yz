<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#need">订单详细</a>
            </li>

            <li class="">
                <a data-toggle="tab" href="#draft">工作者详细</a>
            </li>

            <li class="">
                <a data-toggle="tab" href="#leave">工作者附件</a>
            </li>

            <li class="">
                <a data-toggle="tab" href="#deal">财务流水</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#deal_other">工程配置单</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#small_order_detail">小订单详细</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#principal_material_order">主材选购单</a>
            </li>
        </ul>
    </div>
</div>
<div class="widget-body">
    <div class="modal fade" id="modal-container-939164" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        主材单材料列表
                    </h4>
                </div>
                <div class="modal-body">
                    <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
                        <thead>
                        <tr>
                            <th class="center">材料名称</th>
                            <th class="center">产品型号</th>
                            <th class="center">品牌</th>
                            <th class="center">产品规格</th>
                            <th class="center">需求数量</th>
                            <th class="center">使用区域</th>
                            <th class="center">当前状态</th>
                            <th class="center">操作</th>
                        </tr>
                        </thead>
                        <tbody id="materialDetail"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    {{--<button type="button" class="btn btn-primary">保存</button>--}}
                </div>
            </div>

        </div>

    </div>



    <div class="widget-main paddingTop no-padding-left no-padding-right">
        <div class="tab-content padding-4">
            <div id="need" class="tab-pane active">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>
                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    业主姓名： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-1">{{ empty($taskDetail['boss_nike_name'])?$taskDetail['name']:$taskDetail['boss_nike_name'] }}</label>
                                </div>

                            </div>

                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    工地位置： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">{{ $taskDetail['region'] }}{{ $taskDetail['project_position'] }}</label>
                                </div>
                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    喜好风格： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-1">{{ $taskDetail['favourite_style'] }}</label>

                                </div>


                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    面积： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-1">{{ $taskDetail['square'] }}㎡</label>

                                </div>

                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    状态： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">{{$taskDetail['status_work']}}</label>

                                </div>

                            </div>

                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    单价： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">¥ {{$taskDetail['unit_price']}}</label>

                                </div>

                            </div>

                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    计价面积： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">¥ {{$taskDetail['area']}}</label>

                                </div>

                            </div>

                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    总价： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">¥ {{$taskDetail['first_price']}}</label>

                                </div>

                            </div>
                            @if(!empty($taskDetail['quanlity_price_total']))
                                <div class="form-group interface-bottom col-xs-12">
                                    <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                        质保金总价(质保金单价*面积)： </label>

                                    <div class="col-sm-9">
                                        <label class="col-sm-9">¥ {{ $taskDetail['quanlity_price_unit'] }} * {{$taskDetail['area']}} = {{$taskDetail['quanlity_price_total']}}</label>

                                    </div>

                                </div>
                            @endif
                            @if($taskDetail['user_type']==3)
                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    所选工人星级： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">{{$taskDetail['workerStar']}}星</label>

                                </div>

                            </div>
                            @endif
                            @if($taskDetail['user_type']==3)
                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    所选辅材包： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">{{$taskDetail['auxiliary_id']}}</label>

                                </div>
                            </div>
                            @endif


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    创建时间： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-9">{{$taskDetail['created_at']}}</label>

                                </div>

                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    配置摄像头： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-1">

                                        <input type="text" name="live_tv_url" id="live_tv_res"
                                               value="{{$taskDetail['live_tv_url']}}" placeholder="设备码">
                                        <input type="hidden" name="task_id" value="{{$taskDetail['task_id']}}">
                                    </label>
                                </div>

                                <div class="col-sm-9">
                                    <label class="col-sm-1">
                                        <button class="btn btn-success btn-small submit_live_tv_url" type="button">提交
                                        </button>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="draft" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>
                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    姓名： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-3">{{ $workers['nickname'] }}</label>
                                </div>


                            </div>

                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    手机号码： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-3">{{ $workers['mobile'] }}</label>
                                </div>
                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    单价： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-3">{{ $workers['cost_of_design'] }}</label>

                                </div>


                            </div>


                            <div class="form-group interface-bottom col-xs-12">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                    住址： </label>

                                <div class="col-sm-9">
                                    <label class="col-sm-3">{{ $workers['address'] }}</label>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="leave" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>

                            @foreach($imgList as $value)
                                <div class="form-group interface-bottom col-xs-12">
                                    <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                        {{$value['img_type_name'].$value['img_name']}}： </label>

                                    <div class="col-sm-9">
                                        <label class="col-sm-3"><img src="/{{ $value['url'] }}" alt=""></label>
                                    </div>
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>

            <div id="deal" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>


                            <div class="form-group interface-bottom col-xs-12">
                                <div class="container-fluid">
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        编号
                                                    </th>
                                                    <th>
                                                        支付编号
                                                    </th>
                                                    <th>
                                                        产生时间
                                                    </th>
                                                    <th>
                                                        结算阶段
                                                    </th>
                                                    <th>
                                                        费用说明
                                                    </th>
                                                    <th>
                                                        用户名
                                                    </th>
                                                    <th>
                                                        金额
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($sub_order_info as $key=> $value)
                                                    <tr class="success">
                                                        <td>
                                                            {{$key+1}}
                                                        </td>
                                                        <td>
                                                            {{$value['order_code']}}
                                                        </td>
                                                        <td>
                                                            {{$value['created_at']}}
                                                        </td>
                                                        @if($value['project_type'] == 1)
                                                                <td>
                                                                    拆除阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 2)
                                                                <td>
                                                                    水电阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 3)
                                                                <td>
                                                                    防水阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 4)
                                                                <td>
                                                                    泥工阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 5)
                                                                <td>
                                                                    木工阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 6)
                                                                <td>
                                                                    油漆阶段结算
                                                                </td>
                                                            @elseif($value['project_type'] == 7)
                                                                <td>
                                                                    综合阶段结算
                                                                </td>
                                                            @else
                                                                <td>
                                                                    其他结算
                                                                </td>
                                                        @endif
                                                        @if($value['work_type'] == 5)
                                                            <td>
                                                                【泥水工】{{$value['title']}}
                                                            </td>
                                                        @elseif($value['work_type'] == 6)
                                                            <td>
                                                                【木工】{{$value['title']}}
                                                            </td>
                                                        @elseif($value['work_type'] == 7)
                                                            <td>
                                                                【水电工】{{$value['title']}}
                                                            </td>
                                                        @elseif($value['work_type'] == 8)
                                                            <td>
                                                                【油漆工】{{$value['title']}}
                                                            </td>
                                                        @elseif($value['work_type'] == 9)
                                                            <td>
                                                                【安装工】{{$value['title']}}
                                                            </td>
                                                        @elseif($value['work_type'] == 10)
                                                            <td>
                                                                【拆除工】{{$value['title']}}
                                                            </td>>
                                                        @else
                                                            <td>
                                                                {{$value['title']}}
                                                            </td>

                                                        @endif

                                                        @if($value['nickname'])
                                                            <td>
                                                                {{$value['nickname']}}
                                                            </td>
                                                        @else
                                                            <td>
                                                                系统账户
                                                            </td>
                                                        @endif
                                                        @if($value['fund_state'] == 1)
                                                            <td>
                                                                -{{$value['cash']}}
                                                            </td>
                                                        @else
                                                            <td>
                                                                +{{$value['cash']}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="deal_other" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>


                            <div class="form-group interface-bottom col-xs-12">
                                <div class="container-fluid">
                                    <div class="row-fluid">


                                        <div>
                                            <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
                                                <thead>
                                                <tr>
                                                    <th class="center">工作者</th>
                                                    {{--<th class="center">排序</th>--}}
                                                    <th class="center">工程名称</th>
                                                    <th class="center">工程拆分项</th>
                                                    <th class="center">需要数量</th>
                                                    <th class="center">单价（元）</th>
                                                    <th class="center">总价（元）</th>
                                                    {{--<th class="center">数量</th>--}}
                                                    {{--<th class="center">单价</th>--}}
                                                    {{--<th class="center">拆分总价</th>--}}
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($deatil as $item )
                                                    <tr>
                                                        <td rowspan="{!! $item['row'] !!}">
                                                            {!! $item['labor_name'] !!}

                                                        </td>


                                                        <td rowspan="{!! $item['row'] !!}">
                                                            {!! $item['project_type_name'] !!}
                                                        </td>

                                                    </tr>
                                                    @foreach($item['child'] as $child )
                                                        <tr>
                                                            @if($child['project_type']==2)
                                                            <td >
                                                                {!! $child['name'].'---'.get_work_type_name_other($child['work_type']) !!}
                                                            </td>
                                                            @else
                                                                <td >
                                                                    {!! $child['name'] !!}
                                                                </td>
                                                            @endif
                                                            <td >
                                                                {!! $child['user_need_num'] !!}
                                                            </td>
                                                            <td >
                                                                {!! $child['unit_price'] !!}
                                                            </td>
                                                            <td >
                                                                {!! $child['child_price'] !!}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>


                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div id="small_order_detail" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>
                            <div class="form-group interface-bottom col-xs-12">
                                <div class="container-fluid">
                                    <div class="row-fluid">
                                        <div>
                                            <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
                                                <thead>
                                                <tr>
                                                    <th class="center">订单编号</th>
                                                    <th class="center">生成时间</th>
                                                    <th class="center">订单状态</th>
                                                    <th class="center">工程项目</th>
                                                    <th class="center">项目描述</th>
                                                    <th class="center">工人费用</th>
                                                    <th class="center">工人姓名</th>
                                                    <th class="center">材料费</th>
                                                    <th class="center">总价（元）</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($small_order_info as $item )
                                                    <tr>
                                                        <td>
                                                            {!! $item['small_order_id'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['created_at'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['status'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['project_type'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['desc'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['offer_change_price'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['labor_deatil'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['cash_house_keeper'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $item['total_price'] !!}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div id="principal_material_order" class="tab-pane ">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="g-backrealdetails clearfix bor-border interface">
                            <div class="space-8 col-xs-12"></div>
                            <div class="form-group interface-bottom col-xs-12">
                                <div class="container-fluid">
                                    <div class="row-fluid">
                                        <div>
                                            <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
                                                <thead>
                                                <tr>
                                                    <th class="center">订单编号</th>
                                                    <th class="center">生成时间</th>
                                                    <th class="center">订单状态</th>
                                                    <th class="center">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($principal_material_order as $item )
                                                    <tr>
                                                        <td>
                                                            {!! $item->code !!}
                                                        </td>
                                                        <td>
                                                            {!! $item->created_at !!}
                                                        </td>
                                                        <td>
                                                            @if($item->status == 1)
                                                                管家已提交，待业主确认
                                                            @elseif($item->status == 2)
                                                                业主已确认
                                                            @else
                                                                业主已驳回，待管家重新提交
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a class="principalDetail" onclick="detailList({!! $item->id !!})" alt="{!! $item->id !!}" role="button" class="btn" data-toggle="modal">详细</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
</div>

</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}

<script>
    $(function () {
        $('.submit_live_tv_url').on('click', function () {
            var task_id = $('input[name=task_id]').val();
            var live_tv_url = $('input[name=live_tv_url]').val();
            $.ajax({
                type: "post",
                url: "/api/submitLiveTVUrl",
                data: {
                    task_id: task_id,
                    live_tv_url: live_tv_url
                },
                dataType: 'json',
                success: function (data) {
                    $('#live_tv_res').val(data.live_tv_url)
                    alert('修改成功');
                }
            });
        });

//        $(".principalDetail").click(function(){

//            var id = $(this).attr('alt');
//            $.ajax({
//                type: "get",
//                url: "/manage/getMaterialList/"+id,
//                dataType: 'json',
//                success: function (ret) {
//                    var html = '';
//                    var data = ret.list;
//                    var status = ret.orderStatus;
////                    1为已提交，2为业主确认，3为业主驳回
////                    receiving_state   0为待发货，1为已发货，待管家确认收货  2已收货
//                    for(var i=0;i<data.length;i++){
//                        html += '<tr>';
//                        html += '<td>'+data[i].name+'</td>';
//                        html += '<td>'+data[i].model_name+'</td>';
//                        html += '<td>'+data[i].brand_name+'</td>';
//                        html += '<td>'+data[i].specifications+'</td>';
//                        html += '<td>'+data[i].num+'</td>';
//                        html += '<td>'+data[i].use_area+'</td>';
//                        if(status == 2){
//                            if(data[i].receiving_state == 0){
//                                if(data[i].audit_status == 0){
//                                    html += '<td>待发货</td>';
//                                    html += '<td><a onclick="sureSend('+data[i].id+')" >确认发货</a></td>';
//                                }
//                                if(data[i].audit_status == 1){
//                                    html += '<td>管家提出修改请求</td>';
//                                    html += '<td>待业主确认</td>';
//                                }
//                                if(data[i].audit_status == 2){  //冗余的判断
//                                    html += '<td>业主已确认</td>';
//                                    html += '<td><a onclick="sureSend('+data[i].id+')" >确认发货</a></td>';
//                                }
//                                if(data[i].audit_status == 3){
//                                    html += '<td>业主驳回管家修改</td>';
//                                    html += '<td>待管家提交</td>';
//                                }
//                            }else if(data[i].receiving_state == 1){
//                                html += '<td>已发货，待管家确认收货</td>';
//                                html += '<td>已发货</td>';
//                            }else if(data[i].receiving_state == 2){
//                                html += '<td>管家已确认收货</td>';
//                                html += '<td>已收货</td>';
//                            }
//                        }else{
//                            html += '<td>业主未确认主材单</td>';
//                            html += '<td>业主未确认主材单</td>';
//                        }
//                        html += '</tr>';
//                    }
//                    $("#materialDetail").html(html);
//                    $("#modal-container-939164").modal();
//                }
//            });
//        })



    });


    function detailList(id){
        $.ajax({
            type: "get",
            url: "/manage/getMaterialList/"+id,
            dataType: 'json',
            success: function (ret) {
                var html = '';
                var data = ret.list;
                var status = ret.orderStatus;
//                    1为已提交，2为业主确认，3为业主驳回
//                    receiving_state   0为待发货，1为已发货，待管家确认收货  2已收货
                for(var i=0;i<data.length;i++){
                    html += '<tr>';
                    html += '<td>'+data[i].name+'</td>';
                    html += '<td>'+data[i].model_name+'</td>';
                    html += '<td>'+data[i].brand_name+'</td>';
                    html += '<td>'+data[i].specifications+'</td>';
                    html += '<td>'+data[i].num+'</td>';
                    html += '<td>'+data[i].use_area+'</td>';
                    if(status == 2){
                        if(data[i].receiving_state == 0){
                            if(data[i].audit_status == 0){
                                html += '<td>待发货</td>';
                                html += '<td><a onclick="sureSend('+data[i].id+')" >确认发货</a></td>';
                            }
                            if(data[i].audit_status == 1){
                                html += '<td>管家提出修改请求</td>';
                                html += '<td>待业主确认</td>';
                            }
                            if(data[i].audit_status == 2){  //冗余的判断
                                html += '<td>业主已确认</td>';
                                html += '<td><a onclick="sureSend('+data[i].id+')" >确认发货</a></td>';
                            }
                            if(data[i].audit_status == 3){
                                html += '<td>业主驳回管家修改</td>';
                                html += '<td>待管家提交</td>';
                            }
                        }else if(data[i].receiving_state == 1){
                            html += '<td>已发货，待管家确认收货</td>';
                            html += '<td>已发货</td>';
                        }else if(data[i].receiving_state == 2){
                            html += '<td>管家已确认收货</td>';
                            html += '<td>已收货</td>';
                        }
                    }else{
                        html += '<td>业主未确认主材单</td>';
                        html += '<td>业主未确认主材单</td>';
                    }
                    html += '</tr>';
                }
                $("#materialDetail").html(html);
                $("#modal-container-939164").modal();
            }
        });
    }
    function sureSend(listId){
        var status = confirm('是否确认发货？');
        if(status){
            $.ajax({
                type: "post",
                url: "/manage/sureMaterial",
                dataType: 'json',
                data:{
                    listId:  listId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (ret) {
                    if(ret.code == 200){
                        alert('确认成功!');
                        window.location.href='';
                    }else{
                        alert('确认失败!');
                    }
                }
            });
        }else{
            return;
        }

    }
</script>