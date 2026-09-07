<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockReceipt;
use App\Models\StockReceiptItem;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\SerialNumberService;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockReceiptController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SerialNumberService $serialService,
        protected StockMovementService $movementService
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_receipts', only: ['create', 'store', 'edit', 'update', 'destroy', 'confirm', 'cancel']),
            new Middleware('permission:view_receipts', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = StockReceipt::with(['warehouse', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('serial', 'like', "%{$s}%")
                  ->orWhere('supplier_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $receipts = $query->latest()->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.receipts.index', compact('receipts', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();
        $nextSerial = $this->serialService->generateReceiptSerial();

        return view('admin.receipts.create', compact('warehouses', 'items', 'nextSerial'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receipt_date' => ['required', 'date'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $receipt = DB::transaction(function () use ($data) {
            $receipt = StockReceipt::create([
                'serial' => $this->serialService->generateReceiptSerial(
                    \Carbon\Carbon::parse($data['receipt_date'])
                ),
                'receipt_date' => $data['receipt_date'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_name' => $data['supplier_name'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $row) {
                StockReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'] ?? 0,
                ]);
            }

            return $receipt;
        });

        return redirect()->route('admin.receipts.show', $receipt)
            ->with('success', 'تم إنشاء سند الإدخال بنجاح');
    }

    public function show(StockReceipt $receipt)
    {
        $receipt->load(['warehouse', 'creator', 'confirmer', 'items.item.unit']);
        return view('admin.receipts.show', compact('receipt'));
    }

    public function edit(StockReceipt $receipt)
    {
        if (!$receipt->is_draft) {
            return back()->with('error', 'لا يمكن تعديل سند مؤكد أو ملغي');
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();

        return view('admin.receipts.edit', compact('receipt', 'warehouses', 'items'));
    }

    public function update(Request $request, StockReceipt $receipt)
    {
        if (!$receipt->is_draft) {
            return back()->with('error', 'لا يمكن تعديل سند مؤكد أو ملغي');
        }

        $data = $request->validate([
            'receipt_date' => ['required', 'date'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($receipt, $data) {
            $receipt->update([
                'receipt_date' => $data['receipt_date'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_name' => $data['supplier_name'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $receipt->items()->delete();

            foreach ($data['items'] as $row) {
                StockReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'] ?? 0,
                ]);
            }
        });

        return redirect()->route('admin.receipts.show', $receipt)
            ->with('success', 'تم تحديث السند بنجاح');
    }

    public function destroy(StockReceipt $receipt)
    {
        if (!$receipt->is_draft) {
            return back()->with('error', 'لا يمكن حذف سند مؤكد أو ملغي');
        }

        $receipt->items()->delete();
        $receipt->delete();

        return redirect()->route('admin.receipts.index')
            ->with('success', 'تم حذف السند');
    }

    public function confirm(StockReceipt $receipt)
    {
        try {
            $this->movementService->confirmReceipt($receipt);
            return back()->with('success', 'تم تأكيد السند وتحديث الأرصدة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

       /**
     * إلغاء السند المؤكد → عكس الأرصدة
     */
    public function cancel(StockReceipt $receipt)
    {
        if ($receipt->status !== 'confirmed') {
            return back()->with('error', 'لا يمكن إلغاء سند غير مؤكد');
        }

        DB::transaction(function () use ($receipt) {
            foreach ($receipt->items as $it) {
                $balance = StockBalance::where('item_id', $it->item_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->lockForUpdate()->first();

                if ($balance) {
                    $newQty = (float) $balance->quantity - (float) $it->quantity;
                    if ($newQty < 0) {
                        throw new \Exception("الرصيد غير كافٍ لعكس حركة الصنف #{$it->item_id}");
                    }
                    $balance->update(['quantity' => $newQty]);
                }

                StockTransaction::create([
                    'transaction_type' => 'receipt_cancel',
                    'reference_type' => StockReceipt::class,
                    'reference_id' => $receipt->id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'item_id' => $it->item_id,
                    'quantity' => $it->quantity,
                    'movement' => 'out',
                    'user_id' => Auth::id(),
                    'notes' => "إلغاء سند إدخال {$receipt->serial}",
                ]);
            }

            $receipt->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'تم إلغاء السند وعكس الأرصدة');
    }

    /**
     * توليد رقم تسلسلي: IN-2026-000001
     */
    private function generateSerial(): string
    {
        $year = now()->format('Y');
        $prefix = "IN-{$year}-";

        $last = StockReceipt::withTrashed()
            ->where('serial', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $next = $last ? ((int) substr($last->serial, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
    public function print(StockReceipt $receipt)
{
    $receipt->load(['warehouse', 'creator', 'items.item.unit']);
    return view('admin.print.receipt', compact('receipt'));
}
}
