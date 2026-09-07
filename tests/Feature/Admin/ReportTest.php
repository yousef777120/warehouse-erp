<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Unit;
use App\Models\Item;
use App\Models\StockBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_access_reports_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_stock_on_hand_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/stock-on-hand');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_item_card_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/item-card');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_low_stock_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/low-stock');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_movement_summary_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/movement-summary');
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_reports(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/admin/reports');
        $response->assertStatus(403);
    }

    public function test_stock_on_hand_filters_by_warehouse(): void
    {
        $warehouse = Warehouse::create(['code' => 'WH-TEST', 'name' => 'مخزن تجريبي']);
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);
        $item = Item::create([
            'code' => 'ITM-TEST',
            'name' => 'صنف تجريبي',
            'unit_id' => $unit->id,
        ]);
        StockBalance::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/stock-on-hand?warehouse_id=' . $warehouse->id);

        $response->assertStatus(200);
        $response->assertSee('صنف تجريبي');
    }
}