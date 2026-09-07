<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ===== المخازن =====
    public function test_admin_can_view_warehouses(): void
    {
        Warehouse::create(['code' => 'WH-T1', 'name' => 'مخزن تجريبي']);
        $response = $this->actingAs($this->admin)->get('/admin/warehouses');
        $response->assertStatus(200);
        $response->assertSee('مخزن تجريبي');
    }

    public function test_admin_can_create_warehouse(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/warehouses', [
            'code' => 'WH-NEW',
            'name' => 'مخزن جديد',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.warehouses.index'));
        $this->assertDatabaseHas('warehouses', ['code' => 'WH-NEW']);
    }

    public function test_admin_cannot_create_warehouse_with_duplicate_code(): void
    {
        Warehouse::create(['code' => 'WH-001', 'name' => 'مخزن 1']);
        $response = $this->actingAs($this->admin)->post('/admin/warehouses', [
            'code' => 'WH-001',
            'name' => 'مخزن مكرر',
        ]);
        $response->assertSessionHasErrors('code');
    }

    // ===== التصنيفات =====
    public function test_admin_can_view_categories(): void
    {
        Category::create(['code' => 'CAT-T1', 'name' => 'تصنيف تجريبي']);
        $response = $this->actingAs($this->admin)->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('تصنيف تجريبي');
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'code' => 'CAT-NEW',
            'name' => 'تصنيف جديد',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['code' => 'CAT-NEW']);
    }

    public function test_admin_can_create_sub_category(): void
    {
        $parent = Category::create(['code' => 'CAT-P', 'name' => 'تصنيف رئيسي']);
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'code' => 'CAT-SUB',
            'name' => 'تصنيف فرعي',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['code' => 'CAT-SUB', 'parent_id' => $parent->id]);
    }

    // ===== الوحدات =====
    public function test_admin_can_view_units(): void
    {
        Unit::create(['code' => 'UNT-T1', 'name' => 'وحدة تجريبية']);
        $response = $this->actingAs($this->admin)->get('/admin/units');
        $response->assertStatus(200);
        $response->assertSee('وحدة تجريبية');
    }

    public function test_admin_can_create_unit(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/units', [
            'code' => 'UNT-NEW',
            'name' => 'وحدة جديدة',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.units.index'));
        $this->assertDatabaseHas('units', ['code' => 'UNT-NEW']);
    }

    // ===== الأصناف =====
    public function test_admin_can_view_items(): void
    {
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);
        Item::create(['code' => 'ITM-T1', 'name' => 'صنف تجريبي', 'unit_id' => $unit->id]);
        $response = $this->actingAs($this->admin)->get('/admin/items');
        $response->assertStatus(200);
        $response->assertSee('صنف تجريبي');
    }

    public function test_admin_can_create_item(): void
    {
        $unit = Unit::create(['code' => 'PCS', 'name' => 'قطعة']);
        $response = $this->actingAs($this->admin)->post('/admin/items', [
            'code' => 'ITM-NEW',
            'name' => 'صنف جديد',
            'unit_id' => $unit->id,
            'min_stock' => 10,
            'max_stock' => 100,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseHas('items', ['code' => 'ITM-NEW']);
    }

    // ===== الصلاحيات =====
    public function test_regular_user_cannot_access_warehouses_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/admin/warehouses');
        $response->assertStatus(200); // view_warehouses مسموح

        $response = $this->actingAs($user)->get('/admin/warehouses/create');
        $response->assertStatus(403); // manage_warehouses ممنوع
    }

    public function test_store_keeper_cannot_manage_items(): void
    {
        $user = User::factory()->create();
        $user->assignRole('keeper');

        $response = $this->actingAs($user)->get('/admin/items/create');
        $response->assertStatus(403);
    }
}