{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-12 col-lg-8 mb-4">
                    <x-form-card :title="__('finance.outing')" icon="bi-people">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('finance.date') }}</label>
                                <input type="date" wire:model="date" class="form-control @error('date') !border-red-500 @enderror">
                                @error('date') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
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
                                    <input type="number" step="0.01" wire:model.live="{{ $cat }}" class="form-control @error($cat) !border-red-500 @enderror">
                                    @error($cat) <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>

                        <x-slot:footer>
                            <a href="{{ route('outings.index') }}" class="btn btn-secondary">{{ __('app.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                        </x-slot:footer>
                    </x-form-card>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <x-form-card :title="__('finance.total')" icon="bi-cash-stack">
                        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-indigo-50 text-indigo-700 border-indigo-200 d-flex justify-content-between">
                            <span>{{ __('finance.total') }}</span>
                            <strong>{{ number_format($this->total, 2) }}</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('finance.note') }}</label>
                            <textarea wire:model="note" class="form-control" rows="2"></textarea>
                        </div>

                        <p class="text-muted small mb-0">{{ __('finance.voucher_auto_note') }}</p>
                    </x-form-card>
                </div>
            </div>
        </form>
    </div>
</div>
