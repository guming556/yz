<div>
    <div>
        <div>
            <h3 class="header smaller lighter blue mg-bottom20 mg-top12">添加工程</h3>

            <form class="form-horizontal" action="/manage/updateProject" method="post">
                {!! csrf_field() !!}
                <input type="hidden" value="{!! $id !!}" name="id">
                <div class="widget-body">
                    <div class="">
                        <div class="g-backrealdetails clearfix bor-border">
                            <table class="table table-hover">
                                <tbody id="second">
                                <tr>
                                    <td class="text-right">请输入工程名字：</td>
                                    <td class="text-left">
                                        <input type="text" name="title" class="col-sm-6" value="{!! $pjInfo->title !!}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">默认完成时间：</td>
                                    <td class="text-left">
                                        <div class="ace-spinner touch-spinner" style="width: 100px;"><div class="input-group">

                                                <input type="text" id="spinner3" name="complete" value="{!! $pjInfo->complete !!}" class="input-mini spinner-input form-control"  maxlength="3">

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">描述：</td>
                                    <td class="text-left">
                                        <textarea name="content" cols="30" rows="10" class="col-sm-6">{!! $pjInfo->content !!}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">排序：</td>
                                    <td class="text-left">
                                        <input type="text" name="listorder" class="col-sm-6" value="{!! $pjInfo->listorder !!}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"></td>
                                    <td class="text-left">
                                        <button type="submit" class="btn btn-primary btn-sm">提交</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function addProject() {
        var addP=document.getElementById('second').insertRow(1);
        addP.innerHTML = '<td class="text-right">请输入工程名字：</td>'
                        +'<td class="text-left">'
                            +'<input type="text" name="ad_url"  class="col-sm-6">'
                        +'</td>';
    }
</script>
{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('bootstrap-datetimepicker.css', 'plugins/ace/css/bootstrap-datetimepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('fuelux.spinner.min.js', 'plugins/ace/js/fuelux/fuelux.spinner.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('moment', 'plugins/ace/js/date-time/moment.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepickertime-js', 'plugins/ace/js/date-time/bootstrap-datetimepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('datefuelux-js', 'js/doc/datefuelux.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('ad-js', 'js/doc/ad.js') !!}