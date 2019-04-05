@component('mail::blanklayout')
    {{-- Header --}}
    @slot('header')
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
    @endisset

    {{-- Footer --}}
    @slot('footer')
    @endslot
@endcomponent
