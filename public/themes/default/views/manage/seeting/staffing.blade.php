<style>
    input[readonly] {
        color: #000;
        background: rgba(255,255,255,.15)!important;
        cursor: default;
        text-align: center;
        border: 0px;
    }
</style>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/service') !!}" title="">服务者设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/worker') !!}" title="">工人设置</a>
            </li>
        </ul>
    </div>
</div>
<div class="row pay-api">
    <form action="/manage/staffing" method="post">
        {!! csrf_field() !!}
        <input type="hidden" value="{!! $id !!}" name="id">
        <!-- 等级价格设置 -->
        <div class="col-sm-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="well">
                等级价格设置
                <button type="submit" class="btn btn-primary btn-sm" style="float: right;">提交</button>
            </div>

            <div class="tabbable" id="price">
                <div class="tab-pane active" id="housekeeper">
                    <table id="sample-table-1-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">星级</th>
                                <th class="text-center">单价</th>
                                <th class="text-center hide">单位</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($staffing['price'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key+1 !!}星报价</td>
                                <td class="text-center"><input type="number" name="offer_{!! $key+1 !!}[price]" value="{!! $item->price !!}">元/m<sup>2</sup></td>
                                <td class="text-center hide"><input type="number" name="offer_{!! $key+1 !!}[company]" value="">m<sup>2</sup></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>          
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div>

        <!-- 升级机制设置 -->
        <div class="col-sm-12" style="margin-top: 20px;">
            <!-- PAGE CONTENT BEGINS -->
            <div class="well">
                升级机制设置
                <!-- <a href="/manage/addCharge/1" style="float: right;"><button class="btn btn-sm btn-primary">设置</button></a> -->
            </div>
            <div class="tabbable" id="upgrade">
                <div class="tab-content">
                    <div class="tab-pane active" id="housekeeper2">
                        <table id="sample-table-2-1" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50%">星级</th>
                                    <th class="text-center" style="width: 50%">所需累计评分</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($staffing['upgrade'] as $key => $item)
                                <tr>
                                    <td class="text-center">{!! $key !!}星</td>
                                    <td class="text-center"><input type="number" name="upgrade[{!! $key !!}]" value="{!! $item !!}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div>

        <!-- 得分设置 -->
        <div class="col-sm-12" style="margin-top: 20px;display: none">
            <!-- PAGE CONTENT BEGINS -->
            <div class="well">
                得分设置
                <!-- <a id="scoreId" style="float: right;"><button class="btn btn-sm btn-primary">设置</button></a> -->
            </div>
            <div class="tabbable">
                <div class="tab-content" id="score">
                    <div class="tab-pane active" id="housekeeper3" data-id="housekeeper['score']">
                        <table id="sample-table-3-1" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50%">星级</th>
                                    <th class="text-center" style="width: 50%">累计评分</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($staffing['score'] as $key => $item)
                                <tr>
                                    <td class="text-center">{!! $key !!}"星</td>
                                    <td class="text-center"><input type="number" name="score[{!! $key !!}]" value="{!! $item !!}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div>
    </form>
</div><!-- /.row -->
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}