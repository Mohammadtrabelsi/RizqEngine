{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.month') }}</label>
                                    <select wire:model="month" class="form-select @error('month') is-invalid @enderror">
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                        @endforeach
                                    </select>
                                    @error('month') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.year') }}</label>
                                    <input type="number" wire:model="year" class="form-control @error('year') is-invalid @enderror">
                                    @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('finance.starting_budget') }}</label>
                                    <input type="number" step="0.01" wire:model="starting_budget" class="form-control @error('starting_budget') is-invalid @enderror">
                                    @error('starting_budget') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('finance.note') }}</label>
                                <textarea wire:model="note" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                                <a href="{{ route('monthly-budgets.index') }}" class="btn btn-outline-secondary">{{ __('app.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
