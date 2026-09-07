@extends('layouts.app')

@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'نظرة عامة على النظام')

@push('styles')
<style>
    .hero-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .hero-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    .hero-card .content { position: relative; z-index: 2; }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .stat-card .icon-box {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-card .value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .stat-card .label {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.25rem;
    }
    .stat-card .trend {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .gradient-6 { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

    .soft-primary { background: #eff6ff; color: #1e40af; }
    .soft-success { background: #dcfce7; color: #166534; }
    .soft-warning { background: #fef3c7; color: #92400e; }
    .soft-danger { background: #fee2e2; color: #991b1b; }
    .soft-info { background: #dbeafe; color: #1e3a8a; }
    .soft-purple { background: #f3e8ff; color: #6b21a8; }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    .chart-card .card-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item .icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .activity-item .info { flex: 1; min-width: 0; }
    .activity-item .title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.15rem;
    }
    .activity-item .meta {
        font-size: 0.75rem;
        color: #64748b;
    }

    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
        background: white;
        border-radius: 14px;
        text-decoration: none;
        color: #1e293b;
        transition: all 0.3s;
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        color: var(--primary-color);
    }
    .quick-action .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .quick-action .label {
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
    }

    .alert-card {
        border-radius: 14px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: none;
    }
    .alert-card .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- ========================================== -->
    <!-- 1. بطاقة الترحيب (Hero Section)           -->
    <!-- ========================================== -->
    <div class="hero-card mb-4">
        <div class="content d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar" style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="small" style="opacity: 0.8;">مرحباً بك 👋</div>
                        <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                    </div>
                </div>
                <p class="mb-0" style="opacity: 0.9;">
                    <span class="badge bg-white bg-opacity-25 me-1">
                        {{ $user->roles->first()->name ?? 'مستخدم' }}
                    </span>
                    لديك نظرة شاملة على نظام إدارة المخازن اليوم
                </p>
            </div>
            <div class="text-end">
                <div class="small" style="opacity: 0.8;">{{ now()->translatedFormat('l') }}</div>
                <div class="h5 mb-0 fw-bold">{{ now()->format('Y/m/d') }}</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. الإحصائيات الرئيسية                    -->
    <!-- ========================================== -->
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-grid-1x2-fill"></i> نظرة عامة
    </h6>
    <div class="row g-3 mb-4">
        <!-- المستخدمون -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box soft-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="trend soft-success"><i class="bi bi-arrow-up"></i> نشط</span>
                </div>
                <div class="value text-dark">{{ $stats['users'] }}</div>
                <div class="label">المستخدمون</div>
            </div>
        </div>

        <!-- المخازن -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box soft-warning">
                        <i class="bi bi-building-fill"></i>
                    </div>
                    <span class="trend soft-info"><i class="bi bi-check-circle"></i> فعّال</span>
                </div>
                <div class="value text-dark">{{ $stats['warehouses'] }}</div>
                <div class="label">المخازن</div>
            </div>
        </div>

        <!-- الأصناف -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box soft-info">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <span class="trend soft-primary"><i class="bi bi-tags"></i> متنوع</span>
                </div>
                <div class="value text-dark">{{ $stats['items'] }}</div>
                <div class="label">الأصناف</div>
            </div>
        </div>

        <!-- التحويلات -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box soft-purple">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <span class="trend soft-warning"><i class="bi bi-activity"></i> حركة</span>
                </div>
                <div class="value text-dark">{{ $stats['transfers'] }}</div>
                <div class="label">التحويلات</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. إحصائيات المخزون                       -->
    <!-- ========================================== -->
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-boxes"></i> حالة المخزون
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="border-right: 4px solid #10b981;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box soft-success">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <div class="value" style="color: #10b981;">{{ number_format($stockStats['total_quantity'], 0) }}</div>
                        <div class="label">إجمالي الكمية</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="border-right: 4px solid #f59e0b;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box soft-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="value" style="color: #f59e0b;">{{ $stockStats['low_stock_count'] }}</div>
                        <div class="label">منخفض المخزون</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="border-right: 4px solid #ef4444;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box soft-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="value" style="color: #ef4444;">{{ $stockStats['out_of_stock'] }}</div>
                        <div class="label">نفذ من المخزون</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="border-right: 4px solid #3b82f6;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box soft-primary">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="value" style="color: #3b82f6; font-size: 1.5rem;">{{ number_format($stockStats['total_value'], 0) }}</div>
                        <div class="label">القيمة الإجمالية</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. الرسوم البيانية                        -->
    <!-- ========================================== -->
    <div class="row g-3 mb-4">
        <!-- رسم بياني: المخزون حسب المستودع -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="card-title">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    توزيع المخزون على المستودعات
                </div>
                <canvas id="warehousesChart" height="100"></canvas>
            </div>
        </div>

        <!-- رسم بياني: الأصناف الأعلى -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="card-title">
                    <i class="bi bi-pie-chart-fill text-success"></i>
                    الأصناف الأعلى مخزوناً
                </div>
                <canvas id="topItemsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 5. حالة التحويلات                         -->
    <!-- ========================================== -->
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-activity"></i> حالة التحويلات
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card text-center">
                <div class="value text-warning">{{ $transferStats['draft'] }}</div>
                <div class="label">مسودة</div>
                <small class="text-muted">بانتظار الإرسال</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card text-center">
                <div class="value text-info">{{ $transferStats['in_transit'] }}</div>
                <div class="label">قيد النقل</div>
                <small class="text-muted">في الطريق</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card text-center">
                <div class="value text-success">{{ $transferStats['received'] }}</div>
                <div class="label">مستلمة</div>
                <small class="text-muted">تم الاستلام</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card text-center">
                <div class="value text-danger">{{ $transferStats['cancelled'] }}</div>
                <div class="label">ملغاة</div>
                <small class="text-muted">تم الإلغاء</small>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 6. آخر العمليات                           -->
    <!-- ========================================== -->
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-clock-history"></i> آخر العمليات
    </h6>
    <div class="row g-3 mb-4">
        <!-- آخر التحويلات -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="card-title">
                    <i class="bi bi-arrow-left-right text-primary"></i>
                    آخر التحويلات
                </div>
                @forelse($recentTransfers as $transfer)
                    <div class="activity-item">
                        <div class="icon soft-primary">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div class="info">
                            <div class="title">{{ $transfer->serial }}</div>
                            <div class="meta">
                                {{ $transfer->fromWarehouse->name ?? '-' }} 
                                <i class="bi bi-arrow-left"></i> 
                                {{ $transfer->toWarehouse->name ?? '-' }}
                            </div>
                        </div>
                        @if($transfer->status === 'draft')
                            <span class="badge bg-warning">مسودة</span>
                        @elseif($transfer->status === 'in_transit')
                            <span class="badge bg-info">قيد النقل</span>
                        @else
                            <span class="badge bg-success">مستلم</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0 small">لا توجد تحويلات</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- آخر سندات الإدخال -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="card-title">
                    <i class="bi bi-box-arrow-in-down-left text-success"></i>
                    آخر سندات الإدخال
                </div>
                @forelse($recentReceipts as $receipt)
                    <div class="activity-item">
                        <div class="icon soft-success">
                            <i class="bi bi-box-arrow-in-down-left"></i>
                        </div>
                        <div class="info">
                            <div class="title">{{ $receipt->serial }}</div>
                            <div class="meta">{{ $receipt->warehouse->name ?? '-' }}</div>
                        </div>
                        <span class="badge bg-success">{{ $receipt->status }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0 small">لا توجد سندات</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- آخر سندات الصرف -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="card-title">
                    <i class="bi bi-box-arrow-up-right text-danger"></i>
                    آخر سندات الصرف
                </div>
                @forelse($recentIssues as $issue)
                    <div class="activity-item">
                        <div class="icon soft-danger">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </div>
                        <div class="info">
                            <div class="title">{{ $issue->serial }}</div>
                            <div class="meta">{{ $issue->warehouse->name ?? '-' }}</div>
                        </div>
                        <span class="badge bg-danger">{{ $issue->status }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0 small">لا توجد سندات</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 7. اختصارات سريعة                         -->
    <!-- ========================================== -->
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-lightning-charge-fill"></i> اختصارات سريعة
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.transfers.create') }}" class="quick-action">
                <div class="icon soft-primary"><i class="bi bi-plus-circle"></i></div>
                <div class="label">تحويل جديد</div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.receipts.create') }}" class="quick-action">
                <div class="icon soft-success"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="label">سند إدخال</div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.issues.create') }}" class="quick-action">
                <div class="icon soft-danger"><i class="bi bi-box-arrow-up"></i></div>
                <div class="label">سند صرف</div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.items.index') }}" class="quick-action">
                <div class="icon soft-info"><i class="bi bi-box-seam"></i></div>
                <div class="label">الأصناف</div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.reports.index') }}" class="quick-action">
                <div class="icon soft-purple"><i class="bi bi-graph-up"></i></div>
                <div class="label">التقارير</div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('admin.warehouses.index') }}" class="quick-action">
                <div class="icon soft-warning"><i class="bi bi-building"></i></div>
                <div class="label">المخازن</div>
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 8. تنبيهات ذكية                           -->
    <!-- ========================================== -->
    @if($stockStats['low_stock_count'] > 0 || $transferStats['draft'] > 0)
    <h6 class="text-muted fw-bold mb-3">
        <i class="bi bi-bell-fill"></i> تنبيهات
    </h6>
    <div class="row g-3">
        @if($stockStats['low_stock_count'] > 0)
        <div class="col-md-6">
            <div class="alert-card" style="background: #fef3c7;">
                <div class="icon" style="background: #f59e0b; color: white;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color: #92400e;">أصناف منخفضة المخزون</div>
                    <div class="small" style="color: #92400e;">
                        لديك <strong>{{ $stockStats['low_stock_count'] }}</strong> صنف وصل للحد الأدنى
                    </div>
                </div>
                <a href="#" class="btn btn-sm btn-warning">عرض</a>
            </div>
        </div>
        @endif

        @if($transferStats['draft'] > 0)
        <div class="col-md-6">
            <div class="alert-card" style="background: #dbeafe;">
                <div class="icon" style="background: #3b82f6; color: white;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color: #1e3a8a;">تحويلات بانتظار الإرسال</div>
                    <div class="small" style="color: #1e3a8a;">
                        لديك <strong>{{ $transferStats['draft'] }}</strong> تحويل في حالة المسودة
                    </div>
                </div>
                <a href="{{ route('admin.transfers.index') }}" class="btn btn-sm btn-primary">عرض</a>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // رسم بياني: المخزون حسب المستودع
    const warehousesCtx = document.getElementById('warehousesChart').getContext('2d');
    new Chart(warehousesCtx, {
        type: 'bar',
        data: {
            // ✅ تمت إضافة JSON_UNESCAPED_UNICODE هنا
            labels: {!! json_encode($warehousesChart->pluck('name'), JSON_UNESCAPED_UNICODE) !!},
            datasets: [{
                label: 'الكمية',
                data: {!! json_encode($warehousesChart->pluck('quantity')) !!},
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(79, 172, 254, 0.8)',
                    'rgba(67, 233, 123, 0.8)',
                    'rgba(250, 112, 154, 0.8)',
                    'rgba(48, 207, 208, 0.8)',
                ],
                borderRadius: 10,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // رسم بياني: الأصناف الأعلى
    const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
    new Chart(topItemsCtx, {
        type: 'doughnut',
        data: {
            // ✅ تمت إضافة JSON_UNESCAPED_UNICODE هنا أيضاً
            labels: {!! json_encode($topItems->pluck('name'), JSON_UNESCAPED_UNICODE) !!},
            datasets: [{
                data: {!! json_encode($topItems->pluck('quantity')) !!},
                backgroundColor: [
                    '#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endsection