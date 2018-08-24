
<style>
    #a a:active {
        color: red;
        font-size: 18px;
    }
</style>
<h3 class="header smaller lighter blue mg-bottom20 mg-top12">主材列表</h3>

<div class="row">

    <div class="col-xs-12">
        <a class="btn  btn-info" href="{!! url('manage/goodsEdit') !!}">
            <i class="ace-icon fa fa-add bigger-120">添加材料</i>
        </a>

        <div>
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th>序号</th>
                    <th>分类</th>
                    <th>名称</th>
                    <th>品牌</th>
                    <th>型号</th>
                    <th>规格</th>
                    <th>图片</th>
                    <th>操作</th>
                </tr>
                </thead>
                <form>
                    <tbody>

                        @foreach($goods as $item)
                            <tr>

                                <td>{!! $item->id !!}</td>

                                <td>
                                    {!! $item->cate_name !!}
                                </td>
                                <td>
                                    {!! $item->name !!}
                                </td>
                                <td>
                                    {!! $item->brand_name !!}
                                </td>
                                <td>
                                    {!! $item->model_name !!}
                                </td>
                                <td>
                                    {!! $item->specifications !!}
                                </td>
                                <td>
                                    @if(!empty($item->img))
                                    <img src="{!! $item->img !!}" style="height:100px ">
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">


                                            <a class="btn btn-xs btn-info"
                                               href="{!! url('manage/goodsEdit?id='.$item->id) !!}">
                                                <i class="ace-icon fa fa-edit bigger-120">编辑</i>
                                            </a>

                                        {{--<a class="btn btn-xs btn-danger"--}}
                                           {{--href="{!! url('manage/realnameAuthList') !!}">--}}
                                            {{--<i class="ace-icon fa fa-ban  bigger-120">删除</i>--}}
                                        {{--</a>--}}

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </form>
            </table>

        </div>
    </div>
    <div class="col-xs-12">
        <div class="dataTables_paginate paging_simple_numbers row" id="dynamic-table_paginate">
            {{--{!! $task->render() !!}--}}
            {!! $goods->render() !!}
        </div>
    </div>
</div>


{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}



<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')}
    });
</script>


