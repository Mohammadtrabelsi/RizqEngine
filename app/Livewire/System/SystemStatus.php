<?php

namespace App\Livewire\System;

use App\Services\Monitoring\SystemStatusService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Admin "System status" screen: application health checks and backup status.
 *
 * Read-only by design — backups are created, cleaned and restored from the
 * command line / scheduler, never from the browser.
 */
class SystemStatus extends Component
{
    public function mount(): void
    {
        abort_if(Gate::denies('access_system_monitoring'), 403);
    }

    /**
     * Re-run every health check now.
     */
    public function refreshChecks(SystemStatusService $status): void
    {
        abort_if(Gate::denies('access_system_monitoring'), 403);

        $status->refreshHealth();

        session()->flash('success', __('system.checks_refreshed'));
    }

    public function render(SystemStatusService $status)
    {
        return view('livewire.system.system-status', [
            'health' => $status->health(),
            'backups' => $status->backups(),
            'backupSettings' => $status->backupSettings(),
        ])->layout('components.layouts.admin', ['title' => __('system.title')]);
    }
}
