@extends('layouts.app')
@section('title', 'سند صرف ' . $issue->serial)
@section('page-title', 'تفاصيل سند الصرف')
@section('page-subtitle', $issue->serial)

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>معلومات السند</div>
            <div class="p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted" style="width: 40%;">الرقم:</th><td><code class="fw-bold">{{ $issue->serial }}</code></td></tr>
                    <tr><th class="text-muted">التاريخ:</th><td>{{ $issue->issue_date->format('Y-m-d') }}</td></tr>
                    <tr><th class="text-muted">المخزن:</th><td>{{ $issue->warehouse->name }}</td></tr>
                    <tr><th class="text-muted">المستلم:</th><td>{{ $issue->recipient_name ?? '-' }}</td></tr>
                    <tr><th class="text-muted">المرجع:</th><td>{{ $issue->reference_number ?? '-' }}</td></tr>
                    <tr><th class="text-muted">الحالة:</th><td><span class="badge {{ $issue->status_badge }}">{{ $issue->status_label }}</span></td></tr>
                    <tr><th class="text-muted">أنشأه:</th><td>{{ $issue->creator->name ?? '-' }}</td></tr>
                    @if($issue->confirmed_at)
                    <tr><th class="text-muted">أكّده:</th><td>{{ $issue->confirmer->name ?? '-' }}<br><small class="text-muted">{{ $issue->confirmed_at->format('Y-m-d H:i') }}</small></td></tr>
                    @endif
                    <tr><th class="text-muted">ملاحظات:</th><td>{{ $issue->notes ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i>الأصناف ({{ $issue->items->count() }})</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>الوحدة</th>
                            <th>الكمية</th>
                            <th>المتاح حالياً</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issue->items as $i => $item)
                        @php $available = $balances[$item->item_id] ?? 0; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $item->item->name ?? 'محذوف' }}</strong>
                                <br><small class="text-muted">{{ $item->item->code ?? '' }}</small>
                            </td>
                            <td>{{ $item->item->unit->name ?? '-' }}</td>
                            <td><strong>{{ number_format($item->quantity, 3) }}</strong></td>
                            <td>
                                @if($issue->isConfirmed)
                                    <span class="text-muted">{{ number_format($available, 3) }} <small>(بعد التأكيد)</small></span>
                                @else
                                    <span class="{{ $available >= $item->quantity ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($available, 3) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning">
                        <tr>
                            <th colspan="3" class="text-end">إجمالي الكمية:</th>
                            <th colspan="2">{{ number_format($issue->total_quantity, 3) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
            @can('manage_issues')
                @if($issue->isDraft)
                    <a href="{{ route('admin.issues.edit', $issue) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                    <form action="{{ route('admin.issues.confirm', $issue) }}" method="POST" onsubmit="return confirm('تأكيد السند سيخصم الأصناف من المخزون. متابعة؟')">
                        @csrf
                        <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> تأكيد</button>
                    </form>
                    <form action="{{ route('admin.issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> حذف</button>
                    </form>
                @elseif($issue->isConfirmed)
                    <form action="{{ route('admin.issues.cancel', $issue) }}" method="POST" onsubmit="return confirm('إلغاء السند سيعيد الأصناف للمخزون. متابعة؟')">
                        @csrf
                        <button class="btn btn-warning"><i class="bi bi-x-circle me-1"></i> إلغاء</button>
                    </form>
                @endif
            @endcan
        </div>
    </div>
    <button onclick="window.open('{{ route('admin.receipts.print', $receipt) }}', '_blank')" class="btn btn-outline-dark">
    <i class="bi bi-printer me-1"></i> طباعة
</button>
</div>

@endsection