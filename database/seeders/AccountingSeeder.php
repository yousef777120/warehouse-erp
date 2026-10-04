<?php

namespace Database\Seeders;

use App\Models\Account;      // ✅ الاستيرادات المطلوبة
use App\Models\Employee;     // ✅
use App\Models\JobTitle;     // ✅
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        // ===== دليل الحسابات =====
        $accounts = [
            ['code' => '1000', 'name' => 'النقدية بالصندوق',    'type' => 'asset'],
            ['code' => '1010', 'name' => 'البنك',                'type' => 'asset'],
            ['code' => '1200', 'name' => 'المخزون',              'type' => 'asset'],
            ['code' => '2000', 'name' => 'الموردون',             'type' => 'liability'],
            ['code' => '2100', 'name' => 'رواتب مستحقة الدفع',   'type' => 'liability'],
            ['code' => '2200', 'name' => 'ضريبة القيمة المضافة', 'type' => 'liability'],
            ['code' => '3000', 'name' => 'رأس المال',            'type' => 'equity'],
            ['code' => '4000', 'name' => 'الإيرادات',            'type' => 'revenue'],
            ['code' => '5000', 'name' => 'مصروف المشتريات',      'type' => 'expense'],
            ['code' => '5100', 'name' => 'مصروف الرواتب',        'type' => 'expense'],
            ['code' => '5200', 'name' => 'مصروف إيجار',          'type' => 'expense'],
            ['code' => '5300', 'name' => 'كهرباء وماء',          'type' => 'expense'],
            ['code' => '5400', 'name' => 'مصروفات إدارية',       'type' => 'expense'],
        ];

        foreach ($accounts as $a) {
            Account::firstOrCreate(['code' => $a['code']], $a);
        }

        // ===== المسميات الوظيفية (1,000 → 10,000) + موظف لكل مسمى =====
        $titles = [
            ['name' => 'مدير عام',         'function' => 'الإشراف واتخاذ القرارات',   'salary' => 10000],
            ['name' => 'مدير مالي',        'function' => 'إدارة الحسابات والتقارير',  'salary' => 8500],
            ['name' => 'مدير موارد بشرية', 'function' => 'شؤون الموظفين والرواتب',     'salary' => 7000],
            ['name' => 'محاسب أول',        'function' => 'القيود اليومية والتسويات',   'salary' => 6000],
            ['name' => 'محاسب',            'function' => 'إدخال القيود ومتابعة الذمم', 'salary' => 4500],
            ['name' => 'مندوب مبيعات',     'function' => 'البيع والتحصيل',             'salary' => 3500],
            ['name' => 'أمين مخزن',        'function' => 'استلام وصرف البضائع',        'salary' => 3000],
            ['name' => 'مدخل بيانات',      'function' => 'أرشفة الفواتير',             'salary' => 2500],
            ['name' => 'حارس أمن',         'function' => 'حماية المنشأة',              'salary' => 1800],
            ['name' => 'عامل نظافة',       'function' => 'النظافة العامة',             'salary' => 1500],
            ['name' => 'متدرب',            'function' => 'التعلم والمساندة',           'salary' => 1000],
        ];

        foreach ($titles as $t) {
            $title = JobTitle::firstOrCreate(['name' => $t['name']], $t);

            Employee::firstOrCreate(
                ['name' => 'موظف: ' . $t['name']],
                ['job_title_id' => $title->id, 'salary' => $t['salary'], 'hire_date' => now()->startOfYear()]
            );
        }

        $this->command->info('✅ المحاسبة: الحسابات + المسميات + الموظفون جاهزون');
    }
}