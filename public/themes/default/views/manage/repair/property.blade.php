
    <div class="row">
        <div class="col-xs-12">
            {{--<div class="space"></div>--}}
            <h3 class="header smaller lighter blue mg-bottom20 mg-top12">物管列表</h3>
            {{--<div class="clearfix  table-responsive">--}}
                {{--<div class="form-inline well">--}}
                {{--<form  role="form" action="/manage/bankAuthList" method="get">--}}
                	{{--<div class="form-group search-list width285">--}}
                        {{--<label for="namee" class="">管家姓名　</label>--}}
                        {{--<input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名" @if(isset($merge['username']))value="{!! $merge['username'] !!}"@endif>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                    	 {{--<button type="submit" class="btn btn-primary btn-sm">搜索</button>--}}
                    {{--</div>--}}
                    {{--<div class="space"></div>--}}
                    {{--<div class="form-inline search-group " >--}}
                        {{--<div class="form-group search-list">--}}
                            {{--<select name="time_type">--}}
                                {{--<option value="created_at" @if(isset($merge['time_type']) && $merge['time_type'] == 'created_at')selected="selected"@endif>申请时间</option>--}}
                                {{--<option value="auth_time" @if(isset($merge['time_type']) && $merge['time_type'] == 'auth_time')selected="selected"@endif>认证时间</option>--}}
                            {{--</select>--}}
                            {{--<div class="input-daterange input-group">--}}
                                {{--<input type="text" name="start" class="input-sm form-control" @if(isset($merge['start']))value="{!! $merge['start'] !!}" @endif>--}}
                                {{--<span class="input-group-addon"><i class="fa fa-exchange"></i></span>--}}
                                {{--<input type="text" name="end" class="input-sm form-control" @if(isset($merge['end']))value="{!! $merge['end'] !!}" @endif>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group search-list">--}}
                            {{--<label for="namee" class="">状态　　</label>--}}
                            {{--<select name="status">--}}
                                {{--<option value="">全部</option>--}}
                                {{--<option value="1" @if(isset($merge['status']) && $merge['status'] == 1)selected="selected"@endif>待审核</option>--}}
                                {{--<option value="2" @if(isset($merge['status']) && $merge['status'] == 2)selected="selected"@endif>已打款待验证</option>--}}
                                {{--<option value="3" @if(isset($merge['status']) && $merge['status'] == 3)selected="selected"@endif>认证成功</option>--}}
                                {{--<option value="4" @if(isset($merge['status']) && $merge['status'] == 4)selected="selected"@endif>认证失败</option>--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}

            <!-- <div class="table-responsive"> -->

            <!-- <div class="dataTables_borderWrap"> -->
            <div class="table-responsive">
                <table id="sample-table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th>编号</th>
                        <th>物管姓名</th>
                        <th>注册手机</th>
                        <th>
                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                            注册时间
                        </th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($property->toArray()['data']))
                    @foreach($property->toArray()['data'] as $item)
                        <tr>
                            <td>
                                <a href="#">{!! $item->id !!}</a>
                            </td>
                            <td>{!! $item->name !!}</td>
                            <td>{!! $item->tel !!}</td>

                            <td >{!! $item->created_at !!}</td>

                            <td>
                                {{--<div class=" btn-group">--}}

                                        {{--<a title="已打款" href="{!! url('manage/bankAuth/' . $item->id ) !!}" class="btn btn-xs btn-success">--}}
                                            {{--<i class="ace-icon fa fa-check bigger-120"></i>修改--}}
                                        {{--</a>--}}

                                        {{--<a title="拒绝通过" href="/manage/bankAuthHandle/{!! $item->id !!}/deny" class="btn btn-xs btn-danger">--}}
                                            {{--<i class="ace-icon fa fa-ban bigger-120"></i>详细--}}
                                        {{--</a>--}}
                                {{--</div>--}}

                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="dataTables_info" id="sample-table-2_info" role="status" aria-live="polite">

                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="dataTables_paginate paging_bootstrap text-right row">
                        <ul class="pagination">
                            {!! $property->appends($merge)->render() !!}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.row -->


{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{{--时间插件--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}