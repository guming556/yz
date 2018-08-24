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
            <li class="active">
                <a href="{!! url('manage/service') !!}" title="">服务者设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/worker') !!}" title="">工人设置</a>
            </li>
        </ul>
    </div>
</div>
<div class="row pay-api">
    <!-- 等级价格设置 -->
    <div class="col-sm-12">
        <!-- PAGE CONTENT BEGINS -->
        <div class="well">
            等级价格设置
            <a id="setValue" style="float: right;"><button class="btn btn-sm btn-primary">设置</button></a>
        </div>
        <div class="tabbable" id="price">
            <div class="tab-content">
                <ul class="nav nav-tabs">
                    <li class="active">
                         <a href="#housekeeper" data-toggle="tab">管家</a>
                    </li>
                    <li>
                         <a href="#supervisor" data-toggle="tab">监理</a>
                    </li>
                </ul>
                <div class="tab-pane active" id="housekeeper" data-id="1">
                    <table id="sample-table-1-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">星级</th>
                                <th class="text-center">价格/元</th>
                                <th class="text-center">单位</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($housekeeper['price'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key+1 !!}星报价</td>
                                <td class="text-center">{!! $item->price !!}元</td>
                                <td class="text-center">{!! $item->company !!}m<sup>2</sup></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="supervisor" data-id="2">
                    <table id="sample-table-1-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">星级</th>
                                <th class="text-center">价格/元</th>
                                <th class="text-center">单位</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($supervisor['price'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key+1 !!}星报价</td>
                                <td class="text-center">{!! $item->price !!}元</td>
                                <td class="text-center">{!! $item->company !!}m<sup>2</sup></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                <ul class="nav nav-tabs">
                    <li class="active">
                         <a href="#housekeeper2" data-toggle="tab">管家</a>
                    </li>
                    <li>
                         <a href="#supervisor2" data-toggle="tab">监理</a>
                    </li>
                </ul>
                <div class="tab-pane active" id="housekeeper2">
                    <table id="sample-table-2-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50%">星级</th>
                                <th class="text-center" style="width: 50%">累计评分</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($housekeeper['upgrade'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key !!}星</td>
                                <td class="text-center">{!! $item !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="supervisor2">
                    <table id="sample-table-2-2" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50%">星级</th>
                                <th class="text-center" style="width: 50%">累计评分</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($supervisor['upgrade'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key !!}星</td>
                                <td class="text-center">{!! $item !!}</td>
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
                <ul class="nav nav-tabs">
                    <li class="active">
                         <a href="#housekeeper3" data-toggle="tab">管家</a>
                    </li>
                    <li>
                         <a href="#supervisor3" data-toggle="tab">监理</a>
                    </li>
                </ul>
                <div class="tab-pane active" id="housekeeper3" data-id="housekeeper['score']">
                    <table id="sample-table-3-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50%">星级</th>
                                <th class="text-center" style="width: 50%">累计评分</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($housekeeper['score'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key !!}星</td>
                                <td class="text-center"><input type="text" value="{!! $item !!}" readonly="readonly"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="supervisor3" data-id="supervisor['score']">
                    <table id="sample-table-3-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50%">星级</th>
                                <th class="text-center" style="width: 50%">累计评分</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($supervisor['score'] as $key => $item)
                            <tr>
                                <td class="text-center">{!! $key !!}星</td>
                                <td class="text-center"><input type="text" value="{!! $item !!}" readonly="readonly"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- PAGE CONTENT ENDS -->
    </div>
</div><!-- /.row -->
{!! Theme::asset()->container('custom-css')->usePath()->add('back-stage-css', 'css/backstage/backstage.css') !!}
<!-- <script>
    $(document).ready(function(){
      $("#scoreId").click(function(){
        setValue();
      });
    });

    function setValue()
    {
        //var sid = $('#score').find('div.active').attr("id");
        /*$("#"+sid+" input[type='text']").each(function(){
            $(this).removeAttr("readonly");
        });*/
        /*var sid = $('#score').find('div.active').data('id');
        console.log(sid);*/
    }
</script> -->
<script>
    $('#setValue').click(function(){
        var sid = $('#price').find('div.active').data('id');
        url = "{{ URL('manage/staffing') }}/"+sid;
        window.location.href=url; 
    })
</script>