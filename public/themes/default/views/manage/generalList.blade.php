{{-- <div class="page-header">
     <h3>
           搜索
     </h3>
 </div><!-- /.page-header -->--}}
<h3 class="header smaller lighter blue mg-top12 mg-bottom20">普通业主</h3>
<div class="row">
    <div class="col-xs-12">
        <div class="clearfix  well">
            <form  role="form" class="form-inline search-group" action="{!! url('manage/generalList') !!}" method="get">
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
                            <input type="text" name="start" class="input-sm form-control" @if(isset($search['start']))value="{!! $search['start'] !!}" @endif>
                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                            <input type="text" name="end" class="input-sm form-control" @if(isset($search['end']))value="{!! $search['end'] !!}" @endif>
                        </div>
                    </div>
                    <div class="form-group search-list">
                        <label>状态</label>
                        <select class="" name="status">
                            <option value="-1">全部</option>
                            <option @if(isset($status) && $status == 0)selected="selected"@endif value="0">未激活</option>
                            <option @if(isset($status) && $status == 1)selected="selected"@endif value="1">已激活</option>
                            <option @if(isset($status) && $status == 2)selected="selected"@endif value="2">已禁用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm">搜索</button>
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
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>UID
                        </label>
                    </th>
                    <th>登陆帐号</th>
                    <th>用户组</th>
                    <th>真实姓名</th>
                    <th>昵称</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th>最后使用时间</th>
                    <th>余额</th>
                    <th width="12%">操作</th>
                </tr>
                </thead>
                <form >
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
                                <td><a href="#">{!! $item->name !!}</a></td>
                                <td><a href="#"><a>{!! $item['display_name'] !!}</a> </td>
                                <td><a href="#">{!! $item->realname !!}</a></td>
                                <td><a href="#">{!! $item->nickname !!}</a></td>

                                <td>@if($item->status == 0)未激活@elseif($item->status == 1)已激活@elseif($item->status == 2)已禁用@endif</td>
                                <td>{!! $item->created_at !!}</td>
                                <td>
                                    {!! $item->last_login_time !!}
                                </td>
                                <td>
                                    {!! $item->balance !!}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-xs btn-info" href="{!! url('manage/generalEdit/' . $item->id) !!}">
                                            <i class="fa fa-edit"></i>编辑
                                        </a>
                                        @if($item->status == 1)
                                            <a class="btn btn-xs btn-danger" href="{!! url('manage/handleUser/' . $item->id . '/disable') !!}">
                                                <i class="fa fa-ban"></i>禁用
                                            </a>
                                        @elseif($item->status == 2)
                                            <a class="btn btn-xs btn-success" href="{!! url('manage/handleUser/' . $item->id . '/enable') !!}">
                                                <i class="fa fa-check"></i>启用
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
                    </button>&nbsp;&nbsp;&nbsp;&nbsp;
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

{{--批量操作js--}}
<script type="text/javascript">
    $('#all_disable').on('click',function(){
        var idArray = [];
        $('.ace').each(function(){
            console.log(this)
            if($(this).is(':checked')){
                var id = $(this).val();
                idArray.push(id);
            }
        });
         var ids = idArray.toString();
         console.log(ids);
         $.ajax({
             type: 'post',
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             url: '/manage/allEnterprisePass',
             data: {ids:ids},
             dataType:'json',
             success: function(data){
                 if(data.code == 1){
                     $('[type="checkbox"]').prop('checked','');
                     location.reload();
                 }
             }
         });
    });


     $('#all_able').on('click',function(){
         var idArray = [];
         $('.ace.auth_id').each(function(){
             if($(this).is(':checked')){
                 var id = $(this).val();
                 idArray.push(id);
             }
         });
         var ids = idArray.toString();
         console.log(ids);
         $.ajax({
             type: 'post',
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             url: '/manage/allEnterprisePass',
             data: {ids:ids},
             dataType:'json',
             success: function(data){
                 if(data.code == 1){
                     $('[type="checkbox"]').prop('checked','');
                     location.reload();
                 }
             }
         });
     });
</script>