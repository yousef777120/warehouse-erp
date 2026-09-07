@extends('layouts.app')
@section('title', 'عرض التحويل')
@section('page-title', 'عرض التحويل')
@section('page-subtitle', $transfer->serial)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-arrow-left-right me-2"></i>تفاصيل التحويل</div>
            <div class="p-4">
                <div class="row g-3">
                    <a href="{{ route('admin.receipts.print', $receipt) }}" target="_blank" class="btn btn-outline-secondary">
    <i class="bi bi-printer me-1"></i> طباعة
</a>
                    
                    <div class="col-md-6">
                        <strong>الرقم التسلسلي:</strong> {{ $transfer->serial }}
                    </div>
                    <div class="col-md-6">
                        <strong>التاريخ:</strong> {{ $transfer->transfer_date->format('Y-m-d') }}
                    </div>
                    <div class="col-md-6">
                        <strong>من مخزن:</strong> {{ $transfer->fromWarehouse->name ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>إلى مخزن:</strong> {{ $transfer->toWarehouse->name ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>الحالة:</strong>
                        @switch($transfer->status)
                            @case('draft')
                                <span class="badge badge-soft-warning">مسودة</span>
                                @break
                            @case('in_transit')
                                <span class="badge badge-soft-primary">قيد النقل</span>
                                @break
                            @case('received')
                                <span class="badge badge-soft-success">مستلم</span>
                                @break
                            @case('cancelled')
                                <span class="badge badge-soft-danger">ملغي</span>
                                @break
                        @endswitch
                    </div>
                    <div class="col-md-6">
                        <strong>المنشئ:</strong> {{ $transfer->creator->name ?? '-' }}
                    </div>
                    @if($transfer->notes)
                    <div class="col-12">
                        <strong>ملاحظات:</strong> {{ $transfer->notes }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-card mt-3">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i>الأصناف</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfer->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->item->name ?? '-' }}</td>
                            <td>{{ number_format($item->quantity, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-lightning-fill me-2"></i>الإجراءات</div>
            <div class="p-4 d-flex flex-column gap-2">
                @if($transfer->status === 'draft')
                    @can('manage_transfers')
                    <form action="{{ route('admin.transfers.send', $transfer) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إرسال التحويل؟ سيتم خصم الكميات من المخزن المصدر.')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-send-fill me-1"></i> إرسال التحويل
                        </button>
                    </form>
                    <a href="{{ route('admin.transfers.edit', $transfer) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                    <form action="{{ route('admin.transfers.destroy', $transfer) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> حذف
                        </button>
                    </form>
                    @endcan
                @elseif($transfer->status === 'in_transit')
                    @can('manage_transfers')
                    <form action="{{ route('admin.transfers.receive', $transfer) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من استلام التحويل؟ سيتم إضافة الكميات إلى المخزن الوجهة.')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-box-arrow-in-down me-1"></i> استلام التحويل
                        </button>
                    </form>
                    <form action="{{ route('admin.transfers.cancel', $transfer) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء التحويل؟ سيتم إعادة الكميات إلى المخزن المصدر.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> إلغاء التحويل
                        </button>
                    </form>
                    @endcan
                @elseif($transfer->status === 'received')
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>تم استلام التحويل بنجاح
                    </div>
                @elseif($transfer->status === 'cancelled')
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-x-circle-fill me-2"></i>تم إلغاء هذا التحويل
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection