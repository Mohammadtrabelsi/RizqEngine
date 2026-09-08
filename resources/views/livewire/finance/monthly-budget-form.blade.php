{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                    <div class="flex-auto p-2">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.month') }}</label>
                                    <select wire:model="month" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('month') !border-red-500 @enderror">
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                        @endforeach
                                    </select>
                                    @error('month') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.year') }}</label>
                                    <input type="number" wire:model="year" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('year') !border-red-500 @enderror">
                                    @error('year') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.starting_budget') }}</label>
                                    <input type="number" step="0.01" wire:model="starting_budget" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('starting_budget') !border-red-500 @enderror">
                                    @error('starting_budget') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.note') }}</label>
                                <textarea wire:model="note" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('app.save') }}</button>
                                <a href="{{ route('monthly-budgets.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400">{{ __('app.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
