{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-lg-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="name">{{ __('units.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('name') !border-red-500 @enderror" wire:model="name">
                                        @error('name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="short_name">{{ __('units.short_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('short_name') !border-red-500 @enderror" wire:model="short_name">
                                        @error('short_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="mb-4">
                                        <label for="operator">{{ __('units.operator') }}</label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" wire:model="operator" placeholder="ex: * / + -">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="mb-4">
                                        <label for="operation_value">{{ __('units.operation_value') }}</label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" wire:model="operation_value" placeholder="Enter a number">
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="mb-4">
                                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                            {{ $unitId ? __('units.update_unit') : __('units.create_unit') }} <i class="bi bi-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
