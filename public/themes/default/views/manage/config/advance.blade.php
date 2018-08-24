<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="{!! url('manage/advanceConfig') !!}" title="">预约金设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/payConfig') !!}" title="">支付设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/thirdPay') !!}" title="">第三方支付平台接口</a>
            </li>
            <li class="">
                <a href="{!! url('manage/orderConfig') !!}" title="">接单设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/designConfig') !!}" title="">设计费用设置</a>
            </li>
        </ul>
    </div>
</div>
<!--  /.page-header -->
<div class="row pay-api">
    <div class="col-sm-12">
        <!-- PAGE CONTENT BEGINS -->
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">序号</th>
                    <th class="text-center">服务名称</th>
                    <th class="text-center">金额</th>
                    <th class="text-center">上次编辑时间</th>
                    <th class="text-center">上次编辑人</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($data as $key => $item)
                    <tr>
                        <td class="text-center">{!! $key+1 !!}</td>
                        <td class="text-center">{!! $item['title'] !!}</td>
                        <td class="text-center">{!! $item['rule']['money'] !!}</td>
                        <td class="text-center">{!! $item['rule']['updatetime'] !!}</td>
                        <td class="text-center">{!! $item['rule']['editor'] !!}</td>
                        <td class="text-center">
                            <a class="btn btn-white" href="/manage/advanceConfigUpdate/{!! $item['id'] !!}">
                                修改价格
                            </a>
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
</div><!-- /.row -->
</div>

{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}