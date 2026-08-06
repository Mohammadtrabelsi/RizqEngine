@php
    $flashTypes = [
        'success' => 'success',
        'info' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'danger' => 'danger',
    ];
@endphp
@foreach ($flashTypes as $key => $class)
    @if (session()->has($key))
        <div class="alert alert-{{ $class }} alert-dismissible fade show" role="alert">
            <div class="alert-body">
                <span>{{ session($key) }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
@endforeach
