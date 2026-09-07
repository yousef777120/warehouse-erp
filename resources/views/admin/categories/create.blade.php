@extends('layouts.app')
@section('title', 'إضافة تصنيف')
@section('page-title', 'إضافة تصنيف جديد')
@section('page-subtitle', 'إنشاء تصنيف جديد')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-tags-fill me-2"></i>بيانات التصنيف</div>
    <form action="{{ route('admin.categories.store') }}" method="POST" class="p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كود التصنيف <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required maxlength="50">
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">التصنيف الأب (اختياري)</label>
                <select name="parent_id" class="form-select">
                    <option value="">— تصنيف رئيسي —</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">اتركه فارغاً لإنشاء تصنيف رئيسي</small>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">تصنيف نشط</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="3" maxlength="1000">{{ old('description') }}</textarea>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection