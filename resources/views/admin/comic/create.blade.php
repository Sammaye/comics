@extends('layouts.app')
@section('title', __('Create Comic'))
@section('container', 'container-admin-comic-create')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h1 class="mb-4">{{ __('Create Comic') }}</h1>
            <form method="post" action="{{ route('admin.comic.store') }}">
                @csrf
                <div class="comic-form">
                    @include('admin.comic._form', ['model' => new \App\Comic])
                </div>
                <div class="form-group mt-3">
                    <button type="submit"
                            class="btn btn-lg btn-outline-success"
                    >
                        {{ __('Create Comic') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
