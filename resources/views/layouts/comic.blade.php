@extends('layouts.app')
@section('content')
<div class="layout-comic">
    <div class="comic-selector pt-2">
        <div class="container">
            <div class="row">
                <div class="col-sm-35 pb-2">
                    <select id="comicSelector"
                            class="form-control"
                            name="comicSelector"
                            tabindex="-1"
                            aria-hidden="true"
                    >
                        <option value=""></option>
                        @foreach($comicSelectorOptions as $k => $v)
                            <option value="{{ $k }}"
                                    data-url="{{ route('comic.view', ['comic' => $k]) }}"
                                    {{ isset($selectedComicId) && $selectedComicId === $k ? ' selected' : '' }}
                            >
                                {{ json_encode($v) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <comic-suggestion-form-component
                    action="{{ route('comic.request') }}"
                    :is-logged="{{ Auth::user() ? 'true' : 'false' }}"
                    :form="{{ json_encode(
                        Auth::user() ? ['email' => Auth::user()->email ] : [],
                        JSON_FORCE_OBJECT
                    )  }}"
                ></comic-suggestion-form-component>
            </div>
        </div>
    </div>

    @yield('page')
</div>
@endsection
