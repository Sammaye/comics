@extends('layouts.app')
@section('title', __('Create User'))
@section('container', 'container-admin-user-index')
@section('content')
<user-admin-form-component
    :form="{{ json_encode([] + ['action_url' => route('admin.user.store', [], false)], JSON_FORCE_OBJECT) }}"
></user-admin-form-component>
@endsection
