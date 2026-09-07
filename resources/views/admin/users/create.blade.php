@extends('layouts.app')
@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم جديد')
@section('page-subtitle', 'إنشاء حساب جديد في النظام')

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-person-plus-fill me-2"></i>بيانات المستخدم</div>
    <form action="{{ route('admin.users.store') }}" method="POST" class="p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">الدور <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">-- اختر الدور --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">حساب نشط</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection