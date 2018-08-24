
<style>
    body{
        background: #f2f2f2;
    }
    header, nav {
        display: none;
    }

    section {
        height: 100%
    }
    .rowHeight{
        border-bottom: 1px dashed #f6f6f6;
    }
    .rowHeight>div {
        height: 4rem;
        line-height: 4rem;
        font-size: 1.6rem;
        text-align: left;
        margin: 1rem auto;
    }
.form-control{
    border: none;
}
</style>
{{--<div class="col-md-12 col-left">--}}
{{--<!-- 所在位置 -->--}}
{{--<div class="now-position text-size12">--}}
{{--您的位置：首页 > {{$agree['name']}}--}}
{{--</div>--}}
{{--</div>--}}
</div>
<p style="    font-size: 2rem;
    text-align: center;
    padding-top: 1rem;margin-bottom: 0">请填写注册信息</p>
<div class="row clearfix" style="margin-top: 1.6rem;">
    <!-- main -->

        <input name="openid" id="openid" value="{!! $openid !!}" type="hidden">
    <div class="col-xs-12 clearfix col-left" style="background: white">
        <div class="row rowHeight">
            <div class="col-xs-4">姓名:</div>
            <div class="col-xs-8" style="padding: 0;"><input id="name" name="name" style="height: 4rem" class="form-control" placeholder="请输入真实姓名"/></div>
        </div>
        <div class="row rowHeight">
            <div class="col-xs-4">手机:</div>
            <div class="col-xs-8" style="padding: 0;"><input maxlength="11" id="tel" name="tel" style="height: 4rem" class="form-control" placeholder="请输入手机"/></div>
        </div>
        <div class="row rowHeight">
            <div class="col-xs-4">密码:</div>
            <div class="col-xs-8">
                <input name="password" id="password" maxlength="8" style="height: 4rem;border: none" class="form-control" placeholder="请6到8输入密码" type="password"/>
            </div>
        </div>
        <div class="row rowHeight">
            <div class="col-xs-4">确认密码:</div>
            <div class="col-xs-8">
                <input name="confirm_password" maxlength="8" id="confirm_password" style="height: 4rem;border: none" class="form-control" placeholder="请再次输入密码" type="password"/>
            </div>
        </div>

        <div class="row rowHeight" >
            <div class="col-xs-4">楼盘:</div>
            <div class="col-xs-8">

                <select class="form-control" style="height: 4rem" name="building_id" id="building_id">
                    @foreach($building as $key => $value)
                    <option value="{!! $value->id !!}">{!! $value->building_name !!}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="row" style="text-align: center;">
        <button style="padding: 6px 40px;
    font-size: 2rem;
    background: #c11f20;
    border: none;
    border-radius: 7px;
    color: white;margin-top: 2rem;" type="button" onclick="subData()">提交</button>
    </div>

</div>

<script>

    function subData() {

//        var fomrData = $("#subForm").serialize();
        var name = $("#name").val();
        var tel = $("#tel").val();
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        var building_id = $("#building_id").val();
        var openid = $("#openid").val();

        if(!name || !tel || !password || !confirm_password || !building_id || !openid){
            alert('获取不到要提交的参数');return;
        }


        $.post("subRegData",{
            _token:"{{csrf_token()}}",
            name:name,
            tel:tel,
            password:password,
            confirm_password:confirm_password,
            building_id:building_id,
            openid:openid
        },function(result){
            if(result.code == 200){
                alert('注册成功');
                location.href='';
            }else{
                alert(result.msg);
            }
        });
    }
</script>