@extends('layouts.app')

@section('page-title', 'الإشعارات')
@section('page-subtitle', 'جميع الإشعارات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-bell-fill text-primary"></i>
                        الإشعارات
                    </h5>
                    <button class="btn btn-sm btn-primary" id="markAllReadBtn">
                        <i class="bi bi-check-all"></i> تحديد الكل كمقروء
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->is_read ? '' : 'bg-light' }} p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="icon-wrapper {{ $notification->type === 'success' ? 'bg-success' : ($notification->type === 'warning' ? 'bg-warning' : ($notification->type === 'danger' ? 'bg-danger' : 'bg-primary')) }} text-white rounded-circle p-2 me-3" 
                                             style="min-width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi {{ $notification->icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-bold">{{ $notification->title }}</h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $notification->created_at->format('Y/m/d H:i') }}
                                                </small>
                                                @if(!$notification->is_read)
                                                    <button class="btn btn-sm btn-outline-primary mark-read" data-id="{{ $notification->id }}">
                                                        <i class="bi bi-check"></i> تحديد كمقروء
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bell-slash" style="font-size: 4rem;"></i>
                            <p class="mt-3 mb-0">لا توجد إشعارات</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.mark-read').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
});

document.getElementById('markAllReadBtn').addEventListener('click', function() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});
</script>
@endsection