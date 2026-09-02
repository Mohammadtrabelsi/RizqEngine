{{-- Shared full-page Livewire view for CSV imports (products, customers, suppliers). --}}
<div>
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col">
                <h4 class="mb-0">{{ $this->title() }}</h4>
            </div>
            <div class="col-auto">
                <a href="{{ route($this->redirectRouteName()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('import.back') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <p class="text-muted">{{ __('import.intro') }}</p>
                <p class="mb-2"><strong>{{ __('import.expected_columns') }}:</strong></p>
                <code class="d-block mb-3">{{ implode(', ', $this->expectedColumns()) }}</code>

                <form wire:submit="parse">
                    <div class="form-row align-items-end">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="file">{{ __('import.csv_file') }} <span class="text-danger">*</span></label>
                                <input type="file" accept=".csv,text/csv" class="form-control @error('file') is-invalid @enderror" wire:model="file">
                                @error('file') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="parse,file">
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <span class="badge bg-success">{{ __('import.valid_rows') }}: {{ $this->validCount }}</span>
                            <span class="badge bg-danger">{{ __('import.invalid_rows') }}: {{ $this->invalidCount }}</span>
                        </div>
                        @if ($this->validCount > 0)
                            <button type="button" class="btn btn-success" wire:click="import" wire:loading.attr="disabled" wire:target="import"
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
