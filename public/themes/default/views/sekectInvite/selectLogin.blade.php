<div class="position-relative login-container-bg" style="width: 100%;min-height: 978px">

    <div class="position-absolute position-ab">
        <div class="login-container">
            <div class="center">

            </div>
            <div class="space-10"></div>
            <div>
                <h4 class=" white text-center">
                    易装管理中心
                </h4>
            </div>
            <div class="space-20"></div>

            <div class="position-relative">
                <div id="login-box" class="login-box visible widget-box no-border">
                    <div class="widget-body">
                        <div class="widget-main">

                            <div class="space-6"></div>

                            <form action="{!! url('suppliesLogin') !!}" method="post">
                                {!! csrf_field() !!}
                                <fieldset>

                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right">
                                            <input type="text" class="form-control inputxt" placeholder="登录账号"
                                                   name="username" value="{!! old('username') !!}" nullmsg="请输入您的账号"
                                                   datatype="*" errormsg="请输入您的账号"/>
                                            <i class="ace-icon fa fa-user cor-grayD3"></i>
                                        </span>

                                    </label>

                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right login-error_wrong Validform-wrong-red Validform-wrong-red-height">
                                            <input type="password" class="form-control inputxt" placeholder="登录密码"
                                                   name="password" nullmsg="请输入您的密码" datatype="*6-16"
                                                   errormsg="请输入6-12个字符，支持英文、数字" autocomplete="off"
                                                   disableautocomplete/>
                                            <i class="ace-icon fa fa-key cor-grayD3"></i>
                                        </span>
                                    </label>

                                    <div class="alert-danger">
                                        @if (count($errors) > 0)
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="space"></div>

                                    <div class="clearfix">
                                        <button type="submit" class=" pull-right btn btn-block btn-primary">
                                            <span class="bigger-110 ">登　录</span>
                                        </button>
                                    </div>
                                    <div class="space-4"></div>
                                </fieldset>
                            </form>
                        </div><!-- /.widget-main -->

                    </div><!-- /.widget-body -->
                </div><!-- /.login-box -->

            </div><!-- /.position-relative -->
        </div>
    </div>
    <div class="position-absolute kppw-powered">{!! config('kppw.kppw_powered_by') !!}{!! config('kppw.kppw_version') !!}</div>
</div>

<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='../js/ace/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>

{!! Theme::asset()->container('custom-css')->usePath()->add('backstage-css', 'css/backstage/backstage.css') !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
{!! Theme::asset()->container('specific-css')->usepath()->add('validform-style','plugins/jquery/validform/css/style.css') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('validform','plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}