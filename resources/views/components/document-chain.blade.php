<div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-4">
    <div class="flex-auto p-5">
        <h6 class="mb-3">{{ __('boncommande.document_chain') }}</h6>
        <div class="d-flex flex-wrap align-items-center">
            @foreach($steps as $index => $step)
                @if($index > 0)
                    <i class="bi bi-arrow-right mx-2 text-muted"></i>
                @endif
                <div class="text-center px-2 py-1 rounded {{ $current === $step['key'] ? 'bg-primary text-white' : '' }}">
                    <div class="small text-uppercase {{ $current === $step['key'] ? 'text-white' : 'text-muted' }}">{{ $step['label'] }}</div>
                    @if($step['ref'] && $step['url'])
                        <a class="fw-bold {{ $current === $step['key'] ? 'text-white' : '' }}" href="{{ $step['url'] }}">{{ $step['ref'] }}</a>
                    @elseif($step['ref'])
                        <span class="fw-bold">{{ $step['ref'] }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
