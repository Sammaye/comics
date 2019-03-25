@extends('layouts.app')
@section('title', __('Forgot Password'))
@section('content')
<div class="container py-4">
    <h1 class="text-center">{{ __('Forgot Your Password?') }}</h1>
    <p class="text-muted text-center mb-4">{{ __('No Problem! Just tell us your e-email address...') }}</p>
    <div class="row justify-content-center">
        <div class="col-sm-30 col-md-22 col-lg-15">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

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

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-outline-success btn-lg btn-block">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
