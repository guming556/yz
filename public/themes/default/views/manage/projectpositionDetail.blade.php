<style>
    .body{
        width: 100%;
        height: 200px;
        border: 1px solid #CCC;
    }
    .order_detail{
        margin-left: 30px;
    }
    .table
    {
        width: 100%;
        border: 1px solid #CCC;

    }
    .table tr td{
        border: 1px solid #CCC;
        text-align: center;
    }
    .table tr th{
        border: 1px solid #CCC;
        text-align: center;
    }
    .table_body
    {
        border: 1px solid #CCC;
    }
    .user_table{
        border: 1px solid #CCC;
        width: 100%;
    }
    .user_table tr td{
        border: 1px solid #CCC;
        height: 40px;
        text-align: center;
        font-size: 18px;
    }
    .order_detail button{
        float: right;
        margin-right: 40px;
    }
    .constr_table
    {
        border: 1px solid #CCC;
        width: 100%;
    }
    .constr_table tr td{
        border: 1px solid #CCC;
        height: 40px;
        text-align: center;
        font-size: 18px;
    }
    .sehe_table
    {
        border: 1px solid #CCC;
        width: 100%;
    }
    .sehe_table tr td{
        border: 1px solid #CCC;
        height: 40px;
        text-align: center;
        font-size: 18px;
    }
</style>


<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/projectpositionList') !!}" title="">工地统计</a>
            </li>
            <li class="active">
                <a title="">详情</a>
            </li>
        </ul>
    </div>
</div><br>


<div class="body">
    <div class="well">订单信息</div>
    <div class="order_detail">
        <p>
            <label>工地编号：{!! $ppInfo['id'] !!}</label><button>查看设计订单详情</button><br>
            <label>开工时间：</label><button>查看管家订单详情</button><br>
            <label>业主需求：房屋大小：{!! $ppInfo['square'] !!} ㎡,{!! $ppInfo['room_config'] !!}{!! $ppInfo['favourite_style'] !!},{!! $ppInfo['region'] !!}</label><button>查看监听订单详情</button><br>
            <label>设计师实测面积：</label>
        </p>
    </div>
</div><br>

<div class="well">业主信息</div>
<table class="user_table">
    <thead>
    <tr>
        <td>姓名</td>
        <td>{!! $ppInfo['name'] !!}</td>
    </tr>
    <tr>
        <td>手机</td>
        <td>{!! $ppInfo['mobile'] !!}</td>
    </tr>
    <tr>
        <td>工地区域</td>
        <td>{!! $ppInfo['region'] !!}</td>
    </tr>
    <tr>
        <td>工地地址</td>
        <td></td>
    </tr>
    </thead>
</table>

<br>
<div class="table_body">
    <div class="well">关联服务者信息</div>
    <table class="table">
        <thead>
        <tr>
            <th>职位</th>
            <th>姓名</th>
            <th>手机</th>
        </tr></thead>
        <tbody>
        @foreach($pInfo as $info)
            <tr>
            <td>@if($info['user_type']==2)
                    <span>设计师</span>
                    @elseif($info['user_type']==3)
                        <span>管家</span>
                    @elseif($info['user_type']==4)
                        <span>监听</span>
                    @endif

            </td>
            <td>{!! $info['username'] !!}</td>
            <td>{!! $info['phone'] !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div><br>

<div class="well">施工工程</div>
<div>
    <table class="constr_table">
        <thead>
        <tr>
            <th>费用名称</th>
            <th>施工面积/㎡</th>
            <th>预计施工时间/天</th>
            <th>工种</th>
            <th>星级</th>
            <th>工人数量</th>
        </tr>
        </thead>
        <tr>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
        </tr>
    </table>
</div><br>

<div class="well">施工进度</div>
<div>
    <table class="sehe_table">
        <thead>
        <tr>
            <th>时间</th>
            <th>工程</th>
            <th>施工面积/㎡</th>
            <th>施工人数/个</th>
            <th>预计施工时间/天</th>
            <th>实计施工时间/天</th>
            <th>延期施工时间/天</th>
            <th>施工情况</th>
            <th>预计拖工时间/天</th>
        </tr>
        </thead>
        <tr>
            <td>2016-2-11</td>
            <td>土地改革</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>已完工</td>
            <td>通过</td>
        </tr>
    </table>
</div>