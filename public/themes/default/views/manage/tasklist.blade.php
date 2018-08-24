
<h3 class="header smaller lighter blue mg-top12 mg-bottom20">订单列表</h3>
<div class="row">
    <div class="col-xs-12">
        <div class="clearfix  well">
            <div class="form-inline search-group">
                <form  role="form" action="/manage/taskList" method="get">
                    <div class="form-group search-list">
                        <label for="name">工地地址　</label>
                        <input type="text" class="form-control" id="task_title" name="task_title" placeholder="请输入工地地址" @if(isset($merge['task_title']))value="{!! $merge['task_title'] !!}"@endif>
                    </div>
                    <div class="form-group search-list">
                        <label for="namee">用户名　　</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名" @if(isset($merge['username']))value="{!! $merge['username'] !!}"@endif>
                    </div>
                    <div class="form-group search-list">
                        <label for="nameee">订单号　　</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="请输入订单号" @if(isset($merge['code']))value="{!! $merge['code'] !!}"@endif>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                    </div>
                    <div class="space"></div>
                    <div class="form-inline search-group" >
                        <div class="form-group search-list">
                            <select class="" name="time_type">
                                <option value="task.created_at" @if(isset($merge['time_type']) && $merge['time_type'] == 'task.created_at')selected="selected"@endif>发布时间</option>
                                <option value="task.verified_at" @if(isset($merge['time_type']) && $merge['time_type'] == 'task.verified_at')selected="selected"@endif>审核时间</option>
                            </select>
                            <div class="input-daterange input-group">
                                <input type="text" name="start" class="input-sm form-control" value="@if(isset($merge['start'])){!! $merge['start'] !!}@endif">
                                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                <input type="text" name="end" class="input-sm form-control" value="@if(isset($merge['end'])){!! $merge['end'] !!}@endif">
                            </div>
                        </div>
                        <div class="form-group search-list width285">
                            <label class="">状态　</label>
                            <select name="status">
                                <option value="0">全部</option>
                                <option value="1" @if(isset($merge['status']) && $merge['status'] == '1')selected="selected"@endif>未发布</option>
                                <option value="2" @if(isset($merge['status']) && $merge['status'] == '2')selected="selected"@endif>待审核</option>
                                <option value="3" @if(isset($merge['status']) && $merge['status'] == '3')selected="selected"@endif>进行中</option>
                                <option value="4" @if(isset($merge['status']) && $merge['status'] == '4')selected="selected"@endif>已结束</option>
                                <option value="5" @if(isset($merge['status']) && $merge['status'] == '5')selected="selected"@endif>失败</option>
                                <option value="6" @if(isset($merge['status']) && $merge['status'] == '6')selected="selected"@endif>维权</option>
                            </select>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div>
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th>编号</th>
                    <th style="text-align: center">订单号</th>
                    <th>用户名</th>
                    <th>工地地址</th>

                    <th>
                        发布时间
                    </th>
                    <th>
                        状态
                    </th>
                    <th>
                        订单类型
                    </th>
                    <th>
                        排序(数字越小,排名越高)
                    </th>
                    <th>
                        更多直播隐藏(1隐藏,2不隐藏)
                    </th>


                    <th>处理</th>
                </tr>
                </thead>
                <form action="/manage/taskMultiHandle" method="post">
                    {!! csrf_field() !!}
                    <tbody>
                    @foreach($task as $item)
                        <tr>


                            <td>
                                <div style="text-align: center">{!! $item->id !!}</div>
                            </td>
                            <td>
                                <div style="text-align: center">{!! $item->code !!}</div>
                            </td>
                            <td>{!! empty($item->boss_nike_name)?$item->name:$item->boss_nike_name !!}</td>
                            <td class="hidden-480">
                                <a   href="/manage/taskDetail/{{ $item->id }}">{!! $item->region !!}{!! $item->project_position !!}</a>
                            </td>
                            <td>{!! $item->created_at !!}</td>

                            <td class="hidden-480">
                                @if($item->status == 0)
                                    <span class="label label-sm label-warning">未发布</span>
                                @elseif($item->status == 1 || $item->status == 2)
                                    <span class="label label-sm label-success">待审核</span>
                                @elseif($item->status >= 3 && $item->status <= 8)
                                    <span class="label label-sm label-danger ">进行中</span>
                                @elseif($item->status == 9)
                                    <span class="label label-sm label-inverse">已结束</span>
                                @elseif($item->status == 10)
                                    <span class="label label-sm label-danger">失败</span>
                                @elseif($item->status == 11)
                                    <span class="label label-sm label-inverse">维权</span>
                                @endif
                            </td>

                            <td class="hidden-480">
                                @if($item->type_model == 1)
                                    <span class="label label-sm label-success">抢单
                                        @if($item->user_type == 2)
                                            设计师
                                        @elseif($item->user_type == 3)
                                            管家
                                        @elseif($item->user_type == 4)
                                            监理
                                        @endif
                                    </span>
                                @elseif($item->type_model == 2)
                                    <span class="label label-sm label-success">约单
                                        @if($item->user_type == 2)
                                            设计师
                                        @elseif($item->user_type == 3)
                                            管家
                                        @elseif($item->user_type == 4)
                                            监理
                                        @endif
                                    </span>
                                @endif
                            </td>

                            <td class="hidden-480">
                                @if($item->user_type == 3)
                                    <input type="text" onchange="changeSortId(this)" name="sort_id" value="{{$item->broadcastOrderBy}}" alttaskid="{{$item->id}}">
                                @endif
                            </td>
                            <td class="hidden-480">
                                @if($item->user_type == 3)
                                    <input type="text" onchange="changeHidden(this)" name="hidden_status" value="{{$item->hidden_status}}" alttaskid="{{$item->id}}">
                                @endif
                            </td>

                            <td>
                                <div class="hidden-sm hidden-xs btn-group">
                                    @if($item->status == 1 || $item->status == 2)
                                        <a class="btn btn-xs btn-success" href="/manage/taskHandle/{!! $item->id !!}/pass">
                                            <i class="ace-icon fa fa-check bigger-120">审核通过</i>
                                        </a>

                                        <a class="btn btn-xs btn-danger" href="/manage/taskHandle/{!! $item->id !!}/deny">
                                            <i class="ace-icon fa fa-minus-circle bigger-120"> 审核失败</i>
                                        </a>
                                    @endif

                                    <a href="/manage/taskDetail/{{ $item->id }}" class="btn btn-xs btn-info">
                                        <i class="ace-icon fa fa-edit bigger-120">详情</i>
                                    </a>

                                </div>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </form>
            </table>
        </div>

    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $task->appends($merge)->render() !!}
        </div>
    </div>
</div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>
<script type="text/javascript">
    function changeSortId(obj)
    {
        var sort_id = $(obj).val();
        var taskid = $(obj).attr("alttaskid");

        $.ajax({
            type: "post",
            url: "/manage/broadcastSort",
            data: {
                sort_id: sort_id,
                task_id: taskid,
            },
            dataType: 'json',
            success: function (data) {
                $(obj).val(data.sort_id)
            }
        });
    }

    function changeHidden(obj)
    {
        var hidden_status = $(obj).val();
        var taskid = $(obj).attr("alttaskid");

        $.ajax({
            type: "post",
            url: "/manage/broadcastHidden",
            data: {
                hidden_status: hidden_status,
                task_id: taskid,
            },
            dataType: 'json',
            success: function (data) {
                $(obj).val(data.hidden_status)
            }
        });
    }
</script>
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}

{{--时间插件--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}