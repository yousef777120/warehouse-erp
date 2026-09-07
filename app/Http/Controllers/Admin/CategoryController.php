<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_categories', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('permission:view_categories', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = Category::with('parent')->withCount('children');

        // فلتر البحث
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        // فلتر التصنيف الأب
        if ($request->filled('parent_id')) {
            if ($request->parent_id === '0') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        $categories = $query->latest()->paginate(15)->withQueryString();
        
        // ✅ تمرير التصنيفات الرئيسية للفلتر
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'   => ['nullable', 'exists:categories,id'],
            'code'        => ['required', 'string', 'max:50', 'unique:categories,code'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'parent_id'   => ['nullable', 'exists:categories,id'],
            'code'        => ['required', 'string', 'max:50', 'unique:categories,code,' . $category->id],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لوجود تصنيفات فرعية');
        }

        if ($category->items()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لوجود أصناف مرتبطة به');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'تم حذف التصنيف بنجاح');
    }
}