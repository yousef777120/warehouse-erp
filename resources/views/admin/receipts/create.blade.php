@extends('layouts.app')
@section('title', 'سند إدخال جديد')
@section('page-title', 'سند إدخال جديد')
@section('page-subtitle', 'الرقم المتوقع: ' . $nextSerial)

@section('content')
<form action="{{ route('admin.receipts.store') }}" method="POST" id="receiptForm">
    @csrf
    <div class="row g-3">
        <!-- بيانات السند -->
        <div class="col-12">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>بيانات السند</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">تاريخ السند <span class="text-danger">*</span></label>
                            <input type="date" name="receipt_date" class="form-control" value="{{ old('receipt_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المخزن <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- اختر المخزن --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المورد</label>
                            <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم المرجع</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="رقم فاتورة المورد مثلاً">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عناصر السند -->
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
                        <tbody id="itemsBody">
                            <!-- يتم ملؤها بالجافاسكربت -->
                        </tbody>
                        <tfoot>
                            <tr class="table-warning">
                                <th colspan="3" class="text-end">الإجمالي الكلي:</th>
                                <th id="grandTotal">0.00</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @error('items') 
                    <div class="alert alert-danger m-3">{{ $message }}</div> 
                @enderror
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" name="confirm_now" value="0" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> حفظ كمسودة
                </button>
                <button type="submit" name="confirm_now" value="1" class="btn btn-success" onclick="return confirm('سيتم تأكيد السند فوراً وإضافة الأصناف للمخزون. متابعة؟')">
                    <i class="bi bi-check-circle me-1"></i> حفظ وتأكيد
                </button>
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </div>
    </div>
</form>

{{-- ✅ التحضير المسبق للبيانات (أفضل من @json المباشر مع fn) --}}
@php
    $itemsData = $items->map(function($i) {
        return [
            'id' => $i->id,
            'name' => $i->name . ' [' . ($i->unit->name ?? '') . ']',
            'code' => $i->code ?? '',
        ];
    })->values();
@endphp

@push('scripts')
<script>
    // تمرير البيانات بأمان
    const itemsData = @json($itemsData);
    let rowIndex = 0;

    function addItemRow(item_id = '', quantity = '', unit_price = '', notes = '') {
        rowIndex++;
        
        // بناء قائمة الخيارات
        const options = itemsData.map(i => 
            '<option value="' + i.id + '" ' + (i.id == item_id ? 'selected' : '') + '>' + i.name + ' (' + i.code + ')</option>'
        ).join('');

        const row = '<tr data-row="' + rowIndex + '">' +
            '<td>' +
                '<select name="items[' + rowIndex + '][item_id]" class="form-select form-select-sm item-select" required>' +
                    '<option value="">-- اختر الصنف --</option>' +
                    options +
                '</select>' +
            '</td>' +
            '<td><input type="number" name="items[' + rowIndex + '][quantity]" class="form-control form-control-sm item-qty" step="0.001" min="0.001" value="' + quantity + '" required></td>' +
            '<td><input type="number" name="items[' + rowIndex + '][unit_price]" class="form-control form-control-sm item-price" step="0.01" min="0" value="' + (unit_price || 0) + '"></td>' +
            '<td class="item-total fw-bold">0.00</td>' +
            '<td><input type="text" name="items[' + rowIndex + '][notes]" class="form-control form-control-sm" value="' + notes + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>' +
        '</tr>';
        
        document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
        attachRowEvents(document.querySelector('tr[data-row="' + rowIndex + '"]'));
        recalcAll();
    }

    function attachRowEvents(row) {
        row.querySelector('.item-qty').addEventListener('input', function() { calcRow(row); recalcAll(); });
        row.querySelector('.item-price').addEventListener('input', function() { calcRow(row); recalcAll(); });
        row.querySelector('.remove-row').addEventListener('click', function() { row.remove(); recalcAll(); });
    }

    function calcRow(row) {
        const q = parseFloat(row.querySelector('.item-qty').value) || 0;
        const p = parseFloat(row.querySelector('.item-price').value) || 0;
        row.querySelector('.item-total').textContent = (q * p).toFixed(2);
    }

    function recalcAll() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(function(r) {
            total += parseFloat(r.querySelector('.item-total').textContent) || 0;
        });
        document.getElementById('grandTotal').textContent = total.toFixed(2);
    }

    document.getElementById('addItemBtn').addEventListener('click', function() { addItemRow(); });

    // إضافة صف أولي عند تحميل الصفحة
    addItemRow();
</script>
@endpush
@endsection