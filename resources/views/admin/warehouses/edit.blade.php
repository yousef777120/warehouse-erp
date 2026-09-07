@extends('layouts.app')
@section('title', 'تعديل مخزن')
@section('page-title', 'تعديل المخزن')
@section('page-subtitle', $warehouse->name)

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-pencil-square me-2"></i>بيانات المخزن</div>
    <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST" class="p-4">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كود المخزن</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $warehouse->code) }}" required maxlength="50">
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم المخزن</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $warehouse->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">الموقع</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $warehouse->location) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم المسؤول</label>
                <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $warehouse->manager_name) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $warehouse->phone) }}" maxlength="20">
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">مخزن نشط</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes', $warehouse->notes) }}</textarea>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> تحديث</button>
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection