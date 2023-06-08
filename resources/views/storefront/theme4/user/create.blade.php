@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Register')}}
@endsection
@push('css-page')

@endpush
@section('content')
<div class="main-content">
    <section class="mh-100vh d-flex align-items-center" data-offset-top="#header-main">
        <!-- SVG background -->
        <div class="bg-absolute-cover bg-size--contain d-flex align-items-center zindex0">
            <figure class="w-100 px-4">
                <img alt="Image placeholder" src="{{asset('assets/img/bg-3.svg')}}" class="svg-inject">
            </figure>
        </div>
        <div class="container pt-6 position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <!-- Empty cart container -->
                        <div class="login-form">
                            <div class="categories-heading mb-4 float-left">
                                <h2 class="">{{__('Customer')}} <span> {{__('Register')}} </span></h2>
                            </div>
                            {!! Form::open(array('route' => array('store.userstore', $slug),'class'=>'login-form-main py-5'), ['method' => 'post']) !!}
                            <div class="form-group mt-2">
                            <label for="exampleInputEmail1" class="form-label float-left w-100 text-left">{{__('Full Name')}}</label>
                            <input name="name" class="form-control" type="text" placeholder="{{__('Full Name *')}}" required="required">
                        </div>
                        @error('name')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Email')}}</label>
                            <input name="email" class="form-control" type="email" placeholder="{{__('Email *')}}" required="required">
                        </div>
                        @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Number')}}</label>
                            <input name="phone_number" class="form-control" type="text" placeholder="{{__('Number *')}}" required="required">
                        </div>
                        @error('number')
                        <span class="error invalid-email text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Password')}}</label>
                            <input name="password" class="form-control" type="password" placeholder="{{__('Password *')}}" required="required">
                        </div>
                        @error('password')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Confirm Password')}}</label>
                            <input name="password_confirmation" class="form-control" type="password" placeholder="{{__('Confirm Password *')}}" required="required">
                        </div>
                        @error('password_confirmation')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="log_in_btn form-group mt-5 mb-3 d-flex align-items-center">
                            <button type="submit" class="btn btn-primary rounded-pill hover-translate-y-n3 btn-icon mr-sm-4 scroll-me text-nowrap">{{__('Register')}}</button>
                            <p>{{ __('By using the system, you accept the')}} <a href="" class="text-primary"> {{__('Privacy Policy')}} </a> and <a href="" class="text-primary"> {{__('System Regulations')}}. </a></p>
                        </div>
                        {!! Form::close() !!}
                        {{__('Already registered ?')}}
                        <a href="{{route('customer.loginform',$slug)}}" class="text-primary">{{__('Login')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('script-page')
@endpush
