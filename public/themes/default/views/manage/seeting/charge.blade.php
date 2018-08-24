<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="{!! url('manage/charge') !!}" title="">收费单设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/project') !!}" title="">工程设置</a>
            </li>
        </ul>
    </div>
</div>
<div class="row pay-api">
    <div class="col-sm-4">
        <!-- PAGE CONTENT BEGINS -->
        <div class="well">
            设计师收费单
            <a href="/manage/addCharge/1" style="float: right;"><button class="btn btn-sm btn-primary">添加一级</button></a>
        </div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center">序</th>
                    <th class="text-center" width="46%">分类名称</th>
                    <th class="text-center">%</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($designer as $item)
                <tr>
                    <td class="text-center"><a href="javascript:;"><i class="fa fa-chevron-down"></i></a></td>
                    <td class="text-center">{!! $item['listorder'] !!}</td>
                    <td class="text-center">{!! $item['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                        <a title="浏览" class="btn btn-xs btn-success" href="/manage/addCharge/1/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-plus bigger-120"></i>添加二级
                        </a>
                    </td>
                </tr>
                    @if(isset($children[$item['id']]))
                        @foreach($children[$item['id']] as $value)
                <tr>
                    <td class="text-center"><a href="javascript:;"></td>
                    <td class="text-center">{!! $value['listorder'] !!}</td>
                    <td class="text-center">{!! $value['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $value['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $value['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                    </td>
                </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
    <div class="col-sm-4">
        <!-- PAGE CONTENT BEGINS -->
        <div class="well">
            管家收费单
            <a href="/manage/addCharge/2" style="float: right;"><button class="btn btn-sm btn-primary">添加一级</button></a>
        </div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center">序</th>
                    <th class="text-center" width="46%">分类名称</th>
                    <th class="text-center">%</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($housekeeper as $item)
                <tr>
                    <td class="text-center"><a href="javascript:;"><i class="fa fa-chevron-down"></i></a></td>
                    <td class="text-center">{!! $item['listorder'] !!}</td>
                    <td class="text-center">{!! $item['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                        <a title="浏览" class="btn btn-xs btn-success" href="/manage/addCharge/2/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-plus bigger-120"></i>添加二级
                        </a>
                    </td>
                </tr>
                    @if(isset($children[$item['id']]))
                        @foreach($children[$item['id']] as $value)
                <tr>
                    <td class="text-center"><a href="javascript:;"></td>
                    <td class="text-center">{!! $value['listorder'] !!}</td>
                    <td class="text-center">{!! $value['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $value['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                    </td>
                </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
    <div class="col-sm-4">
        <!-- PAGE CONTENT BEGINS -->
        <div class="well">
            监理收费单
            <a href="/manage/addCharge/3" style="float: right;"><button class="btn btn-sm btn-primary">添加一级</button></a>
        </div>
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center">序</th>
                    <th class="text-center" width="46%">分类名称</th>
                    <th class="text-center">%</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisor as $item)
                <tr>
                    <td class="text-center"><a href="javascript:;"><i class="fa fa-chevron-down"></i></a></td>
                    <td class="text-center">{!! $item['listorder'] !!}</td>
                    <td class="text-center">{!! $item['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                        <a title="浏览" class="btn btn-xs btn-success" href="/manage/addCharge/3/{!! $item['id'] !!}">
                            <i class="ace-icon fa fa-plus bigger-120"></i>添加二级
                        </a>
                    </td>
                </tr>
                    @if(isset($children[$item['id']]))
                        @foreach($children[$item['id']] as $value)
                <tr>
                    <td class="text-center"><a href="javascript:;"></td>
                    <td class="text-center">{!! $value['listorder'] !!}</td>
                    <td class="text-center">{!! $value['title'] !!}</td>
                    <td class="text-center">{!! $item['price'] !!}</td>
                    <td class="text-left">
                        <a class="btn btn-xs btn-info" href="/manage/editCharge/{!! $item['id'] !!}">
                            <i class="fa fa-edit bigger-120"></i>编辑
                        </a>
                        <a title="删除" class="btn btn-xs btn-danger" href="/manage/deleteCharge/{!! $value['id'] !!}">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                        </a>
                    </td>
                </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
</div><!-- /.row -->
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}