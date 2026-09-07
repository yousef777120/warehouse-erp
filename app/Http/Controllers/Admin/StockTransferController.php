<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockBalance;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\SerialNumberService;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SerialNumberService $serialService,
        protected StockMovementService $movementService
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_transfers', only: ['create', 'store', 'edit', 'update', 'destroy', 'send', 'receive', 'cancel']),
            new Middleware('permission:view_transfers', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator']);

        if ($request->filled('search')) {
            $query->where('serial', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->latest()->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.transfers.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();
        $nextSerial = $this->serialService->generateTransferSerial();

        return view('admin.transfers.create', compact('warehouses', 'items', 'nextSerial'));
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'transfer_date'     => ['required', 'date'],
        'from_warehouse_id' => ['required', 'exists:warehouses,id'],
        'to_warehouse_id'   => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'], // ✅ القاعدة هنا
        'notes'             => ['nullable', 'string', 'max:2000'],
        'items'             => ['required', 'array', 'min:1'],
        'items.*.item_id'   => ['required', 'exists:items,id'],
        'items.*.quantity'  => ['required', 'numeric', 'min:0.001'],
    ]);

    $transfer = DB::transaction(function () use ($data) {
        $transfer = StockTransfer::create([
            'serial' => $this->serialService->generateTransferSerial(
                \Carbon\Carbon::parse($data['transfer_date'])
            ),
            'transfer_date'     => $data['transfer_date'],
            'from_warehouse_id' => $data['from_warehouse_id'],
            'to_warehouse_id'   => $data['to_warehouse_id'],
            'notes'             => $data['notes'] ?? null,
            'status'            => 'draft',
            'created_by'        => Auth::id(),
        ]);

        foreach ($data['items'] as $row) {
            StockTransferItem::create([
                'transfer_id' => $transfer->id,
                'item_id'     => $row['item_id'],
                'quantity'    => $row['quantity'],
            ]);
        }

        return $transfer;
    });

    return redirect()
        ->route('admin.transfers.show', $transfer)
        ->with('success', 'تم إنشاء التحويل بنجاح');
}

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['fromWarehouse', 'toWarehouse', 'creator', 'items.item.unit']);

        $fromBalances = StockBalance::where('warehouse_id', $transfer->from_warehouse_id)
            ->whereIn('item_id', $transfer->items->pluck('item_id'))
            ->pluck('quantity', 'item_id');

        $toBalances = StockBalance::where('warehouse_id', $transfer->to_warehouse_id)
            ->whereIn('item_id', $transfer->items->pluck('item_id'))
            ->pluck('quantity', 'item_id');

        return view('admin.transfers.show', compact('transfer', 'fromBalances', 'toBalances'));
    }

    public function edit(StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل تحويل ليس في حالة المسودة');
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();

        return view('admin.transfers.edit', compact('transfer', 'warehouses', 'items'));
    }

    public function update(Request $request, StockTransfer $transfer)
{
    if (!$transfer->is_draft) {
        return back()->with('error', 'لا يمكن تعديل تحويل ليس في حالة المسودة');
    }

    $data = $request->validate([
        'transfer_date'     => ['required', 'date'],
        'from_warehouse_id' => ['required', 'exists:warehouses,id'],
        'to_warehouse_id'   => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'], // ✅ القاعدة هنا
        'notes'             => ['nullable', 'string', 'max:2000'],
        'items'             => ['required', 'array', 'min:1'],
        'items.*.item_id'   => ['required', 'exists:items,id'],
        'items.*.quantity'  => ['required', 'numeric', 'min:0.001'],
    ]);

    DB::transaction(function () use ($transfer, $data) {
        $transfer->update([
            'transfer_date'     => $data['transfer_date'],
            'from_warehouse_id' => $data['from_warehouse_id'],
            'to_warehouse_id'   => $data['to_warehouse_id'],
            'notes'             => $data['notes'] ?? null,
        ]);

        $transfer->items()->delete();

        foreach ($data['items'] as $row) {
            StockTransferItem::create([
                'transfer_id' => $transfer->id,
                'item_id'     => $row['item_id'],
                'quantity'    => $row['quantity'],
            ]);
        }
    });

    return redirect()
        ->route('admin.transfers.show', $transfer)
        ->with('success', 'تم تحديث التحويل بنجاح');
}

    public function destroy(StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف تحويل ليس في حالة المسودة');
        }

        $transfer->items()->delete();
        $transfer->delete();

        return redirect()->route('admin.transfers.index')
            ->with('success', 'تم حذف التحويل بنجاح');
    }

    public function send(StockTransfer $transfer)
    {
        try {
            $this->movementService->sendTransfer($transfer);
            return back()->with('success', 'تم إرسال التحويل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive(StockTransfer $transfer)
    {
        try {
            $this->movementService->receiveTransfer($transfer);
            return back()->with('success', 'تم استلام التحويل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(StockTransfer $transfer)
    {
        try {
            $this->movementService->cancelTransfer($transfer);
            return back()->with('success', 'تم إلغاء التحويل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function print(StockTransfer $transfer)
{
    $transfer->load(['fromWarehouse', 'toWarehouse', 'creator', 'items.item.unit']);
    return view('admin.print.transfer', compact('transfer'));
}
}