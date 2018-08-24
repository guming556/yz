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
            <li class="active">
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
            <a href="/manage/staffing/{!! $active !!}" style="float: right;"><button class="btn btn-sm btn-primary">设置</button></a>
        </div>

        <div class="tabbable" id="price">
            
            <select id="form-field-select-1" name="province" onchange="getWorker(this)" style="margin-bottom: 12px;">
                @foreach($rule as $key => $item)
                <option value="{!! $key !!}" @if($key == $active)selected="selected"@endif> {!! $item !!} </option>
                @endforeach
            </select>
            <a href="/manage/addWorker" style="float: right;">
                <button class="btn btn-sm btn-success">添加新工种</button>
            </a>
            <div class="tab-pane active" id="housekeeper">
                <table id="sample-table-1-1" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">星级</th>
                            <th class="text-center">价格百分比</th>
                            {{--<th class="text-center">单位</th>--}}
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach($worker['price'] as $key => $item)
                        <tr>
                            <td class="text-center">{!! $key+1 !!}星报价</td>
                            <td class="text-center">{!! $item->price !!}%</td>
                            {{--<td class="text-center">{!! $item->company !!}m<sup>2</sup></td>--}}
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
                            
                            @foreach($worker['upgrade'] as $key => $item)
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
                <div class="tab-pane active" id="housekeeper3" data-id="housekeeper['score']">
                    <table id="sample-table-3-1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50%">星级</th>
                                <th class="text-center" style="width: 50%">所需累计评分</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($worker['score'] as $key => $item)
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
<script>
    function getWorker(obj) {
        var id = obj.value;
            url = "{{ URL('manage/worker') }}/"+id;
        window.location.href=url; 
    }
</script>