@extends('layouts.app')
@section('title', 'تعديل وحدة')
@section('page-title', 'تعديل وحدة القياس')
@section('page-subtitle', $unit->name)

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-pencil-square me-2"></i>بيانات الوحدة</div>
    <form action="{{ route('admin.units.update', $unit) }}" method="POST" class="p-4">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">كود الوحدة</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $unit->code) }}" required maxlength="20">
            </div>
            <div class="col-md-4">
                <label class="form-label">الاسم بالعربية</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $unit->name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $unit->name_en) }}" maxlength="100">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">وحدة نشطة</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> تحديث</button>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection