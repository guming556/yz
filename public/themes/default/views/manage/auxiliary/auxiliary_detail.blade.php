<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="/advertisement/adList">辅材包列表</a>
            </li>
        </ul>
    </div>
</div>


<form action="/advertisement/allDelete" method="post">
    {{csrf_field()}}

    <div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>

                <th class="center">ID</th>
                <th class="center">品牌</th>
                <th class="center">型号</th>
                <th class="center">规格</th>
                <th class="center">单位</th>
                <th class="center">数量</th>
                <th class="center">单价</th>
                <th class="center">总额</th>

                {{--<th class="center">操作</th>--}}
            </tr>
            </thead>

            <tbody>
            @foreach($list as $item )
                <tr>

                    <td class="center">
                        {!! $item->id !!}
                    </td>

                    <td class="center">
                        {!! $item->brand !!}
                    </td>

                    <td class="center">
                        {!! $item->model !!} 元 / m<sup>2</sup>
                    </td>

                    <td class="center">
                        {!! $item->spec !!}
                    </td>

                    <td class="center">
                        {!! $item->company !!}
                    </td>

                    <td class="center">
                        {!! $item->num !!}
                    </td>

                    <td class="center">
                        {!! $item->unit_price !!}
                    </td>

                    <td class="center">
                        {!! $item->total !!}
                    </td>

                    {{--<td class="center">--}}
                        {{--<div class="hidden-sm hidden-xs btn-group">--}}
                            {{--<a class="btn btn-xs btn-info" href="/advertisement/update/{!! $item->id !!}">--}}
                                {{--<i class="fa fa-edit bigger-120"></i>编辑--}}
                            {{--</a>--}}
                            {{--<a title="删除" class="btn btn-xs btn-danger" href="/advertisement/deleteInfo/{!! $item->id !!}">--}}
                                {{--<i class="ace-icon fa fa-trash-o bigger-120"></i>删除--}}
                            {{--</a>--}}
                            {{--<a class="btn btn-xs btn-danger" href="/manage/auxiliaryDetail/{!! $item->id !!}">--}}
                                {{--<i class="fa fa-edit bigger-120"></i>详细--}}
                            {{--</a>--}}
                        {{--</div>--}}
                    {{--</td>--}}
                </tr>
            @endforeach

            </tbody>

        </table>

    </div>
</form>
{{--{!! $list->render() !!}--}}

{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
