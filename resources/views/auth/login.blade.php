@extends('layouts.app')
@section('title', 'Let\'s Get You In')
@section('container', 'container-login')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-sm-30 col-md-22 col-lg-15">
            <form method="POST" action="{{ route('login') }}" class="form-login">
                @csrf

                <div class="form-group">
                    <label for="email">{{ __('E-Mail Address') }}</label>
                    <input id="email"
                           type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                    >
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                </div>

                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password"
                           type="password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                           name="password"
                           required
                    >
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="remember"
                               id="remember" {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-outline-success btn-lg btn-block">
                        {{ __('Login') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="btn btn-link btn-block" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
                <div class="sso-login-methods">
                    <a href="{{ route('facebookLogin') }}" class="btn-facebook-login btn btn-link">Facebook</a>
                    <a href="{{ route('googleLogin') }}" class="btn-google-login btn btn-link">Google</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
