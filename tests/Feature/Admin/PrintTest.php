<?php

namespace Tests\Feature\Admin;

use App\Models\Item;
use App\Models\StockReceipt;
use App\Models\StockReceiptItem;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintTest extends TestCase
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

    public function test_admin_can_print_receipt(): void
    {
        $warehouse = Warehouse::create(['code' => 'WH-1', 'name' => 'المخزن الرئيسي']);
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);
        $item = Item::create(['code' => 'ITM-1', 'name' => 'صنف تجريبي', 'unit_id' => $unit->id]);

        $receipt = StockReceipt::create([
            'serial' => 'IN-2025-000001',
            'receipt_date' => now(),
            'warehouse_id' => $warehouse->id,
            'status' => 'confirmed',
            'created_by' => $this->admin->id,
        ]);

        StockReceiptItem::create([
            'receipt_id' => $receipt->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'unit_price' => 5,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/receipts/{$receipt->id}/print");

        $response->assertStatus(200);
        $response->assertSee('IN-2025-000001');
        $response->assertSee('صنف تجريبي');
    }
}