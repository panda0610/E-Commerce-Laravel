@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Login')}}
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
                                    <div class="categories-heading">
                                        <h2 class="float-left">{{__('Customer')}} <span> {{__('login')}} </span></h2>
                                    </div>
                                    {!! Form::open(array('route' => array('customer.login', $slug,(!empty($is_cart) && $is_cart==true)?$is_cart:false),'class'=>'login-form-main py-5'),['method'=>'POST']) !!}
                                    <div class="form-group mb-3 mt-2">
                                        <label for="exampleInputEmail1" class="form-label float-left mt-2">{{__('username')}}</label>
                                        {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Your Email')))}}
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="exampleInputPassword1" class="form-label float-left">{{__('Password')}}</label>
                                        {{Form::password('password',array('class'=>'form-control','id'=>'exampleInputPassword1','placeholder'=>__('Enter Your Password')))}}
                                    </div>
                                    <div class="log_in_btn form-group mt-5 mb-3 d-flex align-items-center text-left">
                                        <button type="submit" class="btn btn-primary rounded-pill hover-translate-y-n3 btn-icon mr-sm-4 scroll-me text-nowrap">{{__('Sign In')}}</button>
                                        <p class="m-0 t-grey">{{__('By using the system, you accept the')}} <a href="" class="text-primary"> {{__('Privacy Policy')}} </a> {{__('and')}} <a href="" class="text-primary"> {{__('System Regulations')}}. </a></p>
                                    </div>
                                    {{Form::close()}}
                                    {{__('Dont have account ?')}}
                                    <a href="{{route('store.usercreate',$slug)}}" class="login-form-main-a text-primary">{{__('Register')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

   
@endsection
@push('script-page')
    <script>
        if ('{!! !empty($is_cart) && $is_cart==true !!}') {
            show_toastr('Error', 'You need to login!', 'error');
        }
    </script>
@endpush
