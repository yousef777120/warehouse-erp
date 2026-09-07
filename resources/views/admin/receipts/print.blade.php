<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سند إدخال {{ $receipt->serial }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Cairo', sans-serif; font-size: 12px; }
        .print-header { border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h2 { margin: 0; color: #0d6efd; }
        .info-box { background: #f8fafc; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        table thead { background: #0d6efd; color: white; }
        .signature-box { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box div { width: 45%; border-top: 1px solid #333; padding-top: 5px; text-align: center; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-3">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> طباعة</button>
        <button onclick="window.close()" class="btn btn-secondary">إغلاق</button>
    </div>

    <div class="print-header text-center">
        <h2>سند إدخال مخزون</h2>
        <div>نظام إدارة المخازن — Warehouse ERP</div>
    </div>

    <div class="row info-box">
        <div class="col-6"><strong>رقم السند:</strong> {{ $receipt->serial }}</div>
        <div class="col-6"><strong>التاريخ:</strong> {{ $receipt->receipt_date->format('Y-m-d') }}</div>
        <div class="col-6"><strong>المخزن:</strong> {{ $receipt->warehouse->name }}</div>
        <div class="col-6"><strong>المورد:</strong> {{ $receipt->supplier_name ?? '-' }}</div>
        <div class="col-6"><strong>المرجع:</strong> {{ $receipt->reference_number ?? '-' }}</div>
        <div class="col-6"><strong>الحالة:</strong> {{ $receipt->status_label }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">الصنف</th>
                <th style="width: 15%;">الوحدة</th>
                <th style="width: 15%;">الكمية</th>
                <th style="width: 15%;">السعر</th>
                <th style="width: 15%;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item->name ?? 'محذوف' }}<br><small>{{ $item->item->code ?? '' }}</small></td>
                <td>{{ $item->item->unit->name ?? '-' }}</td>
                <td>{{ number_format($item->quantity, 3) }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td><strong>{{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: left;">الإجمالي الكلي:</th>
                <th>{{ number_format($receipt->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($receipt->notes)
    <div class="mt-3">
        <strong>ملاحظات:</strong> {{ $receipt->notes }}
    </div>
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