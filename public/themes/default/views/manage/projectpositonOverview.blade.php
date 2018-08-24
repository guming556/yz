<style>
    .tb{
        width: 100%;
        text-align: center;
        font-size: 18px;
        font-weight: 100;
    }
    .tb tr td{
        height: 100px;
        border: 1px solid grey;
    }
    .table{
        border: 1px solid grey;
    }
    .user_de{
        height: 40px;
        font-size: 18px;
    }
    .use_fenxi{
        width: 100%;
        border: 1px solid grey;
    }
    .button{

        display: inline-block;
        position: relative;
        margin: 10px;
        padding: 0 20px;
        text-align: center;
        text-decoration: none;
        font: bold 12px/25px Arial, sans-serif;

        text-shadow: 1px 1px 1px rgba(255,255,255, .22);

        -webkit-border-radius: 30px;
        -moz-border-radius: 30px;
        border-radius: 30px;

        -webkit-box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);
        -moz-box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);
        box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);

        -webkit-transition: all 0.15s ease;
        -moz-transition: all 0.15s ease;
        -o-transition: all 0.15s ease;
        -ms-transition: all 0.15s ease;
        transition: all 0.15s ease;
    }
    .use_span{
        text-align: center;
        font-size: 18px;
    }
</style>


<div class="table ">
    <table class="tb">
        <tr><div class="user_de">用户注册情况（截止：{!! $time !!}）</div></tr>
        <tr>
            <td>
                昨日注册业主<br>
                <span>{!! $old_ppnum !!}</span>
            </td>
            <td>
                本周注册业主<br>
                <span>{!! $week_ppnum !!}</span>
            </td>
            <td>
                本月注册业主<br>
                <span>{!! $month_ppnum !!}</span>
            </td>
            <td>
                平台总注册业主<br>
                <span>{!! $ppnum !!}</span>
            </td>
        </tr>
    </table>
</div><br>
<!--工地统计-->
<div class="use_fenxi">
    <div class="user_de">工地增长</div>
    <div class="use_span">
        <span>工地分析</span>
    </div>
    <div id="sales-charts2"></div><!--折线图-->
</div>

<div id="broken" data-data='{!! $broken !!}'></div>
<div id="maxDay" data-data='{!! $maxDay !!}'></div>
<div id="dateArr" data-data='{!! $dateArr !!}'></div>

<!--地图-->
<div id="main" style="width: 600px;height:400px;"></div>
<script src="/themes/default/assets/js/echarts.js"></script>
{!! Theme::asset()->container('specific-js')->usePath()->add('excanvas-js', 'plugins/ace/js/jquery.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('easypiechart-js', 'plugins/ace/js/jquery.easypiechart.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('sparkline-js', 'plugins/ace/js/jquery.sparkline.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('flot-js', 'plugins/ace/js/flot/jquery.flot.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('flotPie-js', 'plugins/ace/js/flot/jquery.flot.pie.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('flotResize-js', 'plugins/ace/js/flot/jquery.flot.resize.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('backstage-js', 'js/ppstage.js') !!}
