<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/reply') !!}" title="">说明设置</a>
            </li>
            <li class="active">
                <a title="">编辑回复</a>
            </li>
        </ul>
    </div>
</div>

<form class="form-horizontal" action="/manage/savereply" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="hidden" value="{!! $reInfo->id !!}" name="id">
    <div class="g-backrealdetails clearfix bor-border interface">
        <div class="space-8 col-xs-12"></div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">关键字</label>
            <div class="text-left col-sm-9">
                <input type="text" name="keywords" id="keywords" value="{!! $reInfo['keywords'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">回复内容</label>
            <div class="col-sm-9">
                <textarea class="col-xs-5 col-sm-5" name="content" rows="8" id="content">{!! $reInfo['conent'] !!}</textarea>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="clearfix row bg-backf5 padding20 mg-margin12">
                <div class="col-xs-12">
                    <div class="col-sm-1 text-right"></div>
                    <div class="col-sm-10"><button type="submit" class="btn btn-sm btn-primary">提交</button></div>
                </div>
            </div>
        </div>
    </div>
</form>