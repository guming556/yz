

<div class="modal fade" id="modal-container-608683" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">
                    配置工人
                </h4>
            </div>
            <div class="modal-body">
                <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
                    <thead>
                    <tr>
                        <th class="center">工种</th>
                        <th class="center">姓名</th>
                        <th class="center">星级</th>
                        <th class="center">单价（元）</th>
                    </tr>
                    </thead>
                    <tbody>

                        <tr>

                            <td >
                                1
                            </td>

                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> <button type="button" class="btn btn-primary">保存</button>
            </div>
        </div>

    </div>

</div>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#">
                    @if( $type == 'projectConfDetail' )
                        工程配置
                    @else
                        整改工程配置
                    @endif
                </a>
            </li>

        </ul>
    </div>
</div>


<form action="/manage/subWorkerConf" method="post">
    {{csrf_field()}}

    <div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover center">
            <thead>
            <tr>
                <th class="center">操作</th>
                {{--<th class="center">排序</th>--}}
                <th class="center">工程名称</th>
                <th class="center">工程拆分项</th>
                <th class="center">需要数量</th>
                <th class="center">单价（元）</th>
                {{--<th class="center">数量</th>--}}
                {{--<th class="center">单价</th>--}}
                {{--<th class="center">拆分总价</th>--}}
            </tr>
            </thead>
            <tbody>
            @foreach($deatil as $item )
            <tr>
                <td rowspan="{!! $item['row'] !!}">
                        @if($item['project_type'] == 2)
                            @if(isset($item['need_work']))
                                <select class="form-control" name="project[{!! $item['project_type'] !!}][]">
                                    <option value="0">请选择泥水工</option>
                                    @foreach($item['need_work'] as $key => $value)

                                        <option value="{!! $value['uid'] !!}">{!! $value['worker_number'] !!} - {!! $value['realname'] !!}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if(isset($item['need_work_2']))
                                <select class="form-control" name="project[{!! $item['project_type'] !!}][]">
                                    <option value="0">请选择水电工</option>
                                    @foreach($item['need_work_2'] as $key => $value)

                                        <option value="{!! $value['uid'] !!}">
                                            {!! $value['worker_number'] !!} - {!! $value['realname'] !!}
                                        </option>
                                    @endforeach
                                </select>
                             @endif
                        @else
                            @if($item['project_type'] == 7)
                                <select class="form-control" name="project[{!! $item['project_type'] !!}][]">
                                    {{--<option value="0">请选择管家</option>--}}
                                        <option value="1">由管家进行，无需选择</option>
                                </select>
                            @else
                                <select class="form-control" name="project[{!! $item['project_type'] !!}][]">
                                    <option value="0">请选择工人</option>
                                    @foreach($item['need_work'] as $key => $value)
                                        <option value="{!! $value['uid'] !!}">{!! $value['worker_number'] !!} - {!! $value['realname'] !!}</option>
                                    @endforeach
                                </select>
                            @endif
                        @endif


                </td>
                {{--<td rowspan="{!! $item['row'] !!}}">--}}
                    {{--{!! $item['id'] !!}--}}
                {{--</td>--}}
                <td rowspan="{!! $item['row'] !!}">
                    {!! $item['project_type_name'] !!}
                </td>

            </tr>
                @foreach($item['child'] as $child )
                    <tr>
                        <td >
                            {!! $child['name'] !!}
                        </td>
                        <td >
                            {!! $child['user_need_num'] !!}
                        </td>
                        <td >
                            {!! $child['unit_price'] !!}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task_id" value="{!! $task_id !!}"/>
    <input type="hidden" name="type" value="{!! $type !!}"/>
    <button type="submit" class="btn btn-warning">提交工人配置</button>
</form>


{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
