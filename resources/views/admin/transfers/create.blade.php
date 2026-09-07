```blade
@extends('layouts.app')

@section('title', 'إنشاء تحويل جديد')
@section('page-title', 'إنشاء تحويل جديد')
@section('page-subtitle', 'نقل الأصناف بين المخازن')

@section('content')
<div class="container-fluid">

    <div class="table-card">
        <div class="card-header">
            <i class="bi bi-arrow-left-right me-2"></i>
            إنشاء تحويل جديد
        </div>

        <form action="{{ route('admin.transfers.store') }}" method="POST" class="p-4">
            @csrf

            <div class="row g-3">

                {{-- التاريخ --}}
                <div class="col-md-4">
                    <label class="form-label">
                        التاريخ <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="transfer_date"
                        class="form-control"
                        value="{{ old('transfer_date', now()->format('Y-m-d')) }}"
                        required
                    >

                    @error('transfer_date')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- من المخزن --}}
                <div class="col-md-4">
                    <label class="form-label">
                        من مخزن <span class="text-danger">*</span>
                    </label>

                    <select name="from_warehouse_id" class="form-select" required>
                        <option value="">اختر المخزن</option>

                        @foreach($warehouses as $warehouse)
                            <option
                                value="{{ $warehouse->id }}"
                                {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}
                            >
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('from_warehouse_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- إلى المخزن --}}
                <div class="col-md-4">
                    <label class="form-label">
                        إلى مخزن <span class="text-danger">*</span>
                    </label>

                    <select name="to_warehouse_id" class="form-select" required>
                        <option value="">اختر المخزن</option>

                        @foreach($warehouses as $warehouse)
                            <option
                                value="{{ $warehouse->id }}"
                                {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}
                            >
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('to_warehouse_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- الملاحظات --}}
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>

                    <textarea
                        name="notes"
                        class="form-control"
                        rows="2"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <hr class="my-4">

            {{-- الأصناف --}}
            <h6 class="mb-3">
                <i class="bi bi-list-ul me-2"></i>
                الأصناف
            </h6>

            <div id="items-container">

                <div class="row g-2 mb-2 item-row">

                    <div class="col-md-6">
                        <select name="items[0][item_id]" class="form-select" required>
                            <option value="">اختر الصنف</option>

                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input
                            type="number"
                            name="items[0][quantity]"
                            class="form-control"
                            placeholder="الكمية"
                            step="0.001"
                            min="0.001"
                            required
                        >
                    </div>

                    <div class="col-md-2">
                        <button
                            type="button"
                            class="btn btn-outline-danger btn-sm remove-item"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                </div>

            </div>

            <button
                type="button"
                class="btn btn-outline-primary btn-sm mt-2"
                id="add-item-btn"
            >
                <i class="bi bi-plus-lg me-1"></i>
                إضافة صنف
            </button>

            <hr>

            {{-- أزرار الحفظ --}}
            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    حفظ كمسودة
                </button>

                <a
                    href="{{ route('admin.transfers.index') }}"
                    class="btn btn-outline-secondary"
                >
                    إلغاء
                </a>

            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let itemIndex = 1;

    const addButton = document.getElementById('add-item-btn');
    const container = document.getElementById('items-container');

    addButton.addEventListener('click', function () {

        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(function (el) {

            el.name = el.name.replace(
                /\[\d+\]/,
                '[' + itemIndex + ']'
            );

            if (el.tagName === 'INPUT') {
                el.value = '';
            }

            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            }
        });

        container.appendChild(newRow);

        itemIndex++;
    });

    container.addEventListener('click', function (e) {

        const removeButton = e.target.closest('.remove-item');

        if (!removeButton) {
            return;
        }

        const rows = container.querySelectorAll('.item-row');

        if (rows.length > 1) {
            removeButton.closest('.item-row').remove();
        }
    });

});
</script>
@endpush
```