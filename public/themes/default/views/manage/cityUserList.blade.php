{{-- <div class="page-header">
     <h3>
           搜索
     </h3>
 </div><!-- /.page-header -->--}}
<style>
    #a a:active {
        color: red;
        font-size: 18px;
    }
</style>
<div class="modal fade" id="modal-container-684890" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">
                    邀请链接
                </h4>
            </div>
            <div class="modal-body">
                内容...
            </div>
            <div class="modal-footer" style="padding-top: 0;
    padding-bottom: 0;">
                <button type="button" class="btn btn-small" data-dismiss="modal" style="padding: 6px 4px">关闭</button>
            </div>
        </div>

    </div>

</div>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs" id="a">

            @if(!empty($user_type_list))
                @foreach($user_type_list as $index => $_type)
                    @if($index != 1)
                    <li @if($user_type == $index) class="active" @endif>
                        <a href="{!! url('manage/cityStationUser?user_type='.$index) !!}" title="">[ {!! $_type !!} ]</a>
                    </li>
                    @endif
                @endforeach
            @endif

        </ul>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="clearfix  well">
            <form role="form" class="form-inline search-group" action="{!! url('manage/cityStationUser') !!}" method="get">
                <input type="hidden" name="user_type" @if(isset($user_type))value="{!! $user_type !!}"@endif/>
                <div class="">
                <!--
                <div class="form-group search-list">
                    <label for="">真实姓名</label>
                    <input type="text" name="username" @if(isset($username)) value="{!! $username !!}" @endif/>
                </div>
                -->
                <!--  <div class="form-group search-list">
                    <label for="">注册邮箱　</label>
                    <input type="text" name="email" @if(isset($email))value="{!! $email !!}"@endif/>
                </div> -->
                    <div class="form-group search-list">
                        <label for="">登陆帐号</label>
                        <input type="text" name="mobile" @if(isset($mobile))value="{!! $mobile !!}" @endif/>
                    </div>

                    <div class="form-group search-list">
                        <label class="">注册时间</label>
                        <div class="input-daterange input-group">
                            <input type="text" name="start" class="input-sm form-control"
                                   @if(isset($search['start']))value="{!! $search['start'] !!}" @endif>
                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                            <input type="text" name="end" class="input-sm form-control"
                                   @if(isset($search['end']))value="{!! $search['end'] !!}" @endif>
                        </div>
                    </div>
                    <div class="form-group search-list">
                        <label>状态</label>
                        <select class="" name="status">
                            <option value="-1">全部</option>
                            <option @if(isset($status) && $status == 0)selected="selected" @endif value="0">未激活</option>
                            <option @if(isset($status) && $status == 1)selected="selected" @endif value="1">已激活</option>
                            <option @if(isset($status) && $status == 2)selected="selected" @endif value="2">已禁用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm">搜索</button>　　
                        <a href=" @if (isset($export_url) ){{$export_url}} @else /manage/userListExport/ @endif" style="cursor: pointer"> 导出Excel</a>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<a href="/manage/userAdd" class="btn btn-primary btn-sm"--}}
                           {{--style="background-color: #ff9400!important;border-color: #ff9400;">添加业主</a>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <a href="/manage/designerAdd" class="btn btn-primary btn-sm"
                           style="background-color: #ff9400!important;border-color: #ff9400;">添加设计师</a>
                    </div>
                    <div class="form-group">
                        <a href="/manage/housekeeperAdd" class="btn btn-primary btn-sm"
                           style="background-color: #ff9400!important;border-color: #ff9400;">添加管家</a>
                    </div>
                    <div class="form-group">
                        <a href="/manage/supervisorAdd" class="btn btn-primary btn-sm"
                           style="background-color: #ff9400!important;border-color: #ff9400;">添加监理</a>
                    </div>
                    <div class="form-group">
                        <a href="/manage/laborAdd" class="btn btn-primary btn-sm"
                           style="background-color: #ff9400!important;border-color: #ff9400;">添加工人</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- <div class="table-responsive"> -->

        <!-- <div class="dataTables_borderWrap"> -->
        <div>
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="left" width="8%">
                        <label class="position-relative">
                            <input type="checkbox" class="ace"/>
                            <span class="lbl"></span>UID
                        </label>
                    </th>
                    @if($user_type == 5)
                        <th>工人编码</th>
                    @endif
                    <th>登陆帐号</th>
                    <th>用户类别</th>
                    <th>我的推荐人</th>
                    <th>真实姓名</th>
                    <th>昵称</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th>登录设备</th>
                    <th>最后登陆时间</th>

                    <th>余额</th>
                    @if($user_type==1)
                    @else
                        <th>排序(数字越小,排名越高)</th>
                    @endif
                    <th width="12%">操作</th>
                </tr>
                </thead>
                <form>
                    <tbody>
                    @if(!empty($list))
                        @foreach($list as $item)
                            <tr>
                                <td class="left">
                                    <label class="position-relative">
                                        <input type="checkbox" class="ace" name="ckb[]" value="{!! $item->id !!}"/>
                                        <span class="lbl"></span>{!! $item->id !!}
                                    </label>
                                </td>

                                @if($user_type == 5)
                                    <td>
                                    <a href="#">{!! $item->worker_number !!}</a>
                                    </td>
                                @endif
                                <td><a href="#">{!! $item->name !!}</a></td>
                                <td>
                                    <a href="#">{!! isset($user_type_list[$item->user_type]) ? $user_type_list[$item->user_type] : "未分类" !!}
                                        @if($item->user_type==5)
                                            @foreach($workTypeArr as $key => $value)
                                                @if($key == $item->work_type)
                                                    ({!! $value !!})
                                                @endif
                                            @endforeach
                                        @endif
                                        </a>
                                </td>
                                <td>

                                    <a href="#">{!! $item->invite_uid_mobile !!}</a>

                                </td>
                                <td><a href="#">{!! $item->realname !!}</a></td>
                                <td><a href="#">{!! $item->nickname !!}</a></td>
                                <td>@if($item->status == 0)未激活@elseif($item->status == 1)已激活@elseif($item->status == 2)
                                        已禁用@endif</td>
                                <td>{!! $item->created_at !!}</td>
                                <td>{!! $item->device_token_type !!}</td>
                                <td>{!! $item->last_login_time !!}</td>
                                <td>{!! $item->balance !!}</td>
                                @if($item->user_type!=1)
                                    <td>
                                        <input type="text" onchange="changeSortId(this)" name="sort_id"
                                               value="{{$item->sort_id}}"
                                               altsortid="" altuid="{{$item->id}}">
                                    </td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                      
                                    


                                        @if($item->status == 1)
                                            <a class="btn btn-xs btn-danger"
                                               href="{!! url('manage/handleUser/' . $item->id . '/disable') !!}">
                                                <i class="fa fa-ban"></i>禁用
                                            </a>
                              @endif
                                      
                                    


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </form>
            </table>
            <div class="row">
                <div class="col-md-2">
                    <button id="all_disable" class="btn btn-primary btn-default btn-round">禁用
                    </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="all_able" class="btn btn-primary btn-default btn-round">启用
                    </button>
                </div>


                {{--<a href="/manage/userAdd" target="_blank">添加</a>--}}
                <div class="col-md-12">
                    <div class="dataTables_paginate paging_bootstrap text-right row">
                        <!-- 分页列表 -->
                        {!! $list->appends($search)->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}

{{--时间插件--}}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('checked-js', 'js/checkedAll.js') !!}

<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')}
    });
</script>
<script type="text/javascript">
    function changeSortId(obj) {
        var sort_id = $(obj).val();
        var uid = $(obj).attr("altuid");

        $.ajax({
            type: "post",
            url: "/manage/userSortByAdmin",
            data: {
                sort_id: sort_id,
                uid: uid,
            },
            dataType: 'json',
            success: function (data) {

                $(obj).val(data.sort_id)
            }
        });
    }
</script>


{{--批量操作js--}}
<script type="text/javascript">
    $('#all_disable').on('click', function () {
        var idArray = [];
        $('.ace').each(function () {
            console.log(this)
            if ($(this).is(':checked')) {
                var id = $(this).val();
                idArray.push(id);
            }
        });
        var ids = idArray;
        console.log(ids);
//         $.ajax({
//             type: 'post',
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             url: '/manage/handleUser/id/disable',
//             data: {
//                 ids:ids,
//             },
//             dataType:'json',
//             success: function(data){
//                 if(data.errCode == 1){
//                     $('[type="checkbox"]').prop('checked','');
//                     location.reload();
//                 }
//             }
//         });
        //批量禁用
        var url = '/manage/handleUser/id/disable';
        $.get(url, {ids: ids}, function (data) {
            if (data.errCode == 1) {
                location.reload();
            }
        });
    });

    $('#all_able').on('click', function () {
        var idArray = [];
        $('.ace').each(function () {
            if ($(this).is(':checked')) {
                var id = $(this).val();
                idArray.push(id);
            }
        });
        var ids = idArray;
        console.log(ids);
//         $.ajax({
//             type: 'post',
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             url: '/manage/allEnterprisePass',
//             data: {ids:ids},
//             dataType:'json',
//             success: function(data){
//                 if(data.code == 1){
//                     $('[type="checkbox"]').prop('checked','');
//                     location.reload();
//                 }
//             }
//         });
        //批量启用
        var url = '/manage/handleUser/id/enable';
        $.get(url, {ids: ids}, function (data) {
            if (data.errCode == 1) {
                location.reload();
            }
        });
    });



</script>

<script>
    function generate_invit_url(obj) {
        var uid = $(obj).attr("altuid");

        $.ajax({
            type: "post",
            url: "/api/GenInvitationCode",
            dataType: 'json',
            data: {
                uid: uid,
            },
            success: function (data) {
                $('.modal-body').html(data.url);
            }
        });


    }
</script>