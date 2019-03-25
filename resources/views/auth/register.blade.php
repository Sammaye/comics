@extends('layouts.app')
@section('container', 'container-register')
@section('title', __('Let\'s Get You Started'))
@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4">Let's Get You Started</h1>
    <div class="row justify-content-center">
        <div class="col-sm-30 col-md-22 col-lg-15">
            <form method="POST" action="{{ route('register') }}" class="form-register">
                @csrf

                <div class="form-group">
                    <label for="username">{{ __('Username') }}</label>
                    <input id="username"
                           type="text"
                           class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
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
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
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
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
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
                           class="form-control"
                           name="password_confirmation"
                           required
                    >
                </div>

                <div class="form-group mb-0 text-center">
                    <button type="submit" class="btn btn-lg btn-outline-success">
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
