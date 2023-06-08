@extends('layouts.auth')

@section('content')
<div class="col-11 col-xl-6 py-5 mx-auto ml-md-0">
  <h4 class="text-primary font-weight-normal mb-1"><strong>{{__('Verify Your Email Address')}}</strong></h4>
  @if (session('status') == 'verification-link-sent')
  <div class="alert alert-success" role="alert">
    {{ __('A fresh verification link has been sent to your email address.') }}
  </div>
  @endif
  <span>{{ __('Before proceeding, please check your email for a verification link.') }}</span>
  <span>{{ __('If you did not receive the email') }},</span>
  <form action="{{ route('verification.resend') }}" method="POST" class="pt-3 text-left needs-validation" novalidate="">
    @csrf
    <button type="submit" class="btn-primary px-4 py-2 text-xs"><span class="d-block py-1">{{ __('click here to request another') }}</span></button>
  </form>
  <form method="POST" action="{{ route('logout') }}">
      @csrf

      <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
          {{ __('Log Out') }}
      </button>
  </form>
</div>
@endsection
