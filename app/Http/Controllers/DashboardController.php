<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\StockBalance;
use App\Models\StockTransfer;
use App\Models\StockReceipt;
use App\Models\StockIssue;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('roles');

        // الإحصائيات الأساسية
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'warehouses' => Warehouse::where('is_active', true)->count(),
            'items' => Item::where('is_active', true)->count(),
            'categories' => Category::count(),
            'units' => Unit::count(),
            'transfers' => StockTransfer::count(),
            'receipts' => StockReceipt::count(),
            'issues' => StockIssue::count(),
        ];

        // إحصائيات المخزون
        $stockStats = [
            'total_quantity' => StockBalance::sum('quantity'),
            'low_stock_count' => StockBalance::whereHas('item', function($q) {
                $q->whereColumn('stock_balances.quantity', '<=', 'items.min_stock')
                  ->where('items.min_stock', '>', 0);
            })->count(),
            'out_of_stock' => StockBalance::where('quantity', 0)->count(),
            'total_value' => StockBalance::with('item')->get()->sum(function($b) {
                return $b->quantity * ($b->item->cost_price ?? 0);
            }),
        ];

        // حالات التحويلات
        $transferStats = [
            'draft' => StockTransfer::where('status', 'draft')->count(),
            'in_transit' => StockTransfer::where('status', 'in_transit')->count(),
            'received' => StockTransfer::where('status', 'received')->count(),
            'cancelled' => StockTransfer::where('status', 'cancelled')->count(),
        ];

        // آخر 5 تحويلات
        $recentTransfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])
            ->latest()->take(5)->get();

        // آخر 5 سندات إدخال
        $recentReceipts = StockReceipt::with(['warehouse', 'creator'])
            ->latest()->take(5)->get();

        // آخر 5 سندات صرف
        $recentIssues = StockIssue::with(['warehouse', 'creator'])
            ->latest()->take(5)->get();

        // بيانات الرسم البياني: المخزون حسب المستودع
        $warehousesChart = Warehouse::where('is_active', true)
            ->get()
            ->map(function($warehouse) {
                $totalQuantity = StockBalance::where('warehouse_id', $warehouse->id)
                    ->sum('quantity');
                return [
                    'name' => $warehouse->name,
                    'quantity' => (float) $totalQuantity,
                ];
            });

        // بيانات الرسم البياني: الأصناف الأعلى مخزوناً
        $topItems = StockBalance::with('item')
            ->orderByDesc('quantity')
            ->take(5)
            ->get()
            ->map(fn($b) => [
                'name' => $b->item->name ?? 'غير محدد',
                'quantity' => (float) $b->quantity,
            ]);

        // بيانات الرسم البياني: الحركات خلال آخر 7 أيام
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayName = Carbon::now()->subDays($i)->translatedFormat('D');
            
            $receipts = StockReceipt::whereDate('receipt_date', $date)->count();
            $issues = StockIssue::whereDate('issue_date', $date)->count();
            $transfers = StockTransfer::whereDate('transfer_date', $date)->count();
            
            $last7Days->push([
                'day' => $dayName,
                'date' => $date,
                'receipts' => $receipts,
                'issues' => $issues,
                'transfers' => $transfers,
            ]);
        }

        return view('dashboard', compact(
            'user', 'stats', 'stockStats', 'transferStats',
            'recentTransfers', 'recentReceipts', 'recentIssues',
            'warehousesChart', 'topItems', 'last7Days'
        ));
    }
}