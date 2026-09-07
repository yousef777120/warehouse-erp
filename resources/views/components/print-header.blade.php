{{-- رأس الصفحة المطبوعة --}}
<div class="print-header print-only">
    <div class="logo">
        <i class="bi bi-box-seam-fill"></i> نظام المخازن
    </div>
    <div class="company-info">
        <p>نظام إدارة المخازن المتكامل ERP</p>
        <p>التاريخ: {{ date('Y/m/d') }} | الوقت: {{ date('H:i') }}</p>
    </div>
    
    @if(isset($documentTitle))
    <div class="document-title">
        {{ $documentTitle }}
    </div>
    @endif

    @if(isset($documentNumber))
    <div style="margin: 10px 0; font-size: 11pt;">
        <strong>رقم السند:</strong> {{ $documentNumber }}
    </div>
    @endif
</div>

{{-- تذييل الصفحة --}}
<div class="print-footer print-only">
    <p>تم الطباعة بواسطة: {{ auth()->user()->name }} | {{ date('Y/m/d H:i') }}</p>
    <p>نظام المخازن - جميع الحقوق محفوظة © {{ date('Y') }}</p>
</div>