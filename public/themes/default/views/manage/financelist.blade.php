<h3 class="header smaller lighter blue mg-bottom20 mg-top12">网站流水</h3>

<div class="  well">
    <form class="form-inline search-group" role="form" method="get" action="/manage/financeList">



        <div class="form-group search-list">
            <label class="">用户收入和支出　</label>
            <select name="fund_state" id="fund_state">
                <option value="" ></option>
                <option value="1">查看收入</option>
                <option value="2">查看支出</option>
            </select>
        </div>

{{--        <div class="form-group search-list">

            <a href="/manage/financeList?fund_state=1">查看收入</a>
            <a href="/manage/financeList?fund_state=2">查看支出 </a>　
        </div>--}}
        <div class="form-group search-list ">
            <label for="namee" class="">手机号　</label>
            <input name="phone_num" type="text" value="" />
        </div>
        <div class="form-group search-list ">
            <label for="namee" class="">订单号　</label>
            <input name="order_num" type="text" value="" />
        </div>
        <div class="form-group search-list">
            <label for="">选择时间　</label>
            <div class="input-daterange input-group">
                <input type="text" name="start" class="input-sm form-control" value="@if(isset($start)){!! $start !!}@endif">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                <input type="text" name="end" class="input-sm form-control" value="@if(isset($end)){!! $end !!}@endif">
            </div>
        </div>
        <button class="btn btn-primary btn-sm">搜索</button>　　
        <a href=" @if (isset($export_url) ){{$export_url}} @else /manage/financeListExport/ @endif" style="cursor: pointer"> 导出Excel</a>
    </form>

</div>

<div class="table-responsive">
    <table id="sample-table-1" class="table table-striped table-bordered table-hover" style="vertical-align:middle; ">
        <thead>
        <tr>
            <th>编号</th>
            <th>姓名</th>
            <th>手机号</th>
            <th>明细</th>
            <th>订单号</th>
            <th>收入/支出</th>

            <th>金额</th>
            <th>工地</th>

            <th>时间</th>
        </tr>
        </thead>

        <tbody>
        @if(!empty($finance))
        @foreach($finance as $key=>$item)
            <tr>
                {{--{{dd($key,$item)}}--}}
                <td>
                    {!! $key+1 !!}
                </td>

                <td>{{$item['user_name']}}</td>
                <td>{{$item['user_mobile']}}</td>
                <td>{{$item['title']}}</td>
                <td>{{$item['order_code']}}</td>
                <td>@if(empty($item['fund_state']))
                        收入
                    @else
                        @if($item['fund_state']==1)
                            支出
                        @else
                            收入
                        @endif

                    @endif</td>


                <td>@if(empty($item['fund_state']))
                        ￥ +{!! $item['cash'] !!}
                    @else
                        @if($item['fund_state']==1)
                            ￥ -{!! $item['cash'] !!}
                        @else
                            ￥ +{!! $item['cash'] !!}
                        @endif

                    @endif</td>



                <td>
                    {!! $item['project_position'] !!}
                </td>
                <td>
                    {!! $item['created_at'] !!}
                </td>
            </tr>
        @endforeach
        @else
        <tr>
            <td colspan="6">暂无数据</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-xs-12">
        {{--<div class="dataTables_info" id="sample-table-2_info">收入总计：￥ {!! $cashcount !!} 元</div>--}}
    </div>
    <div class="space col-xs-12"></div>
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_bootstrap text-right row">
            @if(!empty($finance)){!!  $finance->appends($search)->render() !!}@endif 共 {{  $finance->total() }} 数据 当前第{{$finance->currentPage()}}页
        </div>
    </div>
</div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
</script>

<script>
    function financeExport(obj) {
        var url_real = location.href;

/*        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        var paraObj = {}
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }*/

        $.ajax({
            type: "post",
            url: "/manage/financeListExport",
            data: {
                url_real: url_real
            },
            dataType: 'json',
            success: function (data) {
                console.log(data.msg);
//                location.href(data.msg);
            }
        });
    }

</script>

{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('datepicker-css', 'plugins/ace/css/datepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepicker-js', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('userfinance-js', 'js/userfinance.js') !!}