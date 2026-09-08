@extends('layouts.app')

@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'نظرة عامة على النظام')

@push('styles')
<style>
    /* ==========================================
       المتغيرات الأساسية
       ========================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
        --purple-color: #8b5cf6;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
    }

    body {
        background: var(--bg-color);
        font-family: 'Cairo', sans-serif;
    }

    /* ==========================================
       بطاقة الترحيب العلوية
       ========================================== */
    .welcome-banner {
        background: var(--primary-gradient);
        border-radius: 24px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .welcome-banner .user-avatar {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .welcome-banner .date-time {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
    }

    /* ==========================================
       عناوين الأقسام
       ========================================== */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .section-header i {
        color: var(--primary-color);
    }

    /* ==========================================
       بطاقات الإحصائيات
       ========================================== */
    .stat-box {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        transition: all 0.3s;
        border: 1px solid rgba(0, 0, 0, 0.04);
        height: 100%;
    }

    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .stat-box .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-box .value {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 0.25rem;
    }

    .stat-box .label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 600;
    }

    .stat-box .badge-custom {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
        margin-top: 0.5rem;
        font-weight: 600;
    }

    /* ألوان البطاقات */
    .stat-box.primary .icon-wrapper { background: rgba(102, 126, 234, 0.1); color: #667eea; }
    .stat-box.primary .value { color: #667eea; }
    
    .stat-box.success .icon-wrapper { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stat-box.success .value { color: #10b981; }
    
    .stat-box.warning .icon-wrapper { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-box.warning .value { color: #f59e0b; }
    
    .stat-box.danger .icon-wrapper { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .stat-box.danger .value { color: #ef4444; }
    
    .stat-box.info .icon-wrapper { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-box.info .value { color: #3b82f6; }
    
    .stat-box.purple .icon-wrapper { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .stat-box.purple .value { color: #8b5cf6; }

    /* ==========================================
       بطاقات الرسوم البيانية
       ========================================== */
    .chart-box {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.04);
        height: 100%;
    }

    .chart-box .title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-primary);
    }

    /* ==========================================
       قائمة العمليات الأخيرة
       ========================================== */
    .activity-list {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.3s;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .activity-item:hover {
        background: var(--bg-color);
        transform: translateX(-4px);
    }

    .activity-item:last-child {
        margin-bottom: 0;
    }

    .activity-item .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .activity-item .content {
        flex: 1;
        min-width: 0;
    }

    .activity-item .title {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        color: var(--text-primary);
    }

    .activity-item .meta {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* ==========================================
       الاختصارات السريعة
       ========================================== */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 1rem;
    }

    .quick-action-btn {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s;
        border: 1px solid rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }

    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .quick-action-btn .icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .quick-action-btn .label {
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* ==========================================
       بطاقة التنبيهات
       ========================================== */
    .alert-box {
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: none;
        margin-bottom: 1rem;
    }

    .alert-box .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .alert-box.warning {
        background: rgba(245, 158, 11, 0.1);
    }

    .alert-box.warning .icon {
        background: #f59e0b;
        color: white;
    }

    .alert-box.danger {
        background: rgba(239, 68, 68, 0.1);
    }

    .alert-box.danger .icon {
        background: #ef4444;
        color: white;
    }

    /* ==========================================
       التجاوب مع الجوالات
       ========================================== */
    @media (max-width: 768px) {
        .welcome-banner {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .welcome-banner .user-avatar {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }

        .welcome-banner .date-time {
            padding: 0.75rem;
        }

        .stat-box {
            padding: 1.25rem;
        }

        .stat-box .value {
            font-size: 1.75rem;
        }

        .chart-box {
            padding: 1.25rem;
        }

        .activity-item {
            padding: 0.75rem;
        }

        .quick-actions {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .quick-action-btn {
            padding: 1rem 0.5rem;
        }

        .quick-action-btn .icon {
            width: 44px;
            height: 44px;
            font-size: 1.25rem;
        }

        .quick-action-btn .label {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }

        .section-header {
            font-size: 1rem;
        }
    }

    /* ==========================================
       تأثيرات إضافية
       ========================================== */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-box, .chart-box, .activity-list {
        animation: fadeIn 0.6s ease-out;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- ========================================== -->
    <!-- 1. بطاقة الترحيب العلوية                  -->
    <!-- ========================================== -->
    <div class="welcome-banner">
        <div class="welcome-content d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="small mb-1" style="opacity: 0.9;">مرحباً بك </div>
                    <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                    <span class="badge bg-white bg-opacity-25 mt-1">{{ $user->roles->first()->name ?? 'مستخدم' }}</span>
                    <div class="small mt-2" style="opacity: 0.9;">
                        لديك نظرة شاملة على نظام إدارة المخازن اليوم
                    </div>
                </div>
            </div>
            <div class="date-time">
                <div class="small mb-1">{{ now()->translatedFormat('l') }}</div>
                <div class="h5 mb-0 fw-bold">{{ now()->format('Y/m/d') }}</div>
                <div class="small mt-1">{{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. نظرة عامة (الإحصائيات الأساسية)       -->
    <!-- ========================================== -->
    <h6 class="section-header">
        <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
        نظرة عامة
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-box primary">
                <div class="icon-wrapper">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="value">{{ $stats['users'] }}</div>
                <div class="label">المستخدمون</div>
                <span class="badge-custom" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-arrow-up"></i> نشط
                </span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box warning">
                <div class="icon-wrapper">
                    <i class="bi bi-building-fill"></i>
                </div>
                <div class="value">{{ $stats['warehouses'] }}</div>
                <div class="label">المخازن</div>
                <span class="badge-custom" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-check-circle"></i> فعّال
                </span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box info">
                <div class="icon-wrapper">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="value">{{ $stats['items'] }}</div>
                <div class="label">الأصناف</div>
                <span class="badge-custom" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                    <i class="bi bi-tags"></i> متنوع
                </span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box purple">
                <div class="icon-wrapper">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="value">{{ $stats['transfers'] }}</div>
                <div class="label">التحويلات</div>
                <span class="badge-custom" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-activity"></i> حركة
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. حالة المخزون                           -->
    <!-- ========================================== -->
    <h6 class="section-header">
        <i class="bi bi-boxes text-success"></i>
        حالة المخزون
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-box success">
                <div class="icon-wrapper">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="value">{{ number_format($stockStats['total_quantity'], 0) }}</div>
                <div class="label">إجمالي الكمية</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box warning">
                <div class="icon-wrapper">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="value">{{ $stockStats['low_stock_count'] }}</div>
                <div class="label">منخفض المخزون</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box danger">
                <div class="icon-wrapper">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="value">{{ $stockStats['out_of_stock'] }}</div>
                <div class="label">نفذ من المخزون</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box primary">
                <div class="icon-wrapper">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="value">{{ number_format($stockStats['total_value'], 0) }}</div>
                <div class="label">القيمة الإجمالية</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. الرسوم البيانية                        -->
    <!-- ========================================== -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="chart-box">
                <div class="title">
                    <i class="bi bi-pie-chart-fill text-success"></i>
                    الأصناف الأعلى مخزوناً
                </div>
                <canvas id="topItemsChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="chart-box">
                <div class="title">
                    <i class="bi bi-graph-up text-primary"></i>
                    الحركات خلال آخر 7 أيام
                </div>
                <canvas id="movementsChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 5. حالة التحويلات                         -->
    <!-- ========================================== -->
    <h6 class="section-header">
        <i class="bi bi-arrow-left-right text-info"></i>
        حالة التحويلات
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-box warning text-center">
                <div class="value">{{ $transferStats['draft'] }}</div>
                <div class="label">مسودة</div>
                <small class="text-muted">بانتظار الإرسال</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box info text-center">
                <div class="value">{{ $transferStats['in_transit'] }}</div>
                <div class="label">قيد النقل</div>
                <small class="text-muted">في الطريق</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box success text-center">
                <div class="value">{{ $transferStats['received'] }}</div>
                <div class="label">مستلمة</div>
                <small class="text-muted">تم الاستلام</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box danger text-center">
                <div class="value">{{ $transferStats['cancelled'] }}</div>
                <div class="label">ملغاة</div>
                <small class="text-muted">تم الإلغاء</small>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 6. آخر العمليات                           -->
    <!-- ========================================== -->
    <h6 class="section-header">
        <i class="bi bi-clock-history text-purple"></i>
        آخر العمليات
    </h6>
    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="activity-list">
                <div class="title mb-3">
                    <i class="bi bi-arrow-left-right text-primary"></i>
                    آخر التحويلات
                </div>
                @forelse($recentTransfers as $transfer)
                    <div class="activity-item">
                        <div class="icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div class="content">
                            <div class="title">{{ $transfer->serial }}</div>
                            <div class="meta">
                                {{ $transfer->fromWarehouse->name ?? '-' }} 
                                <i class="bi bi-arrow-left"></i> 
                                {{ $transfer->toWarehouse->name ?? '-' }}
                            </div>
                        </div>
                        @if($transfer->status === 'draft')
                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">مسودة</span>
                        @elseif($transfer->status === 'in_transit')
                            <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">قيد النقل</span>
                        @else
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">مستلم</span>
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
        <div class="col-lg-4 col-md-6">
            <div class="activity-list">
                <div class="title mb-3">
                    <i class="bi bi-box-arrow-in-down-left text-success"></i>
                    آخر سندات الإدخال
                </div>
                @forelse($recentReceipts as $receipt)
                    <div class="activity-item">
                        <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-box-arrow-in-down-left"></i>
                        </div>
                        <div class="content">
                            <div class="title">{{ $receipt->serial }}</div>
                            <div class="meta">{{ $receipt->warehouse->name ?? '-' }}</div>
                        </div>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">{{ $receipt->status }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0 small">لا توجد سندات</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="activity-list">
                <div class="title mb-3">
                    <i class="bi bi-box-arrow-up-right text-danger"></i>
                    آخر سندات الصرف
                </div>
                @forelse($recentIssues as $issue)
                    <div class="activity-item">
                        <div class="icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </div>
                        <div class="content">
                            <div class="title">{{ $issue->serial }}</div>
                            <div class="meta">{{ $issue->warehouse->name ?? '-' }}</div>
                        </div>
                        <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">{{ $issue->status }}</span>
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
    <h6 class="section-header">
        <i class="bi bi-lightning-charge-fill text-warning"></i>
        اختصارات سريعة
    </h6>
    <div class="quick-actions mb-4">
        <a href="{{ route('admin.transfers.create') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="label">تحويل جديد</div>
        </a>
        <a href="{{ route('admin.receipts.create') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <div class="label">سند إدخال</div>
        </a>
        <a href="{{ route('admin.issues.create') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="bi bi-box-arrow-up"></i>
            </div>
            <div class="label">سند صرف</div>
        </a>
        <a href="{{ route('admin.items.index') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="label">الأصناف</div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="label">التقارير</div>
        </a>
        <a href="{{ route('admin.warehouses.index') }}" class="quick-action-btn">
            <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="bi bi-building"></i>
            </div>
            <div class="label">المخازن</div>
        </a>
    </div>

    <!-- ========================================== -->
    <!-- 8. تنبيهات                                -->
    <!-- ========================================== -->
    @if($stockStats['low_stock_count'] > 0 || $transferStats['draft'] > 0)
    <h6 class="section-header">
        <i class="bi bi-bell-fill text-danger"></i>
        تنبيهات
    </h6>
    <div class="row g-3">
        @if($stockStats['low_stock_count'] > 0)
        <div class="col-md-6">
            <div class="alert-box warning">
                <div class="icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold mb-1">أصناف منخفضة المخزون</div>
                    <div class="small">
                        لديك <strong>{{ $stockStats['low_stock_count'] }}</strong> صنف وصل للحد الأدنى
                    </div>
                </div>
                <a href="#" class="btn btn-sm" style="background: #f59e0b; color: white;">عرض</a>
            </div>
        </div>
        @endif

        @if($transferStats['draft'] > 0)
        <div class="col-md-6">
            <div class="alert-box danger">
                <div class="icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold mb-1">تحويلات بانتظار الإرسال</div>
                    <div class="small">
                        لديك <strong>{{ $transferStats['draft'] }}</strong> تحويل في حالة المسودة
                    </div>
                </div>
                <a href="{{ route('admin.transfers.index') }}" class="btn btn-sm" style="background: #ef4444; color: white;">عرض</a>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // رسم بياني: الأصناف الأعلى
    const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
    new Chart(topItemsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($topItems->pluck('name'), JSON_UNESCAPED_UNICODE) !!},
            datasets: [{
                data: {!! json_encode($topItems->pluck('quantity')) !!},
                backgroundColor: [
                    '#667eea', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        padding: 15, 
                        font: { size: 11, family: 'Cairo' },
                        boxWidth: 12
                    }
                }
            },
            cutout: '65%'
        }
    });

    // رسم بياني: الحركات خلال آخر 7 أيام
    const movementsCtx = document.getElementById('movementsChart').getContext('2d');
    new Chart(movementsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($last7Days->pluck('day'), JSON_UNESCAPED_UNICODE) !!},
            datasets: [
                {
                    label: 'سندات الإدخال',
                    data: {!! json_encode($last7Days->pluck('receipts')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'سندات الصرف',
                    data: {!! json_encode($last7Days->pluck('issues')) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'التحويلات',
                    data: {!! json_encode($last7Days->pluck('transfers')) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        padding: 15, 
                        font: { size: 11, family: 'Cairo' },
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Cairo' } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { family: 'Cairo' } }
                }
            }
        }
    });
</script>
@endsection