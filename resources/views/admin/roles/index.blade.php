@extends('layouts.app')
@section('title', 'الأدوار')
@section('page-title', 'الأدوار والصلاحيات')
@section('page-subtitle', 'إدارة أدوار النظام')

@section('content')
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-lock-fill me-2"></i>قائمة الأدوار</span>
        @can('manage_roles')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة دور
        </a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الدور</th>
                    <th>عدد الصلاحيات</th>
                    <th>عدد المستخدمين</th>
                    <th>تاريخ الإنشاء</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td><strong>{{ $role->name }}</strong></td>
                    <td><span class="badge badge-soft-primary">{{ $role->permissions_count }}</span></td>
                    <td><span class="badge badge-soft-success">{{ $role->users_count }}</span></td>
                    <td><small>{{ $role->created_at->format('Y-m-d') }}</small></td>
                    <td class="text-center">
                        @can('manage_roles')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        @if(!in_array($role->name, ['admin','warehouse_manager','store_keeper','user']))
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد أدوار</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection