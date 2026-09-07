@extends('layouts.app')
@section('title', 'إضافة صنف')
@section('page-title', 'إضافة صنف جديد')
@section('page-subtitle', 'إنشاء صنف جديد في المخزون')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-box-seam-fill me-2"></i>بيانات الصنف</div>
    <form action="{{ route('admin.items.store') }}" method="POST" class="p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">كود الصنف <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required maxlength="50">
            </div>
            <div class="col-md-4">
                <label class="form-label">الباركود</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" maxlength="100">
            </div>
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" maxlength="100">
            </div>
            <div class="col-md-12">
                <label class="form-label">اسم الصنف <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">التصنيف</label>
                <select name="category_id" class="form-select">
                    <option value="">-- بدون تصنيف --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->parent ? '└ ' . $cat->parent->name . ' / ' : '' }}{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الوحدة <span class="text-danger">*</span></label>
                <select name="unit_id" class="form-select" required>
                    <option value="">-- اختر الوحدة --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الحد الأدنى للمخزون</label>
                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 0) }}" step="0.001" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">الحد الأقصى للمخزون</label>
                <input type="number" name="max_stock" class="form-control" value="{{ old('max_stock', 0) }}" step="0.001" min="0">
            </div>
            <div class="col-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="3" maxlength="2000">{{ old('description') }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">صنف نشط</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection