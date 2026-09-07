@extends('layouts.app')
@section('title', 'سند إدخال ' . $receipt->serial)
@section('page-title', 'تفاصيل سند الإدخال')
@section('page-subtitle', $receipt->serial)

@section('content')
{{-- رأس الطباعة --}}
<x-print-header 
    :documentTitle="'سند إدخال مخزني'" 
    :documentNumber="$receipt->serial_number" 
/>
<div class="row g-3">
    <!-- معلومات السند -->
    <div class="col-lg-4">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>معلومات السند</div>
            <div class="p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted" style="width: 40%;">الرقم:</th><td><code class="fw-bold">{{ $receipt->serial }}</code></td></tr>
                    <tr><th class="text-muted">التاريخ:</th><td>{{ $receipt->receipt_date->format('Y-m-d') }}</td></tr>
                    <tr><th class="text-muted">المخزن:</th><td>{{ $receipt->warehouse->name }}</td></tr>
                    <tr><th class="text-muted">المورد:</th><td>{{ $receipt->supplier_name ?? '-' }}</td></tr>
                    <tr><th class="text-muted">المرجع:</th><td>{{ $receipt->reference_number ?? '-' }}</td></tr>
                    <tr><th class="text-muted">الحالة:</th><td><span class="badge {{ $receipt->status_badge }}">{{ $receipt->status_label }}</span></td></tr>
                    <tr><th class="text-muted">أنشأه:</th><td>{{ $receipt->creator->name ?? '-' }}</td></tr>
                    @if($receipt->confirmed_at)
                    <tr><th class="text-muted">أكّده:</th><td>{{ $receipt->confirmer->name ?? '-' }}<br><small class="text-muted">{{ $receipt->confirmed_at->format('Y-m-d H:i') }}</small></td></tr>
                    @endif
                    <tr><th class="text-muted">ملاحظات:</th><td>{{ $receipt->notes ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- عناصر السند -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i>الأصناف ({{ $receipt->items->count() }})</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>الوحدة</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipt->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $item->item->name ?? 'محذوف' }}</strong>
                                <br><small class="text-muted">{{ $item->item->code ?? '' }}</small>
                            </td>
                            <td>{{ $item->item->unit->name ?? '-' }}</td>
                            <td>{{ number_format($item->quantity, 3) }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td><strong>{{ number_format($item->total, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning">
                        <tr>
                            <th colspan="5" class="text-end">الإجمالي الكلي:</th>
                            <th>{{ number_format($receipt->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    <!-- الإجراءات -->
    <div class="col-12">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
            @can('manage_receipts')
                @if($receipt->isDraft)
                    <a href="{{ route('admin.receipts.edit', $receipt) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                    <form action="{{ route('admin.receipts.confirm', $receipt) }}" method="POST" onsubmit="return confirm('تأكيد السند سيُضيف الأصناف للمخزون. متابعة؟')">
                        @csrf
                        <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> تأكيد</button>
                    </form>
                    <form action="{{ route('admin.receipts.destroy', $receipt) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> حذف</button>
                    </form>
                @elseif($receipt->isConfirmed)
                    <form action="{{ route('admin.receipts.cancel', $receipt) }}" method="POST" onsubmit="return confirm('إلغاء السند سيعكس الحركات من المخزون. متابعة؟')">
                        @csrf
                        <button class="btn btn-warning"><i class="bi bi-x-circle me-1"></i> إلغاء</button>
                    </form>
                @endif
            @endcan
        </div>
    </div>
        {{-- قسم التوقيعات --}}
<div class="signature-section print-only mt-5">
    <div class="signature-box">
        <div class="line">
            <strong>المستلم</strong><br>
            <small>الاسم: _______________</small><br>
            <small>التوقيع: _______________</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="line">
            <strong>المسؤول</strong><br>
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
    <button onclick="window.open('{{ route('admin.receipts.print', $receipt) }}', '_blank')" class="btn btn-outline-dark">
    <i class="bi bi-printer me-1"></i> طباعة
</button>
    
</div>
@endsection