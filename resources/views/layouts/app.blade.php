<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — {{ config('app.name', 'نظام المخازن') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <!-- ملف تنسيقات الطباعة -->
<link href="{{ asset('css/print.css') }}" rel="stylesheet" media="print">

    <style>
        :root {
            --app-sidebar-w: 270px;
            --app-navbar-h: 64px;
            --app-bg-dark: #0f172a;
            --app-bg-hover: #1e293b;
            --app-text-muted: #94a3b8;
            --app-text-light: #f8fafc;
            --app-accent: #3b82f6;
            --app-accent-soft: rgba(59, 130, 246, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: #f1f5f9;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ==========================================
           القائمة الجانبية الاحترافية
           ========================================== */
        .app-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--app-sidebar-w);
            height: 100vh;
            background: var(--app-bg-dark);
            color: var(--app-text-muted);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
        }

        /* شعار النظام */
        .app-sidebar__brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        .app-sidebar__brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--app-accent), #2563eb);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .app-sidebar__brand-title {
            color: var(--app-text-light);
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .app-sidebar__brand-subtitle {
            font-size: 0.7rem;
            opacity: 0.7;
            margin: 0;
        }

        /* محتوى القائمة */
        .app-sidebar__menu {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--app-bg-hover) transparent;
        }

        .app-sidebar__menu::-webkit-scrollbar {
            width: 5px;
        }

        .app-sidebar__menu::-webkit-scrollbar-thumb {
            background-color: var(--app-bg-hover);
            border-radius: 10px;
        }

        /* عناوين الأقسام */
        .app-sidebar__category {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            padding: 1.25rem 0.75rem 0.5rem;
            margin-top: 0.5rem;
        }

        /* روابط القائمة */
        .app-sidebar__link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            color: var(--app-text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .app-sidebar__link-icon {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .app-sidebar__link:hover {
            background: var(--app-bg-hover);
            color: var(--app-text-light);
        }

        .app-sidebar__link:hover .app-sidebar__link-icon {
            transform: scale(1.1);
        }

        .app-sidebar__link--active {
            background: var(--app-accent-soft);
            color: var(--app-accent);
            font-weight: 600;
        }

        .app-sidebar__link--active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--app-accent);
            border-radius: 4px 0 0 4px;
        }

        /* تذييل القائمة */
        .app-sidebar__footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
            flex-shrink: 0;
        }

        /* ==========================================
           الشريط العلوي
           ========================================== */
        .app-navbar {
            position: fixed;
            top: 0;
            right: var(--app-sidebar-w);
            left: 0;
            height: var(--app-navbar-h);
            background: #fff;
            padding: 0 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1030;
        }

        .app-navbar__toggle {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #475569;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .app-navbar__toggle:hover {
            background: #f1f5f9;
        }

        .app-navbar__title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .app-navbar__subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        .app-navbar__user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #1e293b;
        }

        .app-navbar__avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* ==========================================
           المحتوى الرئيسي
           ========================================== */
        .app-main {
            margin-right: var(--app-sidebar-w);
            margin-top: var(--app-navbar-h);
            min-height: calc(100vh - var(--app-navbar-h));
            padding: 1.5rem;
        }

        /* ==========================================
           التجاوب مع الموبايل
           ========================================== */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .app-sidebar--open {
                transform: translateX(0);
            }

            .app-navbar {
                right: 0;
            }

            .app-main {
                margin-right: 0;
            }

            .app-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
                backdrop-filter: blur(2px);
            }

            .app-overlay--show {
                display: block;
            }
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ========================================== -->
<!-- القائمة الجانبية                           -->
<!-- ========================================== -->
<aside class="app-sidebar" id="appSidebar">
    <div class="app-sidebar__brand">
        <div class="app-sidebar__brand-icon">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div>
            <h6 class="app-sidebar__brand-title">نظام المخازن</h6>
            <small class="app-sidebar__brand-subtitle">إدارة متكاملة ERP</small>
        </div>
    </div>

    <div class="app-sidebar__menu">
        <div class="app-sidebar__category">الرئيسية</div>
        <a href="{{ route('dashboard') }}" class="app-sidebar__link {{ request()->routeIs('dashboard') ? 'app-sidebar__link--active' : '' }}">
            <i class="bi bi-speedometer2 app-sidebar__link-icon"></i>
            <span>لوحة التحكم</span>
        </a>

        @php $canViewMaster = auth()->user()->canAny(['view_warehouses', 'view_categories', 'view_units', 'view_items']); @endphp
        @if($canViewMaster)
            <div class="app-sidebar__category">البيانات الأساسية</div>
            @can('view_warehouses')
            <a href="{{ route('admin.warehouses.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.warehouses.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-building app-sidebar__link-icon"></i>
                <span>المخازن</span>
            </a>
            @endcan
            @can('view_categories')
            <a href="{{ route('admin.categories.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.categories.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-tags-fill app-sidebar__link-icon"></i>
                <span>التصنيفات</span>
            </a>
            @endcan
            @can('view_units')
            <a href="{{ route('admin.units.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.units.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-rulers app-sidebar__link-icon"></i>
                <span>الوحدات</span>
            </a>
            @endcan
            @can('view_items')
            <a href="{{ route('admin.items.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.items.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-box-seam app-sidebar__link-icon"></i>
                <span>الأصناف</span>
            </a>
            @endcan
        @endif

        @php $canViewOps = auth()->user()->canAny(['view_receipts', 'view_issues', 'view_transfers']); @endphp
        @if($canViewOps)
            <div class="app-sidebar__category">العمليات</div>
            @can('view_receipts')
            <a href="{{ route('admin.receipts.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.receipts.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-box-arrow-in-down app-sidebar__link-icon"></i>
                <span>سندات الإدخال</span>
            </a>
            @endcan
            @can('view_issues')
            <a href="{{ route('admin.issues.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.issues.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-box-arrow-up app-sidebar__link-icon"></i>
                <span>سندات الإخراج</span>
            </a>
            @endcan
            @can('view_transfers')
            <a href="{{ route('admin.transfers.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.transfers.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-arrow-left-right app-sidebar__link-icon"></i>
                <span>التحويلات بين المخازن</span>
            </a>
            @endcan
        @endif

        @can('view_reports')
            <div class="app-sidebar__category">التقارير</div>
            <a href="{{ route('admin.reports.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.reports.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-bar-chart-line-fill app-sidebar__link-icon"></i>
                <span>التقارير والتحليلات</span>
            </a>
        @endcan

        @php $canViewAdmin = auth()->user()->canAny(['view_users', 'view_roles']); @endphp
        @if($canViewAdmin)
            <div class="app-sidebar__category">الإدارة</div>
            @can('view_users')
            <a href="{{ route('admin.users.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.users.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-people-fill app-sidebar__link-icon"></i>
                <span>المستخدمون</span>
            </a>
            @endcan
            @can('view_roles')
            <a href="{{ route('admin.roles.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.roles.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-shield-lock-fill app-sidebar__link-icon"></i>
                <span>الأدوار والصلاحيات</span>
            </a>
            @endcan
            @can('view_roles')
            <a href="{{ route('admin.audit_logs.index') }}" class="app-sidebar__link {{ request()->routeIs('admin.audit_logs.*') ? 'app-sidebar__link--active' : '' }}">
                <i class="bi bi-clock-history app-sidebar__link-icon"></i>
                <span>سجل العمليات</span>
            </a>
            @endcan
        @endif
    </div>

    <div class="app-sidebar__footer">
        <small class="text-muted">v1.0.0 &copy; {{ date('Y') }}</small>
    </div>
</aside>

<!-- ========================================== -->
<!-- الشريط العلوي                              -->
<!-- ========================================== -->
<nav class="app-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="app-navbar__toggle" id="appToggleBtn">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <h6 class="app-navbar__title">@yield('page-title', 'لوحة التحكم')</h6>
            <small class="app-navbar__subtitle">@yield('page-subtitle', 'مرحباً بك في النظام')</small>
        </div>
    </div>

    <div class="dropdown">
        <a href="#" class="app-navbar__user" data-bs-toggle="dropdown">
            <div class="app-navbar__avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div class="d-none d-md-block">
                <div class="fw-bold small">{{ auth()->user()->name }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">
                    {{ auth()->user()->roles->first()->name ?? 'مستخدم' }}
                </div>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-start shadow-sm">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<!-- ========================================== -->
<!-- المحتوى الرئيسي                           -->
<!-- ========================================== -->
<div class="app-main">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<!-- طبقة التعتيم للموبايل -->
<div class="app-overlay" id="appOverlay"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

<script>
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('appOverlay');
    const toggleBtn = document.getElementById('appToggleBtn');

    toggleBtn.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.toggle('app-sidebar--open');
            overlay.classList.toggle('app-overlay--show');
        }
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('app-sidebar--open');
        overlay.classList.remove('app-overlay--show');
    });
    @media print {
    .sidebar, .no-print { display: none !important; }
    .main-content { margin-right: 0 !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</script>
</body>
</html>