@extends('layouts.app')
@section('container', 'container-index full-height')
@section('content')
<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-sm-48 col-md-32">
            <h1 class="text-center text-white title">
                {{ __('Have your favourite cartoons sent straight to your inbox at a time that suits you!') }}
            </h1>
        </div>
    </div>
    <div class="row justify-content-center py-3">
        <div class="col-sm-30 col-md-22 col-lg-15">
            <form method="POST" action="{{ route('register') }}" class="form-register">
                @csrf

                <div class="form-group">
                    <label for="username">{{ __('Username') }}</label>
                    <input id="username"
                           type="text"
                           class="form-control form-control-lg{{ $errors->has('username') ? ' is-invalid' : '' }}"
                           name="username"
                           value="{{ old('username') }}"
                           required
                           autofocus
                    >
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('E-Mail Address') }}</label>

                    <input id="email"
                           type="email"
                           class="form-control form-control-lg{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           name="email"
                           value="{{ old('email') }}"
                           required
                    >
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                </div>

                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password"
                           type="password"
                           class="form-control form-control-lg{{ $errors->has('password') ? ' is-invalid' : '' }}"
                           name="password"
                           required
                    >
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                </div>

                <div class="form-group">
                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm"
                           type="password"
                           class="form-control form-control-lg"
                           name="password_confirmation"
                           required
                    >
                </div>

                <div class="form-group mb-0 text-center">
                    <button type="submit" class="btn btn-lg btn-danger">
                        {{ __('Register') }}
                    </button>
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
