@extends('layouts.app')
@section('title', 'تعديل سند ' . $receipt->serial)
@section('page-title', 'تعديل سند الإدخال')
@section('page-subtitle', $receipt->serial)

@section('content')
<form action="{{ route('admin.receipts.update', $receipt) }}" method="POST" id="receiptForm">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>بيانات السند</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">تاريخ السند</label>
                            <input type="date" name="receipt_date" class="form-control" value="{{ old('receipt_date', $receipt->receipt_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المخزن</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id', $receipt->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المورد</label>
                            <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $receipt->supplier_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم المرجع</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $receipt->reference_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ملاحظات</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes', $receipt->notes) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul me-2"></i>أصناف السند</span>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="bi bi-plus-lg me-1"></i> إضافة صنف
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">الصنف</th>
                                <th style="width: 15%;">الكمية</th>
                                <th style="width: 15%;">سعر الوحدة</th>
                                <th style="width: 15%;">الإجمالي</th>
                                <th style="width: 10%;">ملاحظات</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                        <tfoot>
                            <tr class="table-warning">
                                <th colspan="3" class="text-end">الإجمالي الكلي:</th>
                                <th id="grandTotal">0.00</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> تحديث</button>
                <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
const itemsData = @json($items->map(fn($i) => [
    'id' => $i->id,
    'name' => $i->name . ' [' . ($i->unit->name ?? '') . ']',
    'code' => $i->code,
]));
const existingItems = @json($receipt->items->map(fn($i) => [
    'item_id' => $i->item_id,
    'quantity' => (float)$i->quantity,
    'unit_price' => (float)$i->unit_price,
    'notes' => $i->notes,
]));

let rowIndex = 0;

function addItemRow(data = {}) {
    rowIndex++;
    const options = itemsData.map(i => 
        `<option value="${i.id}" ${i.id == data.item_id ? 'selected' : ''}>${i.name} (${i.code})</option>`
    ).join('');

    const row = `
        <tr data-row="${rowIndex}">
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm item-select" required>
                    <option value="">-- اختر الصنف --</option>
                    ${options}
                </select>
            </td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm item-qty" step="0.001" min="0.001" value="${data.quantity || ''}" required></td>
            <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm item-price" step="0.01" min="0" value="${data.unit_price || 0}"></td>
            <td class="item-total fw-bold">0.00</td>
            <td><input type="text" name="items[${rowIndex}][notes]" class="form-control form-control-sm" value="${data.notes || ''}"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
        </tr>
    `;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
    const rowEl = document.querySelector(`tr[data-row="${rowIndex}"]`);
    rowEl.querySelector('.item-qty').addEventListener('input', () => { calcRow(rowEl); recalcAll(); });
    rowEl.querySelector('.item-price').addEventListener('input', () => { calcRow(rowEl); recalcAll(); });
    rowEl.querySelector('.remove-row').addEventListener('click', () => { rowEl.remove(); recalcAll(); });
    calcRow(rowEl);
    recalcAll();
}

function calcRow(row) {
    const q = parseFloat(row.querySelector('.item-qty').value) || 0;
    const p = parseFloat(row.querySelector('.item-price').value) || 0;
    row.querySelector('.item-total').textContent = (q * p).toFixed(2);
}

function recalcAll() {
    let total = 0;
    document.querySelectorAll('#itemsBody tr').forEach(r => {
        total += parseFloat(r.querySelector('.item-total').textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
}

document.getElementById('addItemBtn').addEventListener('click', () => addItemRow());

// تحميل العناصر الموجودة
if (existingItems.length > 0) {
    existingItems.forEach(i => addItemRow(i));
} else {
    addItemRow();
}
</script>
@endpush
@endsection