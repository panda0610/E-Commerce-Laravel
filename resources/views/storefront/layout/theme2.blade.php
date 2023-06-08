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
    <link rel="stylesheet" href="{{asset('assets/theme2/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset('custom/libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme2/css/purpose.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme2/css/storego.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme2/css/'.(!empty($store->store_theme) ? $store->store_theme : 'green-color.css'))}}">
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

<header class="header {{(Request::segment(3)  != '')?'inner-page':''}}{{(Request::segment(1)  === 'page')?'inner-page':''}}{{(Request::segment(2)  === 'customer-login')?'inner-page':''}}" id="header-main">
    <!-- Main navbar -->
    <nav class="navbar navbar-main navbar-expand-lg navbar-transparent" id="navbar-main">
        <div class="container px-lg-0">
            <!-- Logo -->
            <a class="navbar-brand mr-lg-5" href="{{route('store.slug',$store->slug)}}">

                @if(route('store.slug'))
                    @if(!empty($store->logo))
                        <img alt="Image placeholdergr" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo" class="navbar-logo" style="height: 40px;">
                    @else
                        <img alt="Image placeholdergr" src="{{asset(Storage::url('uploads/store_logo/logo-2.png'))}}" id="navbar-logo" class="navbar-logo" style="height: 40px;">
                    @endif

                    <img alt="Image placeholder" class="mobile-view" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo">
                @else
                    @if(!empty($store->logo))
                        <img alt="Image placeholderrgrg" src="{{asset(Storage::url('uploads/store_logo/'.route('store.slug')?'logo-2.png':$store->logo))}}" id="navbar-logo" class="navbar-logo" style="height: 40px;">
                        <img alt="Image placeholder" class="mobile-view" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo">
                    @else
                        <img alt="Image placeholdergrg" src="{{asset(Storage::url('uploads/store_logo/logo.png'))}}" id="navbar-logo" class="navbar-logo" style="height: 40px;">
                        <img alt="Image placeholdergrg" src="{{asset(Storage::url('uploads/store_logo/logo.png'))}}" class="mobile-view" id="navbar-logo">
                    @endif
                @endif
            </a>
            <!-- Navbar collapse trigger -->
            <button class="navbar-toggler pr-0" type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar nav -->
            <div class="collapse navbar-collapse" id="navbar-main-collapse">
                <ul class="navigation-menu align-items-lg-center">
                    <!-- Home - Overview  -->
                    <li class="nav-item ">
                        <a class="nav-link {{(route('store.slug')?:'text-dark')}} {{(Request::segment(1)  == 'store-blog')?'text-dark':''}}" href="{{route('store.slug',$store->slug)}}">{{ucfirst($store->name)}}</a>
                    </li>
                    @if(!empty($page_slug_urls))
                        @foreach($page_slug_urls as $k=>$page_slug_url)
                            @if($page_slug_url->enable_page_header == 'on')
                                <li class="nav-item ">
                                    <a class="nav-link {{(route('store.slug')?:'text-dark')}} {{(Request::segment(1)  == 'store-blog')?'text-dark':''}}" href="{{env('APP_URL') . '/page/' . $page_slug_url->slug}}">{{ucfirst($page_slug_url->name)}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                    @if($store['blog_enable'] == "on" && !empty($blog))
                        <li class="nav-item ">
                            <a class="nav-link {{(route('store.slug')?:'text-dark')}} {{(Request::segment(1)  == 'store-blog')?'text-dark':''}}" href="{{route('store.blog',$store->slug)}}">{{__('Blog')}}</a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav align-items-lg-center ml-lg-auto nav-my-store">
                    <li class="nav-item search-icon">
                        <a href="#" class="nav-link" data-action="omnisearch-open" data-target="#omnisearch">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>
                    @if(Utility::CustomerAuthCheck($store->slug)==true)
                    <li class="nav-item heart-icon">
                        <a href="{{route('store.wishlist',$store->slug)}}">
                            <i class="fas fa-heart"></i>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link pr-0 text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-language"></i>
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
                    @if(Utility::CustomerAuthCheck($store->slug)==true)
                        <div class="drop-down">
                         <div id="dropDown" class="drop-down__button ">
                           <a class="nav-link nav-link btn ml-2 bg--gray hover-translate-y-n3 icon-font login_btn">{{ucFirst(Auth::guard('customers')->user()->name)}}
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
                        <a href="{{route('customer.login',$store->slug)}}" class="nav-link btn ml-2 bg--gray hover-translate-y-n3 icon-font login_btn">{{__('Log in')}}</a>
                    </li>
                    @endif
                    <li class="nav-item store-bg">
                        <a href="{{route('store.cart',$store->slug)}}" class="nav-heart cart-icon btn btn-primary ml-2 mr-2 rounded-pill hover-translate-y-n3 ">
                            <i class="fas fa-shopping-basket"></i>
                            <p class="badge badge-pill badge-floating bg-green border-dark shoping_counts" id="shoping_counts">
                                {{!empty($total_item)?$total_item:'0'}}
                            </p>
                        </a>
                    </li>

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

@if($storethemesetting['enable_footer_note'] == 'on' && $storethemesetting['enable_footer'] == 'on')
    <footer id="footer-main">
        <div class="container">
            @if($storethemesetting['enable_footer_note'] == 'on')
                <div class="row pt-md top-footer delimiter-top">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <a href="{{route('store.slug',$store->slug)}}">
                            @if(!empty($storethemesetting['footer_logo']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['footer_logo']))
                                <img class="mb-3" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['footer_logo']))}}" alt="Footer logo" style="height: 40px;">
                            @else
                                <img class="mb-3" src="{{asset(Storage::url('uploads/store_logo/footer_logo.png'))}}" alt="Footer logo" style="height: 40px;">
                            @endif
                        </a>
                        <p class="t-l-gray w-75">{{$storethemesetting['footer_desc']}}</p>
                        <div class="get-qus">
                            <i class="fab fa-whatsapp"></i>
                            <div>
                                <p class="mb-0">{{__('Got questions?')}} {{__('Call us')}} 24/7</p>
                                <a href="tel:{{$storethemesetting['footer_number']}}">{{$storethemesetting['footer_number']}}</a>
                            </div>
                        </div>
                    </div>
                    @if(isset($storethemesetting['enable_quick_link1']) && $storethemesetting['enable_quick_link1'] == 'on')
                        <div class="col-lg-2 col-sm-4 col-4 mb-5 mb-lg-0">
                            <h6 class="heading text-primary mb-3">{{__($storethemesetting['quick_link_header_name1'])}}</h6>
                            <ul class="list-unstyled f-list">
                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url1']}}">{{__($storethemesetting['quick_link_name1'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url12']}}">{{__($storethemesetting['quick_link_name12'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url13']}}">{{__($storethemesetting['quick_link_name13'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url14']}}">{{__($storethemesetting['quick_link_name14'])}}</a></li>

                            </ul>
                        </div>
                    @endif
                    @if(isset($storethemesetting['enable_quick_link2']) && $storethemesetting['enable_quick_link2'] == 'on')
                        <div class="col-lg-2 col-sm-4 col-4 mb-5 mb-lg-0">
                            <h6 class="heading text-primary mb-3">{{__($storethemesetting['quick_link_header_name2'])}}</h6>
                            <ul class="list-unstyled f-list">

                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url21']}}">{{__($storethemesetting['quick_link_name21'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url22']}}">{{__($storethemesetting['quick_link_name22'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url23']}}">{{__($storethemesetting['quick_link_name23'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url24']}}">{{__($storethemesetting['quick_link_name24'])}}</a></li>

                            </ul>
                        </div>
                    @endif
                    @if(isset($storethemesetting['enable_quick_link3']) && $storethemesetting['enable_quick_link3'] == 'on')
                        <div class="col-lg-2 col-sm-4 col-4 mb-5 mb-lg-0">
                            <h6 class="heading text-primary mb-3">{{__($storethemesetting['quick_link_header_name3'])}}</h6>
                            <ul class="list-unstyled f-list">

                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url31']}}">{{__($storethemesetting['quick_link_name31'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url32']}}">{{__($storethemesetting['quick_link_name32'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url33']}}">{{__($storethemesetting['quick_link_name33'])}}</a></li>


                                <li><a class="t-gray" target="_blank" href="{{$storethemesetting['quick_link_url34']}}">{{__($storethemesetting['quick_link_name34'])}}</a></li>

                            </ul>
                        </div>
                    @endif
                </div>
            @endif
            @if($storethemesetting['enable_footer'] == 'on')
                <div class="row align-items-center justify-content-md-between py-2 delimiter-top">
                    <div class="col-md-6">
                        <div class="copyright text-sm font-weight-bold text-center text-md-left">
                            {{$storethemesetting['footer_note']}}
                        </div>
                    </div>
                    <div class="col-md-6 footer-social">
                        <ul class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                            <li class="nav-item d-flex align-items-center">
                                <p class="mb-0">Follow us on :</p>
                            </li>
                            @if(!empty($storethemesetting['whatsapp']))
                                <li class="nav-item">
                                    <a class="nav-link" href="https://wa.me/{{$storethemesetting['whatsapp']}}" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['facebook']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['facebook']}}" target="_blank">
                                        <i class="fab fa-facebook-square"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['twitter']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['twitter']}}" target="_blank">
                                        <i class="fab fa-twitter-square"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['email']))
                                <li class="nav-item">
                                    <a class="nav-link" href="mailto:{{$storethemesetting['email']}}">
                                        <i class="far fa-envelope"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['instagram']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['instagram']}}" target="_blank">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($storethemesetting['youtube']))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{$storethemesetting['youtube']}}" target="_blank">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
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
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=0000&ev=PageView&noscript={{$store_settings->fbpixel_code}}"/></noscript>

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
</body>
</html>
