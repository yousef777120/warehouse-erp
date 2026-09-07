<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; color: #111; margin: 0; background: #f1f5f9; }

        .print-toolbar {
            max-width: 210mm; margin: 12px auto; display: flex; gap: 8px; justify-content: center;
        }
        .btn {
            padding: 8px 20px; border: 1px solid #cbd5e1; background: #fff;
            border-radius: 8px; cursor: pointer; font-family: inherit; font-size: 14px;
        }
        .btn-print { background: #0d6efd; color: #fff; border-color: #0d6efd; }

        .sheet {
            background: #fff; width: 210mm; min-height: 297mm;
            margin: 0 auto 20px; padding: 12mm; border: 1px solid #e2e8f0;
        }

        .sheet-header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 14px;
        }
        .sheet-header h4 { margin: 0; }
        .doc-title { text-align: left; }
        .doc-title h5 { margin: 0 0 4px; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.meta td { padding: 6px 4px; }
        table.items th, table.items td { border: 1px solid #94a3b8; padding: 6px 8px; text-align: center; }
        table.items thead th { background: #f1f5f9; }
        table.items tfoot td { font-weight: 700; background: #f8fafc; }

        .notes { margin: 12px 0; font-size: 13px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 45px; font-size: 13px; }
        .sheet-footer {
            margin-top: 30px; border-top: 1px solid #cbd5e1; padding-top: 8px;
            font-size: 11px; color: #64748b; text-align: center;
        }

        @media print {
            body { background: #fff; }
            .print-toolbar { display: none !important; }
            .sheet { border: none; margin: 0; width: auto; min-height: auto; }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>