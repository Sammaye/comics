@extends('layouts.app')
@section('title', __('Manage Comics'))
@section('container', 'container-admin-comic-index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col mb-5">
            <div class="py-4">
                <a href="{{ route('admin.comic.create') }}" class="btn btn-outline-primary btn-lg">Create Comic</a>
            </div>
            <comics-admin-table-component></comics-admin-table-component>
            <hr>
            <h2 class="h4 mb-3">Logs</h2>
            <logs-admin-table-component></logs-admin-table-component>
        </div>
    </div>
</div>
@endsection
