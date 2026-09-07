@extends('layouts.app')

@section('title', 'التقارير')
@section('page-title', 'التقارير')
@section('page-subtitle', 'تقارير المخزون والحركات')

@section('content')
<div class="row g-4">
    {{-- تقرير أرصدة المخزون --}}
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.stock-on-hand') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="icon-box" style="background: #dbeafe; color: #1e40af;">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">أرصدة المخزون</h6>
                        <p class="text-muted small mb-0">عرض الكميات الحالية لجميع الأصناف في المخازن</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- بطاقة الصنف --}}
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.item-card') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="icon-box" style="background: #ede9fe; color: #6d28d9;">
                        <i class="bi bi-card-list"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">بطاقة الصنف</h6>
                        <p class="text-muted small mb-0">حركة صنف معين مع الرصيد الجاري</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- الأصناف تحت الحد الأدنى --}}
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.low-stock') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="icon-box" style="background: #fee2e2; color: #991b1b;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">الأصناف تحت الحد الأدنى</h6>
                        <p class="text-muted small mb-0">الأصناف التي تحتاج إعادة طلب</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- ملخص حركة المخزون --}}
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.movement-summary') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="icon-box" style="background: #fef3c7; color: #92400e;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">ملخص حركة المخزون</h6>
                        <p class="text-muted small mb-0">إجمالي الوارد والصادر خلال فترة</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection