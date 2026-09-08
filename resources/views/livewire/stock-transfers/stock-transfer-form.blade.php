{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-3">
                <div class="flex-auto p-2">
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.from') }} <span class="text-danger">*</span></label>
                                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('from_warehouse_id') !border-red-500 @enderror" wire:model="from_warehouse_id">
                                    <option value="">{{ __('warehouses.select_warehouse') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.to') }} <span class="text-danger">*</span></label>
                                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('to_warehouse_id') !border-red-500 @enderror" wire:model="to_warehouse_id">
                                    <option value="">{{ __('warehouses.select_warehouse') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('date') !border-red-500 @enderror" wire:model="date">
                                @error('date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-3">
                <div class="flex-auto p-2">
                    @error('lines') <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-red-50 text-red-700 border-red-200">{{ $message }}</div> @enderror
                    <table class="w-full mb-4 text-slate-900 border-collapse align-middle">
                        <thead>
                            <tr>
                                <th style="width:65%">{{ __('warehouses.product') }}</th>
                                <th style="width:25%">{{ __('warehouses.quantity') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $index => $line)
                                <tr wire:key="line-{{ $index }}">
                                    <td>
                                        <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('lines.'.$index.'.product_id') !border-red-500 @enderror" wire:model="lines.{{ $index }}.product_id">
                                            <option value="">{{ __('warehouses.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->product_name }} — {{ $product->product_code }}</option>
                                            @endforeach
                                        </select>
                                        @error('lines.'.$index.'.product_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <input type="number" min="1" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('lines.'.$index.'.quantity') !border-red-500 @enderror" wire:model="lines.{{ $index }}.quantity">
                                        @error('lines.'.$index.'.quantity') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="removeLine({{ $index }})"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="addLine">
                        {{ __('warehouses.add_line') }} <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>

            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-3">
                <div class="flex-auto p-2">
                    <div class="mb-4">
                        <label>{{ __('warehouses.note') }}</label>
                        <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" rows="2" wire:model="note"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                            {{ __('warehouses.create_transfer') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
