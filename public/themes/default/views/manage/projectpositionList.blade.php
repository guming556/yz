<style>
    .button{
        height: 100px;
        height: 50px;
    }
    .sousuo{
        background: #CCC;
        display: block;
        width: 100px;
        height: 35px;
        border: 1px solid #CCC;
        text-align: center;
        float: left;
        margin-left: 60px;
        font-size: 18px;
    }
    .add_a
    {
        display: block;
        width: 100px;
        height: 30px;
        border: 1px solid #CCC;
        background: white;
        text-align: center;
        margin-left: 40px;
    }
    .add_a a{

        text-decoration: none;
        font-size: 14px;
        padding: 2px 6px;
        display: block;
        color: black;
    }
    .select{
        float:left;
        margin-left: 20px;
    }
    .select select{
        margin-left: 20px;
        width: 120px;
    }
    .input-daterange
    {
        font-size: 17px;
    }
    .sou_select
    {
        float: right;
        margin-left: 20px;
    }
    .second
    {
        margin-left: 40px;
    }
</style>

<div class="row">
    <form action="/manage/projectpositionList" method="get">
        <div class="quan">
            <div class="well">
                工地统计
            </div>
            <div class="body">
                <div class="button">
                    <div class="select">
                        <select name="province" id="province" onchange="getZone(this.value, 'city');">
                            <option value="">全国</option>
                            @foreach($province as $item)
                                <option value="{!! $item['id'] !!}">{!! $item['name'] !!}</option>
                            @endforeach
                        </select>
                        <select name="city" id="city" onchange="getZone(this.value, 'area');">
                                <option value="">全部城市</option>
                                <option value=""></option>
                        </select>
                        <select name="area" id="area">
                                <option value="">全部区域</option>
                                <option value=""></option>
                        </select>
                        <select>
                            <option>进展</option>
                            <option>开工交底</option>
                        </select>
                        <div class="sou_select">
                            <input type="submit" id="btn" class="sousuo" value="搜索">
                            <input type="text" placeholder="请输入用户名" id="name" name="name" @if(isset($merge['name']))value="{!! $merge['name'] !!}"@endif>
                        </div>
                    </div>
                </div>
                <div class="add_a">
                    <a href="#" onclick="projectpositionExport()">导出表格</a>
                </div>
                <div class="second">
                    <div class="form-group search-list">
                        <label>开播时间：</label>
                        <div class="input-daterange input-group">
                            <input type="text" name="start" class="input-sm form-control" value="">
                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                            <input type="text" name="end" class="input-sm form-control" value="">
                        </div>

                    </div>
                </div>
                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th class="center">工地编号</th>
                        <th class="center">直播标题</th>
                        <th class="center">昵称</th>
                        <th class="center">手机</th>
                        <th class="center">地区</th>
                        <th class="center">管家</th>
                        <th class="center">进展</th>
                        <th class="center">进入直播时间</th>
                        <th class="center">最后进展时间</th>
                        <th class="center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ppList as $item)
                    <tr class="center">
                        <td>{!! $item['id'] !!}</td>
                        <td></td>
                        <td>{!! $item['name'] !!}</td>
                        <td>{!! $item['mobile'] !!}</td>
                        <td>{!! $item['region'] !!}</td>
                        <td>{!! $item['username'] !!}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <a title="查看" class="btn btn-xs btn-success" href="/manage/projectpositionDetail/{!! $item['id'] !!}">
                                <i class="ace-icon fa fa-search bigger-120"></i>查看
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space col-xs-12"></div>
        <div class="col-md-12">
            <div class="dataTables_paginate paging_bootstrap text-right row">
                <!-- 分页列表 -->
                {!! $ppList->appends($paginate)->render() !!}
            </div>
        </div>
    </form>
</div>


{{--时间插件--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}