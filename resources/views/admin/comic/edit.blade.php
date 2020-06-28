@extends('layouts.app')
@section('title', __('Edit Comic #:id', ['id' => $model->id]))
@section('container', 'container-admin-comic-edit')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h1 class="mb-4 text-truncate">{{ __('Edit Comic #:id', ['id' => $model->id]) }}</h1>

            <comic-admin-form-component
                :comic-types="{{ json_encode(
                    collect((new \App\Comic)->getTypes())->map(function($item, $key){
                        return ['value' => $key, 'text' => $item];
                    })->values()
                ) }}"
                :comic-scrapers="{{ json_encode(
                    collect((new \App\Comic)->getScrapers())->map(function($item, $key){
                        return ['value' => $key, 'text' => $key];
                    })->values()
                ) }}"
                :comic-scraper-user-agents="{{ json_encode(
                    collect((new \App\Comic)->getUserAgents())->map(function($item, $key){
                        return ['value' => $item, 'text' => $key];
                    })->values()
                )  }}"
                action="{{ route('admin.comic.update', ['comic' => $model]) }}"
                action-type="PUT"
                :form="{{ json_encode(
                    collect($model)->map(function($item, $key){
                        if (
                            in_array($key, ['current_index', 'first_index', 'last_index'], true) &&
                            $item instanceof \MongoDB\BSON\UTCDateTime
                        ) {
                            return $item->toDateTime()->format(config('app.inputDateFormat'));
                        } else {
                            return $item;
                        }
                    })
                ) }}"
            >
                <template #submit="slotProps">
                    <template v-if="slotProps.isBusy">
                        <b-button type="button" disabled variant="outline-success" size="lg">
                            <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="outline-success" size="lg">Save Comic</b-button>
                    </template>
                </template>
            </comic-admin-form-component>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col">
            <h2>{{ __('Comic Strips') }}</h2>
            <div class="py-4">
                <a href="{{ route('admin.comicStrip.create', ['comic' => $model]) }}" class="btn btn-lg btn-outline-primary">{{ __('Add Strip') }}</a>
            </div>
            <comic-strips-admin-table-component comic-id="{{ $model->_id->__toString() }}"></comic-strips-admin-table-component>
        </div>
    </div>
</div>
@endsection
