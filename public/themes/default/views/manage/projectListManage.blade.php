<script>
    function open_url() {
        var city_id = $('#city_id').val();
        var project_type = $('#project_type').val();
        window.location='/manage/projectListManage/'+project_type+'/cityId/'+city_id;
    }

</script>
<h3 class="header smaller lighter blue mg-top12 mg-bottom20">工程配置单管理</h3>
<div class="well blue">
    <form action="/manage/ProjectListExcelUpload" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="file" name="excel_list" id="">    <br>
        <input type="submit" value="提交">
    </form>
    <br>
    <a href="/excel_attention/excel_example.xls">excel示例下载</a>----------------
    <a href="/excel_attention/excel_attention.xlsx">注意事项以及城市省份对应id下载</a>
</div>


<div class="well blue">
    <select onchange="open_url()" id="project_type">
        @if(!empty($project_name))
            @foreach($project_name as $v)
                <option value="{{$v['project_type']}}" {{ $v['project_type']==$id?'selected':'' }}>{{ $v['desc'] }}</option>
            @endforeach
        @endif
    </select>

    <select onchange="open_url()" id="city_id">
        @if(!empty($city_data))
            @foreach($city_data as $v)
                <option value="{{$v['city_id']}}" {{ $v['city_id']==$city_id?'selected':'' }}>{{ $v['name'] }}</option>
            @endforeach
        @endif
    </select>
</div>


<div>
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>

        <tr>
            <th class="left" >
                <label>

                    <span class="lbl"></span>编号
                </label>
            </th>
            <th width="6%">工程项目</th>
            <th>单位</th>
            <th width="6%">编号</th>
            <th>名称</th>
            <th>描述</th>
            <th>单价</th>
            <th width="6%">城市</th>
            <th width="6%">工人种类</th>

            <th width="12%" style="text-align: center">操作</th>
        </tr>
        </thead>
        <tbody>


    @foreach($new_data as $n=>$m)
        <tr>
            <td class="left">
                <label>

                    <span class="lbl">{{$m['id']}}</span>

                </label>
            </td>

            <td>
                {{get_project_type($m['project_type'])}}
            </td>

            <td>
                {{$m['unit']}}
            </td>
            <td>
                {{$m['cardnum']}}
            </td>
            <td>
                {{$m['name']}}
            </td>

            <td>
                {{$m['desc']}}
            </td>
            <td>
                {{$m['price']}}
            </td>
            <td>
                {{$m['city_name']}}
            </td>
            <td>
                {{get_work_type_name($m['work_type'])}}
            </td>

            <td>
                @if($m['pid']!=0)
                <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                    <a class="btn btn-xs btn-info" href="/manage/projectConfigureEdit/{{$m['id']}}">
                        <i class="fa fa-edit bigger-120"></i>编辑
                    </a>
                    <a href="/manage/projectConfigureDel/{{$m['id']}}" title="删除" class="btn btn-xs btn-danger">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                    </a>
{{--                    <a href="/manage/addMenu/8" title="添加" class="btn btn-xs btn-orange">
                        <i class="fa fa-edit bigger-120"></i>添加
                    </a>--}}
                </div>
                    @endif
            </td>
        </tr>
        @endforeach

        </tbody>
    </table>
</div>



<div class="row">
    <div class="col-xs-12">
        <div class="dataTables_info" id="sample-table-2_info">
            <a href="/manage/projectConfigureEdit" class="btn  btn-primary btn-sm">添加配置单</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $new_data->render() !!}
        </div>
    </div>
</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}