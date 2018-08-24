<style>
    .button{
        width: 100%;
        height: 100px;
    }
    .all_a a,.del_a a, .dao_a a, .disabled_a a,.enabled_a a, .add_a a, .sousuo a
    {
        text-decoration: none;
        font-size: 14px;
        padding: 2px 6px;
        display: block;
        color: black;
    }
    .all_a,.del_a, .disabled_a,.enabled_a
    {
        display: block;
        width: 100px;
        height: 30px;
        border: 1px solid #CCC;
        background: white;
        text-align: center;
        float: left;
        margin-left: 20px;
    }
    .sousuo{
        background: #7B3F25;
        display: block;
        width: 100px;
        height: 35px;
        border: 1px solid #CCC;
        text-align: center;
        float: left;
        margin-left: 60px;
        font-size: 18px;
    }
</style>

<div class="row">
    <form action="/manage/materiaList" method="get">
        <div class="quan">
            <div class="well">
                辅材包套餐管理
            </div>
            <div class="body">
                <div class="button">
                    <div class="all_a">
                        <a href="#">全选</a>
                    </div>
                    <div class="disabled_a">
                        <a href="#">删除</a>
                    </div>
                    <div class="enabled_a">
                        <a href="#">上架</a>
                    </div>
                    <div class="del_a">
                        <a href="#">下架</a>
                    </div>
                    <div>
                        <input type="submit" id="btn" class="sousuo" value="搜索">
                    </div>
                    <div>
                        <input type="text" placeholder="请输入辅材套餐名称" id="name" name="name" @if(isset($merge['name']))value="{!! $merge['name'] !!}"@endif>
                    </div>

                </div>
                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th class="center">选择</th>
                        <th class="center">排序</th>
                        <th class="center">辅材包名称</th>
                        <th class="center">描述</th>
                        <th class="center">价格/元</th>
                        <th class="center">已售/份</th>
                        <th class="center">状态</th>
                        <th class="center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mList['data'] as $item)
                        <tr class="center">
                            <td><input type="checkbox" value="{!! $item['id'] !!}"></td>
                            <td>{!! $item['id'] !!}</td>
                            <td>{!! $item['name'] !!}</td>
                            <td>{!! $item['content'] !!}</td>
                            <td>{!! $item['price'] !!}</td>
                            <td>{!! $item['sell_num'] !!}</td>
                            <td>
                                @if($item['status']=="0")
                                    <span>下架</span>
                                @elseif($item['status']=="1")
                                    <span>上架</span>
                                @endif
                            </td>
                            <td>
                                <a title="编辑" class="btn btn-xs btn-success" href="/manage/editmaterials/{!! $item['id'] !!}">
                                    <i class="ace-icon fa fa-search bigger-120"></i>编辑
                                </a>
                                <a title="删除" class="btn btn-xs btn-success" href="#" onclick="onDel({!! $item['id'] !!});">
                                    <i class="ace-icon fa fa-search bigger-120"></i>删除
                                </a>
                                <a title="下架" class="btn btn-xs btn-success" href="#" onclick="onDisabled({!! $item['id'] !!});">
                                    <i class="ace-icon fa fa-search bigger-120"></i>下架
                                </a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space col-xs-12"></div>
    </form>
</div>