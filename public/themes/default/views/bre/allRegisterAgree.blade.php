
<style>
    header,nav{display: none;}
    section{height: 100%}
    .col-xs-12{
        text-align: center;
        font-size: 2rem;
        margin: 2.6rem auto;
        color: #00a0e9;
    }
</style>

</div>
<div class="row footer-link-area clearfix">
    <!-- main -->
    <div class="col-md-12 clearfix col-left">
        <div class="footer-link-area-detail area-detail-main clearfix">
            <h2 class="footer-link-area-detail-title" style="text-align: center">易装协议列表</h2>
            <div class="tos-main-words clearfix">
                @foreach($agreeList as $key => $value)
                <div class="col-xs-12">
                    <a href="{!! $value->code_name !!}">{!! $value->name !!}</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
{!! Theme::asset()->container('custom-css')->usepath()->add('footerLink','css/footerLink.css') !!}