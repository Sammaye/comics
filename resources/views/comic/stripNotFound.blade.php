@extends('layouts.comic')
@section('title', __('#404 Not Found'))
@section('page')
<div class="container my-5">
    <div class="row">
        <div class="col">
            <form method="get" action="{{ route('comic.view', ['comic' => $model])  }}" class="input-group input-group-lg mb-5">
                <div class="input-group-prepend">
                    @if($model->current_index)
                        <a href="{{ $model->indexUrl($model->current_index) }}"
                           class="btn btn-lg btn-dark"
                        >
                            &laquo;
                        </a>
                    @else
                        <button disabled class="btn btn-lg btn-dark">&laquo;</button>
                    @endif
                </div>
                <input type="text"
                       class="form-control input-lg text-center{{ $model->type === \App\Comic::TYPE_DATE ? ' datepicker' : '' }}"
                       name="index"
                       id="datepicker"
                       value="{{ ($index = request('index', 'd-m-Y'))
                        ? ($index instanceof \Carbon\Carbon ? $index->format('d-m-Y') : $index)
                        : '####' }}"
                />
                <div class="input-group-append">
                    <button disabled class="btn btn-lg btn-dark">&raquo;</button>
                </div>
            </form>
            <h1 class="display-4 text-center">{{ __('#404 Strip Not Found') }}</h1>
            <p class="lead text-center">{!! __(
                'You can <a :backlink>go back to one that does exist</a> or if you believe this is an error and this strip should be here <a :helplink>then please contact me through the help section</a>.',
                [
                    'backlink' => 'href="' . route('comic.view', ['comic' => $model]) . '"',
                    'helplink' => 'href="' . route('help', ['#need-help-support']) . '"'
                ]
            )  !!}</p>
        </div>
    </div>
</div>
@endsection
