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
</style>

<div class="table ">
    <table class="tb">
        <div class="user_de">用户注册情况（截止：{!! $time !!}）</div></tr>
        <tr>
            <td>
                昨日注册业主<br>
                <span>{!! $old_ownersum !!}</span>
            </td>
            <td>
                本周注册业主<br>
                <span>{!! $week_ownersum !!}</span>
            </td>
            <td>
                本月注册业主<br>
                <span>{!! $month_ownersum !!}</span>
            </td>
            <td>
                平台总注册业主<br>
                <span>{!! $ownersum !!}</span>
            </td>
        </tr>
        <tr>
            <td>
                昨日注册设计师<br>
                <span>{!! $old_designersum !!}</span>
            </td>
            <td>
                本周注册设计师<br>
                <span>{!! $week_designersum !!}</span>
            </td>
            <td>
                本月注册设计师<br>
                <span>{!! $month_designersum !!}</span>
            </td>
            <td>
                平台总注册设计师<br>
                <span>{!! $designersum !!}</span>
            </td>
        </tr>
        <tr>
            <td>
                昨日注册管家<br>
                <span>{!! $old_housesum !!}</span>
            </td>
            <td>
                本周注册管家<br>
                <span>{!! $week_housesum !!}</span>
            </td>
            <td>
                本月注册管家<br>
                <span>{!! $month_housesum !!}</span>
            </td>
            <td>
                平台总注册管家<br>
                <span>{!! $housesum !!}</span>
            </td>
        </tr>
        <tr>
            <td>
                昨日注册监听<br>
                <span>{!! $old_monitorsum !!}</span>
            </td>
            <td>
                本周注册监听<br>
                <span>{!! $week_monitorsum !!}</span>
            </td>
            <td>
                本月注册监听<br>
                <span>{!! $month_monitorsum !!}</span>
            </td>
            <td>
                平台总注册监听<br>
                <span>{!! $monitorsum !!}</span>
            </td>
        </tr>
    </table>


</div>

<div class="row">
    <div class="col-sm-6">
        <div class="widget-box">
            <div class="widget-header widget-header-flat widget-header-small">
                <h5 class="widget-title">
                    <i class="ace-icon fa fa-signal"></i>
                    用户分析
                </h5>
            </div>

            <div class="widget-body clearfix padding-8">
                <div class="widget-main clearfix padding-14">
                    <!-- #section:plugins/charts.flotchart -->
                    <div id="sales-charts1"></div>
                    <div class="space-6"></div>
                    <div class="infobox-container">
                        <div class="infobox infobox-orange2">
                            <!-- #section:pages/dashboard.infobox.sparkline -->
                            <div class="infobox-chart">
                                <span class="sparkline sparklineBar-blue" data-values="196,128,202,177,154,94,100,170,224"></span>
                            </div>

                            <!-- /section:pages/dashboard.infobox.sparkline -->
                            <div class="infobox-data">
                                <span class="infobox-data-number">25</span>
                                <div class="infobox-content">总用户数</div>
                            </div>

                        </div>
                        <div class="infobox infobox-orange2">
                            <!-- #section:pages/dashboard.infobox.sparkline -->
                            <div class="infobox-chart">
                                <span class="sparkline sparklineLine-gray" data-values="196,128,202,177,154,94,100,170,224"></span>
                            </div>

                            <!-- /section:pages/dashboard.infobox.sparkline -->
                            <div class="infobox-data">
                                <span class="infobox-data-number">33</span>
                                <div class="infobox-content">今日用户数</div>
                            </div>

                        </div>
                    </div>
                </div><!-- /.widget-main -->
            </div><!-- /.widget-body -->
        </div><!-- /.widget-box -->
    </div><!-- /.col -->
</div>

