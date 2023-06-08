@extends('layouts.admin')
@php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_logo=\App\Models\Utility::getValByName('company_logo');
    $company_favicon=\App\Models\Utility::getValByName('company_favicon');
    $store_logo=asset(Storage::url('uploads/store_logo/'));
    $lang=\App\Models\Utility::getValByName('default_language');
    if(Auth::user()->type == 'Owner')
    {
        $store_lang=$store->lang;
    }
@endphp
@section('page-title')
    {{__('Store Theme Setting')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings') }}">{{ __(' Store Settings') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Store Theme Setting') }}</li>
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-bold mb-0 text-white">{{__('Store Theme Setting')}}</h5>
    </div>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
    <style>
        hr {
            margin: 8px;
        }
    </style>
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        @if(Auth::user()->type == 'Owner')
                            @if($theme == 'theme1')
                                <a href="#Top_Bar_Setting" id="Top_Bar_Setting_tab"
                                    class="list-group-item list-group-item-action">{{ __('Top Bar Setting') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme3'|| $theme == 'theme4')
                                <a href="#Banner_Img_Setting" id="Banner_Img_Setting_tab"
                                    class="list-group-item list-group-item-action">{{__('Banner Img Setting')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1'|| $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                                <a href="#Header_Setting" id="Header_Setting_tab"
                                    class="list-group-item list-group-item-action">{{__('Header Setting')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme3' || $theme == 'theme5')
                                <a href="#Features_Setting" id="Features_Setting_tab"
                                    class="list-group-item list-group-item-action">{{__('Features Setting')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                                <a href="#Email_Subscriber_Setting" id="Email_Subscriber_Setting_tab"
                                    class="list-group-item list-group-item-action">{{__('Email Subscriber Setting')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme3')
                                <a href="#Categories" id="Categories_tab"
                                    class="list-group-item list-group-item-action">{{__('Categories')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme3'|| $theme == 'theme4' || $theme == 'theme5')
                                <a href="#Testimonials" id="Testimonials_tab"
                                    class="list-group-item list-group-item-action">{{__('Testimonials')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                                <a href="#Brand_Logo" id="Brand_Logo_tab"
                                    class="list-group-item list-group-item-action">{{__('Brand Logo')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif

                            @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme5')
                                <a href="#Footer_1" id="Footer_1_tab"
                                    class="list-group-item list-group-item-action">{{__('Footer 1')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme3'|| $theme == 'theme4')
                                <a href="#Footer_1" id="Footer_1_tab"
                                    class="list-group-item list-group-item-action">{{__('Footer 1')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme3'|| $theme == 'theme4' || $theme == 'theme5')
                                <a href="#Footer_2" id="Footer_2_tab"
                                    class="list-group-item list-group-item-action">{{__('Footer 2')}}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
            @if(Auth::user()->type == 'Owner')
            <div class="col-xl-9">
                {{Form::open(array('route'=>array('store.storeeditproducts',[$store->slug,$theme]),'method'=>'post','enctype'=>'multipart/form-data'))}}
                @if($theme == 'theme1')
                <div class="active" id="Top_Bar_Setting">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Top Bar Setting') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note: This detail will use to change header setting.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_top_bar" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_top_bar']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_top_bar" id="enable_top_bar" {{ $getStoreThemeSetting['enable_top_bar'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_top_bar" id="enable_top_bar">
                                                    @endif
                                                    <label class="form-check-label" for="enable_top_bar">{{__('Enable Header Img')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_title',__('Top Bar Title'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_title',!empty($getStoreThemeSetting['top_bar_title'])?$getStoreThemeSetting['top_bar_title']:'',array('class'=>'form-control','placeholder'=>__('Enter Top Bar Title')))}}
                                                    @error('top_bar_title')
                                                    <span class="invalid-top_bar_title" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_number',__('Top Bar Number'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_number',!empty($getStoreThemeSetting['top_bar_number'])?$getStoreThemeSetting['top_bar_number']:'',array('class'=>'form-control','placeholder'=>__('Top Bar Number')))}}
                                                    @error('top_bar_number')
                                                    <span class="invalid-top_bar_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_whatsapp',__('Whatsapp'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_whatsapp',!empty($getStoreThemeSetting['top_bar_whatsapp'])?$getStoreThemeSetting['top_bar_whatsapp']:'',array('class'=>'form-control','placeholder'=>__('Enter Whatsapp')))}}
                                                    @error('top_bar_whatsapp')
                                                    <span class="invalid-top_bar_whatsapp" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_instagram',__('Instagram'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_instagram',!empty($getStoreThemeSetting['top_bar_instagram'])?$getStoreThemeSetting['top_bar_instagram']:'',array('class'=>'form-control','placeholder'=>__('Enter Instagram')))}}
                                                    @error('top_bar_instagram')
                                                    <span class="invalid-top_bar_instagram" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_twitter',__('Twitter'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_twitter',!empty($getStoreThemeSetting['top_bar_twitter'])?$getStoreThemeSetting['top_bar_twitter']:'',array('class'=>'form-control','placeholder'=>__('Enter Twitter')))}}
                                                    @error('top_bar_twitter')
                                                    <span class="invalid-top_bar_twitter" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('top_bar_messenger',__('Messenger'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('top_bar_messenger',!empty($getStoreThemeSetting['top_bar_messenger'])?$getStoreThemeSetting['top_bar_messenger']:'',array('class'=>'form-control','placeholder'=>__('Enter Messenger')))}}
                                                    @error('top_bar_messenger')
                                                    <span class="invalid-top_bar_messenger" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($theme == 'theme3'|| $theme == 'theme4')
                <div class="active" id="Banner_Img_Setting">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Banner Img Setting') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note: This detail will use to change header setting.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_banner_img" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_banner_img']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_banner_img" id="enable_banner_img" {{ $getStoreThemeSetting['enable_banner_img'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_banner_img" id="enable_banner_img">
                                                    @endif
                                                    <label class="form-check-label" for="enable_banner_img">{{__('Enable Banner Img')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="banner_img"
                                                        class="col-form-label">{{ __('Banner Image') }}</label>
                                                    <input type="file" name="banner_img" id="banner_img" value="{{!empty($getStoreThemeSetting['banner_img'])?$getStoreThemeSetting['banner_img']:''}}"
                                                        class="form-control custom-input-file">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1'|| $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                <div class="active" id="Header_Setting">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Header Setting') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note: This detail will use to change header setting.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_header_img" value="off">
                                                    @if(isset($getStoreThemeSetting['enable_header_img']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_header_img" id="enable_header_img" {{ $getStoreThemeSetting['enable_header_img'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_header_img" id="enable_header_img">
                                                    @endif
                                                    <label class="form-check-label" for="enable_header_img">{{__('Enable Header Img')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="header_img"
                                                        class="col-form-label">{{ __('Header Image') }}</label>
                                                    <input type="file" name="header_img" id="header_img" value="{{!empty($getStoreThemeSetting['header_img'])?$getStoreThemeSetting['header_img']:''}}"
                                                        class="form-control custom-input-file">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('header_title',__('Header Title'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('header_title',!empty($getStoreThemeSetting['header_title'])?$getStoreThemeSetting['header_title']:'',array('class'=>'form-control','placeholder'=>__('Enter Header Title')))}}
                                                    @error('header_title')
                                                    <span class="invalid-header_title" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('header_desc',__('Header Description'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('header_desc',!empty($getStoreThemeSetting['header_desc'])?$getStoreThemeSetting['header_desc']:'',array('class'=>'form-control','placeholder'=>__('Enter Header Description')))}}
                                                    @error('header_desc')
                                                    <span class="invalid-header_desc" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('button_text',__('Header Button Text'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('button_text',!empty($getStoreThemeSetting['button_text'])?$getStoreThemeSetting['button_text']:'',array('class'=>'form-control','placeholder'=>__('Enter Header Button Text')))}}
                                                    @error('button_text')
                                                    <span class="invalid-button_text" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme3' || $theme == 'theme5')
                <div class="active" id="Features_Setting">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Features Setting') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note: This detail will use to change header setting.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_features" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_features']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_features" id="enable_features" {{ $getStoreThemeSetting['enable_features'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_features" id="enable_features">
                                                    @endif
                                                    <label class="form-check-label" for="enable_features">{{__('Enable Features')}}</label>
                                                </div>
                                            </div>
                                            <hr class="">
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_features1" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_features1']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_features1" id="enable_features1" {{ $getStoreThemeSetting['enable_features1'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_features1" id="enable_features1">
                                                    @endif
                                                    <label class="form-check-label" for="enable_features1">{{__('Enable Features 1')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('features_icon1',__('Features Font Icon 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('features_icon1',!empty($getStoreThemeSetting['features_icon1'])?$getStoreThemeSetting['features_icon1']:'',array('class'=>'form-control','placeholder'=>__('Enter Features Font Icon 1')))}}
                                                    <a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2" target="_blank">
                                                        <small>{{__('Please click here to find font')}}</small> ... <b class="text-sm font-weight-bold">{{__('fontawesome.com')}}</b>
                                                    </a>
                                                    @error('features_icon1')
                                                    <span class="invalid-features_icon1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('features_title1',__('Features Title 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('features_title1',!empty($getStoreThemeSetting['features_title1'])?$getStoreThemeSetting['features_title1']:'',array('class'=>'form-control','placeholder'=>__('Enter features Title 1')))}}
                                                    @error('features_title1')
                                                    <span class="invalid-features_title1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{Form::label('features_description1',__('Features Description 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('features_description1',!empty($getStoreThemeSetting['features_description1'])?$getStoreThemeSetting['features_description1']:'',array('class'=>'form-control','placeholder'=>__('Features Description 1')))}}
                                                    @error('features_description1')
                                                    <span class="invalid-features_description1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                <div class="active" id="Email_Subscriber_Setting">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Email Subscriber Setting') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note: This detail will use to change header setting.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">

                                                    <input type="hidden" name="enable_email_subscriber" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_email_subscriber']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_email_subscriber" id="enable_email_subscriber" {{ $getStoreThemeSetting['enable_email_subscriber'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_email_subscriber" id="enable_email_subscriber">
                                                    @endif
                                                    <label class="form-check-label" for="enable_email_subscriber">{{__('Enable Email Subscriber')}}</label>
                                                </div>
                                            </div>
                                            @if($theme != 'theme5')
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="logo" class="col-form-label">{{ __('Subscriber Background Image') }}</label>
                                                        @if(!empty($getStoreThemeSetting['subscriber_img']))
                                                            <input type="file" name="subscriber_img" id="subscriber_img" class="form-control custom-input-file" value="{{$getStoreThemeSetting['subscriber_img']}}">
                                                        @else
                                                            <input type="file" name="subscriber_img" id="subscriber_img" class="form-control custom-input-file" value="">
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('subscriber_title',__('Subscriber Title'),array('class'=>'col-form-label  ')) }}
                                                    {{Form::text('subscriber_title',!empty($getStoreThemeSetting['subscriber_title'])?$getStoreThemeSetting['subscriber_title']:'',array('class'=>'form-control','placeholder'=>__('Enter Subscriber Title')))}}
                                                    @error('subscriber_title')
                                                    <span class="invalid-subscriber_title" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('subscriber_sub_title',__('Subscriber Sub Title'),array('class'=>'col-form-label  ')) }}
                                                    {{Form::text('subscriber_sub_title',!empty($getStoreThemeSetting['subscriber_sub_title'])?$getStoreThemeSetting['subscriber_sub_title']:'',array('class'=>'form-control','placeholder'=>__('Enter Subscriber Sub Title')))}}
                                                    @error('subscriber_sub_title')
                                                    <span class="invalid-subscriber_sub_title" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme3')
                <div class="active" id="Categories">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Categories') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">

                                                    <input type="hidden" name="enable_categories" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_categories']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_categories" id="enable_categories" {{ $getStoreThemeSetting['enable_categories'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_categories" id="enable_categories">
                                                    @endif
                                                    <label class="form-check-label" for="enable_categories">{{__('Enable Categories')}}</label>
                                                </div>
                                            </div>
                                            @if($theme != 'theme5')
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('categories',__('Categories'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('categories',!empty($getStoreThemeSetting['categories'])?$getStoreThemeSetting['categories']:'',array('class'=>'form-control','placeholder'=>'Categories'))}}
                                                        @error('categories')
                                                        <span class="invalid-categories" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('categories_title',__('Categories Title'),array('class'=>'col-form-label')) }}
                                                        {{Form::textarea('categories_title',!empty($getStoreThemeSetting['categories_title'])?$getStoreThemeSetting['categories_title']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Categories Title'))}}
                                                        @error('categories_title')
                                                        <span class="invalid-categories_title" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme3'|| $theme == 'theme4' || $theme == 'theme5')
                <div class="active" id="Testimonials">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Testimonials') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_testimonial" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_testimonial']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial" id="enable_testimonial" {{ $getStoreThemeSetting['enable_testimonial'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial" id="enable_testimonial">
                                                    @endif
                                                    <label class="form-check-label" for="enable_testimonial">{{__('Enable Testimonials')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_main_heading',__('Main Heading'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_main_heading',!empty($getStoreThemeSetting['testimonial_main_heading'])?$getStoreThemeSetting['testimonial_main_heading']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Main Heading'))}}
                                                    @error('testimonial_main_heading')
                                                    <span class="invalid-testimonial_main_heading" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_main_heading_title',__('Main Heading Title'),array('class'=>'col-form-label')) }}
                                                    {{Form::textarea('testimonial_main_heading_title',!empty($getStoreThemeSetting['testimonial_main_heading_title'])?$getStoreThemeSetting['testimonial_main_heading_title']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Main Heading Title'))}}
                                                    @error('testimonial_main_heading_title')
                                                    <span class="invalid-testimonial_main_heading_title" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_testimonial1" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_testimonial1']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial1" id="enable_testimonial1" {{ $getStoreThemeSetting['enable_testimonial1'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial1" id="enable_testimonial1">
                                                    @endif
                                                    <label class="form-check-label" for="enable_testimonial1">{{__('Enable Testimonial 1')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="testimonial_img1" class="col-form-label">{{__('Image')}}</label>
                                                    <input type="file" class="form-control custom-input-file" name="testimonial_img1" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_name1',__('Name'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_name1',!empty($getStoreThemeSetting['testimonial_name1'])?$getStoreThemeSetting['testimonial_name1']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Name'))}}
                                                    @error('testimonial_name1')
                                                    <span class="invalid-testimonial_name1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_about_us1',__('About Us'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_about_us1',!empty($getStoreThemeSetting['testimonial_about_us1'])?$getStoreThemeSetting['testimonial_about_us1']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'About Us'))}}
                                                    @error('testimonial_about_us1')
                                                    <span class="invalid-testimonial_about_us1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_description1',__('Description'),array('class'=>'col-form-label')) }}
                                                    {{Form::textarea('testimonial_description1',!empty($getStoreThemeSetting['testimonial_description1'])?$getStoreThemeSetting['testimonial_description1']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Description'))}}
                                                    @error('testimonial_description1')
                                                    <span class="invalid-testimonial_description1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            {{--TESTIMONIAL 2--}}
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_testimonial2" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_testimonial2']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial2" id="enable_testimonial2" {{ $getStoreThemeSetting['enable_testimonial2'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial2" id="enable_testimonial2">
                                                    @endif
                                                    <label class="form-check-label" for="enable_testimonial2">{{__('Enable Testimonial 2')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="testimonial_img2" class="col-form-label">{{__('Image')}}</label>
                                                    <input type="file" class="form-control custom-input-file" name="testimonial_img2" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_name2',__('Name'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_name2',!empty($getStoreThemeSetting['testimonial_name2'])?$getStoreThemeSetting['testimonial_name2']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Name'))}}
                                                    @error('testimonial_name2')
                                                    <span class="invalid-testimonial_name2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_about_us2',__('About Us'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_about_us2',!empty($getStoreThemeSetting['testimonial_about_us2'])?$getStoreThemeSetting['testimonial_about_us2']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'About Us'))}}
                                                    @error('testimonial_about_us2')
                                                    <span class="invalid-testimonial_about_us2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_description2',__('Description'),array('class'=>'col-form-label')) }}
                                                    {{Form::textarea('testimonial_description2',!empty($getStoreThemeSetting['testimonial_description2'])?$getStoreThemeSetting['testimonial_description2']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Description'))}}
                                                    @error('testimonial_description2')
                                                    <span class="invalid-testimonial_description2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            {{--TESTIMONIAL 3--}}
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_testimonial3" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_testimonial3']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial3" id="enable_testimonial3" {{ $getStoreThemeSetting['enable_testimonial3'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_testimonial3" id="enable_testimonial3">
                                                    @endif
                                                    <label class="form-check-label" for="enable_testimonial3">{{__('Enable Testimonial 3')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="testimonial_img3" class="col-form-label">{{__('Image')}}</label>
                                                    <input type="file" class="form-control custom-input-file" name="testimonial_img3" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_name3',__('Name'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_name3',!empty($getStoreThemeSetting['testimonial_name3'])?$getStoreThemeSetting['testimonial_name3']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Name'))}}
                                                    @error('testimonial_name3')
                                                    <span class="invalid-testimonial_name3" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_about_us3',__('About Us'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('testimonial_about_us3',!empty($getStoreThemeSetting['testimonial_about_us3'])?$getStoreThemeSetting['testimonial_about_us3']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'About Us'))}}
                                                    @error('testimonial_about_us3')
                                                    <span class="invalid-testimonial_about_us3" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('testimonial_description3',__('Description'),array('class'=>'col-form-label')) }}
                                                    {{Form::textarea('testimonial_description3',!empty($getStoreThemeSetting['testimonial_description3'])?$getStoreThemeSetting['testimonial_description3']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Description'))}}
                                                    @error('testimonial_description3')
                                                    <span class="invalid-testimonial_description3" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2'|| $theme == 'theme4' || $theme == 'theme5')
                <div class="active" id="Brand_Logo">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Brand Logo') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_brand_logo" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_brand_logo']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_brand_logo" id="enable_brand_logo" {{ $getStoreThemeSetting['enable_brand_logo'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_brand_logo" id="enable_brand_logo">
                                                    @endif
                                                    <label class="form-check-label" for="enable_brand_logo">{{__('Enable Brand Logo')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="file" class="col-form-label">{{__('Brand Logo')}}</label>
                                                    <input type="file" name="file[]" id="file" class="form-control custom-input-file" multiple>
                                                </div>
                                                <div id="img-count" class="badge badge-success rounded-pill"></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="card-wrapper p-3 lead-common-box">
                                                    @if(isset($getStoreThemeSetting['brand_logo']) && $getStoreThemeSetting['brand_logo'] != null)
                                                        @foreach(explode(',',$getStoreThemeSetting['brand_logo']) as $file)
                                                            <div class="card mb-3 border shadow-none product_Image" data-value="{{$file}}">
                                                                <div class="px-3 py-3">
                                                                    <div class="row align-items-center">
                                                                        <div class="col ml-n2">
                                                                            <p class="card-text small text-muted">
                                                                                <img class="rounded" src=" {{asset(Storage::url('uploads/store_logo/'.$file))}}" width="70px" alt="Image placeholder" data-dz-thumbnail>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-auto actions">
                                                                            <a class="action-item" href=" {{asset(Storage::url('uploads/store_logo/'.$file))}}" download="" data-toggle="tooltip" data-original-title="{{__('Download')}}">
                                                                                <i class="fas fa-download"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-auto actions">
                                                                            <a name="deleteRecord" class="action-item deleteRecord" data-name="{{$file}}">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme5')
                <div class="active" id="Footer_1">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Footer 1') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_footer_note" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_footer_note']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer_note" id="enable_footer_note" {{ $getStoreThemeSetting['enable_footer_note'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer_note" id="enable_footer_note">
                                                    @endif
                                                    <label class="form-check-label" for="enable_footer_note">{{__('Enable Footer Note')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                {{Form::label('footer_logo',__('Footer Logo'),array('class'=>'col-form-label')) }}
                                                <input type="file" name="footer_logo" id="footer_logo" class="form-control custom-input-file">
                                                @error('footer_logo')
                                                    <span class="invalid-footer_logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            @if($theme == 'theme2')
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('footer_number',__('Footer Number'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('footer_number',!empty($getStoreThemeSetting['footer_number'])?$getStoreThemeSetting['footer_number']:'',array('class'=>'form-control','placeholder'=>'Enter Footer Number'))}}
                                                    </div>
                                                    @error('footer_number')
                                                    <span class="invalid-footer_number" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            @endif
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link1" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link1']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link1" id="enable_quick_link1" {{ $getStoreThemeSetting['enable_quick_link1'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link1" id="enable_quick_link1">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link1">{{__('Enable Quick Link 1')}}</label>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                {{Form::label('quick_link_header_name1',__('Footer Quick Link Header Name 1'),array('class'=>'col-form-label')) }}
                                                {{Form::text('quick_link_header_name1',!empty($getStoreThemeSetting['quick_link_header_name1'])?$getStoreThemeSetting['quick_link_header_name1']:'',array('class'=>'form-control','placeholder'=>'Enter Footer Quick Link Header Name 1'))}}
                                                @error('quick_link_header_name1')
                                                <span class="invalid-quick_link_header_name1" role="alert">
                                                             <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name1',__('Quick Link Name 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name1',!empty($getStoreThemeSetting['quick_link_name1'])?$getStoreThemeSetting['quick_link_name1']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 1'))}}
                                                    @error('quick_link_name1')
                                                    <span class="invalid-quick_link_name1" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url1',__('Quick Link Url 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url1',!empty($getStoreThemeSetting['quick_link_url1'])?$getStoreThemeSetting['quick_link_url1']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 1'))}}
                                                    @error('quick_link_url1')
                                                    <span class="invalid-quick_link_url1" role="alert">
                                                                             <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name12',__('Quick Link Name 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name12',!empty($getStoreThemeSetting['quick_link_name12'])?$getStoreThemeSetting['quick_link_name12']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name1')
                                                    <span class="invalid-quick_link_name1" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url12',__('Quick Link Url 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url12',!empty($getStoreThemeSetting['quick_link_url12'])?$getStoreThemeSetting['quick_link_url12']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 2'))}}
                                                    @error('quick_link_url1')
                                                    <span class="invalid-quick_link_url1" role="alert">
                                                                             <strong class="text-danger">{{ $message }}</strong>
                                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name13',__('Quick Link Name 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name13',!empty($getStoreThemeSetting['quick_link_name13'])?$getStoreThemeSetting['quick_link_name13']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name1')
                                                    <span class="invalid-quick_link_name1" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url13',__('Quick Link Url 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url13',!empty($getStoreThemeSetting['quick_link_url13'])?$getStoreThemeSetting['quick_link_url13']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 2'))}}
                                                    @error('quick_link_url1')
                                                    <span class="invalid-quick_link_url1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name14',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name14',!empty($getStoreThemeSetting['quick_link_name14'])?$getStoreThemeSetting['quick_link_name14']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name1')
                                                    <span class="invalid-quick_link_name1" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url14',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url14',!empty($getStoreThemeSetting['quick_link_url14'])?$getStoreThemeSetting['quick_link_url14']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 2'))}}
                                                    @error('quick_link_url1')
                                                    <span class="invalid-quick_link_url1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link2" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link2']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link2" id="enable_quick_link2" {{ $getStoreThemeSetting['enable_quick_link2'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link2" id="enable_quick_link2">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link2">{{__('Enable Quick Link 2')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                            <div class="form-group">
                                                {{Form::label('quick_link_header_name2',__('Footer Quick Link Header Name 2'),array('class'=>'col-form-label')) }}
                                                {{Form::text('quick_link_header_name2',!empty($getStoreThemeSetting['quick_link_header_name2'])?$getStoreThemeSetting['quick_link_header_name2']:'',array('class'=>'form-control','placeholder'=>'Enter Footer Quick Link Header Name 2'))}}
                                                @error('quick_link_header_name2')
                                                <span class="invalid-quick_link_header_name2" role="alert">
                                                             <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                @enderror
                                            </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name21',__('Quick Link Name 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name21',!empty($getStoreThemeSetting['quick_link_name21'])?$getStoreThemeSetting['quick_link_name21']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 1'))}}
                                                    @error('quick_link_name2')
                                                    <span class="invalid-footer_link_name" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url21',__('Quick Link Url 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url21',!empty($getStoreThemeSetting['quick_link_url21'])?$getStoreThemeSetting['quick_link_url21']:'',array('class'=>'form-control','placeholder'=>'Quick Link Url 1'))}}
                                                    @error('quick_link_url2')
                                                    <span class="invalid-quick_link_url2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name22',__('Quick Link Name 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name22',!empty($getStoreThemeSetting['quick_link_name22'])?$getStoreThemeSetting['quick_link_name22']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name2')
                                                    <span class="invalid-footer_link_name" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url22',__('Quick Link Url 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url22',!empty($getStoreThemeSetting['quick_link_url22'])?$getStoreThemeSetting['quick_link_url22']:'',array('class'=>'form-control','placeholder'=>'Quick Link Url 2'))}}
                                                    @error('quick_link_url2')
                                                    <span class="invalid-quick_link_url2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name23',__('Quick Link Name 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name23',!empty($getStoreThemeSetting['quick_link_name23'])?$getStoreThemeSetting['quick_link_name23']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 3'))}}
                                                    @error('quick_link_name2')
                                                    <span class="invalid-footer_link_name" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url23',__('Quick Link Url 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url23',!empty($getStoreThemeSetting['quick_link_url23'])?$getStoreThemeSetting['quick_link_url23']:'',array('class'=>'form-control','placeholder'=>'Quick Link Url 3'))}}
                                                    @error('quick_link_url2')
                                                    <span class="invalid-quick_link_url2" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name24',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name24',!empty($getStoreThemeSetting['quick_link_name24'])?$getStoreThemeSetting['quick_link_name24']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                    @error('quick_link_name2')
                                                    <span class="invalid-footer_link_name" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url24',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url24',!empty($getStoreThemeSetting['quick_link_url24'])?$getStoreThemeSetting['quick_link_url24']:'',array('class'=>'form-control','placeholder'=>'Quick Link Url 4'))}}
                                                    @error('quick_link_url2')
                                                    <span class="invalid-quick_link_url2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link3" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link3']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link3" id="enable_quick_link3" {{ $getStoreThemeSetting['enable_quick_link3'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link3" id="enable_quick_link3">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link3">{{__('Enable Quick Link 3')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_header_name3',__('Footer Quick Link Header Name 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_header_name3',!empty($getStoreThemeSetting['quick_link_header_name3'])?$getStoreThemeSetting['quick_link_header_name3']:'Company',array('class'=>'form-control','placeholder'=>'Enter Footer Quick Link Header Name 3'))}}
                                                    @error('quick_link_header_name3')
                                                    <span class="invalid-quick_link_header_name3" role="alert">
                                                             <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name31',__('Quick Link Name 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name31',!empty($getStoreThemeSetting['quick_link_name31'])?$getStoreThemeSetting['quick_link_name31']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 1'))}}
                                                    @error('quick_link_name3')
                                                    <span class="invalid-quick_link_name3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url31',__('Quick Link Url 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url31',!empty($getStoreThemeSetting['quick_link_url31'])?$getStoreThemeSetting['quick_link_url31']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 1'))}}
                                                    @error('quick_link_url3')
                                                    <span class="invalid-quick_link_url3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name32',__('Quick Link Name 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name32',!empty($getStoreThemeSetting['quick_link_name32'])?$getStoreThemeSetting['quick_link_name32']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name3')
                                                    <span class="invalid-quick_link_name3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url32',__('Quick Link Name 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url32',!empty($getStoreThemeSetting['quick_link_url32'])?$getStoreThemeSetting['quick_link_url32']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_url3')
                                                    <span class="invalid-quick_link_url3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name33',__('Quick Link Name 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name33',!empty($getStoreThemeSetting['quick_link_name33'])?$getStoreThemeSetting['quick_link_name33']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 3'))}}
                                                    @error('quick_link_name3')
                                                    <span class="invalid-quick_link_name3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url33',__('Quick Link Url 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url33',!empty($getStoreThemeSetting['quick_link_url33'])?$getStoreThemeSetting['quick_link_url33']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 3'))}}
                                                    @error('quick_link_url3')
                                                    <span class="invalid-quick_link_url3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name34',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name34',!empty($getStoreThemeSetting['quick_link_name34'])?$getStoreThemeSetting['quick_link_name34']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                    @error('quick_link_name3')
                                                    <span class="invalid-quick_link_name3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url34',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url34',!empty($getStoreThemeSetting['quick_link_url34'])?$getStoreThemeSetting['quick_link_url34']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                    @error('quick_link_url3')
                                                    <span class="invalid-quick_link_url3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link4" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link4']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link4" id="enable_quick_link4" {{ $getStoreThemeSetting['enable_quick_link4'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link4" id="enable_quick_link4">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link4">{{__('Enable Quick Link 4')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_header_name4',__('Footer Quick Link Header Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_header_name4',!empty($getStoreThemeSetting['quick_link_header_name4'])?$getStoreThemeSetting['quick_link_header_name4']:'',array('class'=>'form-control','placeholder'=>'Enter Footer Quick Link Header Name 4'))}}
                                                    @error('quick_link_header_name4')
                                                    <span class="invalid-quick_link_header_name4" role="alert">
                                                                 <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name41',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name41',!empty($getStoreThemeSetting['quick_link_name41'])?$getStoreThemeSetting['quick_link_name41']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                    @error('quick_link_name4')
                                                    <span class="invalid-quick_link_name4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url41',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url41',!empty($getStoreThemeSetting['quick_link_url41'])?$getStoreThemeSetting['quick_link_url41']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                    @error('quick_link_url4')
                                                    <span class="invalid-quick_link_url4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name42',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name42',!empty($getStoreThemeSetting['quick_link_name42'])?$getStoreThemeSetting['quick_link_name42']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                    @error('quick_link_name4')
                                                    <span class="invalid-quick_link_name4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url42',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url42',!empty($getStoreThemeSetting['quick_link_url42'])?$getStoreThemeSetting['quick_link_url42']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                    @error('quick_link_url4')
                                                    <span class="invalid-quick_link_url4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if($theme != 'theme5')
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('quick_link_name43',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('quick_link_name43',!empty($getStoreThemeSetting['quick_link_name43'])?$getStoreThemeSetting['quick_link_name43']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                        @error('quick_link_name4')
                                                        <span class="invalid-quick_link_name4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('quick_link_url43',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('quick_link_url43',!empty($getStoreThemeSetting['quick_link_url43'])?$getStoreThemeSetting['quick_link_url43']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                        @error('quick_link_url4')
                                                        <span class="invalid-quick_link_url4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('quick_link_name44',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('quick_link_name44',!empty($getStoreThemeSetting['quick_link_name44'])?$getStoreThemeSetting['quick_link_name44']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'Enter Quick Link Name 4'))}}
                                                        @error('quick_link_name4')
                                                        <span class="invalid-quick_link_name4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        {{Form::label('quick_link_url44',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                        {{Form::text('quick_link_url44',!empty($getStoreThemeSetting['quick_link_url44'])?$getStoreThemeSetting['quick_link_url44']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                        @error('quick_link_url4')
                                                        <span class="invalid-quick_link_url4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($theme == 'theme3'|| $theme == 'theme4')
                <div class="active" id="Footer_1">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Footer 1') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_footer_note" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_footer_note']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer_note" id="enable_footer_note" {{ $getStoreThemeSetting['enable_footer_note'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer_note" id="enable_footer_note">
                                                    @endif
                                                    <label class="form-check-label" for="enable_footer_note">{{__('Enable Footer Note')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('footer_logo',__('Footer Logo'),array('class'=>'form-control-label')) }}
                                                    <input type="file" name="footer_logo" id="footer_logo" class="form-control custom-input-file">
                                                    @error('footer_logo')
                                                            <span class="invalid-footer_logo" role="alert">
                                                             <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                            @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link1" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link1']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link1" id="enable_quick_link1" {{ $getStoreThemeSetting['enable_quick_link1'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link1" id="enable_quick_link1">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link1">{{__('Enable Quick Link 1')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name1',__('Quick Link Name 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name1',!empty($getStoreThemeSetting['quick_link_name1'])?$getStoreThemeSetting['quick_link_name1']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 1'))}}
                                                    @error('quick_link_name1')
                                                    <span class="invalid-quick_link_name1" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url1',__('Quick Link Url 1'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url1',!empty($getStoreThemeSetting['quick_link_url1'])?$getStoreThemeSetting['quick_link_url1']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 1'))}}
                                                    @error('quick_link_url1')
                                                    <span class="invalid-quick_link_url1" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link2" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link2']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link2" id="enable_quick_link2" {{ $getStoreThemeSetting['enable_quick_link2'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link2" id="enable_quick_link2">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link2">{{__('Enable Quick Link 2')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name21',__('Quick Link Name 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name21',!empty($getStoreThemeSetting['quick_link_name21'])?$getStoreThemeSetting['quick_link_name21']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 2'))}}
                                                    @error('quick_link_name2')
                                                    <span class="invalid-footer_link_name" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url21',__('Quick Link Url 2'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url21',!empty($getStoreThemeSetting['quick_link_url21'])?$getStoreThemeSetting['quick_link_url21']:'',array('class'=>'form-control','placeholder'=>'Quick Link Url 2'))}}
                                                    @error('quick_link_url2')
                                                    <span class="invalid-quick_link_url2" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link3" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link3']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link3" id="enable_quick_link3" {{ $getStoreThemeSetting['enable_quick_link3'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link3" id="enable_quick_link3">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link3">{{__('Enable Quick Link 3')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name31',__('Quick Link Name 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name31',!empty($getStoreThemeSetting['quick_link_name31'])?$getStoreThemeSetting['quick_link_name31']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 3'))}}
                                                    @error('quick_link_name3')
                                                    <span class="invalid-quick_link_name3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url31',__('Quick Link Url 3'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url31',!empty($getStoreThemeSetting['quick_link_url31'])?$getStoreThemeSetting['quick_link_url31']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 3'))}}
                                                    @error('quick_link_url3')
                                                    <span class="invalid-quick_link_url3" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_quick_link4" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_quick_link4']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link4" id="enable_quick_link4" {{ $getStoreThemeSetting['enable_quick_link4'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_quick_link4" id="enable_quick_link4">
                                                    @endif
                                                    <label class="form-check-label" for="enable_quick_link4">{{__('Enable Quick Link 4')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_name41',__('Quick Link Name 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_name41',!empty($getStoreThemeSetting['quick_link_name41'])?$getStoreThemeSetting['quick_link_name41']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Name 4'))}}
                                                    @error('quick_link_name4')
                                                    <span class="invalid-quick_link_name4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    {{Form::label('quick_link_url41',__('Quick Link Url 4'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('quick_link_url41',!empty($getStoreThemeSetting['quick_link_url41'])?$getStoreThemeSetting['quick_link_url41']:'',array('class'=>'form-control','placeholder'=>'Enter Quick Link Url 4'))}}
                                                    @error('quick_link_url4')
                                                    <span class="invalid-quick_link_url4" role="alert">
                                                         <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme3'|| $theme == 'theme4' || $theme == 'theme5')
                <div class="active" id="Footer_2">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Footer 2') }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <small> {{__('Note : This detail will use for make explore social media.')}}</small>
                                            </div>
                                            <div class="col-6 py-2 text-right text-end">
                                                <div class="form-check form-switch form-switch-right mb-2">
                                                    <input type="hidden" name="enable_footer" value="off">
                                                    @if(!empty($getStoreThemeSetting['enable_footer']))
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer" id="enable_footer" {{ $getStoreThemeSetting['enable_footer'] == 'on' ? 'checked="checked"' : '' }}>
                                                    @else
                                                        <input type="checkbox" class="form-check-input mx-2" name="enable_footer" id="enable_footer">
                                                    @endif

                                                    <label class="form-check-label" for="enable_footer">{{__('Enable Footer')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fas fa-envelope"></i>
                                                    {{Form::label('email',__('Email'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('email',!empty($getStoreThemeSetting['email'])?$getStoreThemeSetting['email']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>__('Email')))}}
                                                    @error('email')
                                                    <span class="invalid-email" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                                    {{Form::label('whatsapp',__('Whatsapp'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('whatsapp',!empty($getStoreThemeSetting['whatsapp'])?$getStoreThemeSetting['whatsapp']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>'https://www.whatsapp.com/'))}}
                                                    @error('whatsapp')
                                                    <span class="invalid-whatsapp" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fab fa-facebook-square" aria-hidden="true"></i>
                                                    {{Form::label('facebook',__('Facebook'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('facebook',!empty($getStoreThemeSetting['facebook'])?$getStoreThemeSetting['facebook']:'',array('class'=>'form-control','placeholder'=>'https://www.facebook.com/'))}}
                                                    @error('facebook')
                                                    <span class="invalid-facebook" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    {{Form::label('instagram',__('Instagram'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('instagram',!empty($getStoreThemeSetting['instagram'])?$getStoreThemeSetting['instagram']:'',array('class'=>'form-control','placeholder'=>'https://www.instagram.com/'))}}
                                                    @error('instagram')
                                                    <span class="invalid-instagram" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fab fa-twitter" aria-hidden="true"></i>
                                                    {{Form::label('twitter',__('Twitter'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('twitter',!empty($getStoreThemeSetting['twitter'])?$getStoreThemeSetting['twitter']:'',array('class'=>'form-control','placeholder'=>'https://twitter.com/'))}}
                                                    @error('twitter')
                                                    <span class="invalid-twitter" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if($theme != 'theme5')
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <i class="fab fa-youtube" aria-hidden="true"></i>
                                                    {{Form::label('youtube',__('Youtube'),array('class'=>'col-form-label')) }}
                                                    {{Form::text('youtube',!empty($getStoreThemeSetting['youtube'])?$getStoreThemeSetting['youtube']:'',array('class'=>'form-control','placeholder'=>'https://www.youtube.com/'))}}
                                                    @error('youtube')
                                                    <span class="invalid-youtube" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-6">
                                            <div class="form-group">
                                                <i class="fas fa-copyright" aria-hidden="true"></i>
                                                {{Form::label('footer_note',__('Footer Note'),array('class'=>'col-form-label')) }}
                                                {{Form::text('footer_note',!empty($getStoreThemeSetting['footer_note'])?$getStoreThemeSetting['footer_note']:'',array('class'=>'form-control','placeholder'=>__('Footer Note')))}}
                                                @error('footer_note')
                                                <span class="invalid-footer_note" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-6">
                                            {{Form::label('storejs',__('Store Custom JS'),array('class'=>'col-form-label')) }}
                                            {{Form::textarea('storejs',!empty($getStoreThemeSetting['storejs'])?$getStoreThemeSetting['storejs']:'',array('class'=>'form-control','rows'=>3,'placeholder'=>__('Store Custom JS')))}}
                                            @error('storejs')
                                            <span class="invalid-storejs" role="alert">
                                             <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div>

                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-md-12">
                        <div class="card">
                                <div class="card-footer">
                                    <div class="col-sm-12 px-2">
                                        <div class="text-end">
                                            {{Form::submit(__('Save Change'),array('class'=>'btn btn-xs btn-primary'))}}
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                </div>
                {!! Form::close() !!}
            </div>
            @endif

        </div>
        <!-- [ sample-page ] end -->
    </div>
</div>
@endsection
        @push('script-page')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js"></script>
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
            <script>
                $(document).ready(function () {
                    $('.repeater').repeater({
                        initEmpty: false,
                        show: function () {
                            $(this).slideDown();
                        },
                        hide: function (deleteElement) {
                            if (confirm('Are you sure you want to delete this element?')) {
                                $(this).slideUp(deleteElement);
                            }
                        },
                        isFirstItemUndeletable: true
                    })
                });

                $(".deleteRecord").click(function () {
                    var name = $(this).data("name");
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                        {
                            url: '{{ route('brand.file.delete', [$store->slug,$theme,'_name']) }}'.replace('_name', name),
                            type: 'DELETE',
                            data: {
                                "name": name,
                                "_token": token,
                            },
                            success: function (response) {
                                show_toastr('Success', response.success, 'success');
                                $('.product_Image[data-value="' + response.name + '"]').remove();
                            }, error: function (response) {
                                show_toastr('Error', response.error, 'error');
                            }
                        });
                });
            </script>
            <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
            <script>
                var Dropzones = function () {
                    var e = $('[data-toggle="dropzone1"]'), t = $(".dz-preview");
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    e.length && (Dropzone.autoDiscover = !1, e.each(function () {
                        var e, a, n, o, i;
                        e = $(this), a = void 0 !== e.data("dropzone-multiple"), n = e.find(t), o = void 0, i = {
                            url: "{{route('store.storeeditproducts',[$store->slug,$theme])}}",
                            headers: {
                                'x-csrf-token': CSRF_TOKEN,
                            },
                            thumbnailWidth: null,
                            thumbnailHeight: null,
                            previewsContainer: n.get(0),
                            previewTemplate: n.html(),
                            maxFiles: 10,
                            parallelUploads: 10,
                            autoProcessQueue: true,
                            uploadMultiple: true,
                            acceptedFiles: a ? null : "image/*",
                            success: function (file, response) {
                                if (response.status == "success") {
                                    show_toastr('success', response.success, 'success');
                                    {{--// window.location.href = "{{route('product.index')}}";--}}
                                } else {
                                    show_toastr('Error', response.msg, 'error');
                                }
                            },
                            error: function (file, response) {
                                // Dropzones.removeFile(file);
                                if (response.error) {
                                    show_toastr('Error', response.error, 'error');
                                } else {
                                    show_toastr('Error', response, 'error');
                                }
                            },
                            init: function () {
                                var myDropzone = this;
                            }

                        }, n.html(""), e.dropzone(i)
                    }))
                }()

                $("#eventBtn").click(function () {
                    $("#BigButton").clone(true).appendTo("#fileUploadsContainer").find("input").val("").end();
                });
                $("#testimonial_eventBtn").click(function () {
                    $("#BigButton2").clone(true).appendTo("#fileUploadsContainer2").find("input").val("").end();
                });

                $(document).on('click', '#remove', function () {
                    var qq = $('.BigButton').length;

                    if (qq > 1) {
                        var dd = $(this).attr('data-id');

                        $(this).parents('#BigButton').remove();
                    }
                });
                $("input[type='file']").on("change", function () {
                    var numFiles = $(this).get(0).files.length
                    $('#img-count').html(numFiles + ' Images selected');
                })
            </script>
    @endpush
