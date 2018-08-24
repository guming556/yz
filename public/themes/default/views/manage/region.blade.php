{{--雇佣列表--}}
<h3 class="header smaller lighter blue mg-bottom20 mg-top12">区域列表</h3>
<div class=" well">
    <form role="form" class="form-inline search-group">
        <div class="form-group search-list ">
            <label for="">城市名称　　</label>
            <input type="text" name="name" value="{!! $name !!}">
        </div>
        <div class="form-group search-list">
            <button class="btn btn-primary btn-sm">搜索</button>
        </div>
    </form>
</div>
<!-- <div class="dataTables_borderWrap"> -->
<div>
    <form action="" method="post">
        <input type="hidden" name="_token" value="Q8olGWxsp4BTmFfh3mYWlOYNutLUU16oT7LG1xK6">
        <table id="sample-table" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                {{--<th class="center">--}}
                    {{--<label class="position-relative">--}}

                        {{--<input type="checkbox" class="ace">--}}
                        {{--<span class="lbl"></span>--}}

                    {{--</label>--}}
                {{--</th>--}}
                <th>编号</th>
                <th>区域</th>
                <th>当前状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $v)
                <tr>
                <td>
                    <a href="#">{{ $v['id'] }}</a>
                </td>
                <td>{{ $v['name'] }}</td>
                    <td>
                        @if($v['work_select'] == 1)
                            展示
                        @else
                            屏蔽
                        @endif
                    </td>
                <td>
                    <div class="btn-group">
                        @if($v->work_select == 0)
                            <a title="展示" class="btn btn-xs btn-warning" href="changeCurrentStatus/{!! $v->id !!}/{!! $v->work_select !!}">
                                展示
                            </a>
                        @elseif($v->work_select == 1)
                            <a title="屏蔽" class="btn btn-xs btn-inverse" href="changeCurrentStatus/{!! $v->id !!}/{!! $v->work_select !!}">
                                屏蔽
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12">
                <div class="dataTables_paginate paging_bootstrap text-right row">
                    <ul class="">
                        {!! $result->render() !!}

                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
