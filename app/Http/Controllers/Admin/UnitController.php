<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UnitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_units', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('permission:view_units', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = Unit::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $units = $query->latest()->paginate(15)->withQueryString();
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => ['required', 'string', 'max:20', 'unique:units,code'],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Unit::create($data);

        return redirect()->route('admin.units.index')->with('success', 'تم إضافة الوحدة');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'code'      => ['required', 'string', 'max:20', 'unique:units,code,' . $unit->id],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $unit->update($data);

        return redirect()->route('admin.units.index')->with('success', 'تم تحديث الوحدة');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->items()->exists()) {
            return back()->with('error', 'لا يمكن حذف الوحدة لوجود أصناف مرتبطة');
        }
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'تم حذف الوحدة');
    }
}