@extends('layouts.app')
@section('title', __(
    'Create Comic Strip for :comic',
    ['comic' => $comic->title]
))
@section('container', 'container-admin-comic-strip-create')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col">
                <h1 class="mb-4 text-truncate">{{ __(
                    'Create Comic Strip for :comic',
                    ['comic' => $comic->title]
                ) }}</h1>
                <form method="post" action="{{ route('admin.comicStrip.store') }}">
                    @csrf
                    <div class="comic-strip-form">
                        @include('admin.comicStrip._form', ['model' => $model])
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit"
                                class="btn btn-lg btn-outline-success"
                        >
                            {{ __('Create Comic Strip') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
