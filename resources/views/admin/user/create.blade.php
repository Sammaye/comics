@extends('layouts.app')
@section('title', __('Create User'))
@section('container', 'container-admin-user-index')
@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4 text-truncate">Create User</h1>
    <form method="post"
          action="{{ route('admin.user.store') }}"
          class="row justify-content-center"
    >
        @csrf
        <div class="col-sm-24">
            <div class="form-group">
                <label for="username">{{ __('Username') }}</label>
                <input id="username"
                       type="text"
                       class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                       name="username"
                       value="{{ old('username') }}"
                       required
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
                       value="{{ old('password') }}"
                       required
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            </div>

            <div class="form-group">
                <button type="submit"
                        name="action"
                        value="save"
                        class="btn btn-outline-success btn-lg btn-block"
                >
                    {{ __('Create User') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
