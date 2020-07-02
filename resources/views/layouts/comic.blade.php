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
                                    data-url="{{ route('comic.view', ['comicId' => $k]) }}"
                                    {{ isset($selectedComicId) && $selectedComicId === $k ? ' selected' : '' }}
                            >
                                {{ json_encode($v) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-13 text-right pb-2">
                    <a href="#"
                       class="btn btn-default btn-lg btn-outline-secondary"
                       data-toggle="modal"
                       data-target=".request-comic-modal"
                    >
                        <span class="fas fa-plus"></span>
                        {{ __('Demand Addition') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @yield('page')

    <div class="modal fade request-comic-modal" id="request-comic-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('Demand a comic/cartoon to be added') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="requestComicForm" action="{{ route('comic.request') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="alert-summarise"></div>
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input id="name"
                                   type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   required
                            >
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="url">{{ __('Homepage URL') }}</label>
                            <input id="url"
                                   type="text"
                                   class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                                   name="url"
                            >
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('url') }}</strong>
                            </span>
                        </div>
                        @if(!Auth::user())
                            <p class="margined-p">
                                {{ __('Since you are not logged in, add your email address here if you would like to be notified of when your comic is added') }}
                            </p>
                            <div class="form-group">
                                <label for="email">{{ __('E-Mail Address') }}</label>
                                <input id="email"
                                       type="text"
                                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                >
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="email">{{ __('E-Mail Address') }}</label>
                                <input id="email"
                                       type="text"
                                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                       value="{{ Auth::user()->email }}"
                                >
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit demands</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
