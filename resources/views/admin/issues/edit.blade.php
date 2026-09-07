@extends('layouts.app')
@section('title', 'تعديل سند ' . $issue->serial)
@section('page-title', 'تعديل سند الصرف')
@section('page-subtitle', $issue->serial)

@section('content')
<form action="{{ route('admin.issues.update', $issue) }}" method="POST" id="issueForm">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>بيانات السند</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">تاريخ السند</label>
                            <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', $issue->issue_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المخزن</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id', $issue->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">اسم المستلم</label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name', $issue->recipient_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم المرجع</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $issue->reference_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ملاحظات</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes', $issue->notes) }}">
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
                                <th style="width: 35%;">الصنف</th>
                                <th style="width: 12%;">المتاح</th>
                                <th style="width: 13%;">الكمية</th>
                                <th style="width: 25%;">ملاحظات</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                        <tfoot>
                            <tr class="table-warning">
                                <th colspan="2" class="text-end">إجمالي الكمية:</th>
                                <th id="grandTotal" colspan="3">0.000</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> تحديث</button>
                <a href="{{ route('admin.issues.show', $issue) }}" class="btn btn-outline-secondary">إلغاء</a>
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
const existingItems = @json($issue->items->map(fn($i) => [
    'item_id' => $i->item_id,
    'quantity' => (float)$i->quantity,
    'notes' => $i->notes,
]));

let rowIndex = 0;
const warehouseSelect = document.getElementById('warehouse_id');

function addItemRow(data = {}, available = 0) {
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
            <td class="item-available text-muted">—</td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm item-qty" step="0.001" min="0.001" value="${data.quantity || ''}" required></td>
            <td><input type="text" name="items[${rowIndex}][notes]" class="form-control form-control-sm" value="${data.notes || ''}"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
        </tr>
    `;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
    const rowEl = document.querySelector(`tr[data-row="${rowIndex}"]`);
    rowEl.querySelector('.item-select').addEventListener('change', (e) => fetchBalance(rowEl, e.target.value));
    rowEl.querySelector('.item-qty').addEventListener('input', () => { checkBalance(rowEl); recalcAll(); });
    rowEl.querySelector('.remove-row').addEventListener('click', () => { rowEl.remove(); recalcAll(); });
    if (data.item_id) fetchBalance(rowEl, data.item_id);
    recalcAll();
}

async function fetchBalance(row, itemId) {
    const warehouseId = warehouseSelect.value;
    const availCell = row.querySelector('.item-available');
    if (!warehouseId || !itemId) { availCell.textContent = '—'; return; }
    try {
        const res = await fetch(`{{ route('admin.issues.get-balance') }}?item_id=${itemId}&warehouse_id=${warehouseId}`);
        const data = await res.json();
        availCell.textContent = data.quantity.toFixed(3);
        availCell.dataset.available = data.quantity;
        checkBalance(row);
    } catch (e) { availCell.textContent = 'خطأ'; }
}

function checkBalance(row) {
    const available = parseFloat(row.querySelector('.item-available').dataset.available || 0);
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const cell = row.querySelector('.item-available');
    if (qty > available) {
        cell.classList.remove('text-muted', 'text-success');
        cell.classList.add('text-danger', 'fw-bold');
    } else {
        cell.classList.remove('text-danger', 'text-muted');
        cell.classList.add('text-success');
    }
}

function recalcAll() {
    let total = 0;
    document.querySelectorAll('#itemsBody tr').forEach(r => {
        total += parseFloat(r.querySelector('.item-qty').value) || 0;
    });
    document.getElementById('grandTotal').textContent = total.toFixed(3);
}

document.getElementById('addItemBtn').addEventListener('click', () => addItemRow());
warehouseSelect.addEventListener('change', () => {
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const itemId = row.querySelector('.item-select').value;
        fetchBalance(row, itemId);
    });
});

if (existingItems.length > 0) {
    existingItems.forEach(i => addItemRow(i));
} else {
    addItemRow();
}
</script>
@endpush
@endsection