<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Unit;
use App\Models\StockBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مدير النظام ومنحه الصلاحيات
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo([
            'view_dashboard', 'view_warehouses', 'view_items', 'view_reports'
        ]);
    }

    public function test_dashboard_shows_real_counts(): void
    {
        // إنشاء بيانات تجريبية
        Warehouse::create(['name' => 'مخزن 1', 'code' => 'W1', 'is_active' => true]);
        $unit = Unit::firstOrCreate(['name' => 'قطعة', 'code' => 'PCS']);
        Item::create(['name' => 'صنف 1', 'code' => 'I1', 'sku' => 'S1', 'is_active' => true, 'unit_id' => $unit->id]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        
        // التحقق من وجود العناصر الأساسية
        $response->assertSee('مرحباً بك');
        $response->assertSee($this->admin->name);
        $response->assertSee('المستخدمون');
        $response->assertSee('المخازن');
    }

    public function test_dashboard_shows_low_stock_alerts(): void
    {
        // إنشاء صنف منخفض المخزون
        $unit = Unit::firstOrCreate(['name' => 'قطعة', 'code' => 'PCS']);
        $item = Item::create([
            'name' => 'صنف منخفض',
            'code' => 'LOW1',
            'sku' => 'LOW1',
            'is_active' => true,
            'unit_id' => $unit->id,
            'min_stock' => 10
        ]);
        $warehouse = Warehouse::create(['name' => 'مخزن رئيسي', 'code' => 'MAIN', 'is_active' => true]);
        
        // إنشاء رصيد أقل من الحد الأدنى
        StockBalance::create([
            'item_id' => $item->id, 
            'warehouse_id' => $warehouse->id, 
            'quantity' => 5
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        
        // ✅ التحقق من نصوص التنبيه كما هي في التصميم الاحترافي
        $response->assertSee('أصناف منخفضة المخزون');
        $response->assertSee('وصل للحد الأدنى'); // جملة كاملة لتجنب أي مشاكل في مطابقة الأحرف
    }

    public function test_dashboard_shows_chart_and_recent_transactions(): void
    {
        // إنشاء وحدة وصنف
        $unit = Unit::firstOrCreate(['name' => 'قطعة', 'code' => 'PCS']);
        $item = Item::create([
            'name' => 'صنف تجريبي',
            'code' => 'TEST1',
            'sku' => 'TEST1',
            'is_active' => true,
            'unit_id' => $unit->id
        ]);

        // إنشاء مخزن
        $warehouse = Warehouse::create([
            'name' => 'مخزن رئيسي', 
            'code' => 'MAIN', 
            'is_active' => true
        ]);

        // ✅ الحل الجذري: إنشاء رصيد مخزوني لكي يظهر الصنف في الرسم البياني
        StockBalance::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        
        // التحقق من وجود معرف الرسم البياني واسم الصنف
        $response->assertSee('warehousesChart');
        $response->assertSee('صنف تجريبي');
    }
}