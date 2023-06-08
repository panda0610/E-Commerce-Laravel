@php
// get theme color
$setting = App\Models\Utility::colorset();
$color = 'theme-4';
if (!empty($setting['color'])) {
    $color = $setting['color'];
}
$company_logo = \App\Models\Utility::GetLogo();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{env('SITE_RTL') == 'on'?'rtl':''}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="StoreGo - Business Ecommerce">
    <title>{{(\App\Models\Utility::getValByName('title_text')) ? \App\Models\Utility::getValByName('title_text') : config('app.name', 'StoreGo SaaS')}} - @yield('page-title')</title>

    <link rel="icon" href="{{asset(Storage::url('uploads/logo/')).'/favicon.png'}}" type="image/png">
     <!-- CSS Libraries -->
     <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">

      <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('custom/css/custom.css') }}">
    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css')}}" id="main-style-link">
    @else
        @if($setting['cust_darklayout']=='on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css')}}">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}" id="main-style-link">
        @endif
    @endif
</head>
<body class="{{ $color }}">
    <div class="auth-wrapper auth-v3">
        <div class="bg-auth-side bg-primary"></div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md navbar-light default">
                <div class="container-fluid pe-2">
                    <a class="navbar-brand" href="#">
                        <img src="{{asset(Storage::url('uploads/logo/'.$company_logo))}}" alt="{{ config('app.name', 'Storego') }}" class="navbar-brand-img auth-navbar-brand">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                        <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">{{ __('Support')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Terms')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Privacy')}}</a>
                            </li>
                            @yield('language-bar')
                        </ul>
                    </div>
                </div>
            </nav>
            @yield('content')
            <div class="auth-footer">
                <div class="container-fluid">
                    <p class="">{{__('Copyright')}} &copy; {{ (Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'WorkGo') }} {{date('Y')}}</p>
                </div>
            </div>
        </div>
    </div>
@stack('custom-scripts')
<script src="{{asset('custom/js/jquery.min.js')}}"></script>
<script src="{{asset('custom/js/custom.js')}}"></script>
@stack('script')

</body>
</html>
