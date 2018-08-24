<style>
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        color: #ffa72f;
    }
</style>


<div class="container">
    <div class="row">
        <div>
            客户已建工地
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th>编号</th>
                    <th>发展的用户</th>
                    <th>注册时间</th>
                    <th>工地地址</th>
                    <th>状态</th>


                </tr>
                </thead>
                    <tbody>
                    @foreach($taskDetail as $item)
                        <tr>
                            <td>
                                <div style="text-align: center">{!! $item->id !!}</div>
                            </td>
                            <td>{!! $item->name !!}</td>
                            <td>{!! $item->created_at !!}</td>

                            <td class="hidden-480">{!! $item->title !!}</td>


                            <td class="hidden-480">
                               {{ $item->status_work }}
                            </td>

                        </tr>
                    @endforeach
                    </tbody>

            </table>

            我发展的所有客户列表
            <table id="sample-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>

                    <th>编号</th>
                    <th>发展的用户</th>
                    <th>注册时间</th>

                </tr>
                </thead>
                    <tbody>
                    @foreach($users as $item)
                        <tr>
                            <td>
                                <div style="text-align: center">{!! $item->id !!}</div>
                            </td>
                            <td>{!! $item->name !!}</td>
                            <td>{!! $item->created_at !!}</td>
                        </tr>
                    @endforeach
                    </tbody>

            </table>
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


</script>