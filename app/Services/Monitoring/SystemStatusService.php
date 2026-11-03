<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Artisan;
use Spatie\Backup\Config\Config as BackupConfig;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Throwable;

/**
 * Read-only view of application health (spatie/laravel-health) and backup
 * status (spatie/laravel-backup) for the admin System status screen.
 *
 * Only summaries are returned: no file paths, credentials, environment
 * values or download links ever leave this service.
 */
class SystemStatusService
{
    public function __construct(private readonly ResultStore $results) {}

    /**
     * Run every registered health check now and store the results. Failure
     * notifications are suppressed: an admin looking at the screen does not
     * need an e-mail about what they are already seeing.
     */
    public function refreshHealth(): void
    {
        Artisan::call(RunHealthChecksCommand::class, ['--no-notification' => true]);
    }

    /**
     * Latest stored health check results.
     *
     * @return array{checked_at: ?string, healthy: bool, checks: array<int, array{label: string, status: string, summary: string, message: string}>}
     */
    public function health(): array
    {
        $latest = $this->results->latestResults();

        if ($latest === null) {
            return ['checked_at' => null, 'healthy' => false, 'checks' => []];
        }

        return [
            'checked_at' => $latest->finishedAt->format('Y-m-d H:i:s'),
            'healthy' => $latest->allChecksOk(),
            'checks' => $latest->storedCheckResults->map(fn ($check) => [
                'label' => $check->label ?: $check->name,
                'status' => $check->status,
                'summary' => $check->shortSummary,
                'message' => (string) $check->notificationMessage,
            ])->values()->all(),
        ];
    }

    /**
     * Status of every monitored backup destination.
     *
     * @return array<int, array{name: string, disk: string, reachable: bool, healthy: bool, count: int, newest: ?string, used_storage_mb: float, problem: ?string}>
     */
    public function backups(): array
    {
        try {
            $statuses = BackupDestinationStatusFactory::createForMonitorConfig(
                app(BackupConfig::class)->monitoredBackups
            );
        } catch (Throwable) {
            return [];
        }

        return $statuses->map(function (BackupDestinationStatus $status) {
            $destination = $status->backupDestination();
            $reachable = $destination->isReachable();
            $newest = $reachable ? $destination->newestBackup() : null;

            return [
                'name' => $destination->backupName(),
                'disk' => $destination->diskName(),
                'reachable' => $reachable,
                'healthy' => $status->isHealthy(),
                'count' => $reachable ? $destination->backups()->count() : 0,
                'newest' => $newest?->date()->format('Y-m-d H:i'),
                'used_storage_mb' => $reachable ? round($destination->usedStorage() / 1024 / 1024, 2) : 0.0,
                // Package failure messages describe the check, not the environment.
                'problem' => $status->getHealthCheckFailure()?->exception()->getMessage(),
            ];
        })->values()->all();
    }

    /**
     * Non-sensitive summary of the backup configuration.
     *
     * @return array{scheduled: bool, run_at: string, notifications: bool, encrypted: bool}
     */
    public function backupSettings(): array
    {
        return [
            'scheduled' => (bool) config('backup.schedule.enabled'),
            'run_at' => (string) config('backup.schedule.run_at'),
            'notifications' => ! empty(config('backup.notifications.notifications')[BackupHasFailedNotification::class] ?? []),
            'encrypted' => filled(config('backup.backup.password')),
        ];
    }
}
