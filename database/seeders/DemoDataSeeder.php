<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AiInvoice;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Item;
use App\Models\JobTitle;
use App\Models\JournalEntry;
use App\Models\StockBalance;
use App\Models\StockIssue;
use App\Models\StockReceipt;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ========== 0) الأساسيات ==========
        if (Role::count() === 0) $this->call(RolePermissionSeeder::class);

        $admin = User::first();
        if (! $admin) {
            $admin = User::create([
                'name' => 'مدير النظام',
                'email' => 'admin@erp.local',
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');
        }

        // ========== 1) دليل الحسابات ==========
        foreach ([
            ['1000', 'النقدية بالصندوق', 'asset'],
            ['1010', 'البنك', 'asset'],
            ['1200', 'المخزون', 'asset'],
            ['2000', 'الموردون', 'liability'],
            ['2100', 'رواتب مستحقة الدفع', 'liability'],
            ['2200', 'ضريبة القيمة المضافة', 'liability'],
            ['3000', 'رأس المال', 'equity'],
            ['4000', 'الإيرادات', 'revenue'],
            ['5000', 'مصروف المشتريات', 'expense'],
            ['5100', 'مصروف الرواتب', 'expense'],
            ['5200', 'مصروف إيجار', 'expense'],
            ['5300', 'كهرباء وماء', 'expense'],
            ['5400', 'مصروفات إدارية', 'expense'],
        ] as [$code, $name, $type]) {
            Account::firstOrCreate(['code' => $code], ['name' => $name, 'type' => $type]);
        }
        $acc = Account::pluck('id', 'code');

        // ========== 2) المسميات الوظيفية + موظفون ==========
        foreach ([
            ['مدير عام', 'الإشراف واتخاذ القرارات', 10000],
            ['مدير مالي', 'إدارة الحسابات والتقارير', 8500],
            ['مدير موارد بشرية', 'شؤون الموظفين والرواتب', 7000],
            ['محاسب أول', 'القيود اليومية والتسويات', 6000],
            ['محاسب', 'إدخال القيود ومتابعة الذمم', 4500],
            ['مندوب مبيعات', 'البيع والتحصيل', 3500],
            ['أمين مخزن', 'استلام وصرف البضائع', 3000],
            ['مدخل بيانات', 'أرشفة الفواتير', 2500],
            ['حارس أمن', 'حماية المنشأة', 1800],
            ['عامل نظافة', 'النظافة العامة', 1500],
            ['متدرب', 'التعلم والمساندة', 1000],
        ] as [$name, $func, $salary]) {
            $title = JobTitle::firstOrCreate(['name' => $name], ['function' => $func, 'salary' => $salary]);
            Employee::firstOrCreate(['name' => 'موظف: ' . $name], [
                'job_title_id' => $title->id, 'salary' => $salary, 'hire_date' => now()->startOfYear(),
            ]);
        }

        // ========== 3) قيود محاسبية تجريبية ==========
        $e1 = JournalEntry::firstOrCreate(['entry_number' => 'JE-DEMO-0001'], [
            'date' => now()->subDays(6)->toDateString(),
            'description' => 'قيد تلقائي من المخازن: سند إدخال IN-0001 (بضاعة تجارية)',
            'source' => 'warehouse', 'status' => 'posted', 'by_agent' => true,
        ]);
        if ($e1->lines()->count() === 0) {
            $e1->lines()->createMany([
                ['account_id' => $acc['1200'], 'debit' => 15000, 'credit' => 0, 'notes' => 'إضافة مخزون'],
                ['account_id' => $acc['2000'], 'debit' => 0, 'credit' => 15000, 'notes' => 'الشركة الوطنية للتوريدات'],
            ]);
        }

        $e2 = JournalEntry::firstOrCreate(['entry_number' => 'JE-DEMO-0002'], [
            'date' => now()->subDays(3)->toDateString(),
            'description' => 'قيد آلي: فاتورة مكتب العقار الحديث رقم INV-2210',
            'source' => 'ai_invoice', 'status' => 'posted', 'by_agent' => true,
        ]);
        if ($e2->lines()->count() === 0) {
            $e2->lines()->createMany([
                ['account_id' => $acc['5200'], 'debit' => 4000, 'credit' => 0, 'notes' => 'إيجار المكتب'],
                ['account_id' => $acc['2200'], 'debit' => 600, 'credit' => 0, 'notes' => 'ضريبة 15%'],
                ['account_id' => $acc['2000'], 'debit' => 0, 'credit' => 4600, 'notes' => 'مكتب العقار الحديث'],
            ]);
        }
        AiInvoice::firstOrCreate(['journal_entry_id' => $e2->id], [
            'file_path' => 'ai-invoices/demo-rent.jpg', 'status' => 'posted', 'confidence' => 96.5,
            'extracted_data' => [
                'supplier_name' => 'مكتب العقار الحديث', 'invoice_number' => 'INV-2210',
                'invoice_date' => now()->subDays(3)->toDateString(),
                'subtotal' => 4000, 'tax_amount' => 600, 'total_amount' => 4600,
                'suggested_expense_account' => '5200',
            ],
            'ai_notes' => 'فاتورة إيجار شهرية واضحة — ثقة عالية',
        ]);

        $e3 = JournalEntry::firstOrCreate(['entry_number' => 'JE-DEMO-0003'], [
            'date' => now()->toDateString(),
            'description' => 'قيد آلي: فاتورة شركة الكهرباء رقم ELC-889',
            'source' => 'ai_invoice', 'status' => 'draft', 'by_agent' => true,
        ]);
        if ($e3->lines()->count() === 0) {
            $e3->lines()->createMany([
                ['account_id' => $acc['5300'], 'debit' => 850, 'credit' => 0, 'notes' => 'كهرباء وماء'],
                ['account_id' => $acc['2200'], 'debit' => 127.5, 'credit' => 0, 'notes' => 'ضريبة 15%'],
                ['account_id' => $acc['2000'], 'debit' => 0, 'credit' => 977.5, 'notes' => 'شركة الكهرباء'],
            ]);
        }
        AiInvoice::firstOrCreate(['journal_entry_id' => $e3->id], [
            'file_path' => 'ai-invoices/demo-electric.jpg', 'status' => 'pending', 'confidence' => 91.2,
            'extracted_data' => [
                'supplier_name' => 'شركة الكهرباء', 'invoice_number' => 'ELC-889',
                'invoice_date' => now()->toDateString(),
                'subtotal' => 850, 'tax_amount' => 127.5, 'total_amount' => 977.5,
                'suggested_expense_account' => '5300',
            ],
            'ai_notes' => 'فاتورة خدمات — تحتاج اعتماد المحاسب',
        ]);

        // قيد رواتب الشهر الحالي
        $month = now()->format('Y-m');
        if (! JournalEntry::where('entry_number', 'PAY-' . $month)->exists()) {
            app(\App\Services\AccountingService::class)->runPayroll($month);
        }

        // ========== 4) المخازن والتصنيفات والوحدات ==========
        $whMain    = Warehouse::firstOrCreate(['code' => 'WH-001'], ['name' => 'المخزن الرئيسي', 'is_active' => true]);
        $whBranch  = Warehouse::firstOrCreate(['code' => 'WH-002'], ['name' => 'مخزن الفرع', 'is_active' => true]);
        $whTransit = Warehouse::firstOrCreate(['code' => 'WH-003'], ['name' => 'مخزن العبور', 'is_active' => true]);

        $cats = [];
        foreach ([['CAT-01','مواد غذائية'],['CAT-02','منظفات'],['CAT-03','قرطاسية'],['CAT-04','قطع غيار']] as [$c, $n]) {
            $cats[$c] = Category::firstOrCreate(['code' => $c], ['name' => $n]);
        }

        $units = [];
        foreach ([['PCS','قطعة'],['BOX','كرتون'],['KG','كيلوجرام'],['LTR','لتر']] as [$c, $n]) {
            $units[$c] = Unit::firstOrCreate(['code' => $c], ['name' => $n]);
        }

        // ========== 5) الأصناف ==========
        $items = [];
        foreach ([
            ['ITM-001','أرز بسمتي 5 كجم','CAT-01','BOX',20,200],
            ['ITM-002','سكر ناعم 10 كجم','CAT-01','BOX',15,150],
            ['ITM-003','زيت طبخ 1.8 لتر','CAT-01','PCS',30,300],
            ['ITM-004','منظف أرضيات 3 لتر','CAT-02','PCS',10,100],
            ['ITM-005','صابون غسيل 800 جم','CAT-02','BOX',25,250],
            ['ITM-006','ورق طباعة A4','CAT-03','BOX',40,400],
            ['ITM-007','أقلام حبر أزرق','CAT-03','PCS',100,1000],
            ['ITM-008','فلتر زيت سيارة','CAT-04','PCS',8,80],
            ['ITM-009','بطارية 70 أمبير','CAT-04','PCS',5,50],
            ['ITM-010','معجون أسنان 125 مل','CAT-02','PCS',50,500],
        ] as [$code, $name, $cat, $unit, $min, $max]) {
            $items[$code] = Item::firstOrCreate(['code' => $code], [
                'name' => $name, 'category_id' => $cats[$cat]->id, 'unit_id' => $units[$unit]->id,
                'min_stock' => $min, 'max_stock' => $max, 'is_active' => true,
            ]);
        }

        // ========== 6) الأرصدة (3 أصناف تحت الحد الأدنى ⚠️) ==========
        foreach ([
            ['ITM-001', $whMain, 120], ['ITM-001', $whBranch, 35],
            ['ITM-002', $whMain, 80],
            ['ITM-003', $whMain, 25],
            ['ITM-004', $whMain, 60],
            ['ITM-005', $whBranch, 12],
            ['ITM-006', $whMain, 210],
            ['ITM-007', $whMain, 640],
            ['ITM-008', $whTransit, 6],
            ['ITM-009', $whTransit, 22],
            ['ITM-010', $whBranch, 300],
        ] as [$code, $wh, $qty]) {
            StockBalance::updateOrCreate(
                ['item_id' => $items[$code]->id, 'warehouse_id' => $wh->id],
                ['quantity' => $qty]
            );
        }

        // ========== 7) حركات 7 أيام للرسم البياني ==========
        if (StockTransaction::count() === 0) {
            $codes = array_keys($items);
            foreach (range(6, 0) as $i) {
                $day = now()->subDays($i);

                $in = StockTransaction::create([
                    'transaction_type' => 'receipt', 'warehouse_id' => $whMain->id,
                    'item_id' => $items[$codes[array_rand($codes)]]->id,
                    'quantity' => random_int(20, 80), 'movement' => 'in',
                    'user_id' => $admin->id, 'notes' => 'حركة تجريبية واردة',
                ]);
                $in->created_at = $day; $in->save();

                $out = StockTransaction::create([
                    'transaction_type' => 'issue', 'warehouse_id' => $whMain->id,
                    'item_id' => $items[$codes[array_rand($codes)]]->id,
                    'quantity' => random_int(5, 40), 'movement' => 'out',
                    'user_id' => $admin->id, 'notes' => 'حركة تجريبية صادرة',
                ]);
                $out->created_at = $day; $out->save();
            }
        }

        // ========== 8) سندات وتحويل ==========
        $r = StockReceipt::firstOrCreate(['serial' => 'IN-DEMO-0001'], [
            'receipt_date' => now()->subDays(4)->toDateString(), 'warehouse_id' => $whMain->id,
            'supplier_name' => 'الشركة الوطنية للتوريدات', 'status' => 'confirmed',
            'created_by' => $admin->id, 'notes' => 'توريد أسبوعي',
        ]);
        if ($r->items()->count() === 0) {
            $r->items()->createMany([
                ['item_id' => $items['ITM-001']->id, 'quantity' => 60, 'unit_price' => 42],
                ['item_id' => $items['ITM-003']->id, 'quantity' => 40, 'unit_price' => 20],
            ]);
        }

        $iss = StockIssue::firstOrCreate(['serial' => 'OUT-DEMO-0001'], [
            'issue_date' => now()->subDays(2)->toDateString(), 'warehouse_id' => $whMain->id,
            'status' => 'confirmed', 'created_by' => $admin->id, 'notes' => 'صرف لقسم المبيعات',
        ]);
        if ($iss->items()->count() === 0) {
            $iss->items()->createMany([
                ['item_id' => $items['ITM-006']->id, 'quantity' => 15],
                ['item_id' => $items['ITM-007']->id, 'quantity' => 100],
            ]);
        }

        $tr = StockTransfer::firstOrCreate(['serial' => 'TR-DEMO-0001'], [
            'transfer_date' => now()->subDays(1)->toDateString(),
            'from_warehouse_id' => $whMain->id, 'to_warehouse_id' => $whBranch->id,
            'status' => 'sent', 'created_by' => $admin->id,
        ]);
        if ($tr->items()->count() === 0) {
            $tr->items()->create(['item_id' => $items['ITM-010']->id, 'quantity' => 50]);
        }

        $this->command->info('✅ اكتملت البيانات التجريبية: قيود + فواتير + رواتب + مخازن + أصناف + سندات');
    }
}