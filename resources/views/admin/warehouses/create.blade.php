@extends('layouts.app')
@section('title', 'إضافة مخزن')
@section('page-title', 'إضافة مخزن جديد')
@section('page-subtitle', 'إنشاء مخزن جديد في النظام')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-building-fill me-2"></i>بيانات المخزن</div>
    <form action="{{ route('admin.warehouses.store') }}" method="POST" class="p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كود المخزن <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required maxlength="50">
                <small class="text-muted">مثال: WH-001</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم المخزن <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">الموقع</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم المسؤول</label>
                <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" maxlength="20">
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">مخزن نشط</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection