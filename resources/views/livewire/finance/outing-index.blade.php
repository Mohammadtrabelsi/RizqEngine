{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('outings.create') }}" class="btn btn-primary">{{ __('finance.add_outing') }} <i class="bi bi-plus"></i></a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead><tr>
                        <th>{{ __('finance.reference') }}</th>
                        <th>{{ __('finance.date') }}</th>
                        <th>{{ __('finance.location') }}</th>
                        <th>{{ __('finance.purpose') }}</th>
                        <th class="text-end">{{ __('finance.total') }}</th>
                        <th class="text-end">{{ __('finance.actions') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse($outings as $outing)
                            <tr wire:key="outing-{{ $outing->id }}">
                                <td>{{ $outing->reference }}</td>
                                <td>{{ $outing->date->format('d/m/Y') }}</td>
                                <td>{{ $outing->location }}</td>
                                <td>{{ $outing->purpose }}</td>
                                <td class="text-end">{{ number_format($outing->total(), 2) }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @if($outing->hasVoucher())
                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($outing->voucher_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="{{ __('finance.voucher') }}"><i class="bi bi-file-earmark-pdf"></i></a>
                                        @endif
                                        <a href="{{ route('outings.edit', $outing) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $outing->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('finance.no_outings') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $outings->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
