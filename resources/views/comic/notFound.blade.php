@extends('layouts.comic')
@section('title', __('#404 Not Found'))
@section('page')
<div class="container my-5">
    <h1 class="display-4">{{ __('#404 Not Found') }}</h1>
    <p class="lead">
        {!! __('You can have this comic added! Just demand an addition and fill in form.') !!}
    </p>
</div>
@endsection
