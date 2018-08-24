<style>
    .content-body img{
        width: 100%;
    }
</style>
<div class="widget-header mg-bottom20 mg-top12 widget-well">
    <div class="widget-toolbar no-border pull-left no-padding">
        <ul class="nav nav-tabs">
            <li class="">
                <a href="{!! url('manage/explain') !!}" title="">说明设置</a>
            </li>
            <li class="">
                <a href="{!! url('manage/addExplain') !!}" title="">添加说明</a>
            </li>
            <li class="active">
                <a title="">说明设置</a>
            </li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
    
        <div style="width: 100%;">
            <div class="well">
                说明设置
            </div>

            <div class="content-header center">
                <h2>{!! $exInfo->title !!}</h2>
            </div>
            <div class="content-body">
                {!! htmlspecialchars_decode($exInfo->content) !!}
            </div>
        </div>
        
    </div>
    <div class="col-sm-2">
    </div>
</div>

{!! Theme::widget('ueditor')->render() !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
