@php
$lastComicTitle = null;
@endphp

@foreach ($comicStrips as $comicStrip)
    @if ($comicStrip->comic->title != $lastComicTitle)
        <h1>{{ $comicStrip->comic->title }}</h1>

        @php
        $lastComicTitle = $comicStrip->comic->title;
        @endphp
    @endif
    <div style='margin:10px 0;'>
        @if ($comicStrip->skip)
            <a href="<?= $comicStrip->url ?>"
               target="_blank">
                <?= __('This strip is not compatible with this site but you can click here to view it on their site') ?>
            </a>
        @elseif (is_array($comicStrip->img))
            <a href="<?= $comicStrip->url ?>" rel="nofollow" target="_blank">
                @foreach ($comicStrip->img as $k => $img)
                    <img src="{{ $message->embed('http://localhost/' . route('comic.image', ['comicStrip' => $comicStrip, 'index' => $k], false)) }}"
                         style="border:0;"
                    />
                @endforeach
            </a>
        @else
            <a href="{{ $comicStrip->comic->indexUrl($comicStrip->index, 'http') }}" rel="nofollow" target="_blank">
                <img src="{{ $message->embed('http://localhost/' . route('comic.image', ['comicStrip' => $comicStrip], false)) }}"
                     style="border:0;"
                />
            </a>
        @endif
    </div>
@endforeach
@if (count($logEntries) > 0)
    <br/>
    <br/>

    <hr/>

    <br/>
    <br/>
    @foreach ($logEntries as $logEntry)
        {{ $logEntry->message }}<br/>
        {!! nl2br($logEntry->context[0]) !!}<br/>
        <br/>
    @endforeach
@endif
