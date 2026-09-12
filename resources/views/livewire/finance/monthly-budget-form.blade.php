{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form wire:submit="save">
                    <x-form-card :title="__('finance.monthly_budget')" icon="bi-wallet2">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('finance.month') }}</label>
                                <select wire:model="month" class="form-select @error('month') !border-red-500 @enderror">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                    @endforeach
                                </select>
                                @error('month') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('finance.year') }}</label>
                                <input type="number" wire:model="year" class="form-control @error('year') !border-red-500 @enderror">
                                @error('year') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('finance.starting_budget') }}</label>
                                <input type="number" step="0.01" wire:model="starting_budget" class="form-control @error('starting_budget') !border-red-500 @enderror">
                                @error('starting_budget') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-3 mb-0">
                            <label class="form-label">{{ __('finance.note') }}</label>
                            <textarea wire:model="note" class="form-control" rows="3"></textarea>
                        </div>

                        <x-slot:footer>
                            <a href="{{ route('monthly-budgets.index') }}" class="btn btn-secondary">{{ __('app.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                        </x-slot:footer>
                    </x-form-card>
                </form>
            </div>
        </div>
    </div>
</div>
