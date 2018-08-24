<div>
    <div>
        <div>
            <h3 class="header smaller lighter blue mg-bottom20 mg-top12">添加收费单</h3>

            <form class="form-horizontal" action="/manage/updateCharge" method="post">
                {!! csrf_field() !!}
				<input type="hidden" name="id" value="{!! $id !!}">
                <div class="widget-body">
                    <div class="">
                        <div class="g-backrealdetails clearfix bor-border">
                            <table class="table table-hover">
                                <tbody id="second">
                                <tr>
                                    <td class="text-right">请输入收费单名字：</td>
                                    <td class="text-left">
                                        <input type="text" name="title" class="col-sm-6" value="{!! $cgInfo->title !!}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">基本费用（仅接受数字输入）：</td>
                                    <td class="text-left">
                                        <input type="text" name="price" class="col-sm-6" value="{!! $cgInfo->price !!}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">备注：</td>
                                    <td class="text-left">
                                        <textarea name="content" cols="30" rows="10" class="col-sm-6">{!! $cgInfo->content !!}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">排序：</td>
                                    <td class="text-left">
                                        <input type="text" name="listorder" class="col-sm-6" value="{!! $cgInfo->listorder !!}">
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
{!! Theme::asset()->container('custom-css')->usePath()->add('backstage', 'css/backstage/backstage.css') !!}
{!! Theme::asset()->container('specific-css')->usePath()->add('bootstrap-datetimepicker.css', 'plugins/ace/css/bootstrap-datetimepicker.css') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('fuelux.spinner.min.js', 'plugins/ace/js/fuelux/fuelux.spinner.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('moment', 'plugins/ace/js/date-time/moment.min.js') !!}
{!! Theme::asset()->container('specific-js')->usePath()->add('datepickertime-js', 'plugins/ace/js/date-time/bootstrap-datetimepicker.min.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('datefuelux-js', 'js/doc/datefuelux.js') !!}
{!! Theme::asset()->container('custom-js')->usePath()->add('ad-js', 'js/doc/ad.js') !!}