<meta name="_token" content="{!! csrf_token() !!}"/>
<h3 class="header smaller lighter blue mg-top12 mg-bottom20">商家信息管理</h3>
{{--<div class="well blue">
    --}}{{--<h4>菜单列表</h4>--}}{{--

    <select onchange="window.location=this.value;">
        @foreach($first_level_munus as $v)
            <option value="/manage/menuList/{{ $v['id'] }}/{{ $v['level'] }}" {{ ($id!=0 && $id==$v['id'])?'selected':'' }}>{{ $v['name'] }}</option>
        @endforeach
    </select>

</div>--}}
<div>
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>

        <tr>
            <th class="left" >
                <label>
                    <span class="lbl"></span>编号
                </label>
            </th>
            <th>logo</th>
            <th>品牌名称</th>
            <th>广告语</th>
            <th>推广图片</th>
            <th>联系人</th>
            <th>联系电话</th>
            <th>地址</th>
            <th >操作</th>
        </tr>
        </thead>
        <tbody>



        @if(!empty($MerchantDetail))
            @foreach($MerchantDetail as $k=>$v)
        <tr>
            <td class="left">
                <label>
                    <span class="lbl"></span>
                    {{$v['id']}}
                </label>
            </td>

            <td>
                <img src="{{$v['brand_logo']}}" style="width: 80px" alt="">
            </td>
            <td>
                {{$v['brand_name']}}
            </td>

            <td>
                {{$v['ad_slogan']}}
            </td>


            <td>
                <img src="{{$v['popular_img']}}" style="width: 80px" alt="">
            </td>

            <td>
                {{$v['name']}}
            </td>
            <td>
                {{$v['mobile']}}
            </td>

            <td>
                {{$v['address']}}
            </td>

            <td>
                <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                    <a class="btn btn-xs btn-info" href="/manage/businessInfoEdit/{{$v['id']}}">
                        <i class="fa fa-edit bigger-120"></i>编辑
                    </a>
                    <a   altid="{{$v['id']}}" onclick="delete_bus_info(this)"  title="删除" class="btn btn-xs btn-danger">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i>删除
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        @endif

        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="dataTables_info row" id="sample-table-2_info" role="status" aria-live="polite">
            <a href="/manage/businessInfoEdit"><button class="btn btn-sm btn-primary">添加商家</button></a>
        </div>
    </div>
    <div class="space-10 col-xs-12"></div>
    <div class="col-xs-12">
        <div class="dataTables_paginate paging_simple_numbers row" id="dynamic-table_paginate">
            {{--{!! $task->render() !!}--}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap row text-right">
            {!! $MerchantDetail->render() !!}
        </div>
    </div>
</div>
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}

<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
</script>

<script>
    function delete_bus_info(obj) {
        var id = $(obj).attr("altid");
        var res = confirm("您是否确认删除?")
        if (res==true)
        {
            $.ajax({
                type: "post",
                url: "/manage/businessInfoDelete",
                data: {
                    id: id,
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    location.reload()
                }
            });
        }

    }
</script>
