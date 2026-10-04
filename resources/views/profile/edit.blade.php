@extends('layouts.app')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')
@section('page-subtitle', 'إدارة بياناتك الشخصية وكلمة المرور')

@section('content')
<div class="row g-4">
    {{-- بطاقة الحساب --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold fs-2"
                     style="width:90px;height:90px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge bg-primary-subtle text-primary">{{ $user->roles->first()?->name ?? 'مستخدم' }}</span>
                <hr>
                <div class="text-start small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span>تاريخ الانضمام:</span>
                        <strong class="text-dark">{{ $user->created_at->format('Y/m/d') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>حالة البريد:</span>
                        <strong class="text-{{ $user->email_verified_at ? 'success' : 'danger' }}">
                            {{ $user->email_verified_at ? 'موثق' : 'غير موثق' }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>عدد الصلاحيات:</span>
                        <strong class="text-dark">{{ $user->getAllPermissions()->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- البيانات الشخصية --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold"><i class="bi bi-person-lines-fill me-2"></i>البيانات الشخصية</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3"><i class="bi bi-check2-circle me-1"></i>حفظ التغييرات</button>
                </form>
            </div>
        </div>

        {{-- كلمة المرور --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="bi bi-shield-lock me-2"></i>تغيير كلمة المرور</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">كلمة المرور الحالية</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الجديدة</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تأكيد الجديدة</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary mt-3"><i class="bi bi-key me-1"></i>تحديث كلمة المرور</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection