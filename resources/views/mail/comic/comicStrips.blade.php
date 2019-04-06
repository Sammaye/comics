@component('mail::blankmessage')

    @php
    $lastComicTitle = null;
    @endphp

    @foreach ($strips as $strip)
        @if ($strip->comic->title != $lastComicTitle)
            <h1>{{ $strip->comic->title }}</h1>

            @php
            $lastComicTitle = $strip->comic->title;
            @endphp
        @endif
        <div style='margin:10px 0;'>
            @if ($strip->skip)
                <a href="<?= $strip->url ?>"
                   target="_blank">
                    <?= __( "This strip is not compatible with Sammaye's Comics but you can click here to view it on their site") ?>
                </a>
            @elseif (is_array($strip->img))
                <a href="<?= $strip->url ?>" rel="nofollow" target="_blank">
                    @foreach ($strip->img as $k => $img)
                        <img src="{{ route('comic.image', ['comicStrip' => $strip, 'index' => $k]) }}"
                             style="border:0;"
                        />
                    @endforeach
                </a>
            @else
                <a href="{{ $strip->comic->indexUrl($strip->index, 'http') }}" rel="nofollow" target="_blank">
                    <img src="{{ route('comic.image', ['comicStrip' => $strip]) }}"
                         style="border:0;"
                    />
                </a>
            @endif
        </div>
    @endforeach
    @if (count($log_entries) > 0)
        <br/>
        <br/>

        <hr/>

        <br/>
        <br/>
        @foreach ($log_entries as $log_entry)
            {{ nl2br(str_replace(' ', '&nbsp;', $log_entry->message)) }} <br/>
        @endforeach
    @endif

@endcomponent
