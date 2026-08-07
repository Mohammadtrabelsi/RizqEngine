<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('access_activity_logs', 'web');
        Permission::findOrCreate('delete_activity_logs', 'web');
    }

    /** @test */
    public function it_records_activity_when_a_model_is_created_updated_and_deleted()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'category_code' => 'TST',
            'category_name' => 'Test Category',
        ]);
        $category->update(['category_name' => 'Renamed Category']);
        $category->delete();

        $this->assertDatabaseHas('activity_log', ['log_name' => 'Category', 'event' => 'created']);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'Category', 'event' => 'updated']);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'Category', 'event' => 'deleted']);
    }

    /** @test */
    public function it_captures_the_authenticated_user_as_the_causer()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Category::create([
            'category_code' => 'CSR',
            'category_name' => 'Causer Category',
        ]);

        $activity = Activity::where('event', 'created')
            ->where('subject_type', Category::class)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals(User::class, $activity->causer_type);
    }

    /** @test */
    public function it_only_stores_the_attributes_that_actually_changed()
    {
        $this->actingAs(User::factory()->create());

        $category = Category::create([
            'category_code' => 'DRT',
            'category_name' => 'Dirty Category',
        ]);
        $category->update(['category_name' => 'Only Name Changed']);

        $activity = Activity::where('event', 'updated')
            ->where('subject_type', Category::class)
            ->latest()
            ->first();

        $changes = $activity->attribute_changes->toArray();

        $this->assertArrayHasKey('category_name', $changes['attributes']);
        $this->assertArrayNotHasKey('category_code', $changes['attributes']);
        $this->assertEquals('Dirty Category', $changes['old']['category_name']);
        $this->assertEquals('Only Name Changed', $changes['attributes']['category_name']);
    }

    /** @test */
    public function the_activity_log_screen_is_protected_by_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('activity-logs.index'))
            ->assertForbidden();

        $user->givePermissionTo('access_activity_logs');

        $this->actingAs($user)
            ->get(route('activity-logs.index'))
            ->assertOk();
    }

    /** @test */
    public function clearing_the_audit_trail_requires_the_delete_permission()
    {
        Category::create(['category_code' => 'A', 'category_name' => 'A']);

        $viewer = User::factory()->create();
        $viewer->givePermissionTo('access_activity_logs');

        $this->actingAs($viewer)
            ->delete(route('activity-logs.clear'))
            ->assertForbidden();

        $this->assertTrue(Activity::count() > 0);

        $purger = User::factory()->create();
        $purger->givePermissionTo(['access_activity_logs', 'delete_activity_logs']);

        $this->actingAs($purger)
            ->delete(route('activity-logs.clear'))
            ->assertRedirect(route('activity-logs.index'));

        $this->assertEquals(0, Activity::count());
    }
}
