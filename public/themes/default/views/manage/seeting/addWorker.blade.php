
   {{-- <div class="space-2"></div>
    <div class="page-header">
        <h1>
            工种配置
        </h1>
    </div> <!--  /.page-header -->--}}
    <h3 class="header smaller lighter blue mg-bottom20 mg-top12">工种配置</h3>
   <div>
       工种默认星级收费（配置单总价*星级百分比）设置:
       <li>1星：100%</li>
       <li>2星：110%</li>
       <li>3星：120%</li>
       <li>4星：130%</li>
       <li>5星：140%</li>
   </div><br>
    <form action="{{ URL('manage/saveWorker') }}" method="post" id="form-data">
        {{ csrf_field() }}
        <input type="hidden" name="change_ids" id="area-change" value="" />
        <div class="row">

            <div class="col-xs-12">
                <div>
                    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="area_data_change">
                        @foreach($worker as $k => $v)
                            <tr id="area-delete-{{ $k }}" area_id = "{{ $k }}">
                                <td class="text-left">
                                    <input type="text" name="name[{{ $k }}]" value="{{ $v }}" area_id="{{ $k }}"/>
                                </td>
                                <td width="40%">
                                    <span class="btn  btn-xs btn-danger" area_id="{{ $k }}"  onclick="area_delete($(this))" ><i class="ace-icon fa fa-trash-o bigger-120"></i>删除</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <span  class="btn btn-sm btn-info" onclick="area_create($(this))">添加</span>
                <button  class="btn btn-sm btn-info" >提交</button>
            </div>
        </div>
    </form>
<!-- /.page-content-area -->
   {!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}
<script>
    /**
     * 删除地区数据
     * @param obj
     */
    function area_delete(obj)
    {
        var id = obj.attr('area_id');
        var url = '/manage/deleteWorker/'+id;
        $.get(url,function(data){
            if(data.errCode==0)
            {
                alert('删除失败');
            }else if(data.errCode==1)
            {
                $('#area-delete-'+data.id).remove();
            }
        });
        $('#area-delete-'+id).remove();
    }
    /**
     * 地区的添加修改
     * @param obj
     */
    function area_create(obj)
    {
        var id = Number($('#area_data_change').children('tr:last').attr('area_id'))+1;
        if(isNaN(id))
        {
            id= 5;
        }

        //添加一个地区的input框
        var html = "<tr area_id=\""+id+"\" id=\"area-delate-"+id+"\"><td class=\"text-left\"><input type=\"text\" name=\"name["+id+"]\" value=\"\" area_id=\""+id+"\"><\/td><td width=\"40%\"><\/td><\/tr>" ;
        $('#area_data_change').append(html);
    }
</script>