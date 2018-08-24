
<h3 class="header smaller lighter blue mg-top12 mg-bottom20">订单列表</h3>
<div class="row">
    <div class="col-xs-12">
        <div class="clearfix  well">
            <div class="form-inline search-group">
                <form class="form-inline search-group" role="form" method="get" action="{!! url('manage/houseKeeperApply') !!}">
                    <div class="form-group search-list ">
                        <label for="namee" class="">工地名　</label>
                        <input name="position_address" type="text" value="" />
                    </div>

{{--                    <div class="form-group search-list ">
                        <label for="namee" class="">单号　</label>
                        <input name="pay_code" type="text" value="" />
                    </div>

                    <div class="form-group search-list ">
                        <label for="namee" class="">编号　</label>
                        <input name="new_order" type="text" value="" />
                    </div>

                    <div class="form-group search-list ">
                        <label for="namee" class="">提现手机号　</label>
                        <input name="worker_phone_num" type="text" value="" />
                    </div>--}}
{{--                    <div class="form-group search-list width285">
                        <label class="">提现状态　</label>
                        <select name="cashout_status">
                            <option value="" ></option>
                            <option value="1">待审核</option>
                            <option value="2">二审通过</option>
                            <option value="3">三审通过</option>
                            <option value="4">审核不通过</option>
                        </select>
                    </div>--}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th>编号</th>
                    <th>工地名</th>
                    <th>阶段及其状态</th>

                    <th>
                        业主姓名
                    </th>
                    <th>
                        业主电话
                    </th>
                    <th>
                        管家姓名
                    </th>
                    <th>
                        管家电话
                    </th>
                    <th>
                        监理姓名
                    </th>
                    <th>
                        监理电话
                    </th>
                    <th>状态</th>
                    <th>操作</th>

                </tr>
                </thead>
                <form action="/manage/taskMultiHandle" method="post">
                    {!! csrf_field() !!}
                    <tbody>
                    @if(!empty($all_data))
                    @foreach($all_data as $item)
                        <tr>


                            <td>
                                <div style="text-align: center">{!! $item->id !!}</div>
                            </td>
                            <td>{!! $item->position_name !!}</td>
                            <td class="hidden-480">
                                {{$item->sn_title}}
                            </td>
                            <td>{!! $item->boss_name !!}</td>
                            <td>{!! $item->boss_phone_num !!}</td>
                            <td>{!! $item->house_name !!}</td>
                            <td>{!! $item->house_phone_num !!}</td>
                            <td>{!! $item->visor_name !!}</td>
                            <td>{!! $item->visor_phone_num !!}</td>

                            <td class="hidden-480">
                                @if($item->status == 0)
                                    <span class="label label-sm label-warning">待审核</span>
                                @elseif($item->status == 1)
                                    <span class="label label-sm label-success">审核通过</span>
                                @else
                                    <span class="label label-sm label-danger">审核暂未通过</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 0)
                                    <a style="cursor: pointer" altid_index="{{$item->id}}"
                                       onclick="ApplyConfirm(this)">通过</a>
                                @endif
                            </td>
                    @endforeach
                        @endif
                    </tbody>
                </form>
            </table>
        </div>

    </div>
</div><!-- /.row -->



<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $all_data->render() !!}
        </div>
    </div>
</div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>

<script>
    function showAuto(){
        location.reload();
    }
    function ApplyConfirm(obj) {
        var id = $(obj).attr("altid_index");
        $.dialog('confirm', '提示', '您确认要通过么？通过后管家,监理工人将收到对应的工资,业主将扣除对应的工资', 0, function () {
            $.closeDialog(function () {
                $.ajax({
                    type: "post",
                    url: "/manage/houseKeeperApplyConfirm",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function (data) {

                        $.tooltip(data.status_msg, 1000, true);
                        setInterval("showAuto()", 1000);
                    }
                });

            });
        });
    }
</script>

{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}

{{--时间插件--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('hDialog-js', 'js/jquery.hDialog.js') !!}