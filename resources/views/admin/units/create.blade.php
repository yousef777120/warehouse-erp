@extends('layouts.app')
@section('title', 'إضافة وحدة')
@section('page-title', 'إضافة وحدة قياس')
@section('page-subtitle', 'إنشاء وحدة جديدة')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-speedometer me-2"></i>بيانات الوحدة</div>
    <form action="{{ route('admin.units.store') }}" method="POST" class="p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">كود الوحدة <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required maxlength="20">
                <small class="text-muted">مثال: PCS, KG, M</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                <small class="text-muted">مثال: قطعة، كيلو، متر</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" maxlength="100">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">وحدة نشطة</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection