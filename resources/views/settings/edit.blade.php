@extends('layouts.app')

@section('title', 'الإعدادات')

@section('content')

<div class="container-fluid py-4" dir="rtl">

    {{-- رسائل النجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="إغلاق"></button>
        </div>
    @endif

    {{-- أخطاء التحقق --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-gear me-2"></i>
                الإعدادات
            </h3>

            <p class="text-muted mb-0">
                إعدادات النظام والحساب
            </p>
        </div>

        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
            <i class="bi bi-person me-1"></i>
            الملف الشخصي
        </a>

    </div>


    <div class="row g-4">

        {{-- إعدادات النظام --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-sliders me-2"></i>
                        إعدادات النظام
                    </h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('settings.update') }}">

                        @csrf
                        @method('PUT')

                        {{-- اللغة --}}
                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                لغة النظام
                            </label>

                            <select name="language" class="form-select">

                                <option value="ar"
                                    {{ session('app_language', 'ar') === 'ar' ? 'selected' : '' }}>
                                    العربية
                                </option>

                                <option value="en"
                                    {{ session('app_language') === 'en' ? 'selected' : '' }}>
                                    English
                                </option>

                            </select>

                        </div>


                        {{-- المنطقة الزمنية --}}
                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                المنطقة الزمنية
                            </label>

                            <select name="timezone" class="form-select">

                                <option value="Asia/Aden"
                                    {{ session('app_timezone', 'Asia/Aden') === 'Asia/Aden' ? 'selected' : '' }}>
                                    اليمن - صنعاء / عدن
                                </option>

                                <option value="Asia/Riyadh"
                                    {{ session('app_timezone') === 'Asia/Riyadh' ? 'selected' : '' }}>
                                    السعودية - الرياض
                                </option>

                                <option value="Asia/Dubai"
                                    {{ session('app_timezone') === 'Asia/Dubai' ? 'selected' : '' }}>
                                    الإمارات - دبي
                                </option>

                                <option value="UTC"
                                    {{ session('app_timezone') === 'UTC' ? 'selected' : '' }}>
                                    UTC
                                </option>

                            </select>

                        </div>


                        <div class="d-flex justify-content-start">

                            <button type="submit" class="btn btn-primary px-4">

                                <i class="bi bi-save me-1"></i>

                                حفظ الإعدادات

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- معلومات النظام --}}
        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        معلومات النظام
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            التطبيق
                        </small>

                        <strong>
                            Warehouse ERP
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            البيئة
                        </small>

                        <strong>
                            {{ app()->environment() }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            إصدار Laravel
                        </small>

                        <strong>
                            {{ app()->version() }}
                        </strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">
                            المستخدم الحالي
                        </small>

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection