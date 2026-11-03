<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Requires the system cron entry:
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Keep expired stock off the shop floor every night.
Schedule::command('products:mark-expired-out-of-stock')->dailyAt('00:30');

// E-mail managers a low-stock / near-expiry digest every morning.
Schedule::command('stock:send-alerts')->dailyAt('07:00');

// Health monitoring (spatie/laravel-health): refresh the stored results every
// minute and e-mail failures (when HEALTH_TO_ADDRESS is set). The heartbeat
// commands feed the optional schedule / queue checks.
Schedule::command(RunHealthChecksCommand::class)->everyMinute()->withoutOverlapping();

if (config('health.checks.schedule')) {
    Schedule::command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
}

if (config('health.checks.queue')) {
    Schedule::command(DispatchQueueCheckJobsCommand::class)->everyMinute();
}

// Backups (spatie/laravel-backup) — opt-in via BACKUP_SCHEDULE_ENABLED once
// `php artisan backup:run` has been verified manually. Cleanup runs before the
// new backup so the retention policy never deletes the archive just created.
if (config('backup.schedule.enabled')) {
    Schedule::command('backup:clean')->dailyAt((string) config('backup.schedule.cleanup_at'))->withoutOverlapping()->onOneServer();
    Schedule::command('backup:run')->dailyAt((string) config('backup.schedule.run_at'))->withoutOverlapping()->onOneServer();
    Schedule::command('backup:monitor')->dailyAt((string) config('backup.schedule.monitor_at'));
}
