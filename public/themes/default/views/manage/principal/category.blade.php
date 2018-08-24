
<style>
    #a a:active {
        color: red;
        font-size: 18px;
    }
</style>
<h3 class="header smaller lighter blue mg-bottom20 mg-top12">主材分类</h3>

<div class="row">
    <div class="col-xs-12">
        <a class="btn  btn-info" href="{!! url('manage/categoryEdit') !!}">
            <i class="ace-icon fa fa-add bigger-120">添加分类</i>
        </a>
        <div>
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>分类名称</th>
                    <th>操作</th>
                </tr>
                </thead>
                <form>
                    <tbody>

                        @foreach($cate as $item)
                            <tr>

                                <td>{!! $item->id !!}</td>

                                <td>
                                    {!! $item->name !!}
                                </td>


                                <td>
                                    <div class="btn-group">

                                        <a class="btn btn-xs btn-info" href="{!! url('manage/categoryEdit?id='.$item->id) !!}">
                                            <i class="ace-icon fa fa-edit bigger-120">编辑</i>
                                        </a>

                                        {{--<a class="btn btn-xs btn-danger"--}}
                                           {{--href="{!! url('manage/categoryEdit') !!}">--}}
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
</div>


{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}

{{--时间插件--}}

{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('checked-js', 'js/checkedAll.js') !!}

<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')}
    });
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

    //一键启用测试账号
    $('#all_test_able').on('click', function () {
        var action = 'enable';
        var res = confirm("您是否确认一键启用测试账号?")
        if (res == true) {
            $.ajax({
                type: "get",
                url: "/manage/systemAccountUpOrDown",
                data: {
                    action: action,
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    location.reload()
                }
            });
        }

    })

    //一键禁用测试账号
    $('#all_test_disable').on('click', function () {

        var action = 'disable';
        var res = confirm("您是否确认一键启用测试账号?")
        if (res == true) {
            $.ajax({
                type: "get",
                url: "/manage/systemAccountUpOrDown",
                data: {
                    action: action,
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    location.reload()
                }
            });
        }

    })

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