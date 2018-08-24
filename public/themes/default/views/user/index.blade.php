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
                        <label for="effect_img" class="col-sm-2 control-label">参考效果图</label>
                        <div class="col-sm-10">
                            <input type="file" id="effect_img" name="effect_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hydroelectric_img" class="col-sm-2 control-label">参考效果图</label>
                        <div class="col-sm-10">
                            <input type="file" id="hydroelectric_img" name="hydroelectric_img"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">参考效果图</label>
                        <div class="col-sm-10">
                            <input type="file" id="construct_img" name="construct_img"/>
                        </div>
                    </div>
                <input value="0" type="hidden" id="task_id" name="task_id"/>
                <input value="0" type="hidden" id="step_status" name="step_status"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </div>

    </div>

</div>
</form>
<style>
    .user-image-1{
        width: 80px;
        height: 80px;
    }
</style>
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
                        <label for="plane_img" class="col-sm-2 control-label">平面布局图</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_1" id="deep_1" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_1" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="effect_img" class="col-sm-2 control-label">原始结构</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_2" id="deep_2" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_2" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="hydroelectric_img" class="col-sm-2 control-label">结构拆改</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_3" id="deep_3" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_3" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">墙体地位</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_4" id="deep_4" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_4" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">平面索引</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_5" id="deep_5" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_5" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">天花布局</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_6" id="deep_6" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_6" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">墙地面铺贴图</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_7" id="deep_7" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_7" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">水电布局图</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_8" id="deep_8" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_8" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>
                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">效果图</label>
                        <div class="col-sm-3">
                            <div class="memberdiv pull-left">
                                <div class="position-relative">
                                    <input multiple="" type="file" name="deep_9" id="deep_9" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2"><img id="img_deep_9" src="{!! Theme::asset()->url('images/default_avatar.png') !!}" width="50" height="50"></div>
                    </div>

                    <div class="form-group">
                        <label for="construct_img" class="col-sm-2 control-label">材料说明</label>
                        <div class="col-sm-9">
                            <textarea name="content" id="deep_10" rows="12" cols="120"></textarea>
                            {{--<script id="editor" name="content" type="text/plain"></script>--}}
                        </div>
                        {{--<div class="wysiwyg-editor" id="editor1">{!! htmlspecialchars_decode($exInfo->content) !!}</div>
                        <textarea name="content" id="content" style="display: none">{!! htmlspecialchars_decode($exInfo->content) !!}</textarea>--}}
                    </div>
                    <input value="0" type="hidden" id="deep_task_id" name="deep_task_id"/>
                    <input value="0" type="hidden" id="deep_step_status" name="deep_step_status"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>

        </div>

    </div>
</form>



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
                    <div class="space-10"></div>
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
            <div class="g-userhint g-userlist tabbable">
                <div class="clearfix g-userlisthead">
                    <ul class="pull-left text-size16 nav nav-tabs">
                        <li class="active"><a href="#useraccept"   data-toggle="tab">任务列表</a></li>
                    </ul>
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

                                        </div>
                                        <div class="col-sm-3 col-xs-4 text-right hiden590">
                                            @if($v['is_first_upload'])
                                                <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-290991" onclick="getTaskId({{ $v['id'] }} , 2)"   id="modal-deep" role="button" data-toggle="modal">去上传</a>
                                            @endif
                                            @if($v['is_deep_upload'])
                                                <a class="btn-big bg-blue bor-radius2 hov-blue1b" href="#modal-container-deep" onclick="getDeepTaskId({{ $v['id'] }} , 2)"   id="modal-290991" role="button" data-toggle="modal">去上传</a>
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
                            <li class="g-usernoinfo g-usernoinfo-noinfo">暂无任务哦！</li>
                        </ul>@endif
                </div>
            </div></br><br/>
        </div>
    </div>
</div>
{{--{!! Theme::widget('ueditor')->render() !!}--}}
{!! Theme::asset()->container('custom-css')->usepath()->add('froala_editor', 'css/usercenter/usercenter.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('userindex', 'js/doc/userindex.js') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('more-js', 'js/doc/more.js') !!}
{!! Theme::widget('avatar')->render() !!}

{!! Theme::asset()->container('custom-js')->usePath()->add('deepStep-js', 'js/doc/deepStep.js') !!}

<script>
    var task_arr = "{{ $task_arr }}";
    var handleTaskArr = JSON.parse(task_arr.replace(/&quot;/gi, '"'));

    var first_arr = "{{ $first_arr }}";
    var handleFirstArr = JSON.parse(first_arr.replace(/&quot;/gi, '"'));

//    console.log(handleTaskArr[8][1]);
//    console.log(handleFirstArr);

    function getTaskId(obj , step = 0 ){
        document.getElementById('task_id').value = obj;
        document.getElementById('step_status').value = step;
        for(var i=0;i<=8;i++){
            if( typeof(handleFirstArr[obj][i]) != "undefined" ){
                document.getElementById('img_deep_'+i).src = handleFirstArr[obj][i]['url'];
            }
        }

    }


    function getDeepTaskId(obj , step = 0 ){
        document.getElementById('deep_task_id').value = obj;
        document.getElementById('deep_step_status').value = step;
        console.log(step)
        var arr = handleTaskArr[obj];
        for(var i=0;i<arr.length;i++){
            if( arr[i]['name'] == 'deep_10' ){
                document.getElementById('deep_10').value = arr[i]['desc'];
            }else{
                document.getElementById('img_'+arr[i]['name']).src = arr[i]['url'];
            }
        }
    }

</script>