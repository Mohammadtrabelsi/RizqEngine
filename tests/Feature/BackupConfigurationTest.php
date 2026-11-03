<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Tests\TestCase;

/**
 * Guards the safety properties of config/backup.php: backups go to a private
 * disk, contain the database and uploads only, and nothing is scheduled until
 * explicitly enabled.
 */
class BackupConfigurationTest extends TestCase
{
    public function test_backups_are_written_to_a_private_disk(): void
    {
        $disks = config('backup.backup.destination.disks');
        $this->assertSame(['backups'], $disks);

        $root = config('filesystems.disks.backups.root');
        $this->assertSame(storage_path('app/backups'), $root);
        $this->assertArrayNotHasKey('url', config('filesystems.disks.backups'));
        $this->assertStringNotContainsString(storage_path('app/public'), $root);
        $this->assertStringNotContainsString(public_path(), $root);
    }

    public function test_backups_contain_the_database_and_uploaded_files_only(): void
    {
        $this->assertSame([config('database.default')], config('backup.backup.source.databases'));
        $this->assertSame([storage_path('app/public')], config('backup.backup.source.files.include'));
        $this->assertNotContains(base_path(), config('backup.backup.source.files.include'), 'Source code is not backed up (it lives in Git).');
    }

    public function test_the_backup_destination_is_never_inside_the_backed_up_files(): void
    {
        $root = config('filesystems.disks.backups.root');

        foreach (config('backup.backup.source.files.include') as $included) {
            $this->assertFalse(str_starts_with($root, $included), 'A backup must not contain previous backups.');
        }
    }

    public function test_only_failures_are_notified_and_only_when_an_address_is_set(): void
    {
        $notifications = config('backup.notifications.notifications');

        $this->assertSame([], $notifications[BackupWasSuccessfulNotification::class]);
        // phpunit.xml does not set BACKUP_NOTIFICATION_EMAIL.
        $this->assertSame([], $notifications[BackupHasFailedNotification::class]);
    }

    public function test_backups_are_not_scheduled_unless_enabled(): void
    {
        $this->assertFalse(config('backup.schedule.enabled'));

        $commands = collect(app(Schedule::class)->events())->map->command->implode(' ');

        $this->assertStringNotContainsString('backup:run', $commands);
        $this->assertStringNotContainsString('backup:clean', $commands);
        $this->assertStringContainsString('health:check', $commands);
    }

    public function test_the_backup_commands_are_available(): void
    {
        $this->artisan('backup:list')->assertSuccessful();
    }
}
