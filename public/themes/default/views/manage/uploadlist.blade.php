<div class="row">
    @if($count > 0)
    <div class="well">
        您还有{!! $count !!}个订单处理，请尽快处理！
    </div>
    @endif
    <form action="/manage/getProject" method="post">
        <div>
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th class="center">时间</th>
                    <th class="center">订单号</th>
                    <th class="center">业主昵称</th>
                    <th class="center">业主手机</th>
                    <th class="center">地区</th>
                    <th class="center">状态</th>
                    <th class="center">操作</th>
                </tr>
                </thead>

                <tbody>
                @foreach($uList['data'] as $item )
                    <tr class="center">
                        <td>{!! $item['date'] !!}</td>
                        <td>{!! $item['order_id'] !!}</td>
                        <td>{!! $item['name'] !!}</td>
                        <td>{!! $item['phone'] !!}</td>
                        <td>{!! $item['addr'] !!}</td>
                        <td>
                            @if($item['status']== '1')
                                <span>等待上传图纸</span>
                            @elseif($item['status'] == '2')
                                <span>已上传图纸</span>
                            @endif
                        </td>
                        <td>
                            @if($item['status']=='1')
                                <a title="浏览" class="btn btn-xs btn-success" href="/manage/addupload/{!! $item['id'] !!}">
                                    <i class="ace-icon fa fa-search bigger-120"></i>上传图纸
                                </a>
                            @elseif($item['status']=='2')
                                <a class="btn btn-xs btn-info" href="/manage/editupload/{!! $item['id'] !!}">
                                    <i class="fa fa-edit bigger-120"></i>修改图纸
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="space col-xs-12"></div>
    </form>
</div>
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('base', 'js/doc/base.js') !!}
