@extends('layouts.auth')

@section('content')
<div class="col-11 col-xl-6 py-5 mx-auto ml-md-0">
  <h4 class="text-primary font-weight-normal mb-1"><strong>{{__('Confirm Password')}}</strong></h4>
  <span>{{ __('Please confirm your password before continuing.') }}</span>
  <form action="{{ route('password.confirm') }}" method="POST" class="pt-3 text-left needs-validation" novalidate="">
    @csrf
    <label class="d-block position-relative mb-3">
      <p class="text-sm mb-2">{{ __('Password') }}</p>
      <input type="password" id="password" name="password"  class="text-sm rounded-6 w-100 py-3 px-3 border text-grayDark @error('password') is-invalid @enderror" >
      @error('password')
        <span class="invalid-feedback" role="alert">
            <small>{{ $message }}</small>
        </span>
      @enderror
    </label>             
    <button type="submit" class="btn-primary px-4 py-2 text-xs"><span class="d-block py-1">{{ __('Confirm Password') }}</span></button>
    @if (Route::has('password.request'))
        <a class="text-xs text-muted text-center" href="{{ route('password.request') }}">
            {{ __('Forgot Your Password?') }}
        </a>
    @endif
  </form>
</div>
@endsection
