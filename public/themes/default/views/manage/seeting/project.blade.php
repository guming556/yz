<style>
    .small-table {
        width: 100%;
    }
    .small-table tr {
        border-bottom: 1px solid #ddd;
    }
    .small-table tr:last-child { 
        border: 0;
    }
</style>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/charge') !!}" title="">收费单设置</a>
            </li>
            <li class="active">
                <a href="{!! url('manage/project') !!}" title="">工程设置</a>
            </li>
        </ul>
    </div>
</div>
<form action="/manage/getProject" method="post">
    {{csrf_field()}}
    <div class="well">
        <div class="dataTables_info" id="sample-table-2_info">
            <label><input type="checkbox" class="ace" id="allcheck">
                <span class="lbl"></span>全选
            </label>
            <button id="all_delete" type="submit" class="btn btn-sm btn-primary ">删除</button>
            <a class="btn btn-white" href="/manage/addProject" style="float: right;">添加</a>
        </div>
    </div>
    <div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>

                <th class="center">选择</th>
                <th class="center">排序</th>
                <th class="center">工程名字</th>
                <th class="center">二级工程</th>
                <th class="center">默认完工时间</th>
                <th class="center">描述</th>
                <th class="center">操作</th>
            </tr>
            </thead>

            <tbody>
                @foreach($pjList['data'] as $item )
                <tr>
                    <td class="center">
                        <label>
                            <input type="checkbox" name="id_{!! $item['id'] !!}" class="ace" value="{!! $item['id'] !!}"/>
                            <span class="lbl"></span>
                        </label>
                    </td>
                    <td class="center">
                        {!! $item['listorder'] !!}
                    </td>
                    <td class="center">
                        {!! $item['title'] !!}
                    </td>
                    
                        @if(isset($pjChild[$item['id']]))
                        <td class="center" style="padding: 0;">
                            <table class="small-table">
                            @foreach($pjChild[$item['id']] as $value)
                                <tr>
                                    <td>{!! $value['title'] !!}</td>
                                </tr>
                            @endforeach
                            </table>
                            </td>
                        @else
                        <td class="center">-</td>
                        @endif
                    
                        @if(isset($pjChild[$item['id']]))
                        <td class="center" style="padding: 0;">
                            <table class="small-table">
                            @foreach($pjChild[$item['id']] as $value)
                                <tr>
                                    <td>{!! $value['complete'] !!}</td>
                                </tr>
                            @endforeach
                            </table>
                            </td>
                        @else
                        <td class="center">{!! $item['complete'] !!}</td>
                        @endif
                    <td class="center">
                        {!! $item['content'] !!}
                    </td>
                    <td class="center">
                        <a class="btn btn-xs btn-info" href="/manage/editProject/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteProject/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                    </td>
                </tr> 
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="space col-xs-12"></div>
    <!-- 分页 -->
    <div class="col-xs-12">
        <div class="dataTables_paginate paging_bootstrap text-right">
            <ul class="pagination">
                @if(!empty($pjList['prev_page_url']))
                    <li><a href="{!! URL('manage/project').'?'.http_build_query(array_merge($merge,['page'=>$pjList['current_page']-1])) !!}">上一页</a></li>
                @endif
                @if($pjList['last_page']>1)
                    @for($i=1;$i<=$pjList['last_page'];$i++)
                        <li class="{{ ($i==$pjList['current_page'])?'active disabled':'' }}"><a href="{!! URL('manage/project').'?'.http_build_query(array_merge($merge,['page'=>$i])) !!}">{{ $i }}</a></li>
                    @endfor
                @endif
                @if(!empty($pjList['next_page_url']))
                    <li><a href="{!! URL('manage/project').'?'.http_build_query(array_merge($merge,['page'=>$pjList['current_page']+1])) !!}">下一页</a></li>
                @endif
            </ul>
        </div>
    </div>
</form>
{{--<div class="row">
    <div class="col-sm-6">
        <div class="dataTables_info" id="sample-table-2_info">
            <a href="/advertisement/insert"><button class="btn btn-sm btn-primary">添加广告</button></a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_bootstrap text-right">
            {!! $adList->appends($search)->render() !!}
        </div>
    </div>
</div>--}}
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
