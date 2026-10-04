<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User; // ✅ هذا السطر هو الحل

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // مسح الكاش
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // قائمة الصلاحيات
        $permissions = [
            'view_dashboard',
            'view_users', 'manage_users',
            'view_roles', 'manage_roles',
            'view_warehouses', 'manage_warehouses',
            'view_categories', 'manage_categories',
            'view_units', 'manage_units',
            'view_items', 'manage_items',
            'view_receipts', 'manage_receipts',
            'view_issues', 'manage_issues',
            'view_transfers', 'manage_transfers',
            'view_reports', 'export_reports',
            'view_accounting', 'manage_accounting',
            'view_audit_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ✅ المدير: جميع الصلاحيات
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // منح المستخدم الإداري الدور
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
            $admin->syncPermissions(Permission::all());
            $this->command->info("✅ تم منح صلاحيات المدير لـ: {$admin->name}");
        } else {
            $this->command->warn("⚠️ المستخدم admin@example.com غير موجود - تخطي");
        }

        // ✅ مدير المخازن
        $warehouseManager = Role::firstOrCreate(['name' => 'warehouse_manager', 'guard_name' => 'web']);
        $warehouseManager->syncPermissions([
            'view_dashboard',
            'view_warehouses', 'manage_warehouses',
            'view_categories', 'manage_categories',
            'view_units', 'manage_units',
            'view_items', 'manage_items',
            'view_receipts', 'manage_receipts',
            'view_issues', 'manage_issues',
            'view_transfers', 'manage_transfers',
            'view_reports', 'export_reports',
            'view_audit_logs', 'view_accounting',
        ]);

        // ✅ أمين المخزن
        $storeKeeper = Role::firstOrCreate(['name' => 'store_keeper', 'guard_name' => 'web']);
        $storeKeeper->syncPermissions([
            'view_dashboard',
            'view_warehouses',
            'view_items',
            'view_receipts', 'manage_receipts',
            'view_issues', 'manage_issues',
            'view_transfers', 'manage_transfers',
        ]);

        // ✅ المستخدم العادي
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions([
            'view_dashboard',
        ]);

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح');
    }
}