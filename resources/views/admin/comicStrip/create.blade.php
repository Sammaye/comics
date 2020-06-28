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
                <admin-comic-strip-form-component
                    action="{{ route('admin.comicStrip.store') }}"
                    :errors="{{ json_encode($errors->getMessages(), JSON_FORCE_OBJECT) }}"
                    :form="{{ json_encode(
                       collect($model)->map(function($item, $key){
                            if (
                                in_array($key, ['date', 'index', 'previous', 'next'], true) &&
                                $item instanceof \MongoDB\BSON\UTCDateTime
                            ) {
                                return $item->toDateTime()->format(config('app.inputDateFormat'));
                            } else {
                                return $item;
                            }
                        })
                    ) }}"
                    comic-url="{{ route('admin.comic.edit', ['comic' => $model->comic]) }}"
                    comic-title="{{ $model->comic->title }}"
                ></admin-comic-strip-form-component>
            </div>
        </div>
    </div>
@endsection
