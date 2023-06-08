@extends('layouts.admin')
@php
$logo = asset(Storage::url('uploads/logo/'));
$logo_light = \App\Models\Utility::getValByName('company_logo_light');
$logo_dark = \App\Models\Utility::getValByName('company_logo_dark');
$company_favicon = \App\Models\Utility::getValByName('company_favicon');
$store_logo = asset(Storage::url('uploads/store_logo/'));
$lang = \App\Models\Utility::getValByName('default_language');
if (Auth::user()->type == 'Owner') {
    $store_lang = $store_settings->lang;
}
@endphp
@section('page-title')
    @if (Auth::user()->type == 'super admin')
        {{ __('Setting') }}
    @else
        {{ __('Store Setting') }}
    @endif
@endsection
@section('title')
    <div class="d-inline-block">
        @if (Auth::user()->type == 'super admin')
            <h5 class="h4 d-inline-block font-weight-bold mb-0 text-white">{{ __('Setting') }}</h5>
        @else
            <h5 class="h4 d-inline-block font-weight-bold mb-0 text-white">{{ __('Store Setting') }}</h5>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Setting') }}</li>
@endsection
@section('action-btn')
@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
    <style>
        hr {
            margin: 8px;
        }

    </style>
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
         target: '#useradd-sidenav',
         offset: 300,

     })
    $(".list-group-item").click(function(){
           $('.list-group-item').filter(function(){
                 return this.href == id;
         }).parent().removeClass('text-primary');
     });
 </script>
    <script>
        $(document).ready(function() {
            if ($('.gdpr_fulltime').is(':checked')) {

                $('.fulltime').show();
            } else {

                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {

                    $('.fulltime').show();
                } else {

                    $('.fulltime').hide();
                }
            });
        });
    </script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,

        })
        $(".list-group-item").click(function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>
    <script>

        function check_theme(color_val) {
        $('.theme-color').prop('checked', false);
        $('input[value="'+color_val+'"]').prop('checked', true);
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @if (Auth::user()->type == 'Owner')
                                <a href="#store_setting" id="store_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Store Settings') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#theme_setting" id="theme_setting_tab"
                                    class="list-group-item list-group-item-action">{{__('Store Theme Setting')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#site-setting" id="site_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Site Setting') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#store_payment-setting" id="payment-setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Store Payment') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#store_email_setting" id="store_email_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Store Email Setting') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#whatsapp_custom_massage" id="system_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Whatsapp Massage Setting') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                                <a href="#twilio_setting" id="twilio_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Twilio setting') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            @endif

                            @if(Auth::user()->type == 'super admin')
                            <a href="#site-setting" id="site_setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Site Setting') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#payment-setting" id="payment-setting_tab"
                                        class="list-group-item list-group-item-action">{{ __('Payment Setting') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#email-settings" class="list-group-item list-group-item-action dash-link ">{{__('Email Setting')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#recaptcha-settings" class="list-group-item list-group-item-action">{{__('ReCaptcha Setting')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    @if (Auth::user()->type == 'Owner')
                        <div class="active" id="store_setting">
                            {{ Form::model($store_settings, ['route' => ['settings.store', $store_settings['id']],'method' => 'POST','enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Store Settings') }}</h5>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class=" setting-card">
                                                <div class="row mt-2">
                                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5>{{ __('Store Logo') }}</h5>
                                                            </div>
                                                            <div class="card-body pt-0">
                                                                <div class=" setting-card">
                                                                    <div class="logo-content mt-4">
                                                                        <img src="{{ $store_logo .'/' .(isset($store_settings['logo']) && !empty($store_settings['logo']) ? $store_settings['logo'] : 'logo.png') }}"
                                                                            class="big-logo" width="250px">
                                                                    </div>
                                                                    <div class="choose-files mt-5">
                                                                        <label for="logo">
                                                                            <div class=" bg-primary logo_update"> <i
                                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                            </div>
                                                                            <input type="file" class="form-control file" name="logo" id="logo"
                                                                                data-filename="logo_update">
                                                                        </label>
                                                                    </div>
                                                                    @error('logo')
                                                                        <div class="row">
                                                                            <span class="invalid-logo" role="alert">
                                                                                <strong class="text-danger">{{ $message }}</strong>
                                                                            </span>
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5>{{ __('Invoice Logo') }}</h5>
                                                            </div>
                                                            <div class="card-body pt-0">
                                                                <div class=" setting-card">
                                                                    <div class="logo-content mt-4">
                                                                        <img src="{{ $store_logo .'/' .(isset($store_settings['invoice_logo']) && !empty($store_settings['invoice_logo'])? $store_settings['invoice_logo']: 'invoice_logo.png') }}"
                                                                            class="big-logo" width="250px">
                                                                    </div>
                                                                    <div class="choose-files mt-5">
                                                                        <label for="logo">
                                                                            <div class=" bg-primary invoice_logo_update"> <i
                                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                            </div>
                                                                            <input type="file" name="invoice_logo" id="invoice_logo"
                                                                                class="form-control file" data-filename="invoice_logo_update">
                                                                        </label>
                                                                    </div>
                                                                    @error('invoice_logo')
                                                                        <div class="row">
                                                                            <span class="invalid-invoice_logo" role="alert">
                                                                                <strong class="text-danger">{{ $message }}</strong>
                                                                            </span>
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        {{ Form::label('store_name', __('Store Name'), ['class' => 'form-label']) }}
                                                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Store Name')]) !!}
                                                        @error('store_name')
                                                            <span class="invalid-store_name" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                                                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Email')]) }}
                                                        @error('email')
                                                            <span class="invalid-email" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    @if ($plan->enable_custdomain == 'on' || $plan->enable_custsubdomain == 'on')
                                                        <div class="col-md-6 py-4">
                                                            <div class="radio-button-group mts">
                                                                {{-- <div class="btn-group btn-group-toggle" data-toggle="buttons"> --}}
                                                                <div class="item">
                                                                    <label
                                                                        class="btn btn-primary {{ $store_settings['enable_storelink'] == 'on' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            class="domain_click  radio-button"
                                                                            name="enable_domain" value="enable_storelink"
                                                                            id="enable_storelink"
                                                                            {{ $store_settings['enable_storelink'] == 'on' ? 'checked' : '' }}">
                                                                        {{ __('Store Link') }}
                                                                    </label>
                                                                </div>
                                                                @if ($plan->enable_custdomain == 'on')
                                                                    <div class="item">
                                                                        <label
                                                                            class="btn btn-primary {{ $store_settings['enable_domain'] == 'on' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                class="domain_click radio-button"
                                                                                name="enable_domain" value="enable_domain"
                                                                                id="enable_domain"
                                                                                {{ $store_settings['enable_domain'] == 'on' ? 'checked' : '' }}>
                                                                            {{ __('Domain') }}
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                                @if ($plan->enable_custsubdomain == 'on')
                                                                    <div class="item">
                                                                        <label
                                                                            class="btn btn-primary {{ $store_settings['enable_subdomain'] == 'on' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                class="domain_click radio-button"
                                                                                name="enable_domain"
                                                                                value="enable_subdomain"
                                                                                id="enable_subdomain"
                                                                                {{ $store_settings['enable_subdomain'] == 'on' ? 'checked' : '' }}>
                                                                            {{ __('Sub Domain') }}
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                                {{-- </div> --}}
                                                            </div>
                                                            <div class="text-sm mt-2" id="domainnote"
                                                                style="display: none">
                                                                {{ __('Note : Before add custom domain, your domain A record is pointing to our server IP :') }}{{ $serverIp }}
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6" id="StoreLink"
                                                            style="{{ $store_settings['enable_storelink'] == 'on' ? 'display: block' : 'display: none' }}">
                                                            {{ Form::label('store_link', __('Store Link'), ['class' => 'form-label']) }}
                                                            <div class="input-group">
                                                                <input type="text"
                                                                    value="{{ $store_settings['store_url'] }}"
                                                                    id="myInput" class="form-control d-inline-block"
                                                                    aria-label="Recipient's username"
                                                                    aria-describedby="button-addon2" readonly>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-primary" type="button"
                                                                        onclick="myFunction()" id="button-addon2"><i
                                                                            class="far fa-copy"></i>
                                                                        {{ __('Copy Link') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 domain"
                                                            style="{{ $store_settings['enable_domain'] == 'on' ? 'display:block' : 'display:none' }}">
                                                            {{ Form::label('store_domain', __('Custom Domain'), ['class' => 'form-label']) }}
                                                            {{ Form::text('domains', $store_settings['domains'], ['class' => 'form-control','placeholder' => __('xyz.com')]) }}
                                                        </div>
                                                        @if ($plan->enable_custsubdomain == 'on')
                                                            <div class="form-group col-md-6 sundomain"
                                                                style="{{ $store_settings['enable_subdomain'] == 'on' ? 'display:block' : 'display:none' }}">
                                                                {{ Form::label('store_subdomain', __('Sub Domain'), ['class' => 'form-label']) }}
                                                                <div class="input-group">
                                                                    {{ Form::text('subdomain', $store_settings['slug'], ['class' => 'form-control','placeholder' => __('Enter Domain'),'readonly']) }}
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"
                                                                            id="basic-addon2">.{{ $subdomain_name }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="form-group col-md-6" id="StoreLink">
                                                            {{ Form::label('store_link', __('Store Link'), ['class' => 'form-label']) }}
                                                            <div class="input-group">
                                                                <input type="text"
                                                                    value="{{ $store_settings['store_url'] }}"
                                                                    id="myInput" class="form-control d-inline-block"
                                                                    aria-label="Recipient's username"
                                                                    aria-describedby="button-addon2" readonly>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-primary" type="button"
                                                                        onclick="myFunction()" id="button-addon2"><i
                                                                            class="far fa-copy"></i>
                                                                        {{ __('Copy Link') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('tagline', __('Tagline'), ['class' => 'form-label']) }}
                                                        {{ Form::text('tagline', null, ['class' => 'form-control', 'placeholder' => __('Tagline')]) }}
                                                        @error('tagline')
                                                            <span class="invalid-tagline" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                                                        {{ Form::text('address', null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                                                        @error('address')
                                                            <span class="invalid-address" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                                                        {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('City')]) }}
                                                        @error('city')
                                                            <span class="invalid-city" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                                                        {{ Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('State')]) }}
                                                        @error('state')
                                                            <span class="invalid-state" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('zipcode', __('Zipcode'), ['class' => 'form-label']) }}
                                                        {{ Form::text('zipcode', null, ['class' => 'form-control', 'placeholder' => __('Zipcode')]) }}
                                                        @error('zipcode')
                                                            <span class="invalid-zipcode" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                                                        {{ Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('Country')]) }}
                                                        @error('country')
                                                            <span class="invalid-country" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        {{ Form::label('store_default_language', __('Store Default Language'), ['class' => 'form-label']) }}
                                                        <div class="changeLanguage">
                                                            <select name="store_default_language"
                                                                id="store_default_language"
                                                                class="form-control" data-toggle="select">
                                                                @foreach (\App\Models\Utility::languages() as $language)
                                                                    <option
                                                                        @if ($store_lang == $language) selected @endif
                                                                        value="{{ $language }}">
                                                                        {{ Str::upper($language) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        {{ Form::label('decimal_number_format', __('Decimal Number Format'), ['class' => 'form-label']) }}
                                                        {{ Form::number('decimal_number',isset($store_settings['decimal_number']) ? $store_settings['decimal_number'] : 2,['class' => 'form-control', 'placeholder' => __('decimal_number')]) }}
                                                        @error('decimal_number')
                                                            <span class="invalid-decimal_number" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label class="form-check-label" for="enable_rating"></label>
                                                        <div class="custom-control form-switch">
                                                            <input type="checkbox" class="form-check-input"
                                                                name="enable_rating" id="enable_rating"
                                                                {{ $store_settings['enable_rating'] == 'on' ? 'checked=checked' : '' }}>
                                                            {{ Form::label('enable_rating', __('Product Rating Display'), ['class' => 'form-check-label mb-3']) }}
                                                        </div>
                                                    </div>
                                                    @if ($plan->blog == 'on')
                                                        <div class="form-group col-md-2">
                                                            <label class="form-check-label" for="blog_enable"></label>
                                                            <div class="custom-control form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="blog_enable" id="blog_enable"
                                                                    {{ $store_settings['blog_enable'] == 'on' ? 'checked=checked' : '' }}>
                                                                {{ Form::label('blog_enable', __('Blog Menu Dispay'), ['class' => 'form-check-label mb-3']) }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($plan->shipping_method == 'on')
                                                        <div class="form-group col-md-2">
                                                            <label class="form-check-label" for="enable_shipping"></label>
                                                            <div class="custom-control form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="enable_shipping" id="enable_shipping"
                                                                    {{ $store_settings['enable_shipping'] == 'on' ? 'checked=checked' : '' }}>
                                                                {{ Form::label('enable_shipping', __('Shipping Method Enable'), ['class' => 'form-check-label mb-3']) }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <i class="fab fa-google" aria-hidden="true"></i>
                                                            {{ Form::label('google_analytic', __('Google Analytic'), ['class' => 'form-label']) }}
                                                            {{ Form::text('google_analytic', null, ['class' => 'form-control', 'placeholder' => 'UA-XXXXXXXXX-X']) }}
                                                            @error('google_analytic')
                                                                <span class="invalid-google_analytic" role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                            {{ Form::label('facebook_pixel_code', __('Facebook Pixel'), ['class' => 'form-label']) }}
                                                            {{ Form::text('fbpixel_code', null, ['class' => 'form-control', 'placeholder' => 'UA-0000000-0']) }}
                                                            @error('facebook_pixel_code')
                                                                <span class="invalid-google_analytic" role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ Form::label('storejs', __('Store Custom JS'), ['class' => 'form-label']) }}
                                                        {{ Form::textarea('storejs', null, ['class' => 'form-control', 'rows' => 3, 'placehold   er' => __('About')]) }}
                                                        @error('storejs')
                                                            <span class="invalid-about" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group col-md-6 col-lg-6">
                                                        {{ Form::label('metakeyword', __('Meta Keywords'), ['class' => 'form-label']) }}
                                                        {!! Form::textarea('metakeyword', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Meta Keyword')]) !!}
                                                        @error('meta_keywords')
                                                            <span class="invalid-about" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group col-md-6 col-lg-6">
                                                        {{ Form::label('metadesc', __('Meta Description'), ['class' => 'form-label']) }}
                                                        {!! Form::textarea('metadesc', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Meta Description')]) !!}

                                                        @error('meta_description')
                                                            <span class="invalid-about" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="col-sm-12 px-2">
                                                <div class="text-end">
                                                    <button type="button"
                                                        class="btn bs-pass-para btn-secondary btn-light"
                                                        data-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $store_settings->id }}">
                                                        <span
                                                            class="text-black">{{ __('Delete Store') }}</span>
                                                    </button>
                                                    {{ Form::submit(__('Save Change'), ['class' => 'btn btn-xs btn-primary']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                            {!! Form::open(['method' => 'DELETE', 'route' => ['ownerstore.destroy', $store_settings->id], 'id' => 'delete-form-' . $store_settings->id]) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="" id="theme_setting">
                            {{Form::open(array('route' => array('store.changetheme', $store_settings->id),'method' => 'POST'))}}
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Store Theme Setting') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach (\App\Models\Utility::themeOne() as $key => $v)
                                                        <div class="col-4 cc-selector mb-2">
                                                            <div class="mb-3 screen image">
                                                               <img src="{{asset(Storage::url('uploads/store_theme/'.$key.'/Home.png'))}}" class="img-center pro_max_width pro_max_height {{$key}}_img">
                                                                <div class="actions">
                                                                    <a href="">
                                                                        <button type="button"
                                                                            class="btn btn-default delete-image-btn pull-right">
                                                                            <span class="glyphicon glyphicon-trash"></span>
                                                                        </button>
                                                                    </a>
                                                                    <a href="">
                                                                        <button type="button"
                                                                            class="btn btn-default edit-image-btn pull-right">
                                                                            <span class="glyphicon glyphicon-pencil"></span>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="row gutters-xs" id="{{ $key }}">
                                                                    @foreach($v as $css => $val)
                                                                    <div class="col">
                                                                        <label class="colorinput">
                                                                            <input name="theme_color" type="radio" value="{{$css}}" data-theme="{{$key}}" data-imgpath="{{$val['img_path']}}" class="colorinput-input" {{(isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css) ? 'checked' : ''}}>
                                                                            <span class="colorinput-color" style="background:#{{$val['color']}}"></span>
                                                                        </label>
                                                                    </div>
                                                                    @endforeach
                                                            <div class="col">
                                                                @if(isset($store_settings['theme_dir']) && $store_settings['theme_dir'] == $key)
                                                                    <a href="{{route('store.editproducts',[$store_settings->slug,$key])}}" class="btn btn-outline-primary theme_btn" type="button" id="button-addon2">{{__('Edit')}}</a>
                                                                @endif
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                            <div class="card-footer">
                                                <div class="col-sm-12 px-2">
                                                    <div class="text-end">
                                                        {{Form::hidden('themefile',null,array('id'=>'themefile'))}}
                                                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="" id="site-setting">
                            {{Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))}}
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Site Setting') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Logo dark') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                    <img src="{{$logo.'/'.(isset($logo_dark) && !empty($logo_dark)?$logo_dark:' logo-dark.png')}}"
                                                                        class="big-logo" width="170px">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="company_logo">
                                                                        <div class=" bg-primary company_logo_update"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" name="company_logo" id="company_logo" class="form-control file" data-filename="company_logo_update">
                                                                    </label>
                                                                </div>
                                                                @error('company_logo')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Logo Light') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                    <img src="{{$logo.'/'.(isset($logo_light) && !empty($logo_light)?$logo_light:'logo-light.png')}}"
                                                                    class="big-logo img_setting" width="170px">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="company_logo_light">
                                                                        <div class=" bg-primary dark_logo_update"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" class="form-control file" name="company_logo_light" id="company_logo_light"
                                                                            data-filename="dark_logo_update">
                                                                    </label>
                                                                </div>
                                                                @error('company_logo_light')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Favicon') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                    <img src="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" width="50px"
                                                                        class="big-logo img_setting">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="company_favicon">
                                                                        <div class=" bg-primary company_favicon_update"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" class="form-control file"  id="company_favicon" name="company_favicon"
                                                                            data-filename="company_favicon_update">
                                                                    </label>
                                                                </div>
                                                                @error('logo')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    {{Form::label('title_text',__('Title Text'),array('class'=>'form-label')) }}
                                                    {{Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))}}
                                                    @error('title_text')
                                                    <span class="invalid-title_text" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {{Form::label('footer_text',__('Footer Text'),array('class'=>'form-label'))}}
                                                    {{Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))}}
                                                    @error('footer_text')
                                                    <span class="invalid-footer_text" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                     </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="site_date_format" class="form-label">{{__('Date Format')}}</label>
                                                    <select type="text" name="site_date_format" class="form-control" data-toggle="select" id="site_date_format">
                                                        <option value="M j, Y" @if(@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015</option>
                                                        <option value="d-m-Y" @if(@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>d-m-y</option>
                                                        <option value="m-d-Y" @if(@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>m-d-y</option>
                                                        <option value="Y-m-d" @if(@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>y-m-d</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="site_time_format" class="form-label">{{__('Time Format')}}</label>
                                                    <select type="text" name="site_time_format" class="form-control" data-toggle="select" id="site_time_format">
                                                        <option value="g:i A" @if(@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM</option>
                                                        <option value="g:i a" @if(@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm</option>
                                                        <option value="H:i" @if(@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                            <div class="card-footer ">
                                                <div class="col-sm-12 px-2">
                                                    <div class="text-end">
                                                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="card" id="store_payment-setting">
                            <div class="card-header">
                                <h5>{{('Store Payment')}}</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-dark font-weight-bold">{{__("This detail will use for collect payment on invoice from clients. On invoice client will find out pay now button based on your below configuration.")}}</small></br></br>

                                {{Form::open(array('route'=>array('owner.payment.setting',$store_settings->slug),'method'=>'post','novalidate'))}}
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                            <label class="col-form-label">{{__('Currency')}}</label>
                                                            <input type="text" name="currency" class="form-control" id="currency" value="{{ $store_settings['currency_code']}}" required>
                                                            <small class="text-xs">
                                                                {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                                <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                                            </small>
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                            <label for="currency_symbol" class="col-form-label">{{__('Currency Symbol')}}</label>
                                                            <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="{{ $store_settings['currency']}}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-control-label mb-3" for="example3cols3Input">{{__('Currency Symbol Position')}}</label>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-check form-check-inline mb-3">
                                                                            <input type="radio" id="customRadio5" name="currency_symbol_position" value="pre" class="form-check-input" @if($store_settings['currency_symbol_position'] == 'pre' || $store_settings['currency_symbol_position'] == null) checked @endif>
                                                                            <label class="form-check-label" for="customRadio5">{{__('Pre')}}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-check form-check-inline mb-3">
                                                                            <input type="radio" id="customRadio6" name="currency_symbol_position" value="post" class="form-check-input" @if($store_settings['currency_symbol_position'] == 'post') checked @endif>
                                                                            <label class="form-check-label" for="customRadio6">{{__('Post')}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-control-label mb-3" for="example3cols3Input">{{__('Currency Symbol Space')}}</label>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-check form-check-inline mb-3">
                                                                            <input type="radio" id="customRadio7" name="currency_symbol_space" value="with" class="form-check-input" @if($store_settings['currency_symbol_space'] == 'with') checked @endif>
                                                                            <label class="form-check-label" for="customRadio7">{{__('With Space')}}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-check form-check-inline mb-3">
                                                                            <input type="radio" id="customRadio8" name="currency_symbol_space" value="without" class="form-check-input" @if($store_settings['currency_symbol_space'] == 'without' || $store_settings['currency_symbol_space'] == null) checked @endif>
                                                                            <label class="form-check-label" for="customRadio8">{{__('Without Space')}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h6>{{__('Custom Field For Checkout')}}</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{Form::label('custom_field_title_1',__('Custom Field Title'),array("class"=>"col-form-label")) }}
                                                                    {{Form::text('custom_field_title_1',!empty($store_payment_setting['custom_field_title_1'])?$store_payment_setting['custom_field_title_1']:'',array('class'=>'form-control','placeholder'=>__('Enter Custom Field Title')))}}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{Form::label('custom_field_title_2',__('Custom Field Title'),array("class"=>"col-form-label")) }}
                                                                    {{Form::text('custom_field_title_2',!empty($store_payment_setting['custom_field_title_2'])?$store_payment_setting['custom_field_title_2']:'',array('class'=>'form-control','placeholder'=>__('Enter Custom Field Title')))}}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{Form::label('custom_field_title_3',__('Custom Field Title'),array("class"=>"col-form-label")) }}
                                                                    {{Form::text('custom_field_title_3',!empty($store_payment_setting['custom_field_title_3'])?$store_payment_setting['custom_field_title_3']:'',array('class'=>'form-control','placeholder'=>__('Enter Custom Field Title')))}}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{Form::label('custom_field_title_4',__('Custom Field Title'),array("class"=>"col-form-label")) }}
                                                                    {{Form::text('custom_field_title_4',!empty($store_payment_setting['custom_field_title_4'])?$store_payment_setting['custom_field_title_4']:'',array('class'=>'form-control','placeholder'=>__('Enter Custom Field Title')))}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="faq justify-content-center">
                                        <div class="col-sm-12 col-md-10 col-xxl-12">
                                            <div class="accordion accordion-flush" id="accordionExample">

                                                <!-- COD -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('COD') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse0" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Cash On Delivery') }}</h5> --}}
                                                                    <small> {{__('Note : Enable or disable cash on delivery.')}}</small><br>
                                                                    <small> {{__('This detail will use for make checkout of shopping cart.')}}</small>
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="enable_cod" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_cod" id="enable_cod" {{ $store_settings['enable_cod'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="enable_cod">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Telegram -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse01" aria-expanded="true" aria-controls="collapse01">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Telegram') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse01" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Telegram') }}</h5> --}}
                                                                    <small> {{__('Note: This detail will use for make checkout of shopping cart.')}}</small>
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="enable_telegram" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_telegram" id="enable_telegram" {{ $store_settings['enable_telegram'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="enable_telegram">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('telegrambot',__('Telegram Access Token'),array('class'=>'col-form-label')) }}
                                                                        {{Form::text('telegrambot',$store_settings['telegrambot'],array('class'=>'form-control active telegrambot','placeholder'=>'1234567890:AAbbbbccccddddxvGENZCi8Hd4B15M8xHV0'))}}
                                                                        <p>{{__('Get Chat ID')}} : https://api.telegram.org/bot-TOKEN-/getUpdates</p>

                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('telegramchatid',__('Telegram Chat Id'),array('class'=>'col-form-label')) }}
                                                                        {{Form::text('telegramchatid',$store_settings['telegramchatid'],array('class'=>'form-control active telegramchatid','placeholder'=>'123456789'))}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Whatsapp -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse02" aria-expanded="true" aria-controls="collapse02">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Whatsapp') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse02" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Whatsapp') }}</h5> --}}
                                                                    <small> {{__('Note: This detail will use for make checkout of shopping cart.')}}</small>
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="enable_whatsapp" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_whatsapp" id="enable_whatsapp" {{ $store_settings['enable_whatsapp'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="enable_whatsapp">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control input-mask" data-mask="+00 00000000000"  value="{{$store_settings['whatsapp_number']}}" placeholder="+00 00000000000"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bank Transfer -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse03" aria-expanded="true" aria-controls="collapse03">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Bank Transfer') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse03" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Bank Transfer') }}</h5> --}}
                                                                    <small> {{__('Note: Input your bank details including bank name.')}}</small>
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="enable_bank" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_bank" id="enable_bank" {{ $store_settings['enable_bank'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="enable_bank">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <textarea type="text" name="bank_number" id="bank_number" class="form-control" value="" placeholder="{{ __('Bank Transfer Number') }}">{{$store_settings['bank_number']}}   </textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Strip -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Stripe') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse1" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Stripe') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_stripe_enabled" value="off">
                                                                        <input type="checkbox"  class="form-check-input mx-2" name="is_stripe_enabled" id="is_stripe_enabled" {{ isset($store_settings['is_stripe_enabled']) && $store_settings['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_stripe_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('stripe_key',__('Stripe Key'),array('class'=>'col-form-label'))}}
                                                                        {{Form::text('stripe_key',isset($store_settings['stripe_key'])?$store_settings['stripe_key']:'',['class'=>'form-control','placeholder'=>__('Enter Stripe Key')])}}
                                                                        @error('stripe_key')
                                                                        <span class="invalid-stripe_key" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('stripe_secret',__('Stripe Secret'),array('class'=>'col-form-label'))}}
                                                                        {{Form::text('stripe_secret',isset($store_settings['stripe_secret'])?$store_settings['stripe_secret']:'',['class'=>'form-control ','placeholder'=>__('Enter Stripe Secret')])}}
                                                                        @error('stripe_secret')
                                                                        <span class="invalid-stripe_secret" role="alert">
                                                                                <strong class="text-danger">{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paypal -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-3">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paypal') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse2" class="accordion-collapse collapse"aria-labelledby="heading-2-3"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paypal') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                            <input type="hidden" name="is_paypal_enabled" value="off">
                                                                            <input type="checkbox" class="form-check-input mx-2" name="is_paypal_enabled" id="is_paypal_enabled" {{ isset($store_settings['is_paypal_enabled']) && $store_settings['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="is_paypal_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="paypal-label col-form-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paypal_mode" value="sandbox" class="form-check-input" {{ !isset($store_settings['paypal_mode']) || $store_settings['paypal_mode'] == '' || $store_settings['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paypal_mode" value="live" class="form-check-input" {{ isset($store_settings['paypal_mode']) && $store_settings['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Client ID') }}</label>
                                                                        <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{(!isset($store_settings['paypal_client_id']) || is_null($store_settings['paypal_client_id'])) ? '' : $store_settings['paypal_client_id']}}" placeholder="{{ __('Client ID') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{(!isset($store_settings['paypal_secret_key']) || is_null($store_settings['paypal_secret_key'])) ? '' : $store_settings['paypal_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paystack -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-4">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paystack') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse3" class="accordion-collapse collapse"aria-labelledby="heading-2-4"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paystack') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paystack_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="is_paystack_enabled" id="is_paystack_enabled" {{ isset($store_payment_setting['is_paystack_enabled']) && $store_payment_setting['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_paystack_enabled">{{__('Enable')}}</label>

                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{isset($store_payment_setting['paystack_public_key']) ? $store_payment_setting['paystack_public_key']:''}}" placeholder="{{ __('Public Key') }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{isset($store_payment_setting['paystack_secret_key']) ? $store_payment_setting['paystack_secret_key']:''}}" placeholder="{{ __('Secret Key') }}"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- FLUTTERWAVE -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-5">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Flutterwave') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse4" class="accordion-collapse collapse"aria-labelledby="heading-2-5"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Flutterwave') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="is_flutterwave_enabled" id="is_flutterwave_enabled" {{ isset($store_payment_setting['is_flutterwave_enabled'])  && $store_payment_setting['is_flutterwave_enabled']== 'on' ? 'checked="checked"' : '' }}>
                                                                        <label  class="form-check-label" for="is_flutterwave_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{(!isset($store_payment_setting['flutterwave_public_key']) || is_null($store_payment_setting['flutterwave_public_key'])) ? '' : $store_payment_setting['flutterwave_public_key']}}" placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{(!isset($store_payment_setting['flutterwave_secret_key']) || is_null($store_payment_setting['flutterwave_secret_key'])) ? '' : $store_payment_setting['flutterwave_secret_key']}}" placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Razorpay -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-6">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Razorpay') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse5" class="accordion-collapse collapse"aria-labelledby="heading-2-6"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Razorpay') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_razorpay_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_razorpay_enabled" id="is_razorpay_enabled" {{ isset($store_payment_setting['is_razorpay_enabled']) && $store_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_razorpay_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{  __('Public Key')}}</label>

                                                                        <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="{{(!isset($store_payment_setting['razorpay_public_key']) || is_null($store_payment_setting['razorpay_public_key'])) ? '' : $store_payment_setting['razorpay_public_key']}}" placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label"> {{  __('Secret Key')}}</label>
                                                                        <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="{{(!isset($store_payment_setting['razorpay_secret_key']) || is_null($store_payment_setting['razorpay_secret_key'])) ? '' : $store_payment_setting['razorpay_secret_key']}}" placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytm -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-7">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paytm') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse6" class="accordion-collapse collapse"aria-labelledby="heading-2-7"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paytm') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paytm_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="is_paytm_enabled" id="is_paytm_enabled" {{ isset($store_payment_setting['is_paytm_enabled']) && $store_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_paytm_enabled">{{__('Enable')}}</label>

                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="paypal-label col-form-label" for="paypal_mode">{{ __('Paytm Environment')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paytm_mode" value="local" class="form-check-input" {{ !isset($store_payment_setting['paytm_mode']) || $store_payment_setting['paytm_mode'] == '' || $store_payment_setting['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Local')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paytm_mode" value="production" class="form-check-input" {{ isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Production')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_public_key" class="col-form-label">{{ __('Merchant ID')}}</label>
                                                                        <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="{{isset($store_payment_setting['paytm_merchant_id'])? $store_payment_setting['paytm_merchant_id']:''}}" placeholder="{{ __('Merchant ID') }}"/>
                                                                        @if ($errors->has('paytm_merchant_id'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_merchant_id') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_secret_key"  class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="{{ isset($store_payment_setting['paytm_merchant_key']) ? $store_payment_setting['paytm_merchant_key']:''}}" placeholder="{{ __('Merchant Key') }}"/>
                                                                        @if ($errors->has('paytm_merchant_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_merchant_key') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_industry_type"  class="col-form-label">{{ __('Industry Type') }}</label>
                                                                        <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="{{isset($store_payment_setting['paytm_industry_type']) ?$store_payment_setting['paytm_industry_type']:''}}" placeholder="{{ __('Industry Type') }}"/>
                                                                        @if ($errors->has('paytm_industry_type'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_industry_type') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mercado Pago-->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-8">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Mercado Pago') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse7" class="accordion-collapse collapse"aria-labelledby="heading-2-8"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Mercado Pago') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_mercado_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_mercado_enabled" id="is_mercado_enabled" {{isset($store_payment_setting['is_mercado_enabled']) &&  $store_payment_setting['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_mercado_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="coingate-label col-form-label" for="mercado_mode">{{__('Mercado Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="mercado_mode" value="sandbox" class="form-check-input" {{ isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == '' || isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="mercado_mode" value="live" class="form-check-input" {{ isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mercado_access_token" class="col-form-label">{{ __('Access Token') }}</label>
                                                                        <input type="text" name="mercado_access_token" id="mercado_access_token" class="form-control" value="{{isset($store_payment_setting['mercado_access_token']) ? $store_payment_setting['mercado_access_token']:''}}" placeholder="{{ __('Access Token') }}"/>
                                                                        @if ($errors->has('mercado_secret_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('mercado_access_token') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mollie -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-9">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Mollie') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse8" class="accordion-collapse collapse"aria-labelledby="heading-2-9"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Mollie') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_mollie_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_mollie_enabled" id="is_mollie_enabled" {{ isset($store_payment_setting['is_mollie_enabled']) && $store_payment_setting['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_mollie_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key" class="col-form-label">{{ __('Mollie Api Key')}}</label>
                                                                        <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="{{(!isset($store_payment_setting['mollie_api_key']) || is_null($store_payment_setting['mollie_api_key'])) ? '' : $store_payment_setting['mollie_api_key']}}" placeholder="Mollie Api Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_profile_id" class="col-form-label">{{ __('Mollie Profile Id')}}</label>
                                                                        <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="{{(!isset($store_payment_setting['mollie_profile_id']) || is_null($store_payment_setting['mollie_profile_id'])) ? '' : $store_payment_setting['mollie_profile_id']}}" placeholder="Mollie Profile Id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_partner_id" class="col-form-label">{{ __('Mollie Partner Id')}}</label>
                                                                        <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="{{(!isset($store_payment_setting['mollie_partner_id']) || is_null($store_payment_setting['mollie_partner_id'])) ? '' : $store_payment_setting['mollie_partner_id']}}" placeholder="Mollie Partner Id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skrill -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-10">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Skrill') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse9" class="accordion-collapse collapse"aria-labelledby="heading-2-10"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Skrill') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_skrill_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_skrill_enabled" id="is_skrill_enabled" {{ isset($store_payment_setting['is_skrill_enabled']) && $store_payment_setting['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_skrill_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key" class="col-form-label">{{ __('Skrill Email')}}</label>
                                                                        <input type="email" name="skrill_email" id="skrill_email" class="form-control" value="{{ isset($store_payment_setting['skrill_email'])?$store_payment_setting['skrill_email']:''}}" placeholder="{{ __('Mollie Api Key') }}"/>
                                                                        @if ($errors->has('skrill_email'))
                                                                            <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('skrill_email') }}
                                                                                </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CoinGate -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-11">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('CoinGate') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse10" class="accordion-collapse collapse"aria-labelledby="heading-2-11"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('CoinGate') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_coingate_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_coingate_enabled" id="is_coingate_enabled" {{ isset($store_payment_setting['is_coingate_enabled']) && $store_payment_setting['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label  class="form-check-label" for="is_coingate_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="col-form-label" for="coingate_mode">{{ __('CoinGate Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="coingate_mode" value="sandbox" class="form-check-input" {{ !isset($store_payment_setting['coingate_mode']) || $store_payment_setting['coingate_mode'] == '' || $store_payment_setting['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="coingate_mode" value="live" class="form-check-input" {{ isset($store_payment_setting['coingate_mode']) && $store_payment_setting['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="coingate_auth_token" class="col-form-label">{{ __('CoinGate Auth Token')}}</label>
                                                                        <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="{{(!isset($store_payment_setting['coingate_auth_token']) || is_null($store_payment_setting['coingate_auth_token'])) ? '' : $store_payment_setting['coingate_auth_token']}}" placeholder="CoinGate Auth Token">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PaymentWall -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-12">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('PaymentWall') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse11" class="accordion-collapse collapse"aria-labelledby="heading-2-12"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('PaymentWall') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paymentwall_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_paymentwall_enabled" id="is_paymentwall_enabled" {{ isset($store_payment_setting['is_paymentwall_enabled']) && $store_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_paymentwall_enabled">{{ __('Enable') }} </label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_public_key" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="paymentwall_public_key" id="paymentwall_public_key" class="form-control" value="{{(!isset($store_payment_setting['paymentwall_public_key']) || is_null($store_payment_setting['paymentwall_public_key'])) ? '' : $store_payment_setting['paymentwall_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_private_key" class="col-form-label">{{ __('Private Key') }}</label>
                                                                        <input type="text" name="paymentwall_private_key" id="paymentwall_private_key" class="form-control" value="{{(!isset($store_payment_setting['paymentwall_private_key']) || is_null($store_payment_setting['paymentwall_private_key'])) ? '' : $store_payment_setting['paymentwall_private_key']}}" placeholder="{{ __('Private Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="col-sm-12 px-2">
                                        <div class="text-end">
                                            {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                        </div>
                                    </div>
                                </div>
                                {{Form::close()}}
                        </div>
                        <div id="store_email_setting" class="tab-pane">
                            <div class="col-md-12">
                                <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Email settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                    {{Form::open(array('route'=>array('owner.email.setting',$store_settings->slug),'method'=>'post'))}}
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_driver',__('Mail Driver'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_driver',$store_settings->mail_driver,array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))}}
                                            @error('mail_driver')
                                            <span class="invalid-mail_driver" role="alert">
                                             <strong class="text-danger">{{ $message }}</strong>
                                             </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_host',__('Mail Host'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_host',$store_settings->mail_host,array('class'=>'form-control ','placeholder'=>__('Enter Mail Host')))}}
                                            @error('mail_host')
                                            <span class="invalid-mail_driver" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                         </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_port',__('Mail Port'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_port',$store_settings->mail_port,array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))}}
                                            @error('mail_port')
                                            <span class="invalid-mail_port" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_username',__('Mail Username'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_username',$store_settings->mail_username,array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))}}
                                            @error('mail_username')
                                            <span class="invalid-mail_username" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_password',__('Mail Password'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_password',$store_settings->mail_password,array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))}}
                                            @error('mail_password')
                                            <span class="invalid-mail_password" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_encryption',__('Mail Encryption'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_encryption',$store_settings->mail_encryption,array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                            @error('mail_encryption')
                                            <span class="invalid-mail_encryption" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_from_address',__('Mail From Address'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_from_address',$store_settings->mail_from_address,array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))}}
                                            @error('mail_from_address')
                                            <span class="invalid-mail_from_address" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('mail_from_name',__('Mail From Name'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_from_name',$store_settings->mail_from_name,array('class'=>'form-control','placeholder'=>__('Enter Mail From Name')))}}
                                            @error('mail_from_name')
                                            <span class="invalid-mail_from_name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                                @enderror
                                        </div>

                                    </div>
                                </div>
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <div class="card-footer">
                                                <div class="col-sm-12 px-2">
                                                    <div class="d-flex justify-content-end">
                                                            <a href="#" data-url="{{route('test.mail' )}}" data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" class="btn btn-xs btn-info send_email">
                                                                {{__('Send Test Mail')}}
                                                            </a>
                                                        </a>
                                                            {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                        </div>
                        <div id="whatsapp_custom_massage" class="tab-pane">
                            <div class="col-md-12">
                                <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Whatsapp Massage Setting') }}
                                            </h5>
                                        </div>
                                <div class="card-body p-4">
                                        {{Form::model($store_settings, array('route' => array('customMassage',$store_settings->slug), 'method' => 'POST')) }}
                                    <div class="row">
                                        <h6 class="font-weight-bold">{{__('Order Variable')}}</h6>
                                       <div class="form-group col-md-6">
                                            <p class="mb-1">{{__('Store Name')}} : <span class="pull-right text-primary">{store_name}</span></p>
                                            <p class="mb-1">{{__('Order No')}} : <span class="pull-right text-primary">{order_no}</span></p>
                                            <p class="mb-1">{{__('Customer Name')}} : <span class="pull-right text-primary">{customer_name}</span></p>
                                            <p class="mb-1">{{__('Billing Address')}} : <span class="pull-right text-primary">{billing_address}</span></p>
                                            <p class="mb-1">{{__('Billing Ccountry')}} : <span class="pull-right text-primary">{billing_country}</span></p>
                                            <p class="mb-1">{{__('Billing City')}} : <span class="pull-right text-primary">{billing_city}</span></p>
                                            <p class="mb-1">{{__('Billing Postalcode')}} : <span class="pull-right text-primary">{billing_postalcode}</span></p>
                                            <p class="mb-1">{{__('Shipping Address')}} : <span class="pull-right text-primary">{shipping_address}</span></p>
                                            <p class="mb-1">{{__('Shipping Country')}} : <span class="pull-right text-primary">{shipping_country}</span></p>

                                            <p class="mb-1">{{__('Shipping City')}} : <span class="pull-right text-primary">{shipping_city}</span></p>
                                            <p class="mb-1">{{__('Shipping Postalcode')}} : <span class="pull-right text-primary">{shipping_postalcode}</span></p>
                                            <p class="mb-1">{{__('Item Variable')}} : <span class="pull-right text-primary">{item_variable}</span></p>
                                            <p class="mb-1">{{__('Qty Total')}} : <span class="pull-right text-primary">{qty_total}</span></p>
                                            <p class="mb-1">{{__('Sub Total')}} : <span class="pull-right text-primary">{sub_total}</span></p>
                                            <p class="mb-1">{{__('Discount Amount')}} : <span class="pull-right text-primary">{discount_amount}</span></p>
                                            <p class="mb-1">{{__('Shipping Amount')}} : <span class="pull-right text-primary">{shipping_amount}</span></p>
                                            <p class="mb-1">{{__('Total Tax')}} : <span class="pull-right text-primary">{total_tax}</span></p>
                                            <p class="mb-1">{{__('Final Total')}} : <span class="pull-right text-primary">{final_total}</span></p>
                                        </div>
                                       <div class="form-group col-md-6">
                                            <h6 class="font-weight-bold">{{__('Item Variable')}}</h6>
                                            <p class="mb-1">{{__('Sku')}} : <span class="pull-right text-primary">{sku}</span></p>
                                            <p class="mb-1">{{__('Quantity')}} : <span class="pull-right text-primary">{quantity}</span></p>
                                            <p class="mb-1">{{__('Product Name')}} : <span class="pull-right text-primary">{product_name}</span></p>
                                            <p class="mb-1">{{__('Variant Name')}} : <span class="pull-right text-primary">{variant_name}</span></p>
                                            <p class="mb-1">{{__('Item Tax')}} : <span class="pull-right text-primary">{item_tax}</span></p>
                                            <p class="mb-1">{{__('Item total')}} : <span class="pull-right text-primary">{item_total}</span></p>
                                            <div class="form-group">
                                                <label for="storejs" class="col-form-label">{item_variable}</label>
                                                {{Form::text('item_variable',null,array('class'=>'form-control','placeholder'=>"{quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}"))}}
                                            </div>
                                            <div class="form-group">
                                                {{Form::label('content',__('Whatsapp Message'),['class'=>'col-form-label'])}}
                                                {{Form::textarea('content',null,array('class'=>'form-control','required'=>'required'))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <div class="card-footer">
                                            <div class="col-sm-12 px-2">
                                                <div class="d-flex justify-content-end">
                                                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                {{Form::close()}}
                                </div>
                            </div>
                        </div>
                        <div id="twilio_setting" class="tab-pane">
                            <div class="col-md-12">
                                <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Twilio setting') }}
                                            </h5>
                                        </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="{{ route('owner.twilio.setting',$store_settings->slug) }}" accept-charset="UTF-8">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="is_twilio_enabled" id="twilio_module"  {{ ($store_settings['is_twilio_enabled'] == 'on') ? 'checked=checked' : '' }}>
                                                <label class="form-check-label" for="twilio_module">
                                                    {{ __('Twilio') }}
                                                    </a>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                            <label for="twilio_token" class="form-label">{{ __('Twillo SID') }}</label>
                                            <input class="form-control" name="twilio_sid" type="text" value="{{$store_settings->twilio_sid}}" id="twilio_sid">
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                            <label for="twilio_token" class="form-label">{{ __('Twillo Token') }}</label>
                                            <input class="form-control "  name="twilio_token" type="text" value="{{$store_settings->twilio_token}}" id="twilio_token">
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                            <label for="twilio_from" class="form-label">{{ __('Twillo From') }}</label>
                                            <input class="form-control " name="twilio_from" type="text" value="{{$store_settings->twilio_from}}" id="twilio_from">
                                        </div>
                                         <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                            <label for="notification_number" class="form-label">{{ __('Notification Number') }}</label>
                                            <input class="form-control " name="notification_number" type="text" value="{{$store_settings->notification_number}}" id="notification_number">
                                             <small>* Use country code with your number *</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <div class="card-footer">
                                            <div class="col-sm-12 px-2">
                                                <div class="d-flex justify-content-end">
                                                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </form>
                                </div>
                            </div>
                        </div>

                    @endif

                    @if(Auth::user()->type == 'super admin')
                        <div class="" id="site-setting">
                            {{Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))}}
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Site Setting') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Logo dark') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                        <img src="{{asset(Storage::url('uploads/logo/logo-dark.png'))}}" width="170px"  class="big-logo">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="full_logo">
                                                                        <div class=" bg-primary full_logo"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" name="logo_dark" id="full_logo" class="form-control file" data-filename="full_logo">
                                                                    </label>
                                                                </div>
                                                                @error('logo')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Logo Light') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                    <img src="{{asset(Storage::url('uploads/logo/logo-light.png'))}}" width="170px"  class="big-logo img_setting">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="logo_light">
                                                                        <div class=" bg-primary"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" class="form-control file" name="logo_light" id="logo_light"
                                                                            data-filename="logo_light">
                                                                    </label>
                                                                </div>
                                                                @error('logo_light')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-6 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Favicon') }}</h5>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class=" setting-card">
                                                                <div class="logo-content mt-4">
                                                                    <img src="{{$logo.'/'.'favicon.png'}}" width="50px"
                                                                        class="big-logo img_setting">
                                                                </div>
                                                                <div class="choose-files mt-5">
                                                                    <label for="favicon">
                                                                        <div class=" bg-primary favicon_update"> <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        </div>
                                                                        <input type="file" class="form-control file"  id="favicon" name="favicon"
                                                                            data-filename="favicon_update">
                                                                    </label>
                                                                </div>
                                                                @error('favicon')
                                                                    <div class="row">
                                                                        <span class="invalid-logo" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    {{Form::label('title_text',__('Title Text'),array('class'=>'form-label')) }}
                                                    {{Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))}}
                                                    @error('title_text')
                                                    <span class="invalid-title_text" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    {{Form::label('footer_text',__('Footer Text'),array('class'=>'form-label'))}}
                                                    {{Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))}}
                                                    @error('footer_text')
                                                    <span class="invalid-footer_text" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-4">
                                                    {{Form::label('default_language',__('Default Language'),array('class'=>'form-label'))}}
                                                    <div class="changeLanguage">
                                                        <select name="default_language" id="default_language" class="form-control" data-toggle="select">
                                                            @foreach(\App\Models\Utility::languages() as $language)
                                                                <option @if($lang == $language) selected @endif value="{{$language }}">{{Str::upper($language)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <div class="form-group">
                                                        {{Form::label('currency_symbol',__('Currency Symbol*'),array('class'=>'form-label'))}}
                                                        {{Form::text('currency_symbol',$settings['currency_symbol'],array('class'=>'form-control'))}}
                                                        <small>{{__('Note: This value will assign when any new store created by Store Owner.')}}</small>
                                                        @error('currency_symbol')
                                                        <span class="invalid-currency_symbol" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4 mb-0">
                                                    <div class="form-group">
                                                        {{Form::label('currency',__('Currency *'),array('class'=>'form-label'))}}
                                                        {{Form::text('currency',$settings['currency'],array('class'=>'form-control font-style'))}}
                                                        <small>{{__('Note: This value will assign when any new store created by Store Owner.')}}</small>
                                                        <small>
                                                            <a href="https://stripe.com/docs/currencies" target="_blank">{{__('you can find out here..')}}</a>
                                                        </small>
                                                        <br>
                                                        <small>
                                                            {{__('This value will assign when any new store created by Store Owner.')}}
                                                        </small>

                                                        @error('currency')
                                                        <span class="invalid-currency" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                        @enderror

                                                    </div>
                                                </div>
                                                <div class="form-group col-6 col-md-3">
                                                    <div class="custom-control custom-switch p-0">
                                                        <label  class="form-check-label" for="gdpr_cookie">{{ __('GDPR Cookie')}}</label><br>
                                                        <input type="checkbox" class="form-check-input gdpr_fulltime gdpr_type" data-toggle="switchbutton" data-onstyle="primary"  name="gdpr_cookie" id="gdpr_cookie" {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'checked="checked"' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="form-group col-6 col-md-3">
                                                    <div class="custom-control form-switch p-0">
                                                        <label class="form-check-label" for="display_landing_page">{{ __('Enable Landing Page') }}</label><br>
                                                        <input type="checkbox"  name="display_landing_page" class="form-check-input" id="display_landing_page" data-toggle="switchbutton"  {{ $settings['display_landing_page'] == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                                    </div>
                                                </div>

                                                <div class="form-group col-6 col-md-3">
                                                        <div class="custom-control form-switch p-0">
                                                            <label  class="form-check-label" for="SITE_RTL">{{ __('RTL')}}</label><br>
                                                            <input type="checkbox"  class="form-check-input" data-toggle="switchbutton" data-onstyle="primary"  name="SITE_RTL" id="SITE_RTL" {{ env('SITE_RTL') == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                </div>
                                                <div class="form-group col-6 col-md-3">
                                                    <div class="custom-control form-switch p-0">
                                                        <label  class="form-check-label" for="signup_button">{{ __('Sign Up') }}</label><br>
                                                    <input type="checkbox" name="signup_button" class="form-check-input" id="signup_button" data-toggle="switchbutton" {{ Utility::getValByName('signup_button') == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                                </div>
                                            </div>
                                                <div class="form-group col-md-12">
                                                    {{Form::label('cookie_text',__('GDPR Cookie Text'),array('class'=>'fulltime form-label')) }}
                                                    {!! Form::textarea('cookie_text',isset($settings['cookie_text']) && $settings['cookie_text'] ? $settings['cookie_text'] : '', ['class'=>'form-control fulltime','style'=>'display: hidden;resize: none;','rows'=>'2']) !!}

                                                </div>
                                                <div class="setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-4 my-auto">
                                                            <h6 class="mt-2">
                                                                <i data-feather="credit-card" class="me-2"></i>{{ __('Primary color settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="theme-color themes-color">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-1" style="display: none;">

                                                                <a href="#!" class="{{($settings['color'] == 'theme-2') ? 'active_color' : ''}}" data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-2" style="display: none;">

                                                                <a href="#!" class="{{($settings['color'] == 'theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-3" style="display: none;">

                                                                <a href="#!" class="{{($settings['color'] == 'theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                                <input type="radio" class="theme_color"  name="color" value="theme-4" style="display: none;">
                                                            </div>
                                                        </div>
                                                        <div class="col-4 my-auto mt-2">
                                                            <h6 class="rtl-hide">
                                                                <i data-feather="layout" class="me-2"></i>{{__('Sidebar settings')}}
                                                            </h6>
                                                            <hr class="my-2 rtl-hide" />
                                                            <div class="form-check form-switch rtl-hide">
                                                                <input type="checkbox" class="form-check-input" id="cust-theme-bg" name="cust_theme_bg" {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
                                                                <label class="form-check-label f-w-600 pl-1" for="cust-theme-bg"
                                                                >{{__('Transparent layout')}}</label
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="col-4 my-auto mt-2 rtl-hide">
                                                            <h6 class="">
                                                            <i data-feather="sun" class="me-2"></i>{{__('Layout settings')}}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input" id="cust-darklayout" name="cust_darklayout"{{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
                                                                <label class="form-check-label f-w-600 pl-1" for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-footer p-0">
                                                    <div class="col-sm-12 mt-3 px-2">
                                                        <div class="text-end">
                                                            {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="card" id="payment-setting">
                            <div class="card-header">
                                <h5>{{('Payment Setting')}}</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-dark font-weight-bold">{{__("This detail will use for collect payment on invoice from clients. On invoice client will find out pay now button based on your below configuration.")}}</small></br></br>

                                <form id="setting-form" method="post" action="{{route('payment.setting')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                            <label class="col-form-label">{{__('Currency')}}</label>
                                                            <input type="text" name="currency" class="form-control" id="currency" value="{{ env('CURRENCY')}}" required>
                                                            <small class="text-xs">
                                                                {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                                <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                                            </small>
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                            <label for="currency_symbol" class="col-form-label">{{__('Currency Symbol')}}</label>
                                                            <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="{{ env('CURRENCY_SYMBOL')}}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="faq justify-content-center">
                                        <div class="col-sm-12 col-md-10 col-xxl-12">
                                            <div class="accordion accordion-flush" id="accordionExample">
                                                <!-- Strip -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Stripe') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse1" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Stripe') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_stripe_enabled" value="off">
                                                                        <input type="checkbox"  class="form-check-input mx-2" name="is_stripe_enabled" id="is_stripe_enabled" {{ isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_stripe_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('stripe_key',__('Stripe Key'),array('class'=>'col-form-label'))}}
                                                                        {{Form::text('stripe_key',isset($admin_payment_setting['stripe_key'])?$admin_payment_setting['stripe_key']:'',['class'=>'form-control','placeholder'=>__('Enter Stripe Key')])}}
                                                                        @error('stripe_key')
                                                                        <span class="invalid-stripe_key" role="alert">
                                                                            <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('stripe_secret',__('Stripe Secret'),array('class'=>'col-form-label'))}}
                                                                        {{Form::text('stripe_secret',isset($admin_payment_setting['stripe_secret'])?$admin_payment_setting['stripe_secret']:'',['class'=>'form-control ','placeholder'=>__('Enter Stripe Secret')])}}
                                                                        @error('stripe_secret')
                                                                        <span class="invalid-stripe_secret" role="alert">
                                                                                <strong class="text-danger">{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paypal -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-3">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paypal') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse2" class="accordion-collapse collapse"aria-labelledby="heading-2-3"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paypal') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                            <input type="hidden" name="is_paypal_enabled" value="off">
                                                                            <input type="checkbox" class="form-check-input mx-2" name="is_paypal_enabled" id="is_paypal_enabled" {{ isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="is_paypal_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="paypal-label col-form-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paypal_mode" value="sandbox" class="form-check-input" {{ !isset($admin_payment_setting['paypal_mode']) || $admin_payment_setting['paypal_mode'] == '' || $admin_payment_setting['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paypal_mode" value="live" class="form-check-input" {{ isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Client ID') }}</label>
                                                                        <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{(!isset($admin_payment_setting['paypal_client_id']) || is_null($admin_payment_setting['paypal_client_id'])) ? '' : $admin_payment_setting['paypal_client_id']}}" placeholder="{{ __('Client ID') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{(!isset($admin_payment_setting['paypal_secret_key']) || is_null($admin_payment_setting['paypal_secret_key'])) ? '' : $admin_payment_setting['paypal_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paystack -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-4">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paystack') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse3" class="accordion-collapse collapse"aria-labelledby="heading-2-4"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paystack') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paystack_enabled" value="off">
                                                                        <input type="checkbox"  class="form-check-input mx-2" name="is_paystack_enabled" id="is_paystack_enabled" {{ isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label  class="form-check-label" for="is_paystack_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{(!isset($admin_payment_setting['paystack_public_key']) || is_null($admin_payment_setting['paystack_public_key'])) ? '' : $admin_payment_setting['paystack_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{(!isset($admin_payment_setting['paystack_secret_key']) || is_null($admin_payment_setting['paystack_secret_key'])) ? '' : $admin_payment_setting['paystack_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- FLUTTERWAVE -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-5">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Flutterwave') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse4" class="accordion-collapse collapse"aria-labelledby="heading-2-5"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Flutterwave') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2" name="is_flutterwave_enabled" id="is_flutterwave_enabled" {{ isset($admin_payment_setting['is_flutterwave_enabled'])  && $admin_payment_setting['is_flutterwave_enabled']== 'on' ? 'checked="checked"' : '' }}>
                                                                        <label  class="form-check-label" for="is_flutterwave_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{(!isset($admin_payment_setting['flutterwave_public_key']) || is_null($admin_payment_setting['flutterwave_public_key'])) ? '' : $admin_payment_setting['flutterwave_public_key']}}" placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{(!isset($admin_payment_setting['flutterwave_secret_key']) || is_null($admin_payment_setting['flutterwave_secret_key'])) ? '' : $admin_payment_setting['flutterwave_secret_key']}}" placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Razorpay -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-6">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Razorpay') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse5" class="accordion-collapse collapse"aria-labelledby="heading-2-6"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Razorpay') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_razorpay_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_razorpay_enabled" id="is_razorpay_enabled" {{ isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_razorpay_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id" class="col-form-label">{{  __('Public Key')}}</label>

                                                                        <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="{{(!isset($admin_payment_setting['razorpay_public_key']) || is_null($admin_payment_setting['razorpay_public_key'])) ? '' : $admin_payment_setting['razorpay_public_key']}}" placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key" class="col-form-label"> {{  __('Secret Key')}}</label>
                                                                        <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="{{(!isset($admin_payment_setting['razorpay_secret_key']) || is_null($admin_payment_setting['razorpay_secret_key'])) ? '' : $admin_payment_setting['razorpay_secret_key']}}" placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytm -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-7">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Paytm') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse6" class="accordion-collapse collapse"aria-labelledby="heading-2-7"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Paytm') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paytm_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_paytm_enabled" id="is_paytm_enabled" {{ isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_paytm_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="paypal-label col-form-label" for="paypal_mode">{{ __('Paytm Environment')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paytm_mode" value="local" class="form-check-input" {{ !isset($admin_payment_setting['paytm_mode']) || $admin_payment_setting['paytm_mode'] == '' || $admin_payment_setting['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Local')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="paytm_mode" value="production" class="form-check-input" {{ isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Production')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_public_key" class="col-form-label">{{ __('Merchant ID')}}</label>
                                                                        <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="{{isset($admin_payment_setting['paytm_merchant_id'])? $admin_payment_setting['paytm_merchant_id']:''}}" placeholder="{{ __('Merchant ID') }}"/>
                                                                        @if ($errors->has('paytm_merchant_id'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_merchant_id') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_secret_key"  class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="{{ isset($admin_payment_setting['paytm_merchant_key']) ? $admin_payment_setting['paytm_merchant_key']:''}}" placeholder="{{ __('Merchant Key') }}"/>
                                                                        @if ($errors->has('paytm_merchant_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_merchant_key') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_industry_type"  class="col-form-label">{{ __('Industry Type') }}</label>
                                                                        <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="{{isset($admin_payment_setting['paytm_industry_type']) ?$admin_payment_setting['paytm_industry_type']:''}}" placeholder="{{ __('Industry Type') }}"/>
                                                                        @if ($errors->has('paytm_industry_type'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('paytm_industry_type') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mercado Pago-->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-8">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Mercado Pago') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse7" class="accordion-collapse collapse"aria-labelledby="heading-2-8"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Mercado Pago') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_mercado_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_mercado_enabled" id="is_mercado_enabled" {{isset($admin_payment_setting['is_mercado_enabled']) &&  $admin_payment_setting['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_mercado_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="coingate-label col-form-label" for="mercado_mode">{{__('Mercado Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="mercado_mode" value="sandbox" class="form-check-input" {{ isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == '' || isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="mercado_mode" value="live" class="form-check-input" {{ isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mercado_access_token" class="col-form-label">{{ __('Access Token') }}</label>
                                                                        <input type="text" name="mercado_access_token" id="mercado_access_token" class="form-control" value="{{isset($admin_payment_setting['mercado_access_token']) ? $admin_payment_setting['mercado_access_token']:''}}" placeholder="{{ __('Access Token') }}"/>
                                                                        @if ($errors->has('mercado_secret_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('mercado_access_token') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mollie -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-9">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Mollie') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse8" class="accordion-collapse collapse"aria-labelledby="heading-2-9"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Mollie') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_mollie_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_mollie_enabled" id="is_mollie_enabled" {{ isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_mollie_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key" class="col-form-label">{{ __('Mollie Api Key')}}</label>
                                                                        <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="{{(!isset($admin_payment_setting['mollie_api_key']) || is_null($admin_payment_setting['mollie_api_key'])) ? '' : $admin_payment_setting['mollie_api_key']}}" placeholder="Mollie Api Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_profile_id" class="col-form-label">{{ __('Mollie Profile Id')}}</label>
                                                                        <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="{{(!isset($admin_payment_setting['mollie_profile_id']) || is_null($admin_payment_setting['mollie_profile_id'])) ? '' : $admin_payment_setting['mollie_profile_id']}}" placeholder="Mollie Profile Id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_partner_id" class="col-form-label">{{ __('Mollie Partner Id')}}</label>
                                                                        <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="{{(!isset($admin_payment_setting['mollie_partner_id']) || is_null($admin_payment_setting['mollie_partner_id'])) ? '' : $admin_payment_setting['mollie_partner_id']}}" placeholder="Mollie Partner Id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skrill -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-10">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('Skrill') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse9" class="accordion-collapse collapse"aria-labelledby="heading-2-10"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('Skrill') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_skrill_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_skrill_enabled" id="is_skrill_enabled" {{ isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_skrill_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key" class="col-form-label">{{ __('Skrill Email')}}</label>
                                                                        <input type="email" name="skrill_email" id="skrill_email" class="form-control" value="{{ isset($admin_payment_setting['skrill_email'])?$admin_payment_setting['skrill_email']:''}}" placeholder="{{ __('Mollie Api Key') }}"/>
                                                                        @if ($errors->has('skrill_email'))
                                                                            <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('skrill_email') }}
                                                                                </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CoinGate -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-11">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('CoinGate') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse10" class="accordion-collapse collapse"aria-labelledby="heading-2-11"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('CoinGate') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_coingate_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_coingate_enabled" id="is_coingate_enabled" {{ isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label  class="form-check-label" for="is_coingate_enabled">{{__('Enable')}}</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pb-4">
                                                                    <label class="col-form-label" for="coingate_mode">{{ __('CoinGate Mode')}}</label> <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="coingate_mode" value="sandbox" class="form-check-input" {{ !isset($admin_payment_setting['coingate_mode']) || $admin_payment_setting['coingate_mode'] == '' || $admin_payment_setting['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Sandbox')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio" name="coingate_mode" value="live" class="form-check-input" {{ isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{__('Live')}}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="coingate_auth_token" class="col-form-label">{{ __('CoinGate Auth Token')}}</label>
                                                                        <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="{{(!isset($admin_payment_setting['coingate_auth_token']) || is_null($admin_payment_setting['coingate_auth_token'])) ? '' : $admin_payment_setting['coingate_auth_token']}}" placeholder="CoinGate Auth Token">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PaymentWall -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-12">
                                                        <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
                                                            <span class="d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i> {{ __('PaymentWall') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse11" class="accordion-collapse collapse"aria-labelledby="heading-2-12"data-bs-parent="#accordionExample" >
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-6 py-2">
                                                                    {{-- <h5 class="h5">{{ __('PaymentWall') }}</h5> --}}
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <input type="hidden" name="is_paymentwall_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input mx-2"  name="is_paymentwall_enabled" id="is_paymentwall_enabled" {{ isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label" for="is_paymentwall_enabled">{{ __('Enable') }} </label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_public_key" class="col-form-label">{{ __('Public Key')}}</label>
                                                                        <input type="text" name="paymentwall_public_key" id="paymentwall_public_key" class="form-control" value="{{(!isset($admin_payment_setting['paymentwall_public_key']) || is_null($admin_payment_setting['paymentwall_public_key'])) ? '' : $admin_payment_setting['paymentwall_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_private_key" class="col-form-label">{{ __('Private Key') }}</label>
                                                                        <input type="text" name="paymentwall_private_key" id="paymentwall_private_key" class="form-control" value="{{(!isset($admin_payment_setting['paymentwall_private_key']) || is_null($admin_payment_setting['paymentwall_private_key'])) ? '' : $admin_payment_setting['paymentwall_private_key']}}" placeholder="{{ __('Private Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer p-0">
                                        <div class="col-sm-12 mt-3 px-2">
                                            <div class="text-end">
                                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                        <div id="email-settings" class="tab-pane">
                            <div class="col-md-12">

                                <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Email settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            {{Form::open(array('route'=>'email.setting','method'=>'post'))}}
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_driver',__('Mail Driver'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_driver',env('MAIL_DRIVER'),array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))}}
                                            @error('mail_driver')
                                            <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_host',__('Mail Host'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_host',env('MAIL_HOST'),array('class'=>'form-control ','placeholder'=>__('Enter Mail Driver')))}}
                                            @error('mail_host')
                                            <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_port',__('Mail Port'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_port',env('MAIL_PORT'),array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))}}
                                            @error('mail_port')
                                            <span class="invalid-mail_port" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_username',__('Mail Username'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_username',env('MAIL_USERNAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))}}
                                            @error('mail_username')
                                            <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_password',__('Mail Password'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_password',env('MAIL_PASSWORD'),array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))}}
                                            @error('mail_password')
                                            <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_encryption',__('Mail Encryption'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_encryption',env('MAIL_ENCRYPTION'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                            @error('mail_encryption')
                                            <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_from_address',__('Mail From Address'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_from_address',env('MAIL_FROM_ADDRESS'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))}}
                                            @error('mail_from_address')
                                            <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{Form::label('mail_from_name',__('Mail From Name'),array('class'=>'form-label')) }}
                                            {{Form::text('mail_from_name',env('MAIL_FROM_NAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                            @error('mail_from_name')
                                            <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-lg-12 ">
                                        <div class="row">
                                        <div class=" text-end">
                                            <div class="card-footer p-0">
                                                <div class="col-sm-12 mt-3 px-2">
                                                    <div class="d-flex justify-content-end">
                                                            <a href="#"   data-ajax-popup="true" data-size="md" data-url="{{route('test.mail')}}" data-title="{{__('Send Test Mail')}}"  class="btn btn-xs  btn-info send_email">
                                                            {{ __('Send Test Mail') }}
                                                        </a>
                                                            {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{Form::close()}}
                                </div>
                                </div>
                            </div>
                        </div>
                        <div id="recaptcha-settings" class="card">
                            <div class="col-md-12">

                                    <div class="card-header">
                                        <h5 class="">{{ __('ReCaptcha settings') }}</h5>
                                    </div>

                                <div class="card-body">
                                    <form method="POST" action="{{ route('recaptcha.settings.store') }}" accept-charset="UTF-8">
                                        @csrf
                                        <div class="row ">
                                            <div class="col-lg-12 col-md-12 col-sm-12 form-group text-end ">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        <div class="custom-control custom-switch">
                                                            <label class="custom-control-label form-control-label px-2" for="recaptcha_module ">{{__('Google Recaptcha')}}</label><br>
                                                            <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary" class="" value="yes" name="recaptcha_module" id="recaptcha_module" {{!empty(env('RECAPTCHA_MODULE')) && env('RECAPTCHA_MODULE') == 'yes' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="google_recaptcha_key" class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                                <input class="form-control" placeholder="{{ __('Enter Google Recaptcha Key') }}" name="google_recaptcha_key" type="text" value="{{env('NOCAPTCHA_SITEKEY')}}" id="google_recaptcha_key">
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="google_recaptcha_secret" class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                                <input class="form-control " placeholder="{{ __('Enter Google Recaptcha Secret') }}" name="google_recaptcha_secret" type="text" value="{{env('NOCAPTCHA_SECRET')}}" id="google_recaptcha_secret">
                                            </div>
                                            <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/" target="_blank" class="text-blue">
                                                <small>({{__('How to Get Google reCaptcha Site and Secret key')}})</small>
                                            </a>
                                        </div>
                                        <div class="card-footer p-0">
                                            <div class="col-sm-12 mt-3 px-2">
                                                <div class="text-end">
                                                    {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
            <!-- [ sample-page ] end -->
        </div>
    </div>
@endsection
@push('script-page')
    <script src="{{asset('custom/libs/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>

    <script>
        function myFunction() {
            var copyText = document.getElementById("myInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            show_toastr('Success', "{{__('Link copied')}}", 'success');
        }

        $(document).on('click', 'input[name="theme_color"]', function () {
            var eleParent = $(this).attr('data-theme');
            $('#themefile').val(eleParent);
            var imgpath = $(this).attr('data-imgpath');
            $('.' + eleParent + '_img').attr('src', imgpath);
        });

        $(document).ready(function () {
            setTimeout(function (e) {
                var checked = $("input[type=radio][name='theme_color']:checked");
                $('#themefile').val(checked.attr('data-theme'));
                $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
            }, 300);
        });
    </script>
@endpush
