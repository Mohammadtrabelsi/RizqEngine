@foreach (['success', 'info', 'warning'] as $type)
    @if (session()->has($type))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent alert-{{ $type }} pr-12 fade show" role="alert">
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
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-red-50 text-red-700 border-red-200 pr-12 fade show" role="alert">
            <div class="alert-body">
                <span>{{ session($type) }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
@endforeach
