@extends('layouts.app')
@section('title', __('Manage Users'))
@section('container', 'container-admin-user-index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="py-4">
                <a href="{{ route('admin.user.create') }}" class="btn btn-outline-primary btn-lg">Create User</a>
            </div>
            <users-admin-table-component></users-admin-table-component>
        </div>
    </div>
</div>
@endsection
