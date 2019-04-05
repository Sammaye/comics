@component('mail::blankmessage')
{{ __('Hello') }},<br>
{{ __('Someone wants a comic to be added.') }}<br>
{{ __('Name: :name', ['name' => $name]) }}<br>
{{ __('URL: :url', ['url' => $url]) }}<br>
{{ __('Email: :email', ['email' => $email]) }}<br>
<br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
