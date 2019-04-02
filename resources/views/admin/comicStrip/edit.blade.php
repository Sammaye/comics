@extends('layouts.app')
@section('title', __(
    'Edit Comic Strip #:id for :comic',
    ['id' => $model->id, 'comic' => $model->comic->title]
))
@section('container', 'container-admin-comic-strip-edit')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col">
                <h1 class="mb-4 text-truncate">{{ __(
                    'Edit Comic Strip #:id for :comic',
                    ['id' => $model->id, 'comic' => $model->comic->title]
                ) }}</h1>
                <form method="post" action="{{ route('admin.comicStrip.update', ['comicStrip' => $model]) }}">
                    @method('PUT')
                    @csrf
                    <div class="comic-strip-form">
                        @include('admin.comicStrip._form', ['model' => $model])
                    </div>
                    <div class="py-4">
                        <img class="img-fluid"
                             src="{{ route('admin.comicStrip.image', ['comicStrip' => $model]) }}"
                        />
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit"
                                class="btn btn-lg btn-outline-success"
                        >
                            {{ __('Save Comic Strip') }}
                        </button>
                        <a href="{{ route('admin.comicStrip.refresh', ['comicStrip' => $model]) }}"
                           class="btn btn-lg btn-outline-secondary ml-2"
                        >
                            {{ __('Refresh') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
