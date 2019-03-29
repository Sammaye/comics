@extends('layouts.app')
@section('title', __('Account Settings'))
@section('container', 'container-account-settings')
@section('content')
<div class="container py-4">
    <form method="post" action="{{ route('user.update', ['user' => $model]) }}" class="row mb-4">
        @method('PUT')
        @csrf
        <div class="col-sm-48">
            <h2 class="mb-3">{{ __('Subscriptions') }}</h2>
        </div>
        <div class="col-sm-24">

        </div>
        <div class="col-sm-24">
            <div class="form-group">
                <label for="email_frequency">{{ __('Email Frequency') }}</label>
                <select id="email_frequency"
                       type="text"
                       class="form-control{{ $errors->has('email_frequency') ? ' is-invalid' : '' }}"
                       name="email_frequency"
                >
                    @foreach($model->getEmailFrequencies() as $k => $v)
                        <option value="{{ $k }}"{{ $model->email_frequency === $k ? ' selected' : '' }}>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email_frequency') }}</strong>
                </span>
            </div>
        </div>
        <div class="col">
            <div class="row">
                <div class="col-sm-48 text-center">
                    <button type="submit"
                            class="btn btn-lg btn-outline-success"
                            name="action"
                            value="update_subscriptions"
                    >
                        {{ __('Save Subscriptions') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div class="row pb-4">
        <form method="post" action="{{ route('user.update', ['user' => $model]) }}" class="col-sm-24">
            @method('PUT')
            @csrf
            <h2 class="mb-3">{{ __('Details') }}</h2>
            <div class="form-group">
                <label for="username">{{ __('Username') }}</label>
                <input id="username"
                       type="text"
                       class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                       name="username"
                       value="{{ $model->username ?? old('username') }}"
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
                       value="{{ $model->email ?? old('email') }}"
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            </div>
            <div class="form-group mb-0">
                <button type="submit"
                        class="btn btn-lg btn-outline-success"
                        name="action"
                        value="update_details"
                >
                    {{ __('Update Details') }}
                </button>
            </div>
        </form>
        <form method="post" action="{{ route('user.update', ['user' => $model]) }}" class="col-sm-24">
            @method('PUT')
            @csrf
            <h2 class="mb-3">{{ __('Password') }}</h2>
            <div class="form-group">
                <label for="old_password">{{ __('Old Password') }}</label>
                <input id="old_password"
                       type="password"
                       class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                       name="old_password"
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('old_password') }}</strong>
                </span>
            </div>
            <div class="form-group">
                <label for="password">{{ __('New Password') }}</label>
                <input id="password"
                       type="password"
                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password"
                       value="{{ old('password') }}"
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            </div>
            <div class="form-group">
                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                <input id="password-confirm"
                       type="password"
                       name="password_confirmation"
                       class="form-control"
                       required
                >
            </div>
            <div class="form-group mb-0">
                <button type="submit"
                        class="btn btn-lg btn-outline-success"
                        name="action"
                        value="update_password"
                >
                    {{ __('Update Password') }}
                </button>
            </div>
        </form>
    </div>
    <div class="row pt-4">
        <div class="col">
            <div class="card border-danger">
                <div class="card-body text-danger">
                    <h5 class="card-title">{{ __('Danger Ranger!') }}</h5>
                    <p class="card-text">
                        {{ __('Deleting your account cannot be undone!') }}
                    </p>
                    <a href="{{ route('user.destroy', ['user' => $model]) }}"
                       onclick="
                           event.preventDefault();
                           document.getElementById('delete-user-{{ $model->id }}-form').submit();
                       "
                       class="btn btn-danger"
                    >
                        {{ __('Okay, Delete Me') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.user.destroy', ['user' => $model]) }}"
      method="POST"
      id="delete-user-{{ $model->_id }}-form"
>
    @csrf
    @method('DELETE')
</form>
@endsection
