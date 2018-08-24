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
                <a title="">编辑说明</a>
            </li>
        </ul>
    </div>
</div>

<form class="form-horizontal" action="/manage/saveExplain" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="hidden" value="{!! $exInfo->id !!}" name="id">
    <div class="g-backrealdetails clearfix bor-border interface">
        <div class="space-8 col-xs-12"></div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">标题</label>
            <div class="text-left col-sm-9">
                <input type="text" name="title" id="title" value="{!! $exInfo->title !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">简介</label>
            <div class="text-left col-sm-9">
                <input type="text" name="profile" id="profile" value="{!! $exInfo->profile !!}">
            </div>
        </div>
        <div class="form-group interface-bottom col-xs-12">
            <label class="col-sm-1 text-right">文章内容</label>
            <div class="text-left col-sm-8">
                <!--编辑器-->
                <div class="clearfix">
                    <script id="editor" name="content" type="text/plain">{!! htmlspecialchars_decode($exInfo->content) !!}</script>
                    {{--<div class="wysiwyg-editor" id="editor1">{!! htmlspecialchars_decode($exInfo->content) !!}</div>
                    <textarea name="content" id="content" style="display: none">{!! htmlspecialchars_decode($exInfo->content) !!}</textarea>--}}
                </div>
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

{!! Theme::widget('ueditor')->render() !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('datepicker', 'plugins/ace/js/date-time/bootstrap-datepicker.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('custom', 'plugins/ace/js/jquery-ui.custom.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('touch-punch', 'plugins/ace/js/jquery.ui.touch-punch.min.js') !!}

{!! Theme::asset()->container('specific-js')->usepath()->add('chosen', 'plugins/ace/js/chosen.jquery.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('autosize', 'plugins/ace/js/jquery.autosize.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('inputlimiter', 'plugins/ace/js/jquery.inputlimiter.1.3.1.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('maskedinput', 'plugins/ace/js/jquery.maskedinput.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('hotkeys', 'plugins/ace/js/jquery.hotkeys.min.js') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('wysiwyg', 'plugins/ace/js/bootstrap-wysiwyg.min.js') !!}


{!! Theme::asset()->container('custom-js')->usepath()->add('addarticle', 'js/doc/addexplain.js') !!}
