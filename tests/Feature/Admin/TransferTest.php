<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Unit;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. مسح ذاكرة التخزين المؤقت للصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. إنشاء الصلاحيات المطلوبة
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_transfers']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view_transfers']);

        // 3. إنشاء مستخدم مدير ومنحه الصلاحيات
        $this->admin = \App\Models\User::factory()->create();
        $this->admin->givePermissionTo(['manage_transfers', 'view_transfers']);

        // 4. إنشاء المستودعات
        $this->warehouse1 = \App\Models\Warehouse::create([
            'name' => 'مستودع 1',
            'code' => 'WH-001',
            'is_active' => true,
        ]);

        $this->warehouse2 = \App\Models\Warehouse::create([
            'name' => 'مستودع 2',
            'code' => 'WH-002',
            'is_active' => true,
        ]);

        // 5. إنشاء وحدة قياس
        $unit = \App\Models\Unit::firstOrCreate(
            ['name' => 'قطعة'],
            ['code' => 'PCS', 'is_active' => true]
        );

        // 6. إنشاء عنصر (صنف)
        $this->item = \App\Models\Item::create([
            'name' => 'صنف تجريبي',
            'code' => 'ITEM-001',
            'sku' => 'TEST-SKU-001',
            'unit_id' => $unit->id,
            'is_active' => true,
        ]);

        // 7. إنشاء رصيد مخزوني مبدئي
        \App\Models\StockBalance::updateOrCreate(
            [
                'warehouse_id' => $this->warehouse1->id,
                'item_id' => $this->item->id,
            ],
            [
                'quantity' => 100,
            ]
        );
    }

    public function test_admin_can_view_transfers_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/transfers');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_create_transfer_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/transfers/create');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_transfer(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transfers', [
            'transfer_date' => date('Y-m-d'),
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'notes' => 'تحويل تجريبي',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 10],
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_transfers', [
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
        ]);
    }

    public function test_admin_can_send_transfer(): void
    {
        $transfer = StockTransfer::create([
            'serial' => 'TR-2026-000001',
            'transfer_date' => date('Y-m-d'),
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        StockTransferItem::create([
            'transfer_id' => $transfer->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/transfers/{$transfer->id}/send");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_transfers', [
            'id' => $transfer->id,
            'status' => 'in_transit',
        ]);
    }

    public function test_admin_can_receive_transfer(): void
    {
        $transfer = StockTransfer::create([
            'serial' => 'TR-2026-000002',
            'transfer_date' => date('Y-m-d'),
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'status' => 'in_transit',
            'created_by' => $this->admin->id,
            'sent_by' => $this->admin->id,
            'sent_at' => now(),
        ]);

        StockTransferItem::create([
            'transfer_id' => $transfer->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/transfers/{$transfer->id}/receive");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_transfers', [
            'id' => $transfer->id,
            'status' => 'received',
        ]);
    }

    public function test_cannot_send_transfer_with_insufficient_balance(): void
    {
        // تعديل الرصيد الموجود من 100 إلى 5
        $balance = StockBalance::where('item_id', $this->item->id)
            ->where('warehouse_id', $this->warehouse1->id) // <-- تم تصحيح الفاصلة هنا
            ->firstOrFail();

        $balance->update(['quantity' => 5]);

        $transfer = StockTransfer::create([
            'serial' => 'TR-2026-000003',
            'transfer_date' => date('Y-m-d'),
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        StockTransferItem::create([
            'transfer_id' => $transfer->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/transfers/{$transfer->id}/send");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('stock_transfers', [
            'id' => $transfer->id,
            'status' => 'draft',
        ]);
    }

    public function test_cannot_send_same_warehouse_transfer(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transfers', [
            'transfer_date' => date('Y-m-d'),
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse1->id,
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 10],
            ],
        ]);

        $response->assertSessionHasErrors(['to_warehouse_id']);
    }
}