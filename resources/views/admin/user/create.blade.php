@extends('layouts.app')
@section('title', __('Create User'))
@section('container', 'container-admin-user-index')
@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4 text-truncate">Create User</h1>
    <user-admin-form-component
        action="{{ route('admin.user.store') }}"
    ></user-admin-form-component>
</div>
@endsection
