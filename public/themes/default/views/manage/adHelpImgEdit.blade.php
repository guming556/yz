<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/adImgList') !!}" title="">图片管理</a>
            </li>
            <li class="active">
                <a title="">@if(!empty($img_id))图片信息编辑@else图片信息添加 @endif</a>
            </li>

        </ul>
    </div>
    @if(empty($hidden))
    <a id="modal-281004" href="#modal-container-281004" style="float: right" role="button" class="btn btn-primary" data-toggle="modal">添加图片</a>
        @endif
</div>



<div class="">
    <div class="g-backrealdetails clearfix bor-border">

        <div class="alert-danger">
            @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            @endif
        </div>

        @foreach($all_help_img as $k=>$v)

            <div>
                <img src="{{url($v['url'])}}" alt="" style="
    height: 600px;">
            </div>


            <a id="modal-281004" href="#modal-container-281005" style="float: right" role="button" class="btn btn-primary" data-toggle="modal" altid="{!! $v->id !!}" onclick="edit(this)" >编辑图片</a>
            @if(empty($hidden))
            <a id="modal-281004" altid="{{$v->id}}" onclick="delete_img(this)" style="float: right" role="button" class="btn btn-primary" data-toggle="modal">删除此图片</a>
            @endif


<div><br><br><br><br></div>
------------------------------------------------------------------------------------------------------------------------------------------------------------

        @endforeach

    </div>
</div>

<form role="form" action="/manage/adHelpImgSubmit" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="modal fade" id="modal-container-281004" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        请上传要添加的首页图片<span style="color: red">(宽高比为640*1136,或大于此分辨率的等比例图片)</span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputFile">新手图片</label>
                        <input type="file" id="all_img" name="all_img"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form role="form" action="/manage/adHelpImgSubmit" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="modal fade" id="modal-container-281005" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        请上传要添加的首页图片<span style="color: red">(宽高比为640*1136,或大于此分辨率的等比例图片)</span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputFile">首页图片</label>
                        <input type="file" id="all_img" name="all_img"/>
                        <input type="hidden" name="edit-id" id="edit-id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>

<script>
    function delete_img(obj) {
        var id = $(obj).attr("altid");
        var res = confirm("您是否确认删除?")
        if (res==true)
        {
            $.ajax({
                type: "post",
                url: "/manage/adImgDelete",
                data: {
                    id: id,
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.msg);
                    location.reload()
                }
            });
        }
    }



    function edit(obj){
        $("#edit-id").attr("value", $(obj).attr("altid"));
    }
</script>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}