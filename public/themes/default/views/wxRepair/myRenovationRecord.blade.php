<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <title>装修报装</title>
    <style>
        .recordDetail {
            padding: 0 0.5rem 0 0;
        }
        .r-Record .record li>a {
            padding: 0.5rem 0.5rem 0 0.5rem;
        }
    </style>
</head>
<body>
    <div>
        <header>
            <div class="header">
                <h1>我的报装</h1>
                <a href="/renovation" class="return" style="top: 25%"><i class="icon-16"></i></a>
            </div>
        </header>
        <div style="height: 2.5rem;"></div>
    </div>
    <!-- 账户流水 mywalletRecord -->
    <div class="r-Record">
        <div class="Month">
            <!--<h1>本月 <a href="monthbill.html" class="fr">查看月账单<i class="icon-uniE926"></i></a></h1>-->
            <ul class="record">
                <li>
                    <a href="javascript:;">
                        <p class="recordUse">万象城5楼501<span class="" style="color: white;background: #0c0;padding: 0 10px; border-radius: 10px;">已通过</span></p>
                        <p class="recordTime">处理时间：5月-19日&nbsp;15:02:20</p>

                    <div class="recordDetail">
                        <dl><dt>楼　　盘：</dt><dd>万科天誉</dd></dl>
                        <dl><dt>当前状态：</dt><dd>已通过</dd></dl>
                        <dl><dt>报装时间：</dt><dd>2016-05-19&nbsp;15:02:20</dd></dl>
                        <!--<dl><dt>驳回理由：</dt><dd>资料不齐全</dd></dl>-->
                    </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        <p class="recordUse">报名活动-佛山周末采销活动<span class="" style="color: white;background: red;padding: 0 10px; border-radius: 10px;">已驳回</span></p>
                        <p class="recordTime">处理时间：5月-18日&nbsp;12:02</p>

                    <div class="recordDetail">
                        <dl><dt>楼　　盘：</dt><dd>万科天誉</dd></dl>
                        <dl><dt>当前状态：</dt><dd>已通过</dd></dl>
                        <dl><dt>报装时间：</dt><dd>2016-05-19&nbsp;15:02:20</dd></dl>
                        <dl><dt>驳回理由：</dt><dd>资料不齐全</dd></dl>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        <p class="recordUse">赠送-苏晓格<span class="" style="color: white;background: #999;padding: 0 10px; border-radius: 10px;">未审核</span></p>
                        <p class="recordTime">处理时间：5月-15日&nbsp;09:18</p>

                    <div class="recordDetail">
                        <dl><dt>楼　　盘：</dt><dd>万科天誉</dd></dl>
                        <dl><dt>当前状态：</dt><dd>已通过</dd></dl>
                        <dl><dt>报装时间：</dt><dd>2016-05-19&nbsp;15:02:20</dd></dl>
                        <!--<dl><dt>驳回理由：</dt><dd>资料不齐全</dd></dl>-->
                    </div>
                    </a>
                </li>


            </ul>
        </div>
    </div>

    <!-- 引入js资源 -->
    {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_iconfont','css/wxRepair/fonts/iconfont.css') !!}
    {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_index','css/wxRepair/index.css') !!}
    {!! Theme::asset()->container('custom-css')->usepath()->add('wx_repair_bass','css/wxRepair/bass.css') !!}

    {!! Theme::asset()->container('custom-js')->usepath()->add('wx_repair_zepto','js/wxRepair/zepto.min.js') !!}


</body>
</html>