<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockIssue;
use App\Models\StockIssueItem;
use App\Models\StockBalance;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\SerialNumberService;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockIssueController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SerialNumberService $serialService,
        protected StockMovementService $movementService
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_issues', only: ['create', 'store', 'edit', 'update', 'destroy', 'confirm', 'cancel', 'getBalance']),
            new Middleware('permission:view_issues', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = StockIssue::with(['warehouse', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('serial', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $issues = $query->latest()->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.issues.index', compact('issues', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();
        $nextSerial = $this->serialService->generateIssueSerial();

        return view('admin.issues.create', compact('warehouses', 'items', 'nextSerial'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        $issue = DB::transaction(function () use ($data) {
            $issue = StockIssue::create([
                'serial' => $this->serialService->generateIssueSerial(
                    \Carbon\Carbon::parse($data['issue_date'])
                ),
                'issue_date' => $data['issue_date'],
                'warehouse_id' => $data['warehouse_id'],
                'recipient_name' => $data['recipient_name'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $row) {
                StockIssueItem::create([
                    'issue_id' => $issue->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                ]);
            }

            return $issue;
        });

        return redirect()->route('admin.issues.show', $issue)
            ->with('success', 'تم إنشاء سند الصرف بنجاح');
    }

    public function show(StockIssue $issue)
    {
        $issue->load(['warehouse', 'creator', 'confirmer', 'items.item.unit']);
        return view('admin.issues.show', compact('issue'));
    }

    public function edit(StockIssue $issue)
    {
        if (!$issue->is_draft) {
            return back()->with('error', 'لا يمكن تعديل سند مؤكد أو ملغي');
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with('unit')->get();

        return view('admin.issues.edit', compact('issue', 'warehouses', 'items'));
    }

    public function update(Request $request, StockIssue $issue)
    {
        if (!$issue->is_draft) {
            return back()->with('error', 'لا يمكن تعديل سند مؤكد أو ملغي');
        }

        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        DB::transaction(function () use ($issue, $data) {
            $issue->update([
                'issue_date' => $data['issue_date'],
                'warehouse_id' => $data['warehouse_id'],
                'recipient_name' => $data['recipient_name'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $issue->items()->delete();

            foreach ($data['items'] as $row) {
                StockIssueItem::create([
                    'issue_id' => $issue->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.issues.show', $issue)
            ->with('success', 'تم تحديث السند بنجاح');
    }

    public function destroy(StockIssue $issue)
    {
        if (!$issue->is_draft) {
            return back()->with('error', 'لا يمكن حذف سند مؤكد أو ملغي');
        }

        $issue->items()->delete();
        $issue->delete();

        return redirect()->route('admin.issues.index')
            ->with('success', 'تم حذف السند');
    }

    public function confirm(StockIssue $issue)
    {
        try {
            $this->movementService->confirmIssue($issue);
            return back()->with('success', 'تم تأكيد السند وخصم الأرصدة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(StockIssue $issue)
    {
        try {
            $this->movementService->cancelIssue($issue);
            return back()->with('success', 'تم إلغاء السند وإرجاع الأرصدة');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getBalance(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ]);

        $balance = StockBalance::where('item_id', $request->item_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        return response()->json([
            'quantity' => $balance ? (float) $balance->quantity : 0,
        ]);
    }
    public function print(StockIssue $issue)
{
    $issue->load(['warehouse', 'creator', 'items.item.unit']);
    return view('admin.print.issue', compact('issue'));
}
}