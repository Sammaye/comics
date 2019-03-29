@foreach (Flash::messages() as $message)
    <div class="
            alert alert-{{ $message[0] }}{{ $message[2] ? ' alert-dismissible fade show' : '' }}
            {{ isset($class) && $class ? ' ' . $class : '' }}
        "
        role="alert"
    >
        <div class="container">
            {{ $message[1] }}
            @if ($message[2])
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            @endif
        </div>
    </div>
@endforeach
{{ flash::clear() }}
