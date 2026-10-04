<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ✅ المدير: جميع الصلاحيات
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // ✅ مدير المخازن: كل شيء ما عدا إدارة المستخدمين والأدوار
        $warehouseManager = Role::firstOrCreate(['name' => 'warehouse_manager']);
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

        // ✅ أمين المخزن: عمليات فقط (بدون إدارة)
        $storeKeeper = Role::firstOrCreate(['name' => 'store_keeper']);
        $storeKeeper->syncPermissions([
            'view_dashboard',
            'view_warehouses',
            'view_items',
            'view_receipts', 'manage_receipts',
            'view_issues', 'manage_issues',
            'view_transfers', 'manage_transfers',
        ]);

        // ✅ المستخدم العادي: عرض فقط
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'view_dashboard',
        ]);

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح');
    }
}