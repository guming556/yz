<a  href="/manage/auxEdit" role="button" class="btn btn-primary" data-toggle="modal">添加辅材包</a>
<form role="form" action="/manage/editAuxiliary" method="post" enctype="multipart/form-data" >
    {{csrf_field()}}
    <div class="modal fade" id="modal-container-281004" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        请填写要添加的辅材包信息
                    </h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label for="name">辅材包名称</label>
                            <input type="text" class="form-control" id="name" name="name"/>
                        </div>
                        <div class="form-group">
                            <label for="price">辅材包单价（元/m<sup>2</sup>）</label>
                            <input type="text" class="form-control" id="price" placeholder="请填写数字" name="price" />
                        </div>
                    <div class="form-group">
                        <label for="exampleInputFile">辅材包文件</label>
                        <input type="file" id="xls" name="xls" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>
        </div>
    </div>
</form>



<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#">辅材包列表</a>
            </li>
        </ul>
    </div>
</div>


<form method="post">
    {{csrf_field()}}

    <div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>

                <th class="center">编号</th>
                <th class="center">辅材包名称</th>
                <th class="center">单价</th>
                <th class="center">城市</th>

                <th class="center">操作</th>
            </tr>
            </thead>

            <tbody>
            @foreach($list as $item )
                <tr>

                    <td class="center">
                        {!! $item->id !!}
                    </td>

                    <td class="center">
                        {!! $item->name !!}
                    </td>

                    <td class="center">
                        {!! $item->price !!} 元 / m<sup>2</sup>
                    </td>
                    <td class="center">
                        {!! $item->city_name !!}
                    </td>

                    <td class="center">
                        <div class="hidden-sm hidden-xs btn-group">
                            {{--<a class="btn btn-xs btn-info" href="/advertisement/update/{!! $item->id !!}">--}}
                                {{--<i class="fa fa-edit bigger-120"></i>编辑--}}
                            {{--</a>--}}
                            @if(is_null($item->deleted_at))
                            <a title="删除" class="btn btn-xs btn-danger" onclick="if(!confirm('是否确定要删除这个辅材包')){return false;}" href="/manage/auxDelete/{!! $item->id !!}">
                                <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                            </a>
                            @else
                                <a title="恢复" class="btn btn-xs btn-success" onclick="if(!confirm('是否确定要恢复这个辅材包')){return false;}" href="/manage/auxRestore/{!! $item->id !!}">
                                    <i class="ace-icon fa fa-trash-o bigger-120"></i>恢复
                                </a>
                            @endif

                            <a role="button" class="btn btn-xs btn-primary" href="/manage/auxEdit/{{$item->id}}" >
                                <i class="fa fa-edit bigger-120"></i>更改
                            </a>

{{--                            <a   class="btn btn-xs btn-info" href="{!! $item->detail_url !!}" >
                                <i class="fa fa-edit bigger-120"></i>查看文件
                            </a>   --}}
                            <a   class="btn btn-xs btn-info" href="{{url('api/auxDetail/')}}/{!! $item->id !!} " >
                                <i class="fa fa-edit bigger-120"></i>详细
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $list->render() !!}
        </div>
    </div>
</div>

{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
<script>
    function edit(obj){
//        alert(id)
//        document.getElementById('edit-id').value = id;
        $("#edit-price").attr("value", $(obj).attr("altprice"));
        $("#edit-name").attr("value", $(obj).attr("altname"));
        $("#edit-id").attr("value", $(obj).attr("altid"));
        $("#modal-container-edit").modal('show');
    }
</script>