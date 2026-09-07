<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WarehouseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_warehouses', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('permission:view_warehouses', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = Warehouse::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $warehouses = $query->latest()->paginate(15)->withQueryString();
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => ['required', 'string', 'max:50', 'unique:warehouses,code'],
            'name'         => ['required', 'string', 'max:255'],
            'location'     => ['nullable', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'is_active'    => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Warehouse::create($data);

        return redirect()->route('admin.warehouses.index')->with('success', 'تم إضافة المخزن بنجاح');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'code'         => ['required', 'string', 'max:50', 'unique:warehouses,code,' . $warehouse->id],
            'name'         => ['required', 'string', 'max:255'],
            'location'     => ['nullable', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'is_active'    => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $warehouse->update($data);

        return redirect()->route('admin.warehouses.index')->with('success', 'تم تحديث المخزن');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('admin.warehouses.index')->with('success', 'تم حذف المخزن');
    }
}