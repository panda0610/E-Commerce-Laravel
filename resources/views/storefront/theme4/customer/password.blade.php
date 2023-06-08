@extends('storefront.user.user')
@section('page-title')
    {{__('Login')}} - {{($store->tagline) ?  $store->tagline : config('APP_NAME', ucfirst($store->name))}}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{__('Rest Password')}}
@endsection
@section('content')
    <section>
        <div class="container position-relative zindex-100">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <!-- Form -->
                    {!! Form::open(array('route' => array('student.password.email', $slug)), ['method' => 'POST']) !!}
                    <p class="small">{{__('Enter your email below to proceed')}}.</p>
                    <div class="form-group">
                        {{Form::text('email',null,array('class'=>'form-control form-control-lg','placeholder'=>__('Enter Your Email')))}}
                    </div>
                    <div class="text-center">
                        {{Form::submit(__('Send password reset link'),array('class'=>'btn btn-block btn-lg btn-primary mt-4 mb-3','id'=>'saveBtn'))}}
                        {{__('Back to')}}
                        <a href="{{route('student.login',$slug)}}">{{__('Login')}}</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script-page')
    <script>
        if('{!! !empty($is_cart) && $is_cart==true !!}'){
            show_toastr('Error', 'You need to login!', 'error');
        }
    </script>
@endpush
