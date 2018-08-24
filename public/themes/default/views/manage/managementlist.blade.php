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
    .all_a,.del_a, .dao_a, .disabled_a,.enabled_a, .add_a
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
    }
    .add_a{
        margin-left: 100px;
    }
    #data{
        width: 300px;
    }
    .add_manage{
        width: 300px;
        height: 550px;
        border: 1px solid #CCC;
    }
    .add_xuan label{
        display: inline-block;
        min-width: 100px;
        text-align: center;
    }
    #id,#email,#job,#name,#pwd,#qq,#pwd_1,#tel
    {
        margin-top: 10px;
    }
    #btn2,#btn3{

        margin-top: 20px;
        margin-left: 50px;
    }
    .span{
        color: red;
        font-size: 16px;
        margin-left: 30px;
        display: none;
    }
</style>

<script>
    function onShow()
    {
        document.getElementById("add_manage").style.display = "";
    }
    function onClose()
    {
        document.getElementById("add_manage").style.display = "none";
    }
</script>

<script>

</script>

<div class="row">
    <form action="" method="post">
        <div class="quan">
            <div class="well">
                管理员管理
            </div>
            <div class="body">
                <div class="button">
                    <div class="all_a">
                        <a href="#">选择</a>
                    </div>
                    <div class="disabled_a">
                        <a href="#">禁用</a>
                    </div>
                    <div class="enabled_a">
                        <a href="#">启用</a>
                    </div>
                    <div class="del_a">
                        <a href="#">删除</a>
                    </div>
                    <div class="add_a">
                        <a href="#" id="ad_mg" onclick="onShow();">添加管理员</a>
                    </div>
                    <div class="dao_a">
                        <a href="#">导出表格</a>
                    </div>

                    <div class="sousuo" >
                        <a href="#">搜索</a>
                    </div>
                    <div>
                        <input type="text" placeholder="请输入" id="data" name="">
                    </div>

                </div>
                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th class="center">选择</th>
                        <th class="center">id</th>
                        <th class="center">职位（角色，组）</th>
                        <th class="center">姓名</th>
                        <th class="center">手机</th>
                        <th class="center">邮箱</th>
                        <th class="center">QQ</th>
                        <th class="center">状态</th>
                        <th class="center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mList['data'] as $item)
                        <tr class="center">
                            <td><input type="checkbox" value="{!! $item['id'] !!}"></td>
                            <td>{!! $item['manage_id'] !!}</td>
                            <td>{!! $item['job'] !!}</td>
                            <td>{!! $item['name'] !!}</td>
                            <td>{!! $item['tel'] !!}</td>
                            <td>{!! $item['email'] !!}</td>
                            <td>{!! $item['qq'] !!}</td>
                            <td>{!! $item['status'] !!}</td>
                            <td>
                                <a title="编辑" class="btn btn-xs btn-success" href="/manage/editmanagement/{!! $item['id'] !!}">
                                    <i class="ace-icon fa fa-search bigger-120"></i>编辑
                                </a>
                                <a title="删除" class="btn btn-xs btn-success" href="#" onclick="onDel({!! $item['id'] !!});">
                                    <i class="ace-icon fa fa-search bigger-120"></i>删除
                                </a>
                                <a title="禁用" class="btn btn-xs btn-success" href="#" onclick="onDisabled({!! $item['id'] !!});">
                                    <i class="ace-icon fa fa-search bigger-120"></i>禁用
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

    <!--添加管理员-->
    <div class="add_manage" id="add_manage" style="display: none;">
        <form class="form-horizontal" action="/manage/savemanagement" method="post" enctype="multipart/form-data">
            <div class="well">
                添加管理员
            </div>
            <div class="add_xuan">
                <label>账号：</label>
                <input type="text" id="manage_id" name="manage_id"/><br/>
                <label>密码：</label>
                <input type="password" id="pwd" name="pwd"><br/>
                <label>确认密码：</label>
                <input type="password" id="pwd_1" name="pwd1"><span class="span" id="span">两次密码不正确</span><br/>
                <label>姓名：</label>
                <input type="text" id="name" name="name"><br/>
                <label>手机：</label>
                <input type="text" id="tel" name="tel"><br/>
                <label>QQ：</label>
                <input type="text" id="qq" name="qq"><br/>
                <label>邮箱：</label>
                <input type="text" id="email" name="email"><br/>
                <label>职位：</label>
                <select id="job" name="job">
                    <option>--请选择--</option>
                    <option>客服</option>
                    <option>工程师</option>
                    <option>设计师</option>
                    <option>监听</option>
                    <option>管理</option>
                </select><br>

                <div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-sm btn-primary" id="btn2">保存</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="onClose();" id="btn3">取消</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function onDel($id)
        {
            var id = $id;
            var url = '/manage/delmanagement/'+id;

            $.get(url,function(data)
            {
                if(data.errCode==0)
                {
                    alert('删除失败');

                }else if(data.errCode==1)
                {
                    window.location.reload();
                }
            });
        }

        function onDisabled(id)//禁用
        {
            var id = id;
            var url = '/manage/enmanagement/' +id ;
            $.get(url,function(data)
            {
                if(data.errCode==1)
                {
                    window.location.reload();
                }else {
                    alert('禁用失败！');
                }
            });
        }

    </script>
</div>
