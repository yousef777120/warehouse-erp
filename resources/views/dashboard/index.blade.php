@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'نظرة عامة على النظام')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small mb-1">مرحباً بك، {{ auth()->user()->name }}</div>
                    <h4 class="mb-0 fw-bold">أهلاً بك في نظام إدارة المخازن</h4>
                    <small class="text-muted">المرحلة الأولى والثانية</small>
                </div>
                <div class="icon-box" style="background: #dbeafe; color: #1e40af;">
                    <i class="bi bi-emoji-smile-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">المستخدمون</div>
                    <h3 class="fw-bold mt-2 mb-0">{{ $users_count ?? 0 }}</h3>
                    <small class="text-success"><i class="bi bi-people-fill"></i> مستخدم</small>
                </div>
                <div class="icon-box" style="background: #dcfce7; color: #166534;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">الأدوار</div>
                    <h3 class="fw-bold mt-2 mb-0">{{ $roles_count ?? 0 }}</h3>
                    <small class="text-primary"><i class="bi bi-shield-lock-fill"></i> دور</small>
                </div>
                <div class="icon-box" style="background: #ede9fe; color: #6d28d9;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">المخازن</div>
                    <h3 class="fw-bold mt-2 mb-0">{{ $warehouses_count ?? 0 }}</h3>
                    <small class="text-info"><i class="bi bi-building-fill"></i> مخزن</small>
                </div>
                <div class="icon-box" style="background: #cffafe; color: #0e7490;">
                    <i class="bi bi-building-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">الأصناف</div>
                    <h3 class="fw-bold mt-2 mb-0">{{ $items_count ?? 0 }}</h3>
                    <small class="text-warning"><i class="bi bi-box-seam-fill"></i> صنف</small>
                </div>
                <div class="icon-box" style="background: #fef3c7; color: #92400e;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection