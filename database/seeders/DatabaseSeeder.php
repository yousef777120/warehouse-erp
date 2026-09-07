<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\StockBalance;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. الصلاحيات
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'export_reports',
            'manage_categories',
            'manage_issues',
            'manage_items',
            'manage_receipts',
            'manage_roles',
            'manage_transfers',
            'manage_units',
            'manage_users',
            'manage_warehouses',
            'view_audit_logs',
            'view_categories',
            'view_dashboard',
            'view_issues',
            'view_items',
            'view_receipts',
            'view_reports',
            'view_roles',
            'view_transfers',
            'view_units',
            'view_users',
            'view_warehouses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. الأدوار
        |--------------------------------------------------------------------------
        */

        $roleNames = [
            'admin',
            'warehouse_manager',
            'keeper',
            'store_keeper',
            'manager',
            'user',
        ];

        foreach ($roleNames as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. صلاحيات الأدوار
        |--------------------------------------------------------------------------
        */

        Role::findByName('admin', 'web')
            ->syncPermissions(Permission::all());

        Role::findByName('warehouse_manager', 'web')
            ->syncPermissions([
                'view_dashboard',
                'view_warehouses',
                'manage_warehouses',
                'view_categories',
                'manage_categories',
                'view_units',
                'manage_units',
                'view_items',
                'manage_items',
                'view_receipts',
                'manage_receipts',
                'view_issues',
                'manage_issues',
                'view_transfers',
                'manage_transfers',
                'view_reports',
                'export_reports',
            ]);

        Role::findByName('keeper', 'web')
            ->syncPermissions([
                'view_dashboard',
                'view_warehouses',
                'view_categories',
                'view_units',
                'view_items',
                'manage_items',
                'view_receipts',
                'manage_receipts',
                'view_issues',
                'manage_issues',
                'view_transfers',
                'manage_transfers',
                'view_reports',
            ]);

        Role::findByName('store_keeper', 'web')
            ->syncPermissions([
                'view_dashboard',
                'view_warehouses',
                'view_categories',
                'view_units',
                'view_items',
                'view_receipts',
                'view_issues',
                'view_transfers',
                'view_reports',
            ]);

        Role::findByName('manager', 'web')
            ->syncPermissions([
                'view_dashboard',
                'view_warehouses',
                'view_categories',
                'view_units',
                'view_items',
                'view_receipts',
                'view_issues',
                'view_transfers',
                'view_reports',
                'export_reports',
            ]);

        Role::findByName('user', 'web')
            ->syncPermissions([
                'view_dashboard',
            ]);

        /*
        |--------------------------------------------------------------------------
        | 4. المستخدمون
        |--------------------------------------------------------------------------
        */

        $users = [
            [
                'name' => 'مدير النظام',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'مدير المخازن',
                'email' => 'warehouse@example.com',
                'role' => 'warehouse_manager',
            ],
            [
                'name' => 'أمين المخزن',
                'email' => 'keeper@example.com',
                'role' => 'keeper',
            ],
            [
                'name' => 'مدير',
                'email' => 'manager@example.com',
                'role' => 'manager',
            ],
            [
                'name' => 'مستخدم عادي',
                'email' => 'user@example.com',
                'role' => 'user',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. التصنيفات
        |--------------------------------------------------------------------------
        */

        $categories = [];

        $categoryData = [
            ['name' => 'أجهزة كمبيوتر', 'code' => 'CAT-001'],
            ['name' => 'أجهزة شبكات', 'code' => 'CAT-002'],
            ['name' => 'أجهزة مكتبية', 'code' => 'CAT-003'],
            ['name' => 'مستلزمات مكتبية', 'code' => 'CAT-004'],
            ['name' => 'قطع غيار', 'code' => 'CAT-005'],
        ];

        foreach ($categoryData as $data) {
            $categories[$data['code']] = Category::updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. الوحدات
        |--------------------------------------------------------------------------
        */

        $units = [];

        $unitData = [
            ['name' => 'قطعة', 'code' => 'PCS'],
            ['name' => 'كرتون', 'code' => 'BOX'],
            ['name' => 'كيلو', 'code' => 'KG'],
            ['name' => 'متر', 'code' => 'M'],
        ];

        foreach ($unitData as $data) {
            $units[$data['code']] = Unit::updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. المخازن
        |--------------------------------------------------------------------------
        */

        $warehouses = [];

        $warehouseData = [
            [
                'name' => 'المستودع الرئيسي',
                'code' => 'WH-MAIN',
            ],
            [
                'name' => 'مستودع الفرع',
                'code' => 'WH-BRANCH',
            ],
            [
                'name' => 'مستودع تقنية المعلومات',
                'code' => 'WH-IT',
            ],
            [
                'name' => 'مستودع المستلزمات',
                'code' => 'WH-OFFICE',
            ],
        ];

        foreach ($warehouseData as $data) {
            $warehouses[$data['code']] = Warehouse::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. الأصناف
        |--------------------------------------------------------------------------
        */

        $items = [];

        $itemData = [
            [
                'name' => 'لابتوب Dell',
                'code' => 'ITEM-001',
                'sku' => 'SKU-LAPTOP-001',
                'category_id' => $categories['CAT-001']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'جهاز كمبيوتر Lenovo',
                'code' => 'ITEM-002',
                'sku' => 'SKU-PC-002',
                'category_id' => $categories['CAT-001']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'شاشة Dell 24 بوصة',
                'code' => 'ITEM-003',
                'sku' => 'SKU-MONITOR-003',
                'category_id' => $categories['CAT-001']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Switch 24 Port',
                'code' => 'ITEM-004',
                'sku' => 'SKU-SWITCH-004',
                'category_id' => $categories['CAT-002']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'كابل شبكة Cat6',
                'code' => 'ITEM-005',
                'sku' => 'SKU-CAT6-005',
                'category_id' => $categories['CAT-002']->id,
                'unit_id' => $units['M']->id,
                'min_stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'طابعة HP LaserJet',
                'code' => 'ITEM-006',
                'sku' => 'SKU-PRINTER-006',
                'category_id' => $categories['CAT-003']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'ورق A4',
                'code' => 'ITEM-007',
                'sku' => 'SKU-PAPER-007',
                'category_id' => $categories['CAT-004']->id,
                'unit_id' => $units['BOX']->id,
                'min_stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'لوحة مفاتيح',
                'code' => 'ITEM-008',
                'sku' => 'SKU-KEYBOARD-008',
                'category_id' => $categories['CAT-005']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'فأرة كمبيوتر',
                'code' => 'ITEM-009',
                'sku' => 'SKU-MOUSE-009',
                'category_id' => $categories['CAT-005']->id,
                'unit_id' => $units['PCS']->id,
                'min_stock' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($itemData as $data) {
            $items[$data['code']] = Item::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. أرصدة المخزون
        |--------------------------------------------------------------------------
        */

        $balances = [
            // المستودع الرئيسي
            ['ITEM-001', 'WH-MAIN', 15],
            ['ITEM-002', 'WH-MAIN', 25],
            ['ITEM-003', 'WH-MAIN', 18],
            ['ITEM-004', 'WH-MAIN', 8],
            ['ITEM-005', 'WH-MAIN', 500],
            ['ITEM-006', 'WH-MAIN', 6],
            ['ITEM-007', 'WH-MAIN', 50],

            // مستودع الفرع
            ['ITEM-001', 'WH-BRANCH', 5],
            ['ITEM-002', 'WH-BRANCH', 10],
            ['ITEM-003', 'WH-BRANCH', 7],
            ['ITEM-004', 'WH-BRANCH', 3],
            ['ITEM-005', 'WH-BRANCH', 150],

            // مستودع تقنية المعلومات
            ['ITEM-001', 'WH-IT', 10],
            ['ITEM-003', 'WH-IT', 5],
            ['ITEM-008', 'WH-IT', 25],
            ['ITEM-009', 'WH-IT', 30],

            // مستودع المستلزمات
            ['ITEM-007', 'WH-OFFICE', 35],
            ['ITEM-006', 'WH-OFFICE', 4],
            ['ITEM-008', 'WH-OFFICE', 10],
        ];

        foreach ($balances as [$itemCode, $warehouseCode, $quantity]) {
            StockBalance::updateOrCreate(
                [
                    'item_id' => $items[$itemCode]->id,
                    'warehouse_id' => $warehouses[$warehouseCode]->id,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | رسالة النجاح
        |--------------------------------------------------------------------------
        */

        $this->command->info('========================================');
        $this->command->info('تم إنشاء البيانات التجريبية بنجاح');
        $this->command->info('========================================');
        $this->command->info('عدد المستخدمين: 5');
        $this->command->info('عدد التصنيفات: 5');
        $this->command->info('عدد الوحدات: 4');
        $this->command->info('عدد المخازن: 4');
        $this->command->info('عدد الأصناف: 9');
        $this->command->info('========================================');
        $this->command->info('كلمة مرور جميع المستخدمين: password');
        $this->command->info('========================================');
    }
}