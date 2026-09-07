<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit; // ← مرة واحدة فقط
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_items', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('permission:view_items', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
{
    $query = Item::with(['category', 'unit']);

    // البحث بالاسم / الكود / الباركود / SKU
    if ($request->filled('search')) {
        $s = $request->search;

        $query->where(function ($q) use ($s) {
            $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%")
                ->orWhere('barcode', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%");
        });
    }

    // فلترة حسب التصنيف
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // فلترة حسب الوحدة
    if ($request->filled('unit_id')) {
        $query->where('unit_id', $request->unit_id);
    }

    $items = $query
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $categories = Category::orderBy('name')->get();

    $units = Unit::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('admin.items.index', compact(
        'items',
        'categories',
        'units'
    ));
}

    public function create()
    {
        return view('admin.items.create', [
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:items,code'],
            'barcode'     => ['nullable', 'string', 'max:100', 'unique:items,barcode'],
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit_id'     => ['required', 'exists:units,id'],
            'min_stock'   => ['nullable', 'numeric', 'min:0'],
            'max_stock'   => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Item::create($data);

        return redirect()->route('admin.items.index')->with('success', 'تمت إضافة الصنف بنجاح');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit']);
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('admin.items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:items,code,' . $item->id],
            'barcode'     => ['nullable', 'string', 'max:100', 'unique:items,barcode,' . $item->id],
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit_id'     => ['required', 'exists:units,id'],
            'min_stock'   => ['nullable', 'numeric', 'min:0'],
            'max_stock'   => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $item->update($data);

        return redirect()->route('admin.items.index')->with('success', 'تم تحديث الصنف بنجاح');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('admin.items.index')->with('success', 'تم حذف الصنف');
    }
}