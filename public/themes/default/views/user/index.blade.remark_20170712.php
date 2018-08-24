<style>
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        color: #ffa72f;
    }
</style>
<form class="form-horizontal" role="form" action="/user/designerSubImg" method="post" enctype="multipart/form-data"
      xmlns="http://www.w3.org/1999/html">
    {{csrf_field()}}
<div class="modal fade" id="modal-container-290991" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">
                    上传图纸
                </h4>
            </div>
            <div class="modal-body">
                    <div class="form-group">
                        <label for="plane_img" class="col-sm-2 control-label">平面图</label>
                        <div class="col-sm-10">
                            <input type="file" id="plane_img" name="plane_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="effect_img" class="col-sm-2 control-label">效果图</label>
                        <div class="col-sm-10">
                            <input type="file" id="effect_img" name="effect_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hydroelectric_img" class="col-sm-2 control-label">水电图</label>
                        <div class="col-sm-10">
                            <input type="file" id="hydroelectric_img" name="hydroelectric_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">施工图</label>
                        <div class="col-sm-10">
                            <input type="file" id="construct_img" name="construct_img"/>
                        </div>
                    </div>
                <input value="0" type="hidden" id="task_id" name="task_id"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </div>

    </div>

</div>
</form>

<form class="form-horizontal" role="form" action="/user/designerSubDeepImg" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="modal fade" id="modal-container-deep" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 200%;margin-left: -50%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        上传图纸<span style="color: red">PS：请根据实际需求上传对应的图纸（至少一份）</span>
                    </h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="plane_img" class="col-sm-4 control-label">平面布局图</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_1" name="deep_1"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="effect_img" class="col-sm-4 control-label">原始结构</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_2" name="deep_2"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hydroelectric_img" class="col-sm-4 control-label">结构拆改</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_3" name="deep_3"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">墙体地位</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_4" name="deep_4"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">平面索引</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_5" name="deep_5"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">天花布局</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_6" name="deep_6"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">墙地面铺贴图</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_7" name="deep_7"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">水电布局图</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_8" name="deep_8"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">立面布局图</label>
                        <div class="col-sm-8">
                            <input type="file" id="deep_9" name="deep_9"/>
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">材料说明</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<textarea name="deep_10" row="6" style="height: 100px;width: 100%"></textarea>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-4 control-label">材料说明</label>
                        <div class="col-sm-8">
                            <script id="editor" name="content" type="text/plain"></script>
                        </div>
                        {{--<div class="wysiwyg-editor" id="editor1">{!! htmlspecialchars_decode($exInfo->content) !!}</div>
                        <textarea name="content" id="content" style="display: none">{!! htmlspecialchars_decode($exInfo->content) !!}</textarea>--}}
                    </div>
                    <input value="0" type="hidden" id="deep_task_id" name="deep_task_id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>

        </div>

    </div>
</form>

<form class="form-horizontal" role="form" action="/user/designerSubImgAgain" method="post" enctype="multipart/form-data"
      xmlns="http://www.w3.org/1999/html">
    {{csrf_field()}}
    <div class="modal fade" id="modal-container-290991" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">
                        上传图纸
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plane_img" class="col-sm-2 control-label">平面图</label>
                        <div class="col-sm-10">
                            <input type="file" id="plane_img" name="plane_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="effect_img" class="col-sm-2 control-label">效果图</label>
                        <div class="col-sm-10">
                            <input type="file" id="effect_img" name="effect_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hydroelectric_img" class="col-sm-2 control-label">水电图</label>
                        <div class="col-sm-10">
                            <input type="file" id="hydroelectric_img" name="hydroelectric_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">施工图</label>
                        <div class="col-sm-10">
                            <input type="file" id="construct_img" name="construct_img"/>
                        </div>
                    </div>
                    <input value="0" type="hidden" id="task_id" name="task_id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>

        </div>

    </div>
</form>

{{--<form class="form-horizontal" role="form" action="/user/designerSubDeepImgAgain" method="post" enctype="multipart/form-data">--}}
    {{--{{csrf_field()}}--}}
    {{--<div class="modal fade" id="modal-container-deep-again" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content" style="width: 200%;margin-left: -50%;">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>--}}
                    {{--<h4 class="modal-title" id="myModalLabel">--}}
                        {{--上传图纸<span style="color: red">PS：请根据实际需求上传对应的图纸（至少一份）</span>--}}
                    {{--</h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}

                    {{--<div class="form-group">--}}
                        {{--<label for="plane_img" class="col-sm-4 control-label">平面布局图</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_1" name="deep_1"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="effect_img" class="col-sm-4 control-label">原始结构</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_2" name="deep_2"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="hydroelectric_img" class="col-sm-4 control-label">结构拆改</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_3" name="deep_3"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">墙体地位</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_4" name="deep_4"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">平面索引</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_5" name="deep_5"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">天花布局</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_6" name="deep_6"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">墙地面铺贴图</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_7" name="deep_7"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">水电布局图</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_8" name="deep_8"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">立面布局图</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<input type="file" id="deep_9" name="deep_9"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                    {{--<label for="construct_img" class="col-sm-4 control-label">材料说明</label>--}}
                    {{--<div class="col-sm-8">--}}
                    {{--<textarea name="deep_10" row="6" style="height: 100px;width: 100%"></textarea>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="construct_img" class="col-sm-4 control-label">材料说明</label>--}}
                        {{--<div class="col-sm-8">--}}
                            {{--<script id="editor" name="content" type="text/plain"></script>--}}
                        {{--</div>--}}
                        {{--<div class="wysiwyg-editor" id="editor1"></div>--}}
                        {{--<textarea name="content" id="deep-content" ></textarea>--}}
                    {{--</div>--}}
                    {{--<input value="0" type="hidden" id="deep_cancel_task_id" name="deep_cancel_task_id"/>--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>--}}
                    {{--<button type="submit" class="btn btn-primary">提交</button>--}}
                {{--</div>--}}
            {{--</div>--}}

        {{--</div>--}}

    {{--</div>--}}
{{--</form>--}}



<div class="container">
    <div class="row">
        <div class="col-lg-3 col-left">
            <div class="focuside clearfix nodel">
                <div class="text-center col-md-4 col-sm-6 col-lg-12">
                    <div class="s-usercenterimg focusideimg profile-picture col-sm-6 col-lg-12">
                        <img id="avatar" class='user-image editable img-responsive' src="@if(!empty(Theme::get('avatar'))) {!!  url(Theme::get('avatar')) !!} @else {!! Theme::asset()->url('images/default_avatar.png') !!} @endif" />
                    </div>
                    <div class="col-sm-6 col-lg-12">
                        <div class="space-8"></div>
                        <div class="space-20 visible-sm-block visible-md-block"></div>
                        <p class="cor-gray51 text-size18 p-space">{{ $user_data['nickname'] }}</p>
                        <div class="space-2 col-lg-12"></div>
                        {{--
                        <div class=" g-usericon">--}} {{--@if($auth_user['bank'] == true)--}} {{--
                            <a class="u-bankiconact"></a>--}} {{--@else--}} {{--
                            <a class="u-bankicon "></a>--}} {{--@endif--}} {{--@if($auth_user['realname'] == true)--}} {{--
                            <a class="u-infoiconact"></a>--}} {{--@else--}} {{--
                            <a class="u-infoicon "></a>--}} {{--@endif--}} {{--@if(Auth::User()->email_status != 2)--}} {{--
                            <a class="u-messageicon"></a>--}} {{--@else--}} {{--
                            <a class="u-messageiconact"></a>--}} {{--@endif--}} {{--@if($auth_user['alipay'] == true)--}} {{--
                            <a class="u-aliiconact"></a>--}} {{--@else--}} {{--
                            <a class="u-aliicon"></a>--}} {{--@endif--}} {{--@if($auth_user['enterprise'] == true)--}} {{--
                            <a class="u-comicon"></a>--}} {{--@else--}} {{--
                            <a class="u-comicon-no"></a>--}} {{--@endif--}} {{--
                        </div>--}}
                    </div>
                </div>
                <div class="space-14 col-lg-12"></div>
                <div class="row g-userinfo visible-lg-block col-lg-12">
                    <div class="col-xs-6 text-center g-userborr"><a><b>{{ $focus_num }}</b></a>
                        <p class="text-size14 g-usermarbot2">关注</p>
                    </div>
                    <div class="col-xs-6 text-center"><a><b>{{ $fans_num }}</b></a>
                        <p class="text-size14 g-usermarbot2">粉丝</p>
                    </div>
                    <div class="space-6 col-xs-12 visible-lg-block"></div>
                </div>
                <div class="space-14 col-lg-12"></div>
                <div class="g-userassets text-center col-md-4 col-sm-6 col-lg-12">
                    <b class="text-size18 cor-gray51">我的资产</b>
                    <div class="space-4"></div>
                    <p class="text-size20 cor-orange"><b>￥{{ $user_data['balance'] }}</b></p>
                    <div class="space-4"></div>
                    {{-- TODO 屏蔽了--}} {{--
                    <div>--}} {{--
                        <a href="/finance/cash" class="btn-big bg-orange bor-radius2 hov-bgorg88">充值</a>--}} {{--
                        <a href="/finance/cashout" class="btn-big bg-gary bor-radius2 hov-bggryb0">提现</a>--}} {{--
                    </div>--}}
                    <div class="space-10"></div>
                    {{--
                    <div class="g-usersidebor row">--}} {{--
                        <a class="text-under" href="/finance/list" target="_blank">查看明细></a>--}} {{--
                        <p class="space-14"></p>--}} {{--
                    </div>--}}
                </div>
                <div class="space-14 col-lg-12 visible-lg-block"></div>

            </div>
        </div>
        <div class="col-lg-9 g-side2 martop20 col-left">
            <div class="g-userhint">
                <div class="g-usertitname cor-gray51 text-size14"><a href="">{{ $user_data['nickname'] }}</a>，这里是你的【主页】！</div>
                <div class="space-6"></div>
                <p class="cor-gray51 text-size14">结交各路豪杰的地盘儿。</p>
                <div class="space-14"></div>
            </div>
            <div class="space-10"></div>
            {{--被驳回的任务--}}
            <div class="g-userhint g-userlist tabbable">
                <div class="clearfix g-userlisthead">
                    <ul class="pull-left text-size16 nav nav-tabs">
                        <li class="active"><a href="#useraccept"   data-toggle="tab">被驳回的任务</a></li>

                        {{--<div class="pull-left">|</div>--}}
                        {{--<li><a onclick="changeurl($(this))" url="/user/myTasksList" href="#userrelease" data-toggle="tab">历史记录</a></li>--}}
                    </ul>
                    {{--TODO--}}
                    {{--<a id="more-task" class="pull-right hov-corblue2f" href="/user/acceptTasksList" target="_blank">更多</a>--}}
                </div>
                <div class="tab-content">
                    @if(count($task_cancel)>0)
                        <ul id="useraccept" class="{{ (count($my_task)>0)?'':'g-userlistno' }} tab-pane g-releasetask g-releasnopt g-releasfirs fade active in dialogs">
                            @foreach($task_cancel as $v)
                                <li class="row width590">
                                    <div class="col-sm-1 col-xs-2 u-headimg"><img class="user-image2" src="{{ $domain.'/'.$v['avatar'] }}" onerror="onerrorImage('{{ Theme::asset()->url('images/defauthead.png')}}',$(this))"></div>
                                    <div class="col-sm-11 col-xs-10 usernopd">
                                        <div class="col-sm-9 col-xs-8">
                                            <div class="text-size14 cor-gray51">
                                                <a class="cor-blue42" href="#">
                                                    {{--/task/{{ $v['id'] }}--}}
                                                    {{ $v['title'] }}
                                                </a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{{ $v['status_text'] }}
                                            </div>
                                            <div class="space-6"></div>
                                            <p class="cor-gray87">
                                                <i class="ace-icon fa fa-user bigger-110 cor-grayd2"></i>
                                                {{ str_limit($v['nickname'],10) }}&nbsp;&nbsp;&nbsp;

                                                <i class="fa fa-unlock-alt cor-grayd2"></i>
                                                {{ $v['bounty_status']==1?'已托管赏金':'待托管赏金' }}
                                            </p>
                                            <div class="space-6"></div>
                                            <p class="cor-gray51 userrelp p-space">{!! strip_tags(htmlspecialchars_decode($v['desc'])) !!}</p>
                                            <div class="space-2"></div>
                                            {{--
                                            <div class="g-userlabel"><a href="">{{ $v['category_name'] }}</a>--}} {{--@if($v['region_limit']==1)--}} {{--
                                        <a href="">{{ CommonClass::getRegion($v['city']) }}</a>--}} {{--@endif--}} {{--
                                    </div>--}}
                                        </div>
                                        <div class="col-sm-3 col-xs-4 text-right hiden590">
                                            @if($v['step'] == 'deep')
                                            {{--<a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-deep-again" onclick="getCancelDeepTaskId({{ $v['id'] }})"   id="modal-deep-again" role="button" data-toggle="modal">重新上传</a>--}}
                                                <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-deep" onclick="getDeepTaskId({{ $v['id'] }})"   id="modal-deep" role="button" data-toggle="modal">去上传</a>
                                            @elseif($v['step'] == 'first')
                                                {{--<a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-first-again" onclick="getCancelFirstTaskId({{ $v['id'] }})"   id="modal-first-again" role="button" data-toggle="modal">重新上传</a>--}}
                                                <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-290991" onclick="getTaskId({{ $v['id'] }})"   id="modal-290991" role="button" data-toggle="modal">去上传</a>
                                            @endif
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="g-userborbtm"></div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(count($my_task)==0)
                        <ul id="useraccept" class="g-userlistno tab-pane g-releasetask g-releasnopt g-releasfirs fade active in">
                            <li class="g-usernoinfo g-usernoinfo-noinfo">暂无要重新上传的任务哦！</li>
                        </ul>@endif
                </div>
            </div></br><br/>



            {{--初步设计--}}
            <div class="g-userhint g-userlist tabbable">
                <div class="clearfix g-userlisthead">
                    <ul class="pull-left text-size16 nav nav-tabs">
                        <li class="active"><a href="#useraccept" onclick="changeurl($(this))" url="/user/acceptTasksList" data-toggle="tab">上传初步设计图纸</a></li>

                        {{--<div class="pull-left">|</div>--}}
                        {{--<li><a onclick="changeurl($(this))" url="/user/myTasksList" href="#userrelease" data-toggle="tab">历史记录</a></li>--}}
                    </ul>
                    {{--TODO--}}
                    {{--<a id="more-task" class="pull-right hov-corblue2f" href="/user/acceptTasksList" target="_blank">更多</a>--}}
                </div>
                <div class="tab-content">
                    @if(count($my_task)>0)
                        <ul id="useraccept" class="{{ (count($my_task)>0)?'':'g-userlistno' }} tab-pane g-releasetask g-releasnopt g-releasfirs fade active in dialogs">
                            @foreach($my_task as $v)
                                <li class="row width590">
                                    <div class="col-sm-1 col-xs-2 u-headimg"><img class="user-image2" src="{{ $domain.'/'.$v['avatar'] }}" onerror="onerrorImage('{{ Theme::asset()->url('images/defauthead.png')}}',$(this))"></div>
                                    <div class="col-sm-11 col-xs-10 usernopd">
                                        <div class="col-sm-9 col-xs-8">
                                            <div class="text-size14 cor-gray51">
                                                <a class="cor-blue42" href="#">
                                                    {{--/task/{{ $v['id'] }}--}}
                                                    {{ $v['title'] }}
                                                </a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{{ $v['status_text'] }}
                                            </div>
                                            <div class="space-6"></div>
                                            <p class="cor-gray87">
                                                <i class="ace-icon fa fa-user bigger-110 cor-grayd2"></i>
                                                {{ str_limit($v['nickname'],10) }}&nbsp;&nbsp;&nbsp;

                                                <i class="fa fa-unlock-alt cor-grayd2"></i>
                                                {{ $v['bounty_status']==1?'已托管赏金':'待托管赏金' }}
                                            </p>
                                            <div class="space-6"></div>
                                            <p class="cor-gray51 userrelp p-space">{!! strip_tags(htmlspecialchars_decode($v['desc'])) !!}</p>
                                            <div class="space-2"></div>
                                            {{--
                                            <div class="g-userlabel"><a href="">{{ $v['category_name'] }}</a>--}} {{--@if($v['region_limit']==1)--}} {{--
                                        <a href="">{{ CommonClass::getRegion($v['city']) }}</a>--}} {{--@endif--}} {{--
                                    </div>--}}
                                        </div>
                                        <div class="col-sm-3 col-xs-4 text-right hiden590">
                                            <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-290991" onclick="getTaskId({{ $v['id'] }})"   id="modal-290991" role="button" data-toggle="modal">去上传</a>

                                        </div>
                                        <div class="col-xs-12">
                                            <div class="g-userborbtm"></div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                        @if(count($my_task)==0)
                    <ul id="useraccept" class="g-userlistno tab-pane g-releasetask g-releasnopt g-releasfirs fade active in">
                        <li class="g-usernoinfo g-usernoinfo-noinfo">暂无要上传的任务哦！</li>
                    </ul>@endif
                </div>
            </div></br><br/>


            {{--深化设计--}}
            <div class="g-userhint g-userlist tabbable">
                <div class="clearfix g-userlisthead">
                    <ul class="pull-left text-size16 nav nav-tabs">
                        <li class="active"><a href="#useraccept" onclick="changeurl($(this))" url="/user/acceptTasksList" data-toggle="tab">上传深化设计图纸</a></li>

                        {{--<div class="pull-left">|</div>--}}
                        {{--<li><a onclick="changeurl($(this))" url="/user/myTasksList" href="#userrelease" data-toggle="tab">历史记录</a></li>--}}
                    </ul>
                    {{--TODO--}}
                    {{--<a id="more-task" class="pull-right hov-corblue2f" href="/user/acceptTasksList" target="_blank">更多</a>--}}
                </div>
                <div class="tab-content">
                    @if(count($deep_arr)>0)
                        <ul id="useraccept" class="{{ (count($deep_arr)>0)?'':'g-userlistno' }} tab-pane g-releasetask g-releasnopt g-releasfirs fade active in dialogs">
                            @foreach($deep_arr as $v)
                                <li class="row width590">
                                    <div class="col-sm-1 col-xs-2 u-headimg"><img class="user-image2" src="{{ $domain.'/'.$v['avatar'] }}" onerror="onerrorImage('{{ Theme::asset()->url('images/defauthead.png')}}',$(this))"></div>
                                    <div class="col-sm-11 col-xs-10 usernopd">
                                        <div class="col-sm-9 col-xs-8">
                                            <div class="text-size14 cor-gray51">
                                                <a class="cor-blue42" href="#">
                                                    {{--/task/{{ $v['id'] }}--}}
                                                    {{ $v['title'] }}
                                                </a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{{ $v['status_text'] }}
                                            </div>
                                            <div class="space-6"></div>
                                            <p class="cor-gray87">
                                                <i class="ace-icon fa fa-user bigger-110 cor-grayd2"></i>
                                                {{ str_limit($v['nickname'],10) }}&nbsp;&nbsp;&nbsp;

                                                <i class="fa fa-unlock-alt cor-grayd2"></i>
                                                {{ $v['bounty_status']==1?'已托管赏金':'待托管赏金' }}
                                            </p>
                                            <div class="space-6"></div>
                                            <p class="cor-gray51 userrelp p-space">{!! strip_tags(htmlspecialchars_decode($v['desc'])) !!}</p>
                                            <div class="space-2"></div>
                                            {{--
                                            <div class="g-userlabel"><a href="">{{ $v['category_name'] }}</a>--}} {{--@if($v['region_limit']==1)--}} {{--
                                        <a href="">{{ CommonClass::getRegion($v['city']) }}</a>--}} {{--@endif--}} {{--
                                    </div>--}}
                                        </div>
                                        <div class="col-sm-3 col-xs-4 text-right hiden590">
                                            <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-deep" onclick="getDeepTaskId({{ $v['id'] }})"   id="modal-deep" role="button" data-toggle="modal">去上传</a>

                                        </div>
                                        <div class="col-xs-12">
                                            <div class="g-userborbtm"></div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(count($deep_arr)==0)
                        <ul id="useraccept" class="g-userlistno tab-pane g-releasetask g-releasnopt g-releasfirs fade active in">
                            <li class="g-usernoinfo g-usernoinfo-noinfo">暂无要上传的任务哦！</li>
                        </ul>@endif
                </div>
            </div>
        </div>
    </div>
</div>
{!! Theme::widget('ueditor')->render() !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('froala_editor', 'css/usercenter/usercenter.css') !!} {!! Theme::asset()->container('custom-js')->usepath()->add('userindex', 'js/doc/userindex.js') !!} {!! Theme::asset()->container('custom-js')->usepath()->add('more-js', 'js/doc/more.js') !!} {!! Theme::widget('avatar')->render() !!}
<script>
    function getTaskId(obj){
        document.getElementById('task_id').value = obj;
    }
    function getDeepTaskId(obj){
        document.getElementById('deep_task_id').value = obj;
    }
    function getCancelDeepTaskId(obj){
        document.getElementById('deep_cancel_task_id').value = obj;
    }
    function getCancelFirstTaskId(obj){
        document.getElementById('first_cancel_task_id').value = obj;
    }
</script>