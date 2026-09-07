@extends('layouts.app')
@section('title', 'تعديل التحويل')
@section('page-title', 'تعديل التحويل')
@section('page-subtitle', $transfer->serial)

@section('content')
<div class="table-card">
    <div class="card-header"><i class="bi bi-pencil me-2"></i>تعديل التحويل</div>
    <form action="{{ route('admin.transfers.update', $transfer) }}" method="POST" class="p-4">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                <input type="date" name="transfer_date" class="form-control" value="{{ $transfer->transfer_date->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">من مخزن <span class="text-danger">*</span></label>
                <select name="from_warehouse_id" class="form-select" required>
                    <option value="">اختر المخزن</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $transfer->from_warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">إلى مخزن <span class="text-danger">*</span></label>
                <select name="to_warehouse_id" class="form-select" required>
                    <option value="">اختر المخزن</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $transfer->to_warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ $transfer->notes }}</textarea>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3"><i class="bi bi-list-ul me-2"></i>الأصناف</h6>
        <div id="items-container">
            @foreach($transfer->items as $index => $item)
            <div class="row g-2 mb-2 item-row">
                <div class="col-md-6">
                    <select name="items[{{ $index }}][item_id]" class="form-select" required>
                        <option value="">اختر الصنف</option>
                        @foreach($items as $itemOption)
                            <option value="{{ $itemOption->id }}" {{ $item->item_id == $itemOption->id ? 'selected' : '' }}>{{ $itemOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}" step="0.001" min="0.001" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-item-btn">
            <i class="bi bi-plus-lg me-1"></i> إضافة صنف
        </button>

        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ التعديلات</button>
            <a href="{{ route('admin.transfers.show', $transfer) }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $transfer->items->count() }};
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            if (el.tagName === 'INPUT') el.value = '';
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
        });
        container.appendChild(newRow);
        itemIndex++;
    });

    document.getElementById('items-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const rows = this.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
            }
        }
    });
});
</script>
@endpush
@endsection