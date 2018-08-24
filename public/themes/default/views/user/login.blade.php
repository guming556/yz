




<div class="position-relative login-container-bg" style="width: 100%;min-height: 978px">
    {{--<img class="img-responsive" src="{!! Theme::asset()->url('images/backlogin.png') !!}" alt="客客族">--}}

    <div class="position-absolute position-ab">
        <div class="login-container">
            <div class="center">
                {{--<img src="{!! Theme::asset()->url('images/loginlogo.png') !!}" alt="">--}}
            </div>
            <div class="space-10"></div>
            <div>
                <h4 class=" white text-center">
                    易装设计师后台管理中心
                </h4>
            </div>
            <div class="space-20"></div>

            <div class="position-relative">
                <div id="login-box" class="login-box visible widget-box no-border">
                    <div class="widget-body">
                        <div class="widget-main">

                            <div class="space-6"></div>

                            <form action="{!! url('login') !!}" method="post">
                                {!! csrf_field() !!}
                                <fieldset>

                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right">
                                            <input type="text" class="form-control inputxt" placeholder="登录账号" name="username" value="{!! old('username') !!}" nullmsg="请输入您的账号" datatype="*" errormsg="请输入您的账号"/>
                                            <i class="ace-icon fa fa-user cor-grayD3"></i>
                                        <span class="Validform_checktip validform-login-form login-validform-static">
                                                <span class="login-red">{!! $errors->first('username') !!}</span>
                                            </span>
                                        </span>
                                        <div class="error_wrong">{!! $errors->first('username') !!}</div>
                                    </label>

                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right login-error_wrong Validform-wrong-red Validform-wrong-red-height">
                                            <input type="password" class="form-control inputxt" placeholder="登录密码" name="password" nullmsg="请输入您的密码" datatype="*6-16" errormsg="请输入6-12个字符，支持英文、数字" autocomplete="off" disableautocomplete/>
                                            <i class="ace-icon fa fa-key cor-grayD3"></i>
                                            <span class="Validform_checktip validform-login-form login-validform-static">
                                                <span class="login-red">{!! $errors->first('password') !!}</span>
                                            </span>
                                        </span>
                                        <div class="error_wrong">{!! $errors->first('password') !!}</div>
                                    </label>

                                    @if(!empty($errors->first('password')) || !empty($errors->first('code')))
                                        <div class="clearfix">
                                            <label class="inline">
                                                <input type="text" class="form-control form-input-code" placeholder="验证码" name="code">

                                                <div class="error_wrong">{!! $errors->first('code') !!}</div>
                                            </label>
                                            <a href="javascript:;"><img src="{!! $code !!}" alt="" class="pull-right" onclick="flushCode(this)"></a>
                                        </div>
                                    @endif

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
    if('ontouchstart' in document.documentElement) document.write("<script src='../js/ace/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>

{!! Theme::asset()->container('custom-css')->usePath()->add('backstage-css', 'css/backstage/backstage.css') !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('main-js', 'js/main.js') !!}
{!! Theme::asset()->container('specific-css')->usepath()->add('validform-style','plugins/jquery/validform/css/style.css') !!}
{!! Theme::asset()->container('specific-js')->usepath()->add('validform','plugins/jquery/validform/js/Validform_v5.3.2_min.js') !!}