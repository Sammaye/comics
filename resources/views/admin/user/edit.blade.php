@extends('layouts.app')
@section('title', __('Edit User #:id', ['id' => $model->id]))
@section('container', 'container-admin-user-index')
@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4 text-truncate">Edit User #{{ $model->_id }}</h1>
    <form method="post"
          action="{{ route('admin.user.update', ['user' => $model]) }}"
          class="row justify-content-center"
    >
        @method('PUT')
        @csrf
        <div class="col-sm-24">
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
            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
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
                <label for="facebook_id">{{ __('Facebook ID') }}</label>
                <input id="facebook_id"
                       type="text"
                       class="form-control{{ $errors->has('facebook_id') ? ' is-invalid' : '' }}"
                       name="facebook_id"
                       value="{{ $model->facebook_id ?? old('facebook_id') }}"
                       readonly
                       disabled
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('facebook_id') }}</strong>
                </span>
            </div>
            <div class="form-group">
                <label for="google_id">{{ __('Google ID') }}</label>
                <input id="google_id"
                       type="text"
                       class="form-control{{ $errors->has('google_id') ? ' is-invalid' : '' }}"
                       name="google_id"
                       value="{{ $model->google_id ?? old('google_id') }}"
                       readonly
                       disabled
                >
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('google_id') }}</strong>
                </span>
            </div>

            <div class="row">
                <div class="col-sm-24">
                    <p>Roles</p>
                    @foreach($roles as $role)
                        <div class="form-group form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="role-{{ $role->id }}"
                                   name="role[]"
                                   value="{{ $role->id }}"
                                    {{ $model->hasRole($role->name) ? ' checked="checked"' : '' }}
                            >
                            <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                    @endforeach

                    <span class="invalid-feedback{{ $errors->has('role') ? ' d-block' : '' }}" role="alert">
                        <strong>{{ $errors->first('role') }}</strong>
                    </span>
                </div>
                <div class="col-sm-24">
                    <p>Permissions</p>
                    @foreach($permissions as $permission)
                        <div class="form-group form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="permission-{{ $permission->id }}"
                                   name="permission[]"
                                   value="{{ $permission->id }}"
                                    {{ $model->hasDirectPermission($permission->name) ? ' checked="checked"' : '' }}
                            >
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach

                    <span class="invalid-feedback{{ $errors->has('permission') ? ' d-block' : '' }}" role="alert">
                        <strong>{{ $errors->first('permission') }}</strong>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <button type="submit"
                        name="action"
                        value="save"
                        class="btn btn-outline-success btn-lg btn-block"
                >
                    {{ __('Save User') }}
                </button>
            </div>

            @if (!$model->hasVerifiedEmail())
                <div class="form-group">
                    <button type="submit"
                            name="action"
                            value="verify_email"
                            class="btn btn-outline-info btn-lg btn-block"
                    >
                        {{ __('Verify E-Mail Address') }}
                    </button>
                </div>
            @endif

            <div class="form-group">
                @if($model->isBlocked())
                    <button type="submit"
                            name="action"
                            value="unblock"
                            class="btn btn-outline-warning btn-lg btn-block"
                    >
                        {{ __('Unblock User') }}
                    </button>
                @else
                    <button type="submit"
                            name="action"
                            value="block"
                            class="btn btn-outline-warning btn-lg btn-block"
                    >
                        {{ __('Block User') }}
                    </button>
                @endif
                <a href="{{ route('admin.user.destroy', ['user' => $model]) }}"
                   onclick="
                       event.preventDefault();
                       document.getElementById('delete-user-{{ $model->id }}-form').submit();
                   "
                   class="btn btn-outline-danger btn-lg btn-block"
                >
                    {{ __('Delete User') }}
                </a>
            </div>
        </div>
    </form>
</div>

<form action="{{ route('admin.user.destroy', ['user' => $model]) }}"
      method="POST"
      id="delete-user-{{ $model->_id }}-form"
>
    @csrf
    @method('DELETE')
</form>
@endsection
