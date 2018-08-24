<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/materiaList') !!}" title="">辅材包套餐管理</a>
            </li>
            <li class="active">
                <a title="">编辑辅材包</a>
            </li>
        </ul>
    </div>
</div>

<form class="form-horizontal" action="/manage/savematerials" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="hidden" value="{!! $maInfo->id !!}" name="id">
    <div class="g-backrealdetails clearfix bor-border interface">
        <div class="space-8 col-xs-12"></div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">辅材包名字</label>
            <div class="text-left col-sm-9">
                <input type="text" name="name" id="name" value="{!! $maInfo['name'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">描述</label>
            <div class="col-sm-9">
                <textarea class="col-xs-5 col-sm-5" name="content" rows="8" id="content">{!! $maInfo['content'] !!}</textarea>
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">价格</label>
            <div class="text-left col-sm-9">
                <input type="text" name="price" id="price" value="{!! $maInfo['price'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">已售数量</label>
            <div class="text-left col-sm-9">
                <input type="text" name="sell_num" id="sell_num" value="{!! $maInfo['sell_num'] !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">库存</label>
            <div class="text-left col-sm-9">
                <input type="text" name="count" id="count" value="{!! $maInfo['count'] !!}">
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