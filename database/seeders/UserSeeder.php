<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'مدير النظام',
                'email' => 'admin@erp.local',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'مدير المخازن',
                'email' => 'manager@erp.local',
                'password' => Hash::make('12345678'),
                'role' => 'warehouse_manager',
            ],
            [
                'name' => 'أمين المخزن',
                'email' => 'keeper@erp.local',
                'password' => Hash::make('12345678'),
                'role' => 'store_keeper',
            ],
            [
                'name' => 'مستخدم',
                'email' => 'user@erp.local',
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // ✅ تعيين الدور
            $user->assignRole($role);
        }

        $this->command->info('✅ تم إنشاء المستخدمين وتعيين الأدوار بنجاح');
    }
}