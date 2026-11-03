{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        @if (session()->has('success'))
            <div class="relative px-5 py-3 mb-4 rounded-md border bg-emerald-50 text-emerald-700 border-emerald-200" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Health checks --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="flex-auto p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-heart-pulse text-primary"></i> {{ __('system.health') }}</h5>
                        <small class="text-muted">
                            {{ $health['checked_at'] ? __('system.checked_at', ['time' => $health['checked_at']]) : __('system.never_checked') }}
                        </small>
                    </div>
                    <button type="button" wire:click="refreshChecks" wire:loading.attr="disabled" wire:target="refreshChecks" class="btn btn-outline">
                        <span wire:loading.remove wire:target="refreshChecks"><i class="bi bi-arrow-clockwise"></i> {{ __('system.run_checks') }}</span>
                        <span wire:loading wire:target="refreshChecks">{{ __('system.running') }}</span>
                    </button>
                </div>

                @if (count($health['checks']) === 0)
                    <p class="text-muted mb-0">{{ __('system.no_results') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('system.check') }}</th>
                                    <th>{{ __('system.status') }}</th>
                                    <th>{{ __('system.details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($health['checks'] as $check)
                                    <tr wire:key="check-{{ $loop->index }}">
                                        <td class="fw-semibold">{{ $check['label'] }}</td>
                                        <td><x-health-status :status="$check['status']" /></td>
                                        <td class="text-muted small">{{ $check['message'] ?: $check['summary'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Backups --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="flex-auto p-3">
                <h5 class="mb-1"><i class="bi bi-archive text-primary"></i> {{ __('system.backups') }}</h5>
                <p class="small text-muted mb-3">
                    {{ $backupSettings['scheduled'] ? __('system.backups_scheduled', ['time' => $backupSettings['run_at']]) : __('system.backups_not_scheduled') }}
                    · {{ $backupSettings['notifications'] ? __('system.notifications_on') : __('system.notifications_off') }}
                    · {{ $backupSettings['encrypted'] ? __('system.encrypted') : __('system.not_encrypted') }}
                </p>

                @if (count($backups) === 0)
                    <p class="text-muted mb-0">{{ __('system.no_backup_destinations') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('system.destination') }}</th>
                                    <th>{{ __('system.status') }}</th>
                                    <th>{{ __('system.backup_count') }}</th>
                                    <th>{{ __('system.newest_backup') }}</th>
                                    <th>{{ __('system.used_storage') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backups as $backup)
                                    <tr wire:key="backup-{{ $backup['disk'] }}">
                                        <td class="fw-semibold">{{ $backup['name'] }} <span class="text-muted">({{ $backup['disk'] }})</span></td>
                                        <td>
                                            <x-health-status :status="! $backup['reachable'] ? 'crashed' : ($backup['healthy'] ? 'ok' : 'failed')" />
                                            @if ($backup['problem'])
                                                <div class="small text-muted">{{ $backup['problem'] }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $backup['count'] }}</td>
                                        <td>{{ $backup['newest'] ?? '—' }}</td>
                                        <td>{{ number_format($backup['used_storage_mb'], 2) }} MB</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle"></i> {{ __('system.backups_cli_only') }}</p>
            </div>
        </div>
    </div>
</div>
