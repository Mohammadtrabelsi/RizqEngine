<?php

namespace App\Providers;

use App\Health\StorageWritableCheck;
use Illuminate\Support\ServiceProvider;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

/**
 * Registers the application health checks (spatie/laravel-health).
 *
 * Which optional checks run is controlled by config('health.checks'), so an
 * install without a queue worker or cron does not report false failures.
 */
class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $checks = config('health.checks', []);

        $registered = [
            DatabaseCheck::new(),
            CacheCheck::new(),
            StorageWritableCheck::new()->name('Storage')->disks(['local', 'public']),
        ];

        if ($checks['used_disk_space']['enabled'] ?? true) {
            $registered[] = UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage((int) ($checks['used_disk_space']['warn_percentage'] ?? 70))
                ->failWhenUsedSpaceIsAbovePercentage((int) ($checks['used_disk_space']['fail_percentage'] ?? 90));
        }

        // Production must not leak stack traces or run with a non-prod env.
        if ($this->app->isProduction()) {
            $registered[] = DebugModeCheck::new();
            $registered[] = EnvironmentCheck::new()->expectEnvironment('production');
        }

        if ($checks['queue'] ?? false) {
            $registered[] = QueueCheck::new();
        }

        if ($checks['schedule'] ?? false) {
            $registered[] = ScheduleCheck::new();
        }

        if ($checks['backups'] ?? false) {
            $maxAgeDays = (int) (config('backup.monitor_backups.0.health_checks')[MaximumAgeInDays::class] ?? 1);

            $registered[] = BackupsCheck::new()
                ->onDisk((string) (config('backup.backup.destination.disks')[0] ?? 'backups'))
                ->locatedAt(config('backup.backup.name').'/*.zip')
                // A little grace so a backup that is still running does not alert.
                ->youngestBackShouldHaveBeenMadeBefore(now()->subDays($maxAgeDays)->subHours(2));
        }

        Health::checks($registered);
    }
}
