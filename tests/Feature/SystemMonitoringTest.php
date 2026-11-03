<?php

namespace Tests\Feature;

use App\Health\StorageWritableCheck;
use App\Livewire\System\SystemStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Facades\Health;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SystemMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    /** Register only checks whose outcome does not depend on the CI host. */
    private function useDeterministicChecks(): void
    {
        Health::clearChecks()->checks([
            DatabaseCheck::new(),
            CacheCheck::new(),
        ]);
    }

    public function test_the_default_checks_are_registered(): void
    {
        $names = Health::registeredChecks()->map->getName()->all();

        foreach (['Database', 'Cache', 'Storage', 'UsedDiskSpace'] as $expected) {
            $this->assertContains($expected, $names);
        }

        // Production-only checks are not registered outside production.
        $this->assertNotContains((new DebugModeCheck)->getName(), $names);
    }

    public function test_the_public_health_endpoint_reports_503_before_any_check_ran(): void
    {
        $this->getJson(route('health'))
            ->assertStatus(503)
            ->assertExactJson(['healthy' => false]);
    }

    public function test_the_public_health_endpoint_reports_healthy_without_details(): void
    {
        $this->useDeterministicChecks();
        Artisan::call('health:check', ['--no-notification' => true]);

        $response = $this->getJson(route('health'))
            ->assertOk()
            ->assertExactJson(['healthy' => true]);

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_the_public_endpoint_cannot_trigger_a_check_run(): void
    {
        $this->getJson(route('health').'?fresh=1')->assertStatus(503);
    }

    public function test_the_storage_check_fails_when_a_disk_is_not_writable(): void
    {
        Storage::fake('local');
        $ok = StorageWritableCheck::new()->disks(['local'])->run();
        $this->assertSame('ok', $ok->status->value);

        config(['filesystems.disks.broken' => ['driver' => 'local', 'root' => '/proc/rizq-not-writable']]);
        $failed = StorageWritableCheck::new()->disks(['broken'])->run();
        $this->assertSame('failed', $failed->status->value);
    }

    public function test_guests_are_redirected_from_the_status_screen(): void
    {
        $this->get(route('system-status.index'))->assertRedirect();
    }

    public function test_the_status_screen_requires_the_monitoring_permission(): void
    {
        $this->actingAs($this->userWith(['access_products']))
            ->get(route('system-status.index'))
            ->assertForbidden();
    }

    public function test_an_authorised_admin_sees_health_and_backup_status(): void
    {
        $this->useDeterministicChecks();
        $this->actingAs($this->userWith(['access_system_monitoring']));

        Livewire::test(SystemStatus::class)
            ->assertSee(__('system.never_checked'))
            ->call('refreshChecks')
            ->assertSee('Database')
            ->assertSee(__('system.status_ok'))
            ->assertSee('backups')
            ->assertSee(__('system.backups_not_scheduled'));
    }

    public function test_the_status_screen_never_exposes_secrets(): void
    {
        config(['database.connections.sqlite.password' => 'db-secret-value', 'backup.backup.password' => 'zip-secret-value']);
        $this->useDeterministicChecks();
        $this->actingAs($this->userWith(['access_system_monitoring']));

        Livewire::test(SystemStatus::class)
            ->call('refreshChecks')
            ->assertDontSee('db-secret-value')
            ->assertDontSee('zip-secret-value')
            ->assertDontSee((string) config('app.key'))
            ->assertSee(__('system.encrypted'));
    }

    public function test_refreshing_checks_requires_the_permission(): void
    {
        $user = $this->userWith(['access_system_monitoring']);
        $this->actingAs($user);

        $component = Livewire::test(SystemStatus::class);

        $user->revokePermissionTo('access_system_monitoring');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $component->call('refreshChecks')->assertForbidden();
    }

    public function test_the_permission_migration_grants_monitoring_to_owner_and_admin_only(): void
    {
        foreach (['Owner', 'Admin', 'Manager', 'Cashier'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $migration = require database_path('migrations/2026_11_03_000001_add_system_monitoring_and_export_permissions.php');
        $migration->up();

        $this->assertTrue(Role::findByName('Owner')->hasPermissionTo('access_system_monitoring'));
        $this->assertTrue(Role::findByName('Admin')->hasPermissionTo('access_system_monitoring'));
        $this->assertFalse(Role::findByName('Manager')->hasPermissionTo('access_system_monitoring'));
        $this->assertFalse(Role::findByName('Cashier')->hasPermissionTo('access_system_monitoring'));
        $this->assertTrue(Role::findByName('Manager')->hasPermissionTo('export_products'));
        $this->assertFalse(Role::findByName('Cashier')->hasPermissionTo('export_products'));
    }
}
