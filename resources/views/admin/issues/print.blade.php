<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سند صرف {{ $issue->serial }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Cairo', sans-serif; font-size: 12px; }
        .print-header { border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h2 { margin: 0; color: #dc2626; }
        .info-box { background: #f8fafc; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        table thead { background: #dc2626; color: white; }
        .signature-box { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box div { width: 45%; border-top: 1px solid #333; padding-top: 5px; text-align: center; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="no-print text-center mb-3">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> طباعة</button>
        <button onclick="window.close()" class="btn btn-secondary">إغلاق</button>
    </div>

    <div class="print-header text-center">
        <h2>سند صرف مخزون</h2>
        <div>نظام إدارة المخازن — Warehouse ERP</div>
    </div>

    <div class="row info-box">
        <div class="col-6"><strong>رقم السند:</strong> {{ $issue->serial }}</div>
        <div class="col-6"><strong>التاريخ:</strong> {{ $issue->issue_date->format('Y-m-d') }}</div>
        <div class="col-6"><strong>المخزن:</strong> {{ $issue->warehouse->name }}</div>
        <div class="col-6"><strong>المستلم:</strong> {{ $issue->recipient_name ?? '-' }}</div>
        <div class="col-6"><strong>المرجع:</strong> {{ $issue->reference_number ?? '-' }}</div>
        <div class="col-6"><strong>الحالة:</strong> {{ $issue->status_label }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">الصنف</th>
                <th style="width: 20%;">الوحدة</th>
                <th style="width: 15%;">الكمية</th>
                <th style="width: 20%;">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issue->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item->name ?? 'محذوف' }}<br><small>{{ $item->item->code ?? '' }}</small></td>
                <td>{{ $item->item->unit->name ?? '-' }}</td>
                <td><strong>{{ number_format($item->quantity, 3) }}</strong></td>
                <td>{{ $item->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: left;">إجمالي الكمية:</th>
                <th colspan="2">{{ number_format($issue->total_quantity, 3) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($issue->notes)
    <div class="mt-3"><strong>ملاحظات:</strong> {{ $issue->notes }}</div>
    @endif

    <div class="signature-box">
        <div>توقيع المستلم</div>
        <div>توقيع المسؤول</div>
    </div>

    <div class="text-center mt-4" style="font-size: 10px; color: #666;">
        تم الطباعة في: {{ now()->format('Y-m-d H:i') }} — بواسطة: {{ auth()->user()->name }}
    </div>
</body>
</html>