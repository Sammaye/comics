@extends('layouts.app')
@section('title', __('Edit Comic #:id', ['id' => $model->id]))
@section('container', 'container-admin-comic-edit')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h1 class="mb-4 text-truncate">{{ __('Edit Comic #:id', ['id' => $model->id]) }}</h1>
            <form method="post" action="{{ route('admin.comic.update', ['comic' => $model]) }}">
                @method('PUT')
                @csrf
                <div class="comic-form">
                    @include('admin.comic._form', ['model' => $model])
                </div>
                <div class="form-group mt-3">
                    <button type="submit"
                            class="btn btn-lg btn-outline-success"
                    >
                        {{ __('Save Comic') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col">
            <h2>{{ __('Comic Strips') }}</h2>
            <div class="py-4">
                <a href="{{ route('admin.comicStrip.create', ['comic' => $model]) }}" class="btn btn-lg btn-outline-primary">{{ __('Add Strip') }}</a>
            </div>
            {!!
                \sammaye\Grid\Grid::make('comicStrip')
                    ->setData($comicStrips)
                    ->setAttributes(['class' => 'table table-bordered table-striped gridview'])
                    ->setColumns([
                        \sammaye\Grid\Column::make('id')
                            ->setDataCellTag('th')
                            ->setLabel(__('#'))
                            ->setAttributes(['scope' => 'row']),
                        \sammaye\Grid\Column::make('url'),
                        \sammaye\Grid\Column::make('image_url'),
                        \sammaye\Grid\Column::make('image_md5')
                            ->setFilterCell(false),
                        \sammaye\Grid\Column::make('index')
                            ->setDataContent(function($column, $row){
                                if ($row->index instanceof \Carbon\Carbon) {
                                    return $row->index->format('Y-m-d');
                                }
                                return $row->index;
                            }),
                        \sammaye\Grid\Column::make('created_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\Column::make('updated_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\ActionColumn::make('actions')
                            ->setEditButton(function($column, $row){
                                return route('admin.comicStrip.edit', ['comicStrip' => $row]);
                            }, __('Edit'))
                            ->setDeleteButton(function($column, $row){
                                return route('admin.comicStrip.destroy', ['comicStrip' => $row]);
                            }, __('Delete'))
                    ])
                ->getTable()
            !!}
        </div>
    </div>
</div>
@endsection
