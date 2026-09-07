@extends('layouts.print')

@section('title', 'سند صرف رقم ' . $issue->serial)

@section('content')
<div class="print-toolbar">
    <button onclick="window.print()" class="btn btn-print">🖨️ طباعة</button>
    <button onclick="window.close()" class="btn">إغلاق</button>
</div>

<div class="sheet">
    <header class="sheet-header">
        <div>
            <h4>{{ config('app.name') }}</h4>
            <small>نظام إدارة المخازن</small>
        </div>
        <div class="doc-title">
            <h5>سند صرف مخزن</h5>
            <span>رقم: <strong>{{ $issue->serial }}</strong></span>
        </div>
    </header>

    <table class="meta">
        <tr>
            <td>التاريخ: {{ $issue->issue_date->format('Y/m/d') }}</td>
            <td>المخزن: {{ $issue->warehouse->name ?? '-' }}</td>
            <td>الجهة/المستلم: {{ $issue->receiver_name ?? '-' }}</td>
            <td>الحالة: {{ $issue->status_label }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th><th>الكود</th><th>الصنف</th><th>الوحدة</th><th>الكمية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issue->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item->code ?? '-' }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->item->unit->name ?? '-' }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($issue->notes)
        <p class="notes"><strong>ملاحظات:</strong> {{ $issue->notes }}</p>
    @endif

    <div class="signatures">
        <div>إعداد: {{ $issue->creator->name ?? '-' }}</div>
        <div>صرف: ....................</div>
        <div>اعتماد: ....................</div>
    </div>

    <footer class="sheet-footer">
        طُبع بتاريخ {{ now()->format('Y/m/d H:i') }} — بواسطة {{ auth()->user()->name }}
    </footer>
</div>
@endsection