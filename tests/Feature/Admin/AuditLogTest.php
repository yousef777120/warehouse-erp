<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_audit_logs_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/audit-logs')
            ->assertStatus(200);
    }

    public function test_creating_model_writes_audit_log(): void
    {
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);

        $this->actingAs($this->admin)->post('/admin/items', [
            'code' => 'ITM-AUD',
            'name' => 'صنف التدقيق',
            'unit_id' => $unit->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'auditable_type' => Item::class,
        ]);
    }

    public function test_updating_model_stores_old_and_new_values(): void
    {
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);
        $item = Item::create(['code' => 'ITM-1', 'name' => 'قبل التعديل', 'unit_id' => $unit->id]);

        $this->actingAs($this->admin)->put("/admin/items/{$item->id}", [
            'code' => 'ITM-1',
            'name' => 'بعد التعديل',
            'unit_id' => $unit->id,
        ]);

        $log = AuditLog::where('action', 'updated')
            ->where('auditable_type', Item::class)
            ->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertEquals('قبل التعديل', $log->old_values['name'] ?? null);
        $this->assertEquals('بعد التعديل', $log->new_values['name'] ?? null);
    }

    public function test_admin_can_view_log_details(): void
    {
        $log = AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'auditable_type' => Item::class,
            'auditable_id' => 1,
            'new_values' => ['name' => 'صنف'],
        ]);

        $this->actingAs($this->admin)
            ->get("/admin/audit-logs/{$log->id}")
            ->assertStatus(200);
    }

    public function test_regular_user_cannot_view_audit_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/admin/audit-logs')
            ->assertStatus(403);
    }
}