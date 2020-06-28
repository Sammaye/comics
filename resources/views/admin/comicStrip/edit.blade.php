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

                <comic-strip-admin-form-component
                    action="{{ route('admin.comicStrip.update', ['comicStrip' => $model]) }}"
                    method="PUT"
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
                    image-src="{{ route('admin.comicStrip.image', ['comicStrip' => $model]) }}"
                    refresh-url="{{ route('admin.comicStrip.refresh', ['comicStrip' => $model]) }}"
                    comic-url="{{ route('admin.comic.edit', ['comic' => $model->comic]) }}"
                    comic-title="{{ $model->comic->title }}"
                ></comic-strip-admin-form-component>
            </div>
        </div>
    </div>
@endsection
