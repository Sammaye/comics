@extends('layouts.app')
@section('title', __('Create Comic'))
@section('container', 'container-admin-comic-create')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h1 class="mb-4">{{ __('Create Comic') }}</h1>
            <admin-comic-form-component
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
                action="{{ route('admin.comic.store') }}"
            >
                <template #submit="slotProps">
                    <template v-if="slotProps.isBusy">
                        <b-button type="button" disabled variant="outline-success" size="lg">
                            <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="outline-success" size="lg">Create Comic</b-button>
                    </template>
                </template>
            </admin-comic-form-component>
        </div>
    </div>
</div>
@endsection
