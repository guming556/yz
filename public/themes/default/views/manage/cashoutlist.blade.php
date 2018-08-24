



<h3 class="header smaller lighter blue mg-bottom20 mg-top12">提现审核</h3>

@if ($errors->has('messageOfStatus'))

    <div class="alertmsg hide">{{ $errors->first('messageOfStatus') }}</div>

@endif






<div class="well">
    <form class="form-inline search-group" role="form" method="get" action="{!! url('manage/cashoutList') !!}">
        <div class="form-group search-list ">
            <label for="namee" class="">提现工地　</label>
            <input name="position_address" type="text" @if(isset($search['position_address']))value="{!! $search['position_address'] !!}@endif" />
        </div>

        <div class="form-group search-list ">
            <label for="namee" class="">单号　</label>
            <input name="pay_code" type="text" @if(isset($search['pay_code']))value="{!! $search['pay_code'] !!}@endif" />
        </div>

        <div class="form-group search-list ">
            <label for="namee" class="">编号　</label>
            <input name="new_order" type="text" @if(isset($search['new_order']))value="{!! $search['new_order'] !!}@endif" />
        </div>

        <div class="form-group search-list ">
            <label for="namee" class="">提现手机号　</label>
            <input name="worker_phone_num" type="text" @if(isset($search['worker_phone_num']))value="{!! $search['worker_phone_num'] !!}@endif" />
        </div>
   <div class="form-group search-list width285">
            <label class="">提现状态　</label>
       <select name="cashout_status">
           <option value="" ></option>
           <option value="1">待审核</option>
           <option value="2">二审通过</option>
           <option value="3">三审通过</option>
           <option value="4">审核不通过</option>
       </select>
        </div>
{{--        <div class="form-group search-list ">
            <label for="namee" class="">时间　　　</label>
            <div class="input-daterange input-group">
                <input type="text" name="start" class="input-sm form-control" value="@if(isset($start)){!! $start !!}@endif">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                <input type="text" name="end" class="input-sm form-control" value="@if(isset($end)){!! $end !!}@endif">
            </div>
        </div>--}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-sm">搜索</button>
        </div>
    </form>
</div>
<div class="">
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>
                <label>
                    <span class="lbl"></span>
                    编号
                </label>
            </th>
            <th>提现类型</th>
            <th>提现手机号</th>
            <th>姓名</th>
            <th>单号</th>
            <th>施工状态</th>
            <th>提现金额</th>
            <th>费用说明</th>
            <th>业主手机号</th>
            <th>提现工地</th>
            <th>服务总费用</th>

            <th>平台赠送充值金额</th>
            <th>平台到账金额</th>
            <th>抵用金额</th>
            <th>充值平台费率</th>
            <th>应付工作者</th>

            <th>收款账号</th>
            <th>银行</th>
            <th>提现时间</th>
            <th>提现状态</th>
            <th>是否已打款</th>
            <th>操作</th>
        </tr>
        </thead>

        <tbody>
        @if(!empty($all_list))

            @foreach($all_list as $item)

                <!-- 注意：请将要放入弹框的内容放在比如id="HBox"的容器中，然后将box的值换成该ID即可，比如：$(element).hDialog({'box':'#HBox'}); -->
                <div id="HBox">
                    <form action="/manage/cashConfirm" method="post">
                        {{csrf_field()}}
                        <ul class="list">
                            <li>
                                <strong>平台赠送金额  <font color="#ff0000">*</font></strong>
                                <div class="fl"><input type="text" name="privilege_amount_task" value="" class="ipt nickname" /></div>

                            </li>
                            <li>
                                <strong>抵用金额 <font color="#ff0000">*</font></strong>
                                <div class="fl"><input type="text" name="privilege_amount_sn" value="" class="ipt phone"  /></div>

                            </li>
                            <li>
                                <strong>充值费率 <font color="#ff0000">*</font></strong>
                                <div class="fl"><input type="text" name="fees" value="" class="ipt email" placeholder="必须在0-1之间"/>%</div>

                            </li>
                            <li><input type="submit" value="确认提交" class="submitBtn" /></li>
                            <input type="hidden" name="cash_out_id" id="cash_out_id" value="">
                            @foreach($search as $key => $value)
                            <input type="hidden" name="search[{!! $key !!}]"  value="{!! $value !!}">
                            @endforeach;
                        </ul>
                    </form>
                </div><!-- HBox end -->


        <tr>
            <td>
                {!! $item->new_order !!}
            </td>
            <td>
                {!! $item->user_type_name !!}
            </td>
            <td>
                {!! $item->worker_phone_num !!}
            </td>

            <td>
                {!! $item->worker_name !!}
            </td>

            <td>
                {!! $item->pay_code !!}
            </td>

            <td>
                {!! $item->work_offer_status_name !!}
            </td>

            <td>
                {!! $item->cash !!}
            </td>

            <td>
                {!! $item->sn_title !!}
            </td>

            <td>
                {!! $item->boss_phone_num !!}
            </td>

            <td>
                {!! $item->position_address !!}
            </td>

            <td>
                <a style="cursor: pointer" onclick="show_all_pay_detail(this)" data-task_id="{{$item->task_id}}"  data-uid="{{$item->uid}}">{!! $item->total_pay_task !!}</a>
            </td>

            <td>
                {!! $item->privilege_amount_task !!}
            </td>

            <td style="color:red">
                {!! $item->total_pay_task_actual !!}
            </td>

            <td style="color:red">
                {!! $item->privilege_amount_sn !!}
            </td>

            <td style="color:red">
                {!! $item->fees !!}
            </td>

            <td style="color:red">
                {!! $item->real_cash !!}
            </td>

            <td>
                {!! $item->cashout_account !!}
            </td>

            <td>
                {!! $item->bank_name !!}
            </td>

            <td>
                {!! $item->created_at !!}
            </td>

            <td style="color:red">
                {{--@if($item->status == 0)待审核@elseif($item->status == 2)未通过审核@else已打款@endif--}}
                @if($item->status == 1)
                    待审核
                @elseif($item->status == 2)
                    二审通过
                @elseif($item->status == 3)
                    三审通过
                @elseif($item->status == 4)
                    审核不通过
                @else
                    审核完成
                @endif

            </td>
            <td>
                @if($item->status == 3)
                    <a style="cursor: pointer" altid="{{$item->id}} "  altsub_order_index_id="{{$item->sub_order_index_id}}" onclick="withdraw_remit(this)">打款</a>
                @elseif($item->status == 5)
                    <span>已打款</span>
                @else
                    <span>未打款</span>
                @endif
            </td>
            <td>
                {{--@if($item->status == 0)--}}
                    {{--<a href="{!! url('manage/cashoutHandle/' . $item->id . '/pass') !!}" class="btn btn-xs btn-success" title="确认打款"><i class="ace-icon fa fa-check bigger-120"></i></a>--}}
                    {{--<a href="{!! url('manage/cashoutHandle/' . $item->id . '/deny') !!}" class="btn btn-xs btn-danger" title="不通过审核"><i class="ace-icon fa fa-ban bigger-120"></i></a>--}}
                {{--@endif--}}
                {{--<a href="{!! url('manage/cashoutInfo/' . $item->id) !!}" class="btn btn-xs btn-info" title="查看"><i class="ace-icon fa fa-search bigger-120"></i></a>--}}
                @if($item->status == 1)
                    <a class="demo_3" style="cursor: pointer" altid="{{$item->id}}" onclick="edit(this)">审核</a>
                @elseif($item->status == 2)
                    <a style="cursor: pointer" altid="{{$item->id}}" altrate = "{{ $item->fees }}" privilege_amount_task = "{{ $item->privilege_amount_task }}" onclick="edit_end(this)">确认二审</a>**

                    <a class="demo_3" style="cursor: pointer" altid="{{$item->id}}" onclick="edit(this)">修改</a>
                @else
                    <span style="cursor: pointer" altid="{{$item->id}}" ></span>
                @endif


            </td>
        </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>




<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $all_list->appends($search)->render() !!}
        </div>
    </div>
</div>
{{--<script src="/themes/default/assets/js/jquery.hDialog.js"></script>--}}

<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>

<script>
    $(function () {
        var $el = $('.dialog');
        $el.hDialog(); //默认调用

        $(".demo_3").hDialog(
                {
                    resetForm: false
                }
        );
    })

    function edit(obj) {
        $("#cash_out_id").attr("value", $(obj).attr("altid"));
    }

    function edit_end(obj) {
        var id = $(obj).attr("altid");
        var altrate = $(obj).attr("altrate");
        var privilege_amount_task = $(obj).attr("privilege_amount_task");

        var res = confirm("您是否确认二审通过?")
        if (res==true)
        {
            $.ajax({
                type: "post",
                url: "/manage/cashConfirmEnd",
                data: {
                    id: id,
                    altrate: altrate,
                    privilege_amount_task: privilege_amount_task
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    location.reload()
                }
            });
        }

    }

    function show_all_pay_detail(obj) {

        var task_id = $(obj).attr("data-task_id");
        var uid = $(obj).attr("data-uid");

        alert('费用详细正在完善')

    }

    function withdraw_remit(obj) {
        var id = $(obj).attr("altid");
        var sub_order_index_id = $(obj).attr("altsub_order_index_id");
        var res = confirm("您是否确认已打款?")
        if (res==true)
        {
            $.ajax({
                type: "post",
                url: "/manage/withdrawRemit",
                data: {
                    id: id,
                    sub_order_index_id: sub_order_index_id,

                },
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    location.reload()
                }
            });
        }

    }
</script>

<script type="text/javascript">
    $(function () {
        if ($('.alertmsg').length) {
            alert($('.alertmsg').html());
        }
    })
</script>
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('hDialog-js', 'js/jquery.hDialog.js') !!}

