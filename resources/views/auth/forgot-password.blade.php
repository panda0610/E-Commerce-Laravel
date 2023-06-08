@extends('layouts.auth')
@section('page-title')
    {{__('Reset Password')}}
@endsection
@section('language-bar')
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <select name="language" id="language" class="btn-primary custom_btn ms-2 me-2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach(App\Models\Utility::languages() as $language)
                    <option @if($lang == $language) selected @endif value="{{ url('/forgot-password',$language) }}">{{Str::upper($language)}}</option>
                @endforeach
            </select>
        </li>
    </ul>
@endsection
@section('content')
<div class="card">
    <div class="row align-items-center text-start">
        <div class="col-xl-6">
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}</h2>
                    @if(session('status'))
                        <div class="alert alert-primary">
                            {{ session('status') }}
                        </div>
                    @endif
                  <p class="text-xs text-muted">{{__('We will send a link to reset your password')}}</p>
                </div>
                <form method="POST" action="{{ route('password.email') }}" id="form_data">
                    @csrf
                    <div class="">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="error invalid-email text-danger" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                            @enderror
                        </div>
                        @if(env('RECAPTCHA_MODULE') == 'yes')
                            <div class="form-group col-lg-12 col-md-12 mt-3">
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        @endif

                        <div class="d-grid">
                            <button class="btn btn-primary btn-submit btn-block mt-2">{{ __('Send Password Reset Link') }}  </button>
                        </div>
                        <p class="my-4 text-center">{{__('Or')}}
                            <a href="{{route('login',$lang)}}" class="my-4 text-primary">{{ __('Login') }}</a>{{ __(' With') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-6 img-card-side">
            <div class="auth-img-content">
                <img src="{{ asset('assets/images/auth/img-auth-3.svg') }}" alt="" class="img-fluid">
                <h3 class="text-white mb-4 mt-5"> {{ __('“Attention is the new currency”') }}</h3>
                <p class="text-white"> {{__('The more effortless the writing looks, the more effort the writer
                    actually put into the process.')}}</p>
            </div>
        </div>
    </div>
</div>
@endsection
@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush
