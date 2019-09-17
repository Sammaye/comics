<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (View::hasSection('title'))
        <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    @else
        <title>{{ config('app.name', 'Laravel') }}</title>
    @endif

    @if (View::hasSection('description'))
        <meta name="description" content="@yield('description')">
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item{{ Route::currentRouteName() === 'comic.view' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('comic.view') }}">{{ __('Comic Archive') }}</a>
                            </li>
                            <li class="nav-item{{ Route::currentRouteName() === 'login' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item{{ Route::currentRouteName() === 'register' ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item{{ Route::currentRouteName() === 'comic.view' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('comic.view') }}">{{ __('View Archive') }}</a>
                            </li>
                            <li
                                class="nav-item dropdown{{ in_preg_array(Route::currentRouteName(), [
                                    '/^user\.edit$/',
                                    '/^admin\.user/',
                                    '/^admin\.comic/',
                                ]) ? ' active' : '' }}"
                            >
                                <a id="navbarDropdown"
                                   class="nav-link dropdown-toggle"
                                   href="#"
                                   role="button"
                                   data-toggle="dropdown"
                                   aria-haspopup="true"
                                   aria-expanded="false"
                                   v-pre
                                >
                                    {{ __('Account') }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item{{ Route::currentRouteName() === 'user.edit' ? ' active' : '' }}"
                                       href="{{ route('user.edit', ['user' => Auth::user()]) }}"
                                    >
                                        {{ __('Settings') }}
                                    </a>
                                    @can('admin-user')
                                        <a class="dropdown-item{{ preg_match('/^admin\.user/', Route::currentRouteName()) ? ' active' : '' }}"
                                           href="{{ route('admin.user.index') }}"
                                        >
                                            {{ __('Admin Users') }}
                                        </a>
                                    @endcan
                                    @can('admin-comic')
                                        <a class="dropdown-item{{ preg_match('/^admin\.comic/', Route::currentRouteName()) ? ' active' : '' }}"
                                           href="{{ route('admin.comic.index') }}"
                                        >
                                            {{ __('Admin Comics') }}
                                        </a>
                                    @endcan
                                    <a class="dropdown-item"
                                       href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                    >
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                        <li class="nav-item{{ Route::currentRouteName() === 'help' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('help') }}">
                                {{ __('Help') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="body-fixed-top @yield('container')">
            @if (session('resent'))
                <div class="alert alert-warning mb-0 alert-sticky-flash" role="alert">
                    <div class="container">
                        {{ __('A fresh verification link has been sent to your E-Mail address.') }}
                    </div>
                </div>
            @elseif(session('verified'))
                <div class="alert alert-success mb-0 alert-sticky-flash" role="alert">
                    <div class="container">
                        {{ __('Your E-Mail address is now verified, thank you!') }}
                    </div>
                </div>
            @elseif(Auth::user() && !Auth::user()->hasVerifiedEmail())
                <div class="alert alert-danger mb-0 alert-sticky-flash" role="alert">
                    <div  class="container">
                        <form id="resend-email" class="d-none" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit">
                                {{ __('Resend') }}
                            </button>
                        </form>
                        {!! __(
                            'You must verify your E-Mail address before receiving E-Mails, <a href=":url" onclick="$(\'#resend-email\').submit()">please click here to verify</a>',
                            ['url' => route('verification.resend')]
                        ) !!}
                    </div>
                </div>
            @endif
            @include('flash::flash', ['class' => 'mb-0 alert-sticky-flash'])
            <main class="">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
