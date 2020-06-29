@extends('layouts.app')
@section('title', __('Edit User #:id', ['id' => $model->id]))
@section('container', 'container-admin-user-index')
@section('content')
<user-admin-form-component
    :form="{{ json_encode($vue_model) }}"
></user-admin-form-component>
@endsection
