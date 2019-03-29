@extends('layouts.app')
@section('title', __('Manage Users'))
@section('container', 'container-admin-user-index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="py-4">
                <a href="{{ route('admin.user.create') }}" class="btn btn-outline-primary btn-lg">Create User</a>
            </div>
            {!!
                \sammaye\Grid\Grid::make('user')
                    ->setData($users)
                    ->setAttributes(['class' => 'table table-bordered table-striped gridview'])
                    ->setColumns([
                        \sammaye\Grid\Column::make('id')
                            ->setDataCellTag('th')
                            ->setLabel(__('#'))
                            ->setAttributes(['scope' => 'row']),
                        \sammaye\Grid\Column::make('username'),
                        \sammaye\Grid\Column::make('email')
                            ->setDataContent(function($column, $row){
                                return $row->email . (
                                    $row->hasVerifiedEmail()
                                        ? '<span class="text-success pl-2">' . __(
                                            'Verified: :date',
                                            ['date' => $row->email_verified_at]
                                        ) . '</span>'
                                        : '<span class="text-danger pl-2">' . __('Not Verified') . '</span>'
                                );
                            }),
                        \sammaye\Grid\Column::make('created_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\Column::make('updated_at')
                            ->setFilterCell(false),
                        \sammaye\Grid\ActionColumn::make('actions')
                            ->setEditButton(function($column, $row){
                                return route('admin.user.edit', ['user' => $row]);
                            }, __('Edit'))
                            ->setDeleteButton(function($column, $row){
                                return route('admin.user.destroy', ['user' => $row]);
                            }, __('Delete'))
                    ])
                ->getTable()
            !!}
        </div>
    </div>
</div>
@endsection
