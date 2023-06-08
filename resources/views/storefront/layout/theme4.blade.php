<!DOCTYPE html>
<html lang="en" dir="{{env('SITE_RTL') == 'on'?'rtl':''}}">
@php
    $userstore = \App\Models\UserStore::where('store_id', $store->id)->first();
    $settings   =\DB::table('settings')->where('name','company_favicon')->where('created_by', $store->id)->first();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ucfirst(env('APP_NAME'))}} - {{ucfirst($store->tagline)}}">
    <meta name="keywords" content="{{$store->metakeyword}}">
    <meta name="description" content="{{$store->metadesc}}">

    <title>@yield('page-title') - {{($store->tagline) ?  $store->tagline : config('APP_NAME', ucfirst($store->name))}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset(Storage::url('uploads/logo/').(!empty($settings->value)?$settings->value:'favicon.png'))}}" type="image/png">

    <link rel="stylesheet" href="{{asset('custom/libs/@fortawesome/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme4/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset('custom/libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme4/css/purpose.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme4/css/storego.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme4/css/'.(!empty($store->store_theme) ? $store->store_theme : 'green-color.css'))}}">
    <link rel="stylesheet" href="{{asset('custom/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">
    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    @stack('css-page')
</head>
<body>
@php
    if(!empty(session()->get('lang')))
    {

        $currantLang = session()->get('lang');
    }else{
        $currantLang = $store->lang;
    }

    $languages=\App\Models\Utility::languages();
    $storethemesetting=\App\Models\Utility::demoStoreThemeSetting($store->id,$store->theme_dir);
@endphp
<header class="header home-hader" id="header-main">
    <!-- Main navbar -->
    <nav class="navbar navbar-main navbar-expand-lg navbar-transparent {{(Request::segment(4) != '')?'bg-primary':''}}{{(Request::segment(1) == 'page')?'bg-primary':''}}{{(Request::segment(1) == 'store-blog')?'bg-primary':''}}" id="navbar-main">
        <div class="container px-lg-0">
            <!-- Logo -->
            <a class="navbar-brand mr-lg-5" href="{{route('store.slug',$store->slug)}}">
                @if(!empty($store->logo))
                    <img alt="Image placeholder" class="desktop-logo" src="{{asset(Storage::url('uploads/store_logo/'.($store->logo)))}}" id="navbar-logo" style="height: 40px;">
                    <img alt="Image placeholder" class="mobile-logo" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo">
                @else
                    <img alt="Image placeholder" class="desktop-logo" src="{{asset(Storage::url('uploads/store_logo/logo4.png'))}}" id="navbar-logo" style="height: 40px;">
                    <img alt="Image placeholder" class="mobile-logo" src="{{asset(Storage::url('uploads/store_logo/logo4.png'))}}" class="mobile-view" id="navbar-logo">
                @endif
            </a>
            <!-- Navbar collapse trigger -->
            <button class="navbar-toggler pr-0" type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar nav -->
            <div class="collapse navbar-collapse" id="navbar-main-collapse">
                <ul class="navbar-nav ml-lg-auto align-items-lg-center">
                    <!-- Home - Overview  -->
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('store.slug',$store->slug)}}">{{ucfirst($store->name)}}</a>
                    </li>
                    @if(!empty($page_slug_urls))
                        @foreach($page_slug_urls as $k=>$page_slug_url)
                            @if($page_slug_url->enable_page_header == 'on')
                                <li class="nav-item ">
                                    <a class="nav-link" href="{{env('APP_URL') . '/page/' . $page_slug_url->slug}}">{{ucfirst($page_slug_url->name)}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                    @if($store['blog_enable'] == "on" && !empty($blog))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('store.blog',$store->slug)}}">{{__('Blog')}}</a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav align-items-lg-center ml-lg-auto nav-my-store">
                    <li class="nav-item search-icon">
                        <a href="#" class="nav-heart btn  ml-2 mr-2 bg--gray rounded-pill hover-translate-y-n3 " data-action="omnisearch-open" data-target="#omnisearch">
                            <i class="fas fa-search  border-dark"></i>
                        </a>
                    </li>
                    @if(Utility::CustomerAuthCheck($store->slug)==true)
                    <li class="nav-item">
                        <a href="{{route('store.wishlist',$store->slug)}}" class="nav-heart btn  ml-2 mr-2 bg--gray rounded-pill hover-translate-y-n3 ">
                            <i class="fas fa-heart border-dark"></i>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{route('store.cart',$store->slug)}}" class="nav-heart btn  ml-2 mr-2 bg--gray rounded-pill hover-translate-y-n3 ">
                            <i class="fas fa-shopping-basket mr-0"></i>
                            <span class="badge badge-pill badge-floating border-dark shoping_counts " id="shoping_counts"   >
                                    {{!empty($total_item)?$total_item:'0'}}
                                </span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-heart btn  ml-2 mr-2 bg--gray rounded-pill hover-translate-y-n3" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <!-- <i class="fas fa-language"></i> -->
                            {{Str::upper($currantLang)}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            @foreach($languages as $language)
                                <a href="{{route('change.languagestore',[$store->slug,$language])}}" class="dropdown-item @if($language == $currantLang) active-language @endif">
                                    <span> {{Str::upper($language)}}</span>
                                </a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('store.categorie.product',[$store->slug,'Start shopping'])}}" class=" btn mr-2 bg--gray rounded-pill hover-translate-y-n3  big-btn bg-white ml-2 rounded-pill hover-translate-y-n3 shopping_btn">
                            <span class="nav-text">{{__('Start shopping')}}</span>
                            <i class="fas fa-shopping-basket"></i>
                        </a>
                    </li>

                    @if(Utility::CustomerAuthCheck($store->slug)==true)
                        <div class="drop-down w-auto">
                         <div id="dropDown" class="drop-down__button ">
                           <a class="nav-link nav-link btn ml-2 bg--gray hover-translate-y-n3 icon-font login_btn btn-black t-black">{{ucFirst(Auth::guard('customers')->user()->name)}}
                           <i class="fas fa-sort-down ml-2 mr-0 down_icon"></i>
                       </a>
                         </div>
                         <div class="drop-down__menu-box">
                            <ul class="drop-down__menu">
                                <li data-name="profile" class="drop-down__item">
                                     <a href="{{route('store.slug',$store->slug)}}" class="nav-link">
                                        {{__('My Dashboard')}}
                                    </a>
                                 </li>
                                <li data-name="activity" class="drop-down__item">
                                    <a href="" data-size="lg"
data-url="{{route('customer.profile',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt(Auth::guard('customers')->user()->id)])}}" data-ajax-popup="true" data-title="{{__('Edit Profile')}}"  data-toggle="modal"  class="nav-link">

                                        {{__('My Profile')}}
                                    </a>
                                </li>
                                <li data-name="activity" class="drop-down__item">
                                 <a href="{{route('customer.home',$store->slug)}}"  class="nav-link">

                                        {{__('My Orders')}}
                                    </a>
                                </li>
                                <li>
                                @if( Utility::CustomerAuthCheck($store->slug) == false)
                                        <a href="{{route('customer.login',$store->slug)}}"  class="nav-link">
                                            {{__('Sign in')}}
                                        </a>
                                    @else
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('customer-frm-logout').submit();"  class="nav-link">
                                            {{__('Logout')}}
                                        </a>
                                        <form id="customer-frm-logout" action="{{ route('customer.logout',$store->slug)  }}" method="POST" class="d-none">
                                            {{ csrf_field() }}
                                        </form>
                                    @endif
                                </li>
                            </ul>
                        </div>
                       </div>
                    @else
                     <li class="nav-item">
                        <a href="{{route('customer.login',$store->slug)}}" class="btn big-btn bg-white ml-2 rounded-pill hover-translate-y-n3 big-btn ml-2 rounded-pill hover-translate-y-n3 text-center login_btn_4">{{__('Log in')}}</a>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
    </nav>
</header>
<div id="omnisearch" class="omnisearch">
    <div class="container">
        <!-- Search form -->
        <form class="omnisearch-form" action="{{route('store.categorie.product',[$store->slug,'Start shopping'])}}" method="get">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search_data" class="form-control form-control-flush" placeholder="Type your product...">
                    {{--                    <input type="text" class="form-control" placeholder="Type and hit enter ...">--}}
                </div>
            </div>
        </form>
    </div>
</div>
@yield('content')

@if($storethemesetting['enable_footer_note'] == 'on' || $storethemesetting['enable_footer'] == 'on')
    <footer id="footer-main" class="mt-0">
        <div class="container">
            <div class="row pt-md top-footer delimiter-top">
                <div class="col-lg-12 mb-2 mb-lg-0 text-center">
                    @if($storethemesetting['enable_footer_note'] == 'on')
                        <a href="{{route('store.slug',$store->slug)}}">
                            @if(!empty($storethemesetting['footer_logo']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['footer_logo']))
                                <img class="mb-3" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['footer_logo4'])?$storethemesetting['footer_logo']:'footer_logo4 .png')))}}" alt="Footer logo" style="height: 40px;">
                            @else
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/footer_logo.png'))}}" class="mb-3" style="height: 40px;">
                            @endif

                        </a>
                    @endif
                    @if($storethemesetting['enable_footer'] == 'on')
                        <ul class="nav justify-content-center justify-content-md-center mt-4">
                            @if(!empty($storethemesetting['whatsapp']))
                                <li class="nav-item">
                                    <a class="nav-link" href="https://wa.me/{{$storethemesetting['whatsapp']}}" target="_blank">
                                        <i class="fab fa-whatsapp t-white"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['facebook']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['facebook']}}" target="_blank">
                                        <i class="fab fa-facebook-square t-white"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['twitter']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['twitter']}}" target="_blank">
                                        <i class="fab fa-twitter-square t-white"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['email']))
                                <li class="nav-item">
                                    <a class="nav-link" href="mailto:{{$storethemesetting['email']}}">
                                        <i class="far fa-envelope t-white"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['instagram']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['instagram']}}" target="_blank">
                                        <i class="fab fa-instagram t-white"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['youtube']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['youtube']}}" target="_blank">
                                        <i class="fab fa-youtube t-white"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
            <div class="row align-items-center justify-content-md-between py-4 delimiter-top">
                <div class="col-md-12 text-center">
                    @if($storethemesetting['enable_footer_note'] == 'on')
                        <ul class="list-unstyled f-list">
                            <li><a class="t-white" href="{{$storethemesetting['quick_link_url1']}}">{{__($storethemesetting['quick_link_name1'])}}</a></li>
                            <li><a class="t-white" href="{{$storethemesetting['quick_link_url21']}}">{{__($storethemesetting['quick_link_name21'])}}</a></li>
                            <li><a class="t-white" href="{{$storethemesetting['quick_link_url31']}}">{{__($storethemesetting['quick_link_name31'])}}</a></li>
                            <li><a class="t-white" href="{{$storethemesetting['quick_link_url41']}}">{{__($storethemesetting['quick_link_name41'])}}</a></li>
                        </ul>
                    @endif
                    @if($storethemesetting['enable_footer'] == 'on')
                        <div class="copyright t-white font-weight-bold text-center ">
                            {{$storethemesetting['footer_note']}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </footer>
@endif

<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <div class="modal-title">
                    <h6 class="mb-0" id="modelCommanModelLabel"></h6>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

{{--<script src="{{asset('assets/theme1/js/all.min.js')}}"></script>--}}
<script src="{{asset('assets/theme1/js/purpose.core.js')}}"></script>
<script src="{{ asset('custom/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/theme1/js/swiper.min.js')}}"></script>
<script src="{{asset('assets/theme1/js/purpose.js')}}"></script>
<script>
var dataTabelLang = {
        paginate: {
        previous: "{{('Previous')}}",
        next: "{{('Next')}}"
        },
        lengthMenu: "{{('Show')}} MENU {{('entries')}}",
        zeroRecords: "{{('No data available in table')}}",
        info: "{{('Showing')}} START {{('to')}} END {{('of')}} TOTAL {{('entries')}}",
        infoEmpty: " ",
        search: "{{('Search:')}}"
}
</script>
<script src="{{asset('custom/js/custom.js')}}"></script>
<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>


@if(App\Models\Utility::getValByName('gdpr_cookie') == 'on')

    <script type="text/javascript">

        var defaults = {
            'messageLocales': {
                /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                'en': "{{App\Models\Utility::getValByName('cookie_text')}}"
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'cookieNoticePosition': 'bottom',
            'learnMoreLinkEnabled': false,
            'learnMoreLinkHref': '/cookie-banner-information.html',
            'learnMoreLinkText': {
                'it': 'Saperne di più',
                'en': 'Learn more',
                'de': 'Mehr erfahren',
                'fr': 'En savoir plus'
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'expiresIn': 30,
            'buttonBgColor': '#d35400',
            'buttonTextColor': '#fff',
            'noticeBgColor': '#000',
            'noticeTextColor': '#fff',
            'linkColor': '#009fdd'
        };
    </script>
    <script src="{{ asset('custom/js/cookie.notice.js') }}"></script>
@endif

@stack('script-page')
@if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif
@if(Session::has('error'))
    <script>
        show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif
@php
    $store_settings = \App\Models\Store::where('slug',$store->slug)->first();
@endphp
<script async src="https://www.googletagmanager.com/gtag/js?id={{$store_settings->google_analytic}}"></script>

{!! $store_settings->storejs !!}

<script>


    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', '{{ $store_settings->google_analytic }}');




</script>

<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{$store_settings->fbpixel_code}}');
  fbq('track', 'PageView');
</script>

<script type="text/javascript">
    $(function() {
      $(".drop-down__button ").on("click", function(e) {
        $(".drop-down").addClass("drop-down--active");
        e.stopPropagation()
      });
      $(document).on("click", function(e) {
        if ($(e.target).is(".drop-down") === false) {
          $(".drop-down").removeClass("drop-down--active");
        }
      });
    });
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=0000&ev=PageView&noscript={{$store_settings->fbpixel_code}}"/></noscript>

</body>
</html>
