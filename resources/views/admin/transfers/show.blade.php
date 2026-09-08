@extends('layouts.app')

@section('title', 'تحويل رقم ' . $transfer->serial)
@section('page-title', 'تفاصيل التحويل')
@section('page-subtitle', 'رقم السند: ' . $transfer->serial)

@section('content')

{{-- رأس الطباعة (يظهر فقط عند الطباعة) --}}
<div class="print-header print-only">
    <div class="logo">
        <i class="bi bi-box-seam-fill"></i> نظام المخازن
    </div>
    <div class="company-info">
        <p>نظام إدارة المخازن المتكامل ERP</p>
        <p>التاريخ: {{ date('Y/m/d') }} | الوقت: {{ date('H:i') }}</p>
    </div>
    <div class="document-title">
        سند تحويل مخزني
    </div>
    <div style="margin: 10px 0; font-size: 11pt;">
        <strong>رقم السند:</strong> {{ $transfer->serial }}
    </div>
</div>

{{-- أزرار الإجراءات (تختفي عند الطباعة) --}}
<div class="no-print mb-4">
    <div class="d-flex gap-2 flex-wrap">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer-fill"></i> طباعة
        </button>
        
        @if($transfer->status === 'draft')
            <form action="{{ route('admin.transfers.send', $transfer) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('هل تريد إرسال هذا التحويل؟')">
                    <i class="bi bi-send"></i> إرسال التحويل
                </button>
            </form>
        @endif

        @if($transfer->status === 'in_transit')
            <form action="{{ route('admin.transfers.receive', $transfer) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('هل تريد استلام هذا التحويل؟')">
                    <i class="bi bi-check-circle"></i> استلام التحويل
                </button>
            </form>
        @endif

        @if(in_array($transfer->status, ['draft', 'in_transit']))
            <form action="{{ route('admin.transfers.cancel', $transfer) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('هل تريد إلغاء هذا التحويل؟')">
                    <i class="bi bi-x-circle"></i> إلغاء
                </button>
            </form>
        @endif

        <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right"></i> رجوع للقائمة
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- تفاصيل التحويل --}}
    <div class="col-lg-8">
        <div class="table-card no-break">
            <div class="card-header">
                <i class="bi bi-arrow-left-right me-2"></i>تفاصيل التحويل
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>الرقم التسلسلي:</strong> 
                        <span class="badge bg-primary">{{ $transfer->serial }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>التاريخ:</strong> 
                        {{ $transfer->transfer_date->format('Y-m-d') }}
                    </div>
                    <div class="col-md-6">
                        <strong>من مخزن:</strong> 
                        {{ $transfer->fromWarehouse->name ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>إلى مخزن:</strong> 
                        {{ $transfer->toWarehouse->name ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>الحالة:</strong>
                        @if($transfer->status === 'draft')
                            <span class="badge bg-warning">مسودة</span>
                        @elseif($transfer->status === 'in_transit')
                            <span class="badge bg-info">قيد النقل</span>
                        @elseif($transfer->status === 'received')
                            <span class="badge bg-success">تم الاستلام</span>
                        @else
                            <span class="badge bg-danger">ملغي</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>أنشأه:</strong> 
                        {{ $transfer->creator->name ?? '-' }}
                    </div>
                    
                    @if($transfer->sent_at)
                    <div class="col-md-6">
                        <strong>تاريخ الإرسال:</strong> 
                        {{ $transfer->sent_at->format('Y-m-d H:i') }}
                    </div>
                    @endif

                    @if($transfer->received_at)
                    <div class="col-md-6">
                        <strong>تاريخ الاستلام:</strong> 
                        {{ $transfer->received_at->format('Y-m-d H:i') }}
                    </div>
                    @endif

                    @if($transfer->notes)
                    <div class="col-12">
                        <strong>ملاحظات:</strong>
                        <p class="mb-0 mt-1">{{ $transfer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- حالة التحويل --}}
    <div class="col-lg-4">
        <div class="table-card no-break">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>حالة التحويل
            </div>
            <div class="p-4">
                <div class="timeline">
                    <div class="timeline-item {{ $transfer->status !== 'draft' ? 'completed' : 'active' }}">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-file-earmark"></i>
                        </div>
                        <div class="timeline-content">
                            <strong>إنشاء</strong>
                            <small class="d-block text-muted">
                                {{ $transfer->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                    </div>

                    <div class="timeline-item {{ $transfer->status === 'in_transit' || $transfer->status === 'received' ? 'completed' : ($transfer->status === 'draft' ? 'pending' : 'active') }}">
                        <div class="timeline-icon bg-warning">
                            <i class="bi bi-send"></i>
                        </div>
                        <div class="timeline-content">
                            <strong>إرسال</strong>
                            <small class="d-block text-muted">
                                {{ $transfer->sent_at ? $transfer->sent_at->format('Y-m-d H:i') : 'بانتظار الإرسال' }}
                            </small>
                        </div>
                    </div>

                    <div class="timeline-item {{ $transfer->status === 'received' ? 'completed' : ($transfer->status === 'in_transit' ? 'active' : 'pending') }}">
                        <div class="timeline-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <strong>استلام</strong>
                            <small class="d-block text-muted">
                                {{ $transfer->received_at ? $transfer->received_at->format('Y-m-d H:i') : 'بانتظار الاستلام' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- أصناف التحويل --}}
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="table-card no-break">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i>أصناف التحويل</span>
                <span class="badge bg-primary">{{ $transfer->items->count() }} صنف</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">الصنف</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 20%;">الوحدة</th>
                            <th style="width: 25%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfer->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->item->name ?? '-' }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->item->code ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">
                                    {{ number_format($item->quantity, 3) }}
                                </span>
                            </td>
                            <td>{{ $item->item->unit->name ?? '-' }}</td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">لا توجد أصناف في هذا التحويل</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- قسم التوقيعات (يظهر فقط عند الطباعة) --}}
<div class="signature-section print-only mt-5">
    <div class="signature-box">
        <div class="line">
            <strong>المُرسل</strong><br>
            <small>الاسم: _______________</small><br>
            <small>التوقيع: _______________</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="line">
            <strong>المستلم</strong><br>
            <small>الاسم: _______________</small><br>
            <small>التوقيع: _______________</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="line">
            <strong>المدير</strong><br>
            <small>الاسم: _______________</small><br>
            <small>التوقيع: _______________</small>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* تنسيقات Timeline */
    .timeline {
        position: relative;
        padding-right: 20px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        right: 30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        display: flex;
        gap: 15px;
    }
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        z-index: 1;
    }
    .timeline-item.pending .timeline-icon {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
    }
    .timeline-item.active .timeline-icon {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
    }
    .timeline-content {
        flex: 1;
        padding-top: 8px;
    }

    /* تنسيقات الطباعة */
    @media print {
        .no-print, .btn, button, nav, aside, .app-sidebar, .app-navbar {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        .app-main {
            margin: 0 !important;
            padding: 20px !important;
        }
        body {
            background: white !important;
        }
        .table-card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
        .print-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .print-header .logo {
            font-size: 24pt;
            font-weight: bold;
        }
        .print-header .document-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 15px 0;
            padding: 10px;
            background: #f0f0f0;
            border: 2px solid #000;
        }
        .signature-section {
            display: flex !important;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-box .line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        @page {
            size: A4;
            margin: 15mm;
        }
    }
</style>
@endpush