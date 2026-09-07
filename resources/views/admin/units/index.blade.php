
@extends('layouts.app')

@section('page-title', 'وحدات القياس')
@section('page-subtitle', 'إدارة وحدات القياس')

@section('content')
<div class="container-fluid py-4">

    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="bi bi-speedometer text-primary"></i> وحدات القياس
        </h3>

        @can('manage_units')
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> إضافة وحدة
            </a>
        @endcan
    </div>

    <!-- جدول البيانات -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>قائمة الوحدات</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الكود</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $unit->name }}</strong>
                            </td>

                            <td>
                                <code>{{ $unit->code }}</code>
                            </td>

                            <td>
                                @if($unit->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطّل</span>
                                @endif
                            </td>

                            <td>
                                {{ $unit->created_at?->format('Y-m-d') ?? '-' }}
                            </td>

                            <td class="text-center">
                                @can('manage_units')
                                    <a href="{{ route('admin.units.edit', $unit) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.units.destroy', $unit) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد وحدات قياس مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
            <div class="card-footer bg-white">
                {{ $units->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
