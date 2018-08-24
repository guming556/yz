{{--驳回--}}
<div class="modal fade" id="modal-container-812642" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">
                    驳回
                </h4>
            </div>
            <div class="modal-body">
                <textarea name="remark" id="remark" style="width: 100%;height: 200px" placeholder="请输入驳回理由"></textarea>
                <input type="hidden" value="0" id="order_id" name="order_id"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="rejectOrder();">保存</button>
            </div>
        </div>

    </div>

</div>



{{--资料详细--}}

<div class="modal fade" id="modal-container-619541" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">
                    报建资料
                </h4>
            </div>
            <div class="modal-body">
                <p>报修地址：<span id="room"></span></p>
                <p>装修程度：<span id="repair_type"></span></p>
                <p>装修负责人：<span id="charger_name"></span></p>
                <p>负责人电话：<span id="charger_tel"></span></p>
                <p>装修公司营业执照：<span id="business_image"></span></p>

                <p>业主身份证正面图片：<span id="owner_card_1"></span></p>
                <p>业主身份证反面图片：<span id="owner_card_2"></span></p>
                <p>负责人身份证正面图片：<span id="charger_card_1"></span></p>
                <p>负责人身份证反面图片：<span id="charger_card_2"></span></p>

                <p>特种作业复印件：<span id="special"></span></p>

                <div id="otherItem"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                {{--<a href="zipUpload" type="button" class="btn btn-primary">打包下载</a>--}}
            </div>
        </div>

    </div>

</div>


    <div class="row">
        <div class="col-xs-12">


            <h3 class="header smaller lighter blue mg-bottom20 mg-top12">报建单列表

            </h3>
            <a  href="{!! url('manage/createRepairOrderView') !!}" role="button" class="btn btn-primary" >添加报建</a>



            <div class="table-responsive">
                <table id="sample-table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th>编号</th>
                        <th>业主账户</th>
                        <th>报建房号</th>
                        <th>负责人姓名</th>
                        <th>负责人电话</th>
                        <th>当前状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($repair->toArray()['data']))
                    @foreach($repair->toArray()['data'] as $item)
                        <tr>
                            <td>
                                <a href="#">{!! $item->id !!}</a>
                            </td>
                            <td>
                                <a href="#">{!! $item->boss_tel !!}</a>
                            </td>

                            <td>{!! $item->detail['room'] !!}</td>
                            <td>{!! $item->detail['charger_name'] !!}</td>
                            <td>{!! $item->detail['charger_tel'] !!}</td>
                            <td>
                                @if($item->status == 0)<span class="btn btn-xs btn-danger">处理中</span>@endif
                                @if($item->status == 1)<span class="btn btn-xs btn-warning">已审核</span>@endif
                                @if($item->status == 3)<span class="btn btn-xs btn-success">已驳回</span>@endif
                                @if($item->status == 2)<span class="btn btn-xs btn-success">已完成</span>@endif
                            </td>

                            <td>
                                <div class=" btn-group">
                                        @if($item->status == 0)
                                        <a title="通过"  onclick="passOrder({!! $item->id !!} , 2)" class="btn btn-xs btn-success">
                                            <i class="ace-icon fa fa-check bigger-120"></i>通过
                                        </a>
                                        <a id="modal-812642" href="#modal-container-812642" role="button" data-toggle="modal" title="驳回"  onclick="rejectOrderButton({!! $item->id !!})"  class="btn btn-xs btn-danger">
                                            <i class="ace-icon fa fa-ban bigger-120"></i>驳回
                                        </a>
                                        @endif
                                    <a  title="查看详细"  onclick="orderDetail({!! $item->id !!})"  class="btn btn-xs btn-warning">
                                        <i class="ace-icon fa fa-adjust bigger-120"></i>查看详细
                                    </a>
                                            <a href="zipUpload?id={!! $item->id !!}" type="button" class="btn btn-xs btn-primary">打包下载</a>
                                </div>

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
                            {!! $repair->appends($merge)->render() !!}
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
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>
    <script>
        function passOrder(id){
            if(!id){
                alert('获取不到要提交的参数!!!');return;
            }
            $.post("updateOrderStatus",{order_id:id,type:1},function(result){
                if(result.code === 200){
                    alert('审核成功');
                    location.href='';
                }else{
                    alert('操作失败!请联系技术人员');
                }
            },'JSON');
        }

        function rejectOrderButton(id){
            var order_id = $("#order_id").attr('value',id);
        }

        function rejectOrder(){
            var order_id = $("#order_id").val();
            var remark = $("#remark").val();
            console.log(order_id,remark);
            if(!order_id || !remark){
                alert('获取不到要提交的参数!!!');return;
            }
            $.post("updateOrderStatus",{order_id:order_id,type:2,remark:remark},function(result){
                if(result.code === 200){
                    alert('驳回成功');
                    location.href='';
                }else{
                    alert('操作失败!请联系技术人员');
                }
            },'JSON');
        }

        function orderDetail(id){
            $("#otherItem").html('');
            if(!id){
                alert('获取不到参数!!!');return;
            }
            {{--<img src="http://img.zcool.cn/community/01711b59426ca1a8012193a31e5398.gif@2o.png" style="height: 120px;">--}}
            $.post("orderDetail",{order_id:id},function(result){
                console.log(result);
                if(result.code === 200){
                    var data = result.data;
                    $("#charger_name").html(data.charger_name);
                    $("#charger_tel").html(data.charger_tel);
                    $("#room").html(data.room);
                    $("#repair_type").html(data.repair_type);
                    if(data.business_license){
                        $("#business_image").html('<img style="height: 100px" src='+data.business_license+'>');
                    }else{
                        $("#business_image").html('');
                    }

                    if(data.owner_positive_identity_card){
                        $("#owner_card_1").html('<img style="height: 100px" src='+data.owner_positive_identity_card+' >');
                    }else{
                        $("#owner_card_1").html('');
                    }

                    if(data.owner_opposite_identity_card){
                        $("#owner_card_2").html('<img style="height: 100px" src='+data.owner_opposite_identity_card+' >');
                    }else{
                        $("#owner_card_2").html('');
                    }

                    if(data.charger_positive_identity_card){
                        $("#charger_card_1").html('<img style="height: 100px" src='+data.charger_positive_identity_card+' >');
                    }else{
                        $("#charger_card_1").html('');
                    }

                    if(data.charger_opposite_identity_card){
                        $("#charger_card_2").html('<img style="height: 100px" src='+data.charger_opposite_identity_card+' >');
                    }else{
                        $("#charger_card_2").html('');
                    }

                    if(data.copy_of_work){
                        $("#special").html('<img style="height: 100px" src='+data.copy_of_work+' >');
                    }else{
                        $("#special").html('');
                    }

                    var arrHtml = '';
                    var workArr = data.workers;
//                    console.log(workArr)
                    var projectImgArr = data.project_picture;
                    for(var j = 0; j < workArr.length; j++) {
                        console.log(workArr[j].name);
                        arrHtml += '<p>工人姓名：'+workArr[j].name+'</p>';
                        arrHtml += '<p>工人电话：'+workArr[j].tel+'</p>';
                        arrHtml += '<p>工人工种：'+workArr[j].worker_type_name+'</p>';
                        arrHtml += '<p>身份证正面：<img style="height: 100px" src='+workArr[j].worker_positive_identity_card+' ></p>';
                        arrHtml += '<p>身份证反面：<img style="height: 100px" src='+workArr[j].worker_opposite_identity_card+' ></p>';
                    }

                    for(var i=0;i<projectImgArr.length;i++){
                        arrHtml += '<p>工程图纸'+(i+1)+'<img style="height: 100px" src='+projectImgArr[i]+'></p>';
                    }
                    $("#otherItem").append(arrHtml);

                }else{
                    alert('请求失败!请联系技术人员');
                }
            },'JSON');

            $('#modal-container-619541').modal({
                keyboard: true
            })
        }

    </script>
