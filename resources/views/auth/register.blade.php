@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <form method="POST" action="{{ route('register') }}">
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

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
