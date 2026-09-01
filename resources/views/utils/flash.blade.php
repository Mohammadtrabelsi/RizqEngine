@foreach (['success', 'info', 'warning'] as $type)
    @if (session()->has($type))
        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
            <div class="alert-body">
                <span>{{ session($type) }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
@endforeach

@foreach (['error', 'danger'] as $type)
    @if (session()->has($type))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">
                <span>{{ session($type) }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
@endforeach
