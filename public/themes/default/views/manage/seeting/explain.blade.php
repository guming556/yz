<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="{!! url('manage/explain') !!}" title="">说明设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/addExplain') !!}" title="">添加说明</a>
            </li>
        </ul>
    </div>
</div>
<div class="">
    <div class="well">
        说明设置
        <a href="/manage/addExplain" style="float: right;"><button class="btn btn-sm btn-primary">添加说明</button></a>
    </div>
    <form action="/manage/getProject" method="post">
        <div>
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th class="center">名字</th>
                    <th class="center">简介</th>
                    <th class="center">最后一次编辑时间</th>
                    <th class="center">最后一次编辑人</th>
                    <th class="center">操作</th>
                </tr>
                </thead>

                <tbody>
                    @foreach($exList['data'] as $item )
                    <tr class="center">
                        <td>{!! $item['title'] !!}</td>
                        <td>{!! $item['profile'] !!}</td>
                        <td>{!! $item['updated_at'] !!}</td>
                        <td>{!! $item['editor'] !!}</td>
                        <td>
                            <a title="浏览" class="btn btn-xs btn-success" href="/manage/explainDetail/{!! $item['id'] !!}">
                                <i class="ace-icon fa fa-search bigger-120"></i>浏览
                            </a>
                            <a class="btn btn-xs btn-info" href="/manage/editExplain/{!! $item['id'] !!}">
                                <i class="fa fa-edit bigger-120"></i>编辑
                            </a>
                            <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteExplain/{!! $item['id'] !!}">
                                <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="space col-xs-12"></div>
        <div class="col-xs-12">
            <div class="dataTables_paginate paging_bootstrap text-right">
                <ul class="pagination">
                    @if(!empty($exList['prev_page_url']))
                        <li><a href="{!! URL('manage/explain').'?'.http_build_query(array_merge($merge,['page'=>$exList['current_page']-1])) !!}">上一页</a></li>
                    @endif
                    @if($exList['last_page']>1)
                        @for($i=1;$i<=$exList['last_page'];$i++)
                            <li class="{{ ($i==$exList['current_page'])?'active disabled':'' }}"><a href="{!! URL('manage/explain').'?'.http_build_query(array_merge($merge,['page'=>$i])) !!}">{{ $i }}</a></li>
                        @endfor
                    @endif
                    @if(!empty($exList['next_page_url']))
                        <li><a href="{!! URL('manage/explain').'?'.http_build_query(array_merge($merge,['page'=>$exList['current_page']+1])) !!}">下一页</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
