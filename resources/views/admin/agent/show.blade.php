@extends('layouts.app')

@section('title', 'قيد ' . $entry->entry_number)
@section('page-title', 'القيد: ' . $entry->entry_number)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>{{ $entry->description }}</span>
        <span class="badge bg-{{ $entry->status === 'posted' ? 'success' : 'warning' }}">{{ $entry->status }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead><tr><th>الحساب</th><th>مدين</th><th>دائن</th><th>ملاحظات</th></tr></thead>
            <tbody>
                @foreach($entry->lines as $line)
                    <tr>
                        <td>{{ $line->account->code }} — {{ $line->account->name }}</td>
                        <td>{{ $line->debit ? number_format($line->debit, 2) : '-' }}</td>
                        <td>{{ $line->credit ? number_format($line->credit, 2) : '-' }}</td>
                        <td class="small text-muted">{{ $line->notes ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td>الإجمالي</td>
                    <td>{{ number_format($entry->lines->sum('debit'), 2) }}</td>
                    <td>{{ number_format($entry->lines->sum('credit'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white">
        <a href="{{ route('admin.agent.index') }}" class="btn btn-outline-secondary btn-sm">رجوع</a>
        <a href="{{ route('admin.agent.print', $entry) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-printer me-1"></i>طباعة A4
</a>
    </div>
</div>
@endsection