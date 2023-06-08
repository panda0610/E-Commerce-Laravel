
<!DOCTYPE html>
<html lang="en" dir="{{env('SITE_RTL') == 'on'?'rtl':''}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="StoreGo - Business Ecommerce">


    <title>{{__('Completed')}} - {{($store->tagline) ?  $store->tagline : config('APP_NAME', 'StoreGo')}}</title>

    <link rel="icon" href="{{asset(Storage::url('uploads/logo/').(!empty($settings->value)?$settings->value:'favicon.png'))}}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('custom/libs/@fortawesome/fontawesome-free/css/all.min.css')}}"><!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('custom/libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/swiper/dist/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset('custom/libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/site.css')}}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('custom/css/custom.css')}}" id="stylesheet')}}">
    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    <script type="text/javascript" src="{{ asset('assets/js/jquery-1.11.1.min.js')}}"></script>
    @stack('css-page')
</head>
<body>

<div class="main-content">
    <section class="">
        <div class="main-content">
            <section class="mh-100vh d-flex align-items-center" data-offset-top="#header-main">
                <div class="bg-absolute-cover bg-size--contain d-flex align-items-center zindex0">
                    <figure class="w-100 success_img">
                        <img alt="Image placeholder" src="{{asset('assets/img/bg-2.svg')}}" class="svg-inject success_img">
                    </figure>
                </div>
                <div class="container pt-10 position-relative">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="text-center pt-10">
                                <!-- SVG illustration -->
                                <div class="row justify-content-center mb-5">
                                    <div class="col-md-5">
                                        <img alt="Image placeholder" src="{{asset('assets/img/celebration.png')}}" class="svg-inject img-fluid">
                                    </div>
                                </div>
                                <!-- Empty cart container -->
                                <h6 class="h4 my-2">{{__('Your Order Successfully Completed')}}.</h6>
                                <p class="px-md-5 mb-3 text-center">
                                    {{__('We received your purchase request')}},<br>
                                    {{__('we\'ll be in touch shortly')}}!
                                </p>

                                <div class="input-group mb-3">
                                    <input type="text" value="{{route('user.order',[$store->slug,$order_id])}}" id="myInput" class="form-control d-inline-block" aria-label="Recipient's username" aria-describedby="button-addon2" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" onclick="myFunction()" id="button-addon2"><i class="far fa-copy"></i> {{__('Copy Link')}}</button>
                                    </div>
                                </div>

                                <a href="{{route('store.slug',$store->slug)}}" class="btn btn-sm btn-primary btn-icon rounded-pill mt-5">
                                    <span class="btn-inner--icon"><i class="fas fa-angle-left"></i></span>
                                    <span class="btn-inner--text">{{__('Return to shop')}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
</div>
<!-- Core JS - includes jquery, bootstrap, popper, in-view and sticky-kit -->
<script src="{{asset('assets/js/site.core.js')}}"></script>
<!-- notify -->
<script type="text/javascript" src="{{ asset('custom/js/custom.js') }}"></script>

<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<!-- Page JS -->
<script src="{{asset('assets/libs/swiper/dist/js/swiper.min.js')}}"></script>
<!-- Site JS -->
<script src="{{asset('assets/js/site.js')}}"></script>
<!-- Demo JS - remove it when starting your project -->
<script src="{{asset('assets/js/demo.js')}}"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->

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

    gtag('config', '{{ !empty($store_settings->google_analytic) }}');
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
  fbq('init', '{{!empty($store_settings->fbpixel_code)}}');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=0000&ev=PageView&noscript={{$store_settings->fbpixel_code}}"
/></noscript>



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
<script>
    function myFunction() {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        show_toastr('Success', 'Link copied', 'success');
    }
</script>
</body>

</html>


