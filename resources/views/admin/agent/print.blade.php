@extends('layouts.print')

@section('title', 'قيد يومي رقم ' . $entry->entry_number)

@section('content')
<div class="print-toolbar">
    <button onclick="window.print()" class="btn btn-print">🖨️ طباعة</button>
    <button onclick="window.close()" class="btn">إغلاق</button>
</div>

<div class="sheet">
    <header class="sheet-header">
        <div>
            <h4>{{ config('app.name') }}</h4>
            <small>نظام إدارة المخازن — الحسابات</small>
        </div>
        <div class="doc-title">
            <h5>قيد يومي</h5>
            <span>رقم: <strong>{{ $entry->entry_number }}</strong></span>
        </div>
    </header>

    <table class="meta">
        <tr>
            <td>التاريخ: {{ $entry->date->format('Y/m/d') }}</td>
            <td>الحالة: {{ $entry->status === 'posted' ? 'مرحّل' : 'مسودة' }}</td>
            <td>المصدر: {{ ['ai_invoice' => 'فاتورة ذكية', 'payroll' => 'رواتب', 'warehouse' => 'المخازن', 'manual' => 'يدوي'][$entry->source] ?? $entry->source }}</td>
            <td>أنشأه: {{ $entry->by_agent ? 'الوكيل الذكي 🤖' : 'المستخدم' }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>#</th><th>كود الحساب</th><th>اسم الحساب</th><th>مدين</th><th>دائن</th><th>ملاحظات</th></tr>
        </thead>
        <tbody>
            @foreach($entry->lines as $i => $line)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $line->account->code }}</td>
                    <td>{{ $line->account->name }}</td>
                    <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                    <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                    <td>{{ $line->notes ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>الإجمالي</strong></td>
                <td><strong>{{ number_format($entry->lines->sum('debit'), 2) }}</strong></td>
                <td><strong>{{ number_format($entry->lines->sum('credit'), 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($entry->description)
        <p class="notes"><strong>البيان:</strong> {{ $entry->description }}</p>
    @endif

    <div class="signatures">
        <div>إعداد: ....................</div>
        <div>مراجعة: ....................</div>
        <div>اعتماد: ....................</div>
    </div>

    <footer class="sheet-footer">
        طُبع بتاريخ {{ now()->format('Y/m/d H:i') }} — {{ auth()->user()?->name ?? 'النظام' }}
    </footer>
</div>
@endsection