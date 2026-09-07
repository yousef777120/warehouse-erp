{{-- أزرار الطباعة (تظهر فقط على الشاشة) --}}
<div class="no-print mb-3">
    <div class="d-flex gap-2 flex-wrap">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer-fill"></i> طباعة
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> إغلاق
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>
</div>