
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#">工程列表</a>
            </li>
        </ul>
    </div>
</div>

{{--<form class="form-inline"  role="form" action="/advertisement/adList" method="get">--}}
{{--<div class="well">--}}
{{--<div class="form-group search-list width285">--}}
{{--作品名称：<input type="text" id="ad_name" name="ad_name" value="@if(isset($ad_name)){!! $ad_name !!}@endif">--}}
{{--</div>--}}
{{--<div class="form-group search-list">--}}
{{--<button type="submit" class="btn btn-primary btn-sm">搜索</button>--}}
{{--</div>--}}
{{--</div>--}}
{{--</form>--}}
<form action="/advertisement/allDelete" method="post">
    {{csrf_field()}}
    {{--<div class="  well">--}}
    {{--<div class="dataTables_info" id="sample-table-2_info">--}}
    {{--<label><input type="checkbox" class="ace" id="allcheck">--}}
    {{--<span class="lbl"></span>全选--}}
    {{--</label>--}}
    {{--<button id="all_delete" type="submit" class="btn btn-sm btn-primary ">批量删除</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                {{--<th class="center">选择</th>--}}
                <th class="center">排序</th>
                <th class="center">地址</th>
                <th class="center">创建时间</th>


                <th class="center">状态</th>
                <th class="center">操作</th>
            </tr>
            </thead>

            <tbody>
            @foreach($list as $item )
                <tr>
                    {{--<td class="center">--}}
                    {{--<label>--}}
                    {{--<input type="checkbox" name="id_{!! $item->id !!}" class="ace" value="{!! $item->id !!}"/>--}}
                    {{--<span class="lbl"></span>--}}
                    {{--</label>--}}
                    {{--</td>--}}
                    <td class="center">
                        {!! $item->id !!}
                    </td>

                    <td class="center">
                        {!! $item->title !!}
                    </td>
                    <td class="center">

                        {!! $item->created_at !!}

                    </td>
                    <td class="center">

                        未匹配工人

                    </td>
                    {{--<td class="center">--}}
                    {{--@if($item->is_open == '1')--}}
                    {{--上架--}}
                    {{--@elseif($item->is_open == '2')--}}
                    {{--下架--}}
                    {{--@endif--}}
                    {{--</td>--}}
                    <td class="center">
                        <div class="hidden-sm hidden-xs btn-group">
                            {{--<a  class="btn btn-xs btn-info" href="/manage/editWorkGoods/{!! $item->id !!}">--}}

                            {{--<i class="fa fa-edit bigger-120"></i>详细--}}
                            {{--</a>--}}
                            @if($type == 'projectChangeConfList')
                                <a title="详细和配置" class="btn btn-xs btn-danger"
                                   delWorkGoods  href="/manage/projectChangeConfDetail/{!!  $item->id !!}">
                                    <i class="ace-icon fa fa-trash-o bigger-120"></i>详细和配置
                                </a>
                            @else
                                <a title="详细和配置" class="btn btn-xs btn-danger"
                                   delWorkGoods  href="/manage/projectConfDetail/{!!  $item->id !!}">
                                    <i class="ace-icon fa fa-trash-o bigger-120"></i>详细和配置
                                </a>
                            @endif


                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</form>


{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
