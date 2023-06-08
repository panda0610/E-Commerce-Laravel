@extends('layouts.auth')
@section('page-title')
    {{__('Verify Email')}}
@endsection
@push('css-page')
    <style>
        .btn-login {
            font-size: 12px;
            color: #fff;
            font-family: 'Montserrat-SemiBold';
            background: #0f5ef7;
            margin-top: 20px;
            padding: 10px 30px;
            width: 100%;
            border-radius: 10px;
            border: none;
        }
    </style>
@endpush
@section('content')
    <div class="login-contain">
        <div class="login-inner-contain">
            <div class="login-form">
                <div class="page-title">
                    <h6>{{__('Verify Your Email Address')}}</h6>
                </div>
                <p>{{__('You are receiving this email because we received a password reset request for your account')}}</p><br><br>

                <a href="{{ route('student.password.reset',[$slug,$token]) }}" class="btn-login">{{__('Reset Password')}}</a><br><br>

                <p class="text-muted">
                    {{ __('If you did not request a password reset, no further action is required..') }}
                </p><br><br>
                <p> {{__('If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:')}} {{ url($slug.'/student-password/reset/'.$token) }}</p>
            </div>
        </div>
    </div>
@endsection
