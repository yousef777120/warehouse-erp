<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\StockReceipt;
use App\Models\StockIssue;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_reports'),
        ];
    }

    /**
     * صفحة التقارير الرئيسية
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
 * بطاقة الصنف — تتبع حركة صنف معين مع الرصيد الجاري
 */
public function itemCard(Request $request)
{
    $items = Item::orderBy('name')->get();

    $warehouses = Warehouse::where('is_active', true)->get();

    $item = null;
    $transactions = collect();

    if ($request->filled('item_id')) {
        $item = Item::with('unit')->find($request->item_id);

        $query = StockTransaction::with(['warehouse'])
            ->where('item_id', $request->item_id);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $running = 0;

        foreach ($transactions as $t) {
            $running += ($t->movement === 'in')
                ? (float) $t->quantity
                : -(float) $t->quantity;

            $t->running_balance = $running;
        }
    }

    return view('admin.reports.item-card', compact(
        'items',
        'warehouses',
        'item',
        'transactions'
    ));
}
public function stockOnHand(Request $request)
{
    $warehouseId = $request->input('warehouse_id');

    $query = Item::query()
        ->with(['category', 'unit'])
        ->addSelect([
            'current_quantity' => StockBalance::selectRaw('COALESCE(SUM(quantity), 0)')
                ->whereColumn('stock_balances.item_id', 'items.id')
                ->when(
                    $warehouseId,
                    fn ($q) => $q->where('warehouse_id', $warehouseId)
                ),
        ]);

    if ($warehouseId) {
        $query->whereHas('stockBalances', function ($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        });
    }

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $items = $query
        ->orderBy('name')
        ->paginate(20)
        ->withQueryString();

    $warehouses = Warehouse::orderBy('name')->get();
    $categories = Category::orderBy('name')->get();

    return view(
        'admin.reports.stock-on-hand',
        compact('items', 'warehouses', 'categories')
    );
}
    /**
     * تقرير بطاقة الصنف (حركات صنف معين)
     */
    public function stockCard(Request $request)
    {
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $transactions = collect();

        if ($request->filled('item_id')) {
            $query = StockTransaction::with(['item.unit', 'warehouse'])
                ->where('item_id', $request->item_id);

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        }

        return view('admin.reports.stock-card', compact(
            'items', 'warehouses', 'transactions'
        ));
    }

    /**
     * تقرير الأصناف تحت الحد الأدنى
     */
    public function lowStock(Request $request)
    {
        $query = StockBalance::with(['item.unit', 'warehouse'])
            ->whereHas('item', fn($q) => $q->where('min_stock', '>', 0))
            ->whereColumn('quantity', '<=', \DB::raw('(SELECT min_stock FROM items WHERE items.id = stock_balances.item_id)'));

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $lowStockItems = $query->orderBy('quantity')->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.reports.low-stock', compact('lowStockItems', 'warehouses'));
    }

/**
 * تقرير ملخص حركة المخزون
 */
public function movementSummary(Request $request)
{
    // تحديد الفترة الزمنية (افتراضياً: الشهر الحالي)
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->toDateString());

    // بناء الاستعلام الأساسي
    $baseQuery = StockTransaction::query()
        ->whereBetween('created_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ]);

    // تطبيق فلتر المخزن
    if ($request->filled('warehouse_id')) {
        $baseQuery->where('warehouse_id', $request->warehouse_id);
    }

    // تطبيق فلتر نوع الحركة
    if ($request->filled('movement')) {
        $baseQuery->where('movement', $request->movement);
    }

    // حساب الإحصائيات
    $totalIn = (clone $baseQuery)
        ->where('movement', 'in')
        ->sum('quantity');

    $totalOut = (clone $baseQuery)
        ->where('movement', 'out')
        ->sum('quantity');

    $totalTransactions = (clone $baseQuery)->count();

    // تجميع الحركات حسب النوع والمخزن
    $summaryByWarehouse = (clone $baseQuery)
        ->selectRaw('
            warehouse_id,
            SUM(CASE WHEN movement = "in" THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN movement = "out" THEN quantity ELSE 0 END) as total_out
        ')
        ->groupBy('warehouse_id')
        ->with('warehouse')
        ->get();

    // تجميع الحركات حسب النوع
    $summaryByType = (clone $baseQuery)
        ->selectRaw('
            transaction_type,
            movement,
            COUNT(*) as operations_count,
            SUM(quantity) as total_quantity
        ')
        ->groupBy('transaction_type', 'movement')
        ->get();

    // آخر 20 حركة
    $recentTransactions = (clone $baseQuery)
        ->with(['item.unit', 'warehouse', 'user'])
        ->orderByDesc('created_at')
        ->limit(20)
        ->get();

    // جلب المخازن للفلاتر
    $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

    return view('admin.reports.movement-summary', compact(
        'totalIn',
        'totalOut',
        'totalTransactions',
        'summaryByWarehouse',
        'summaryByType',
        'recentTransactions',
        'warehouses',
        'fromDate',
        'toDate'
    ));
}



/**
 * تصدير تقرير الأرصدة إلى CSV
 */
public function exportStockOnHand(Request $request)
{
    // ...
}



    /**
     * تصدير تقرير الأصناف تحت الحد الأدنى إلى CSV
     */
    public function exportLowStock(Request $request)
    {
        $query = StockBalance::with(['item.unit', 'warehouse'])
            ->whereHas('item', fn($q) => $q->where('min_stock', '>', 0))
            ->whereColumn('quantity', '<=', \DB::raw('(SELECT min_stock FROM items WHERE items.id = stock_balances.item_id)'));

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $lowStockItems = $query->orderBy('quantity')->get();

        $filename = 'low_stock_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($lowStockItems) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['الكود', 'الصنف', 'المخزن', 'الكمية الحالية', 'الحد الأدنى', 'العجز']);

            foreach ($lowStockItems as $balance) {
                $deficit = $balance->item->min_stock - $balance->quantity;
                fputcsv($file, [
                    $balance->item->code,
                    $balance->item->name,
                    $balance->warehouse->name,
                    number_format($balance->quantity, 3),
                    number_format($balance->item->min_stock, 3),
                    number_format($deficit, 3),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}