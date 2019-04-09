@extends('layouts.comic')
@section('title', __(
    'View :title for :index',
    [
        'title' => $model->title,
        'index' => $comicStrip->index instanceof \Carbon\Carbon
            ? $comicStrip->index->format(config('app.inputDateFormat'))
            : $comicStrip->index
    ]
))
@section('container', 'container-comic-view')
@section('page')
<div class="container my-5">
    <div class="row">
        <div class="col-md-35 col-sm-30">
            @if($model->description)
                <p class="lead">{{ $model->description }}</p>
            @endif
            @if ($model->author || $model->homepage)
                <p class="text-muted">
                    @if ($model->author)
                        @if ($model->author_homepage)
                            {!! __(
                                'By <a :link>:name</a>',
                                [
                                    'name' => $model->author,
                                    'link' => 'href="' . $model->author_homepage . '" rel="nofollow" target="_blank" class="mr-2"'
                                ]
                            ) !!}
                        @else
                            {{ __('By :name', ['name' => $model->author]) }}
                        @endif
                    @endif
                    @if($model->homepage)
                        {!! __ (
                            '<a :link>Homepage</a>',
                            ['link' => 'href="' . $model->homepage . '" rel="nofollow" target="_blank"']
                        ) !!}
                    @endif
                </p>
            @endif
        </div>
        <div class="col-md-10 col-md-push-2 col-sm-18 mb-4">
            <a href="{{ route('comic.subscribe', ['comic' => $model]) }}"
               class="btn btn-lg btn-outline-success btn-subscribe{{ $isSubscribed ? ' d-none' : '' }}"
            >
                <span class="fas fa-check ml-2"></span>
                {{ __('Subscribe') }}
            </a>
            <a href="{{ route('comic.unsubscribe', ['comic' => $model]) }}"
               class="btn btn-lg btn-outline-danger btn-unsubscribe{{ !$isSubscribed ? ' d-none' : '' }}"
            >
                <span class="fas fa-times ml-2"></span>
                {{ __('Unsubscribe') }}
            </a>
        </div>
    </div>
    <form method="get" action="{{ route('comic.view', ['comic' => $model])  }}" class="input-group input-group-lg mb-5">
        <div class="input-group-prepend">
            @if($previousStrip)
                <a href="{{ $model->indexUrl($previousStrip->index) }}"
                   class="btn btn-lg btn-dark"
                >
                    &laquo;
                </a>
            @else
                <button class="btn btn-lg btn-dark" disabled>&laquo;</button>
            @endif
        </div>
        <input type="text"
               class="form-control input-lg text-center{{ $model->type === \App\Comic::TYPE_DATE ? ' datepicker' : '' }}"
               name="index"
               id="comic-strip-index"
               data-latestindex="{{
                $model->getLatestIndexValue() instanceof \Carbon\Carbon
                    ? $model->getLatestIndexValue()->format(config('app.inputDateFormat'))
                    : $model->getLatestIndexValue()
               }}"
               value="{{ $comicStrip->index instanceof \Carbon\Carbon ? $comicStrip->index->format(config('app.inputDateFormat')) : $comicStrip->index }}"
        />
        <div class="input-group-append">
            @if($nextStrip)
                <a href="{{ $model->indexUrl($nextStrip->index) }}"
                   class="btn btn-lg btn-dark"
                >
                    &raquo;
                </a>
            @else
                <button class="btn btn-lg btn-dark" disabled>&raquo;</button>
            @endif
        </div>
    </form>
    <div class="text-center">
        @if ($comicStrip->skip)
            <div class="strip-not-compatible text-center">
                <a href="<?= $comicStrip->url ?>" target="_blank" rel="nofollow">
                    {{ __('This strip is not compatible with this site but you can click here to view it on their site') }}
                </a>
            </div>
        @elseif (is_array($comicStrip->img))
            <a href="{{ $model->scrapeUrl($comicStrip->index) }}" rel="nofollow" target="_blank">
                @foreach ($comicStrip->img as $k => $img)
                    <img src="{{ route('comic.image', ['comicStrip' => $comicStrip, 'index' => $k]) }}"
                        class="img-fluid comic-img"
                    />
                @endforeach
            </a>
        @else
            <a href="{{ $model->scrapeUrl($comicStrip->index) }}" rel="nofollow" target="_blank">
                <img src="{{ route('comic.image', ['comicStrip' => $comicStrip]) }}"
                     class="img-fluid comic-img mr-auto ml-auto"
                />
            </a>
        @endif
    </div>
</div>
@endsection
