<style>
    .button{
        width: auto;
        height: 80px;
    }
    .add_a,.all_a,.del_a,.add1_a{

        display: block;
        width: 100px;
        height: 30px;
        border: 1px solid #CCC;
        background: white;
        text-align: center;
        float: left;

    }
    .quan{
        width: auto;
        height: auto;
        border: 1px solid #CCC;
    }
    .add_a a, .del_a a, .all_a a, .add1_a a{
        text-decoration: none;
        font-size: 14px;
        padding: 2px 6px;
        display: block;
        color: black;
    }
    .all_a{
        margin-left: 20px;
    }
    .del_a{
        margin-left: 40px;
    }
    .add_a{
        float: right;
        margin-right: 40px;
    }
    .od_d_ves{
        width: 100%;
        height: 450px;
        margin-top: 40px;
        border: 1px solid #CCC;
    }
    .col-sm-10{
        float: right;
        margin-right: 40px;
        margin-top: 20px;
    }
    .od_dd_ves{
        margin-left: 20px;
    }
    .keywords{
        width: 600px;
    }
</style>
<script>
    window.onload = function()
    {
        var Otn = document.getElementById("add");
        Otn.onclick = function()
        {
            document.getElementById("keywords").focus();
        }
    }
</script>
<div class="row">
    <form action="" method="post">
        <div class="quan">
            <div class="well">
                快速回复管理
            </div>
            <div class="body">
                <div class="button">
                    <div class="all_a">
                        <a href="#">全选</a>
                    </div>
                    <div class="del_a">
                        <a href="#">删除</a>
                    </div>
                    <div class="add_a">
                        <a href="#" id="add">添加自动回复</a>
                    </div>

                </div>
                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>

                        <th class="center">选择</th>
                        <th class="center">关键词</th>
                        <th class="center">自动回复</th>
                        <th class="center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rList['data'] as $item)
                        <tr class="center">

                            <td><input type="checkbox" value="{!! $item['id'] !!}"></td>
                            <td>{!! $item['keywords'] !!}</td>
                            <td>{!! $item['conent'] !!}</td>
                            <td>
                                <a title="编辑" class="btn btn-xs btn-success" href="/manage/upreply/{!! $item['id'] !!}">
                                    <i class="ace-icon fa fa-search bigger-120"></i>编辑
                                </a>
                                <a title="删除" class="btn btn-xs btn-success" href="#" onclick="onDel({!! $item['id'] !!});">
                                    <i class="ace-icon fa fa-search bigger-120"></i>删除
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

    <!--添加自动回复-->

    <div class="od_d_ves">
        <form class="form-horizontal" action="/manage/savereply" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="well">添加自动回复</div>
            <div class="od_dd_ves">
                <div class="od_btn_ves">
                    <span>输入关键字（可多写，用空格隔开）</span><br/><br>
                    <input type="text" name="keywords" id="keywords" class="keywords">
                </div><br/>
                <div>
                    <span>自动回复内容</span><br><br>
                    <div class="col-sm-9">
                        <textarea class="col-xs-5 col-sm-5" name="content" rows="8" id="content"></textarea>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="clearfix row bg-backf5 padding20 mg-margin12">
                        <div class="col-xs-12">
                            <div class="col-sm-1 text-right"></div>
                            <div class="col-sm-10"><button type="submit" class="btn btn-sm btn-primary">保存</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>

        function onDel($id)
        {
            var id = $id;
            var url = '/manage/delreply/'+id;

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
    </script>
</div>
