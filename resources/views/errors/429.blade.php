@extends('errors::minimal')

@section('title', __('Server Error'))

@section('content')
    <div class="min-vh-100 h-100vh py-5 d-flex align-items-center bg-gradient-primary">
        <div class="bg-absolute-cover vh-100 overflow-hidden d-none d-md-block">
            <figure class="w-100">
                <img alt="Image placeholder" src="{{ asset('assets/img/bg-4.svg') }}" class="svg-inject">
            </figure>
        </div>
        <div class="container position-relative zindex-100">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6">
                    <h6 class="display-1 mb-3 font-weight-600 text-white">{{__('429')}}</h6>
                    <p class="lead text-lg text-white mb-5">
                        {{__("Too Many Requests.")}}
                    </p>
                    @if(\Auth::check())
                        <a href="{{ route('dashboard') }}" class="btn btn-white btn-icon rounded-pill hover-translate-y-n3">
                            <span class="btn-inner--icon"><i class="fas fa-home"></i></span>
                            <span class="btn-inner--text">{{__('Return home')}}</span>
                        </a>
                    @else
                        <a href="{{ route('store.slug','my-store') }}" class="btn btn-white btn-icon rounded-pill hover-translate-y-n3">
                            <span class="btn-inner--icon"><i class="fas fa-home"></i></span>
                            <span class="btn-inner--text">{{__('Return home')}}</span>
                        </a>
                    @endif
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <figure class="w-100">
                        <img alt="Image placeholder" src="{{ asset('assets/img/in-sync.svg') }}" class="svg-inject opacity-8 img-fluid" style="height: 500px;">
                    </figure>
                </div>
            </div>
        </div>
    </div>
@endsection
