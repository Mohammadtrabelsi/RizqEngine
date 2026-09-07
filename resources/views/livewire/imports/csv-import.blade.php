{{-- Shared full-page Livewire view for CSV imports (products, customers, suppliers). --}}
<div>
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col">
                <h4 class="mb-0">{{ $this->title() }}</h4>
            </div>
            <div class="col-auto">
                <a href="{{ route($this->redirectRouteName()) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400">
                    <i class="bi bi-arrow-left"></i> {{ __('import.back') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-emerald-50 text-emerald-700 border-emerald-200 pr-12 fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('warning'))
            <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 pr-12 fade show">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-3">
            <div class="flex-auto p-5">
                <p class="text-muted">{{ __('import.intro') }}</p>
                <p class="mb-2"><strong>{{ __('import.expected_columns') }}:</strong></p>
                <code class="d-block mb-3">{{ implode(', ', $this->expectedColumns()) }}</code>

                <form wire:submit="parse">
                    <div class="form-row align-items-end">
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <label for="file">{{ __('import.csv_file') }} <span class="text-danger">*</span></label>
                                <input type="file" accept=".csv,text/csv" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('file') !border-red-500 @enderror" wire:model="file">
                                @error('file') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" wire:loading.attr="disabled" wire:target="parse,file">
                                    <span wire:loading.remove wire:target="parse"><i class="bi bi-search"></i> {{ __('import.preview') }}</span>
                                    <span wire:loading wire:target="parse">{{ __('import.loading') }}...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div wire:loading wire:target="file" class="text-muted small">{{ __('import.loading') }}...</div>
                </form>
            </div>
        </div>

        @if ($parsed)
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                <div class="flex-auto p-5">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('import.valid_rows') }}: {{ $this->validCount }}</span>
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-danger">{{ __('import.invalid_rows') }}: {{ $this->invalidCount }}</span>
                        </div>
                        @if ($this->validCount > 0)
                            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600" wire:click="import" wire:loading.attr="disabled" wire:target="import"
                                wire:confirm="{{ __('import.confirm', ['count' => $this->validCount]) }}">
                                <i class="bi bi-cloud-upload"></i> {{ __('import.import_selected', ['count' => $this->validCount]) }}
                            </button>
                        @endif
                    </div>

                    @if (count($rows) === 0)
                        <p class="text-muted mb-0">{{ __('import.no_rows') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($this->previewColumns() as $label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                        <th>{{ __('import.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $line => $row)
                                        <tr class="{{ count($row['errors']) ? 'table-danger' : '' }}">
                                            <td>{{ $line }}</td>
                                            @foreach ($this->previewColumns() as $key => $label)
                                                <td>{{ $row['attributes'][$key] ?? '' }}</td>
                                            @endforeach
                                            <td>
                                                @if (count($row['errors']))
                                                    <ul class="mb-0 ps-3 text-danger small">
                                                        @foreach ($row['errors'] as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-success"><i class="bi bi-check-circle"></i> {{ __('import.ok') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
