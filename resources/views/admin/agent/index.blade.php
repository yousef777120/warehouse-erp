@extends('layouts.app')

@section('title', 'الوكيل المحاسبي الذكي')
@section('page-title', 'الوكيل المحاسبي الذكي 🤖')
@section('page-subtitle', 'تحليل الفواتير وتوليد القيود وإدارة الرواتب')

@section('content')
<div class="row g-4">
    {{-- العمود الأيمن: الإدخال --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-upc-scan me-2"></i>أعطِ الوكيل فاتورة</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.agent.analyze') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="invoice" accept="image/*" class="form-control mb-3" required>
                    <button class="btn btn-primary w-100"><i class="bi bi-magic me-1"></i>حلّل وأنشئ القيد</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-cash-coin me-2"></i>قيد الرواتب الشهري</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.agent.payroll') }}">
                    @csrf
                    <input type="month" name="month" value="{{ now()->format('Y-m') }}" class="form-control mb-3">
                    <button class="btn btn-success w-100"><i class="bi bi-calculator me-1"></i>تشغيل قيد الرواتب</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-person-plus me-2"></i>إضافة موظف</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.agent.employees.store') }}">
                    @csrf
                    <input type="text" name="name" class="form-control mb-2" placeholder="اسم الموظف" required>
                    <select name="job_title_id" class="form-select mb-2" required>
                        <option value="">اختر المسمى الوظيفي</option>
                        @foreach($titles as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} — {{ number_format($t->salary, 0) }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="salary" class="form-control mb-2" placeholder="الراتب (1000 - 10000)" min="1000" max="10000" step="100" required>
                    <button class="btn btn-outline-primary w-100">إضافة</button>
                </form>
            </div>
        </div>
    </div>

    {{-- العمود الأيسر: الجداول --}}
    <div class="col-lg-8">
        {{-- المسميات والرواتب --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>المسميات الوظيفية والرواتب</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead><tr><th>المسمى</th><th>الوظيفة</th><th>الراتب</th><th>الموظفون</th></tr></thead>
                    <tbody>
                        @foreach($titles as $t)
                            <tr>
                                <td><strong>{{ $t->name }}</strong></td>
                                <td class="small text-muted">{{ $t->function }}</td>
                                <td><span class="badge bg-primary">{{ number_format($t->salary, 0) }}</span></td>
                                <td>{{ $t->employees_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- الفواتير بانتظار الاعتماد --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-receipt me-2"></i>فواتير الوكيل</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>#</th><th>المورد</th><th>الإجمالي</th><th>الثقة</th><th>الحالة</th><th>إجراء</th></tr></thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td>{{ $inv->id }}</td>
                                <td>{{ $inv->extracted_data['supplier_name'] ?? '-' }}</td>
                                <td>{{ number_format($inv->extracted_data['total_amount'] ?? 0, 2) }}</td>
                                <td>{{ $inv->confidence ?? '-' }}%</td>
                                <td><span class="badge bg-{{ ['pending'=>'warning','posted'=>'success','rejected'=>'danger','approved'=>'info'][$inv->status] }}">{{ $inv->status }}</span></td>
                                <td>
                                    @if($inv->status === 'pending')
                                        <form method="POST" action="{{ route('admin.agent.approve', $inv) }}" class="d-inline">@csrf
                                            <button class="btn btn-sm btn-success">اعتماد</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.agent.reject', $inv) }}" class="d-inline">@csrf
                                            <button class="btn btn-sm btn-outline-danger">رفض</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">لا توجد فواتير</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- القيود --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-text me-2"></i>القيود اليومية</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>الرقم</th><th>التاريخ</th><th>البيان</th><th>المصدر</th><th>الحالة</th><th></th></tr></thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td><code>{{ $e->entry_number }}</code></td>
                                <td>{{ $e->date->format('Y/m/d') }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($e->description, 45) }}</td>
                                <td>
                                    @if($e->by_agent)<span class="badge bg-primary">🤖 الوكيل</span>
                                    @else<span class="badge bg-secondary">يدوي</span>@endif
                                </td>
                                <td><span class="badge bg-{{ $e->status === 'posted' ? 'success' : 'warning' }}">{{ $e->status === 'posted' ? 'مرحّل' : 'مسودة' }}</span></td>
                                <td><a href="{{ route('admin.agent.show', $e) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">لا توجد قيود</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection