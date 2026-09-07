@extends('layouts.app')

@section('title', 'المستخدمون')
@section('page-title', 'المستخدمون')
@section('page-subtitle', 'إدارة حسابات النظام')

@section('content')

<div class="table-card">

    {{-- رأس البطاقة --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people-fill me-2"></i>
            قائمة المستخدمين
        </span>

        @can('manage_users')
            <a href="{{ route('admin.users.create') }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>
                إضافة مستخدم
            </a>
        @endcan
    </div>

    {{-- جدول المستخدمين --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">

            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)

                    <tr>
                        <td>{{ $user->id }}</td>

                        <td>
                            <strong>{{ $user->name }}</strong>
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>{{ $user->phone ?? '-' }}</td>

                        <td>
                            <span class="badge badge-soft-primary">
                                {{ $user->roles->first()->name ?? 'بدون' }}
                            </span>
                        </td>

                        <td>
                            @if($user->is_active)
                                <span class="badge badge-soft-success">
                                    <i class="bi bi-check-circle"></i>
                                    نشط
                                </span>
                            @else
                                <span class="badge badge-soft-danger">
                                    <i class="bi bi-x-circle"></i>
                                    معطّل
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            @can('manage_users')

                                {{-- تعديل --}}
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- حذف --}}
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد؟')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif

                            @endcan
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7"
                            class="text-center text-muted py-4">
                            لا يوجد مستخدمون
                        </td>
                    </tr>

                @endforelse
            </tbody>

        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif

</div>

@endsection