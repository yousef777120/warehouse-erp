<?php
namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // وحدات القياس
        $units = [
            ['code' => 'PCS', 'name' => 'قطعة', 'name_en' => 'Piece'],
            ['code' => 'KG',  'name' => 'كيلوجرام', 'name_en' => 'Kilogram'],
            ['code' => 'M',   'name' => 'متر', 'name_en' => 'Meter'],
            ['code' => 'L',   'name' => 'لتر', 'name_en' => 'Liter'],
            ['code' => 'BOX', 'name' => 'علبة', 'name_en' => 'Box'],
        ];
        foreach ($units as $u) {
            Unit::firstOrCreate(['code' => $u['code']], $u);
        }

        // المخازن
        $warehouses = [
            ['code' => 'WH-MAIN', 'name' => 'المخزن الرئيسي', 'location' => 'الرياض', 'manager_name' => 'أحمد محمد'],
            ['code' => 'WH-BR01', 'name' => 'مخزن الفرع 1', 'location' => 'جدة', 'manager_name' => 'خالد عبدالله'],
            ['code' => 'WH-BR02', 'name' => 'مخزن الفرع 2', 'location' => 'الدمام', 'manager_name' => 'سعد إبراهيم'],
        ];
        foreach ($warehouses as $w) {
            Warehouse::firstOrCreate(['code' => $w['code']], $w);
        }

        // التصنيفات
        $cat1 = Category::firstOrCreate(['code' => 'CAT-ELEC'], ['name' => 'إلكترونيات']);
        $cat2 = Category::firstOrCreate(['code' => 'CAT-OFC'], ['name' => 'مستلزمات مكتبية']);
        Category::firstOrCreate(['code' => 'CAT-PHONES'], ['name' => 'هواتف', 'parent_id' => $cat1->id]);
        Category::firstOrCreate(['code' => 'CAT-LAPTOPS'], ['name' => 'حواسيب محمولة', 'parent_id' => $cat1->id]);
        Category::firstOrCreate(['code' => 'CAT-PAPER'], ['name' => 'أوراق', 'parent_id' => $cat2->id]);

        // الأصناف
        $pcs = Unit::where('code', 'PCS')->first();
        $kg  = Unit::where('code', 'KG')->first();

        $items = [
            ['code' => 'ITM-001', 'name' => 'ورق A4', 'category_id' => Category::where('code', 'CAT-PAPER')->first()->id, 'unit_id' => $pcs->id, 'min_stock' => 10, 'max_stock' => 500],
            ['code' => 'ITM-002', 'name' => 'قلم جاف أزرق', 'category_id' => $cat2->id, 'unit_id' => $pcs->id, 'min_stock' => 50, 'max_stock' => 1000],
            ['code' => 'ITM-003', 'name' => 'هاتف Samsung A55', 'category_id' => Category::where('code', 'CAT-PHONES')->first()->id, 'unit_id' => $pcs->id, 'min_stock' => 2, 'max_stock' => 50],
            ['code' => 'ITM-004', 'name' => 'لابتوب Dell Latitude', 'category_id' => Category::where('code', 'CAT-LAPTOPS')->first()->id, 'unit_id' => $pcs->id, 'min_stock' => 1, 'max_stock' => 20],
            ['code' => 'ITM-005', 'name' => 'أرز', 'category_id' => null, 'unit_id' => $kg->id, 'min_stock' => 20, 'max_stock' => 200],
        ];
        foreach ($items as $i) {
            Item::firstOrCreate(['code' => $i['code']], $i);
        }
    }
}