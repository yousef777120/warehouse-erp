@extends('layouts.app')
@section('title', 'إضافة دور')
@section('page-title', 'إضافة دور جديد')
@section('page-subtitle', 'إنشاء دور مع صلاحياته')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-shield-plus me-2"></i>بيانات الدور</div>
    <form action="{{ route('admin.roles.store') }}" method="POST" class="p-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">اسم الدور <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            <small class="text-muted">مثال: accountant, supervisor</small>
        </div>

        <hr>
        <h6 class="fw-bold mb-3"><i class="bi bi-key-fill me-1"></i>الصلاحيات</h6>
        <div class="row g-2">
            @foreach($permissions as $perm)
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}" {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                </div>
            </div>
            @endforeach
        </div>

        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection