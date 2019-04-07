@extends('layouts.app')
@section('title', __('Manage Comics'))
@section('container', 'container-admin-comic-index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col mb-5">
            <div class="py-4">
                <a href="{{ route('admin.comic.create') }}" class="btn btn-outline-primary btn-lg">Create Comic</a>
            </div>
            {!!
                \sammaye\Grid\Grid::make('comic')
                    ->setData($comics)
                    ->setAttributes(['class' => 'table table-bordered table-striped gridview'])
                    ->setColumns([
                        \sammaye\Grid\Column::make('id')
                            ->setDataCellTag('th')
                            ->setLabel(__('#'))
                            ->setAttributes(['scope' => 'row']),
                        \sammaye\Grid\Column::make('title'),
                        \sammaye\Grid\Column::make('abstract'),
                        \sammaye\Grid\Column::make('strips')
                            ->setLabel('Strips')
                            ->setSortable(false)
                            ->setFilterCell(false)
                            ->setDataContent(function($column, $row){
                                return $row->strips()->count();
                            }),
                        \sammaye\Grid\Column::make('created_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\Column::make('updated_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\ActionColumn::make('actions')
                            ->setEditButton(function($column, $row){
                                return route('admin.comic.edit', ['comic' => $row]);
                            }, __('Edit'))
                            ->setDeleteButton(function($column, $row){
                                return route('admin.comic.destroy', ['comic' => $row]);
                            }, __('Delete'))
                    ])
                ->getTable()
            !!}

            <hr>
            <h2 class="h4 mb-3">Logs</h2>

            {!!
                \sammaye\Grid\Grid::make('log')
                    ->setData($logs)
                    ->setAttributes(['class' => 'table table-bordered table-striped gridview'])
                    ->setColumns([
                        \sammaye\Grid\Column::make('id')
                            ->setDataCellTag('th')
                            ->setLabel(__('#'))
                            ->setAttributes(['scope' => 'row']),
                        \sammaye\Grid\Column::make('message'),
                        \sammaye\Grid\Column::make('trace')
                            ->setFilterCell(false)
                            ->setDataContent(function($column, $row){
                                return '<small>' . nl2br($row->context[0]) . '</small>';
                            }),
                          \sammaye\Grid\Column::make('created_at')
                            ->setFilterCell(false),
                    ])
                ->getTable()
            !!}
        </div>
    </div>
</div>
@endsection
