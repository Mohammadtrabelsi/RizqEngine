{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.date') }}</label>
                                    <input type="date" wire:model="date" class="form-control @error('date') is-invalid @enderror">
                                    @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.location') }}</label>
                                    <input type="text" wire:model="location" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.purpose') }}</label>
                                    <input type="text" wire:model="purpose" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('finance.participants') }}</label>
                                <input type="text" wire:model="participantsText" class="form-control" placeholder="{{ __('finance.participants_hint') }}">
                            </div>

                            <div class="row">
                                @foreach(['food','gas','water','transport','misc'] as $cat)
                                    <div class="col-md-4 col-lg mb-3">
                                        <label class="form-label">{{ __('finance.cat_'.$cat) }}</label>
                                        <input type="number" step="0.01" wire:model.live="{{ $cat }}" class="form-control @error($cat) is-invalid @enderror">
                                        @error($cat) <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-info d-flex justify-content-between">
                                <span>{{ __('finance.total') }}</span>
                                <strong>{{ number_format($this->total, 2) }}</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('finance.note') }}</label>
                                <textarea wire:model="note" class="form-control" rows="2"></textarea>
                            </div>

                            <p class="text-muted small">{{ __('finance.voucher_auto_note') }}</p>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                                <a href="{{ route('outings.index') }}" class="btn btn-outline-secondary">{{ __('app.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
