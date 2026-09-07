<?php
namespace App\Services;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\StockReceipt;
use App\Models\StockIssue;
use App\Models\StockTransfer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * تقرير الأرصدة الحالية
     */
    public function stockOnHand(array $filters = []): Collection
    {
        $query = StockBalance::with(['item.unit', 'item.category', 'warehouse'])
            ->join('items', 'items.id', '=', 'stock_balances.item_id')
            ->where('items.is_active', true)
            ->where('stock_balances.quantity', '>', 0);

        if (!empty($filters['warehouse_id'])) {
            $query->where('stock_balances.warehouse_id', $filters['warehouse_id']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('items.category_id', $filters['category_id']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('items.name', 'like', "%{$s}%")
                  ->orWhere('items.code', 'like', "%{$s}%")
                  ->orWhere('items.barcode', 'like', "%{$s}%");
            });
        }

        return $query
            ->orderBy('items.name')
            ->select('stock_balances.*')
            ->get();
    }

    /**
     * كرت الصنف — كل الحركات لصنف محدد
     */
    public function stockCard(int $itemId, ?int $warehouseId = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = StockTransaction::with(['item.unit', 'warehouse'])
            ->where('item_id', $itemId)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $transactions = $query->get();

        // حساب الرصيد التراكمي
        $runningBalance = 0;
        if ($warehouseId) {
            $runningBalance = (float) StockBalance::where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->value('quantity') ?? 0;
            // نطرح الحركات داخل الفترة لنحصل على رصيد البداية
            $periodQty = $transactions->sum(fn($t) => $t->movement === 'in' ? (float)$t->quantity : -(float)$t->quantity);
            $runningBalance = $runningBalance - $periodQty;
        } else {
            $runningBalance = (float) StockBalance::where('item_id', $itemId)->sum('quantity');
            $periodQty = $transactions->sum(fn($t) => $t->movement === 'in' ? (float)$t->quantity : -(float)$t->quantity);
            $runningBalance = $runningBalance - $periodQty;
        }

        foreach ($transactions as $t) {
            if ($t->movement === 'in') {
                $runningBalance += (float) $t->quantity;
            } else {
                $runningBalance -= (float) $t->quantity;
            }
            $t->running_balance = $runningBalance;
        }

        return $transactions;
    }

    /**
     * تقرير الأصناف تحت الحد الأدنى
     */
    public function lowStock(?int $warehouseId = null): Collection
    {
        $query = StockBalance::with(['item.unit', 'item.category', 'warehouse'])
            ->join('items', 'items.id', '=', 'stock_balances.item_id')
            ->where('items.is_active', true)
            ->where('items.min_stock', '>', 0)
            ->whereColumn('stock_balances.quantity', '<=', 'items.min_stock');

        if ($warehouseId) {
            $query->where('stock_balances.warehouse_id', $warehouseId);
        }

        return $query
            ->orderByRaw('(items.min_stock - stock_balances.quantity) DESC')
            ->select('stock_balances.*', DB::raw('items.min_stock as item_min_stock'))
            ->get();
    }

    /**
     * تقرير الأصناف الراكدة (بدون حركات في فترة)
     */
    public function noMovement(?int $warehouseId = null, int $days = 90): Collection
    {
        $since = now()->subDays($days);

        $query = StockBalance::with(['item.unit', 'item.category', 'warehouse'])
            ->join('items', 'items.id', '=', 'stock_balances.item_id')
            ->where('items.is_active', true)
            ->where('stock_balances.quantity', '>', 0)
            ->whereNotExists(function ($q) use ($since) {
                $q->select(DB::raw(1))
                  ->from('stock_transactions')
                  ->whereColumn('stock_transactions.item_id', 'stock_balances.item_id')
                  ->whereColumn('stock_transactions.warehouse_id', 'stock_balances.warehouse_id')
                  ->where('stock_transactions.created_at', '>=', $since);
            });

        if ($warehouseId) {
            $query->where('stock_balances.warehouse_id', $warehouseId);
        }

        return $query
            ->orderBy('items.name')
            ->select('stock_balances.*')
            ->get();
    }

    /**
     * ملخص الحركات لفترة
     */
    public function movementSummary(?int $warehouseId = null, ?string $from = null, ?string $to = null): array
    {
        $query = StockTransaction::query();

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $totalIn = (clone $query)->where('movement', 'in')->sum('quantity');
        $totalOut = (clone $query)->where('movement', 'out')->sum('quantity');

        $byType = (clone $query)
            ->select('transaction_type', DB::raw('SUM(quantity) as total_qty'), DB::raw('COUNT(*) as count'))
            ->groupBy('transaction_type')
            ->get()
            ->keyBy('transaction_type');

        return [
            'total_in'  => (float) $totalIn,
            'total_out' => (float) $totalOut,
            'net'       => (float) $totalIn - (float) $totalOut,
            'by_type'   => $byType,
        ];
    }

    /**
     * بيانات الرسم البياني — حركات آخر 30 يوم
     */
    public function chartLast30Days(?int $warehouseId = null): array
    {
        $query = StockTransaction::select(
                DB::raw('DATE(created_at) as date'),
                'movement',
                DB::raw('SUM(quantity) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date', 'movement')
            ->orderBy('date');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $rows = $query->get();

        $labels = [];
        $inData = [];
        $outData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('m-d');
            $inData[] = 0;
            $outData[] = 0;
        }

        foreach ($rows as $row) {
            $idx = array_search(date('m-d', strtotime($row->date)), $labels);
            if ($idx !== false) {
                if ($row->movement === 'in') {
                    $inData[$idx] = (float) $row->total;
                } else {
                    $outData[$idx] = (float) $row->total;
                }
            }
        }

        return compact('labels', 'inData', 'outData');
    }

    /**
     * توزيع الأرصدة على المخازن (لرسم بياني دائري)
     */
    public function stockByWarehouse(): array
    {
        $data = StockBalance::select('warehouse_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('warehouse_id')
            ->with('warehouse:id,name')
            ->get();

        return [
            'labels' => $data->map(fn($r) => $r->warehouse->name ?? 'غير معروف')->toArray(),
            'data'   => $data->map(fn($r) => (float) $r->total)->toArray(),
        ];
    }

    /**
     * أفضل 10 أصناف حركة
     */
    public function topItems(?int $warehouseId = null, int $days = 30): array
    {
        $query = StockTransaction::select('item_id', DB::raw('SUM(quantity) as total'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('item:id,name,code');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->get()->values()->toArray();
    }
}