<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/management') !!}" title="">管理员账号管理</a>
            </li>
            <li class="active">
                <a title="">编辑管理员账号</a>
            </li>
        </ul>
    </div>
</div>

<form class="form-horizontal" action="/manage/savemanagement" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="hidden" value="{!! $maInfo->id !!}" name="id">
    <div class="g-backrealdetails clearfix bor-border interface">
        <div class="space-8 col-xs-12"></div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">账号</label>
            <div class="text-left col-sm-9">
                <input type="text" name="manage_id" id="manage_id" value="{!! $maInfo['manage_id'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">姓名</label>
            <div class="text-left col-sm-9">
                <input type="text" name="name" id="name" value="{!! $maInfo['name'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">手机</label>
            <div class="text-left col-sm-9">
                <input type="text" name="tel" id="tel" value="{!! $maInfo['tel'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">QQ</label>
            <div class="text-left col-sm-9">
                <input type="text" name="qq" id="qq" value="{!! $maInfo['qq'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">邮箱</label>
            <div class="text-left col-sm-9">
                <input type="text" name="email" id="email" value="{!! $maInfo['email'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">职位</label>
            <div class="text-left col-sm-9">
                <select  id="job" name="job">
                    <option>客服</option>
                    <option>工程师</option>
                    <option>设计师</option>
                    <option>监听</option>
                    <option>管理</option>
                </select>
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