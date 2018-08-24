
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="/manage/workGoodsList/{!! $worker_id !!}">返回作品列表</a>
            </li>
            <li class="active">
                <a href="#">版块列表</a>
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
<form action="/manage/section_edit_submit" method="post">
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
                {{--<th class="center">排序</th>--}}
                <th class="center">版块图片</th>
                <th class="center">位置名称</th>
                <th class="center">位置描述</th>
                <th class="center">生成时间</th>
                {{--<th class="center">修改时间</th>--}}
                {{--<th class="center">点击</th>--}}
                {{--<th class="center">状态</th>--}}
                <th class="center hide">操作</th>
            </tr>
            </thead>

            <tbody>
            @foreach($goods as $good )
                <tr>
                    {{--<td class="center">--}}
                    {{--<label>--}}
                    {{--<input type="checkbox" name="id_{!! $good->id !!}" class="ace" value="{!! $good->id !!}"/>--}}
                    {{--<span class="lbl"></span>--}}
                    {{--</label>--}}
                    {{--</td>--}}
                    {{--<td class="center">--}}
                        {{--{!! $good->id !!}--}}
                    {{--</td>--}}
                    <td class="center">
                        <img id="" width="120" height="120" src="{{url($good->url)}}"/>
                    </td>
                    <td class="center">
                        <input name="section_title[{!! $good->id !!}]" value="{!! trim($good->title) !!}" class="input-sm form-control" style="text-align: center;"/>
                        {{--{!! $good->title !!}--}}
                    </td>
                    <td class="center">
                        <textarea name="section_desc[{!! $good->id !!}]" class="form-control">
                            {!! trim($good->desc) !!}
                        </textarea>

                    </td>
                    <td class="center">

                        {!! $good->created_at !!}

                    </td>
                    {{--<td class="center">--}}

                        {{--{!! $good->updated_at !!}--}}

                    {{--</td>--}}
                    {{--<td class="center">--}}
                        {{--{!! $good->view_num !!}--}}
                    {{--</td>--}}
                    {{--<td class="center">--}}
                    {{--@if($good->is_open == '1')--}}
                    {{--上架--}}
                    {{--@elseif($good->is_open == '2')--}}
                    {{--下架--}}
                    {{--@endif--}}
                    {{--</td>--}}
                    <td class="center hide">
                        <div class="hidden-sm hidden-xs btn-group">
                            {{--<a  class="btn btn-xs btn-info" href="/manage/editWorkGoods/{!! $good->id !!}">--}}
                            {{--<i class="fa fa-edit bigger-120"></i>详细--}}
                            {{--</a>--}}
                            {{--<a title="删除" class="btn btn-xs btn-danger"--}}
                               {{--href="/manage/delWorkGoods/{!! $good->id !!}/{!! $workder_id !!}">--}}
                                {{--<i class="ace-icon fa fa-trash-o bigger-120"></i>删除--}}
                            {{--</a>--}}
                            {{--<a title="编辑版块" class="btn btn-xs btn-info"--}}
                               {{--href="/manage/delWorkGoods/{!! $good->id !!}/{!! $workder_id !!}">--}}
                                {{--<i class="ace-icon fa fa-trash-o bigger-120"></i>编辑版块信息--}}
                            {{--</a>--}}
                            <!--  @if($good->is_open == '1')
                                    <a title="下架" class="btn btn-xs btn-warning" href="/advertisement/changeState/{!! $good->id !!}">
                                    下架
                                </a>
                            @elseif($good->is_open == '2')
                                    <a title="上架" class="btn btn-xs btn-inverse" href="/advertisement/changeState/{!! $good->id !!}">
                                    上架
                                </a>
                            @endif -->
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="dataTables_info row" id="sample-table-2_info" role="status" aria-live="polite">
                    <input name="worker_id" value="{!! $worker_id !!}" class="hide">
                    <input name="goods_id" value="{!! $goods_id !!}" class="hide">

                    <button class="btn btn-sm btn-primary" type="submit">提交修改</button>
            </div>
        </div>
        <div class="space-10 col-xs-12"></div>
    </div>
</form>


{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
