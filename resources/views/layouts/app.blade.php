<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — {{ config('app.name', 'نظام المخازن') }}</title>

    <!-- Bootstrap 5 RTL & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Cairo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --sidebar-bg: #0f172a;
            --sidebar-bg-light: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-text-light: #f8fafc;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --sidebar-active-bg: rgba(59, 130, 246, 0.15);
            --sidebar-border: rgba(255, 255, 255, 0.05);
            --navbar-height: 70px;
            --transition-speed: 0.3s;
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

        /* Sidebar */
        .app-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .sidebar-brand .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .sidebar-brand .brand-text {
            flex: 1;
            min-width: 0;
        }

        .sidebar-brand .brand-text h6 {
            color: var(--sidebar-text-light);
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-brand .brand-text small {
            font-size: 0.75rem;
            opacity: 0.7;
            white-space: nowrap;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0.75rem;
            scrollbar-width: thin;
            scrollbar-color: var(--sidebar-hover) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background-color: var(--sidebar-hover);
            border-radius: 10px;
        }

        .menu-category {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            padding: 1.25rem 0.75rem 0.5rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            white-space: nowrap;
        }

        .menu-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .menu-link span {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-link:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-light);
            transform: translateX(-4px);
        }

        .menu-link:hover i {
            transform: scale(1.1);
        }

        .menu-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active);
            font-weight: 600;
        }

        .menu-link.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--sidebar-active);
            border-radius: 4px 0 0 4px;
        }

               /* شارة AI المدمجة — حجم ثابت لا يضغط النص */
                   .badge-ai {
            flex: 0 0 auto;
            font-size: 0.58rem;
            padding: 0.12rem 0.4rem;
            border-radius: 8px;
            letter-spacing: 0.5px;
            color: #fff;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
            animation: pulse-ai 2s ease-in-out infinite;
            line-height: 1.2;
            margin-right: 0;          /* ✅ ثبات على اليسار في RTL */
            margin-inline-start: auto; /* ✅ يدفعها لليسار */
        }

        @keyframes pulse-ai {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(0.92); }
        }

        .menu-badge {
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-user {
            padding: 1rem;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 10px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .user-profile:hover {
            background: var(--sidebar-hover);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-info .name {
            color: var(--sidebar-text-light);
            font-size: 0.9rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info .role {
            font-size: 0.75rem;
            opacity: 0.7;
            white-space: nowrap;
        }

        /* Navbar */
        .app-navbar {
            position: fixed;
            top: 0;
            right: var(--sidebar-width);
            left: 0;
            height: var(--navbar-height);
            background: white;
            padding: 0 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1030;
            transition: all var(--transition-speed) ease;
        }

        .navbar-brand-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #475569;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-btn:hover {
            background: #f1f5f9;
            color: var(--sidebar-active);
        }

        .page-title-section h6 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .page-title-section small {
            font-size: 0.8rem;
            color: #64748b;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            position: relative;
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: #475569;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .notification-btn:hover {
            background: #f1f5f9;
            color: var(--sidebar-active);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 10px;
            font-weight: 700;
            border: 2px solid white;
        }

        .user-dropdown .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Main Content */
        .app-main {
            margin-right: var(--sidebar-width);
            margin-top: var(--navbar-height);
            min-height: calc(100vh - var(--navbar-height));
            padding: 1.5rem;
            transition: all var(--transition-speed) ease;
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1035;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Collapsed State */
        .app-sidebar.collapsed {
            width: var(--sidebar-collapsed-width) !important;
        }

        .app-sidebar.collapsed .sidebar-brand .brand-text,
        .app-sidebar.collapsed .user-info,
        .app-sidebar.collapsed .menu-category,
        .app-sidebar.collapsed .menu-link span,
        .app-sidebar.collapsed .menu-badge,
        .app-sidebar.collapsed .badge-ai {
            display: none !important;
        }

        .app-sidebar.collapsed .sidebar-brand {
            justify-content: center !important;
            padding: 1.5rem 0.5rem !important;
        }

        .app-sidebar.collapsed .menu-link {
            justify-content: center !important;
            padding: 0.75rem !important;
        }

        .app-sidebar.collapsed .user-profile {
            justify-content: center !important;
            padding: 0.75rem !important;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(100%);
            }

            .app-sidebar.show {
                transform: translateX(0);
            }

            .app-navbar {
                right: 0;
            }

            .app-main {
                margin-right: 0;
            }

            .app-sidebar.collapsed {
                width: var(--sidebar-width) !important;
            }

            .app-sidebar.collapsed .sidebar-brand .brand-text,
            .app-sidebar.collapsed .user-info,
            .app-sidebar.collapsed .menu-category,
            .app-sidebar.collapsed .menu-link span,
            .app-sidebar.collapsed .menu-badge,
            .app-sidebar.collapsed .badge-ai {
                display: block !important;
            }

            .app-sidebar.collapsed .sidebar-brand {
                justify-content: flex-start !important;
                padding: 1.5rem !important;
            }

            .app-sidebar.collapsed .menu-link {
                justify-content: flex-start !important;
                padding: 0.75rem 1rem !important;
            }

            .app-sidebar.collapsed .user-profile {
                justify-content: flex-start !important;
                padding: 0.75rem !important;
            }
        }

        @media (max-width: 767.98px) {
            .app-navbar {
                padding: 0 1rem;
            }

            .page-title-section small {
                display: none;
            }

            .app-main {
                padding: 1rem;
            }

            .user-info {
                display: none;
            }
        }

        @media (max-width: 479.98px) {
            .navbar-actions .d-none {
                display: none !important;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .menu-link {
            animation: slideIn 0.3s ease-out;
            animation-fill-mode: both;
        }

        .menu-link:nth-child(1) { animation-delay: 0.05s; }
        .menu-link:nth-child(2) { animation-delay: 0.1s; }
        .menu-link:nth-child(3) { animation-delay: 0.15s; }
        .menu-link:nth-child(4) { animation-delay: 0.2s; }
        .menu-link:nth-child(5) { animation-delay: 0.25s; }
        .menu-link:nth-child(6) { animation-delay: 0.3s; }

        /* الطباعة */
        @media print {
            .app-sidebar, .app-navbar, .sidebar-overlay { display: none !important; }
            .app-main { margin: 0 !important; padding: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div class="brand-text">
            <h6>نظام المخازن</h6>
            <small>إدارة متكاملة ERP</small>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-category">الرئيسية</div>
        <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>لوحة التحكم</span>
        </a>

        @php
            $canViewMaster = auth()->user()->canAny(['view_warehouses', 'view_categories', 'view_units', 'view_items']);
        @endphp
        @if($canViewMaster)
            <div class="menu-category">البيانات الأساسية</div>

            @can('view_warehouses')
            <a href="{{ route('admin.warehouses.index') }}" class="menu-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>المخازن</span>
            </a>
            @endcan

            @can('view_categories')
            <a href="{{ route('admin.categories.index') }}" class="menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i>
                <span>التصنيفات</span>
            </a>
            @endcan

            @can('view_units')
            <a href="{{ route('admin.units.index') }}" class="menu-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>
                <span>الوحدات</span>
            </a>
            @endcan

            @can('view_items')
            <a href="{{ route('admin.items.index') }}" class="menu-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>الأصناف</span>
            </a>
            @endcan
        @endif

        @php
            $canViewOps = auth()->user()->canAny(['view_receipts', 'view_issues', 'view_transfers']);
        @endphp
        @if($canViewOps)
            <div class="menu-category">العمليات</div>

            @can('view_receipts')
            <a href="{{ route('admin.receipts.index') }}" class="menu-link {{ request()->routeIs('admin.receipts.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>سندات الإدخال</span>
            </a>
            @endcan

            @can('view_issues')
            <a href="{{ route('admin.issues.index') }}" class="menu-link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up"></i>
                <span>سندات الإخراج</span>
            </a>
            @endcan

            @can('view_transfers')
            <a href="{{ route('admin.transfers.index') }}" class="menu-link {{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>التحويلات بين المخازن</span>
            </a>
            @endcan
        @endif
        
        {{-- ===== قسم المحاسبة ===== --}}
        <div class="menu-category">المحاسبة</div>

        @if(Route::has('admin.agent.index'))
        <a href="{{ route('admin.agent.index') }}"
           class="menu-link {{ request()->routeIs('admin.agent.*') ? 'active' : '' }}"
           title="الوكيل المحاسبي الذكي">
            <i class="bi bi-robot"></i>
            <span>الوكيل المحاسبي</span>
            <span class="badge-ai">المحاسبي AI </span>
        </a>
        @endif

        @can('view_reports')
            <div class="menu-category">التقارير</div>
            <a href="{{ route('admin.reports.index') }}" class="menu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>التقارير والتحليلات</span>
            </a>
        @endcan

        @php
            $canViewAdmin = auth()->user()->canAny(['view_users', 'view_roles']);
        @endphp
        @if($canViewAdmin)
            <div class="menu-category">الإدارة</div>

            @can('view_users')
            <a href="{{ route('admin.users.index') }}" class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>المستخدمون</span>
            </a>
            @endcan

            @can('view_roles')
            <a href="{{ route('admin.roles.index') }}" class="menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i>
                <span>الأدوار والصلاحيات</span>
            </a>
            @endcan

            @can('view_roles')
            <a href="{{ route('admin.audit_logs.index') }}" class="menu-link {{ request()->routeIs('admin.audit_logs.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>سجل العمليات</span>
            </a>
            @endcan
        @endif
    </div>

    <div class="sidebar-user">
        <div class="user-profile" data-bs-toggle="dropdown">
            <div class="user-avatar">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ auth()->user()->roles->first()->name ?? 'مستخدم' }}</div>
            </div>
            <i class="bi bi-chevron-down" style="color: var(--sidebar-text);"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
            <li>
             <a class="dropdown-item" href="{{ route('profile.edit') }}">
             <i class="bi bi-person me-2"></i>الملف الشخصي
                </a>
             </li>
             <li>
             <a class="dropdown-item" href="{{ route('settings.edit') }}">
                <i class="bi bi-gear me-2"></i>الإعدادات
              </a>
             </li>
          
           
            <li><hr class="dropdown-divider"></li>
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
</aside>

<!-- Navbar -->
<nav class="app-navbar">
    <div class="navbar-brand-section">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="page-title-section">
            <h6>@yield('page-title', 'لوحة التحكم')</h6>
            <small>@yield('page-subtitle', 'مرحباً بك في النظام')</small>
        </div>
    </div>

    <div class="navbar-actions">
        <!-- زر الإشعارات -->
        <button class="notification-btn position-relative" id="notificationBtn" data-bs-toggle="dropdown">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 350px; max-width: 350px;">
            <li class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0 fw-bold">الإشعارات</h6>
                <button class="btn btn-sm btn-link text-decoration-none" id="markAllRead" style="font-size: 0.8rem;">
                    تحديد الكل كمقروء
                </button>
            </li>
            <li id="notificationsList">
                <div class="text-center text-muted py-4" id="loadingNotifications">
                    <i class="bi bi-hourglass-split"></i> جاري التحميل...
                </div>
            </li>
            <li class="p-3 border-top">
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary w-100">
                    عرض جميع الإشعارات
                </a>
            </li>
        </ul>

        <!-- User Dropdown -->
        <div class="user-dropdown dropdown d-none d-md-block">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark" data-bs-toggle="dropdown">
                <div class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                <div class="d-none d-lg-block">
                    <div class="fw-bold small">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        {{ auth()->user()->roles->first()->name ?? 'مستخدم' }}
                    </div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                         <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
            <li><a class="dropdown-item" href="{{ route('settings.edit') }}"><i class="bi bi-gear me-2"></i>الإعدادات</a></li>
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
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

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

<!-- Sidebar Toggle Script -->
<script>
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    });

    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });
</script>

<script>
// تحديث الإشعارات
function fetchNotifications() {
    fetch('{{ route("notifications.unread") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateNotificationsUI(data.notifications, data.count);
    })
    .catch(error => console.error('Error:', error));
}

function updateNotificationsUI(notifications, count) {
    const badge = document.getElementById('notificationBadge');
    const list = document.getElementById('notificationsList');

    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }

    if (notifications.length === 0) {
        list.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-bell-slash" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">لا توجد إشعارات جديدة</p>
            </div>
        `;
        return;
    }

    let html = '';
    notifications.forEach(notification => {
        const typeColors = {
            'info': 'bg-primary',
            'success': 'bg-success',
            'warning': 'bg-warning',
            'danger': 'bg-danger'
        };

        html += `
            <a href="#" class="dropdown-item d-flex align-items-start p-3 notification-item ${notification.is_read ? '' : 'bg-light'}"
               data-id="${notification.id}" data-link="${notification.link}">
                <div class="icon-wrapper ${typeColors[notification.type]} text-white rounded-circle p-2 me-2"
                     style="min-width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi ${notification.icon}"></i>
                </div>
                <div class="flex-grow-1 ms-2" style="min-width: 0;">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1 small fw-bold text-truncate" style="max-width: 200px;">${notification.title}</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">${timeAgo(notification.created_at)}</small>
                    </div>
                    <p class="mb-1 small text-truncate">${notification.message}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted" style="font-size: 0.7rem;">
                            ${new Date(notification.created_at).toLocaleString('ar-SA')}
                        </small>
                        <button class="btn btn-sm btn-link p-0 mark-read" data-id="${notification.id}" style="font-size: 0.7rem;">
                            تحديد كمقروء
                        </button>
                    </div>
                </div>
            </a>
        `;
    });

    list.innerHTML = html;

    document.querySelectorAll('.mark-read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            markAsRead(this.dataset.id);
        });
    });

    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.id;
            const link = this.dataset.link;
            markAsRead(id);
            if (link && link !== '#') {
                window.location.href = link;
            }
        });
    });
}

function markAsRead(id) {
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
            fetchNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

document.getElementById('markAllRead')?.addEventListener('click', function() {
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
            fetchNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
});

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'الآن';
    if (seconds < 3600) return `منذ ${Math.floor(seconds / 60)} دقيقة`;
    if (seconds < 86400) return `منذ ${Math.floor(seconds / 3600)} ساعة`;
    if (seconds < 604800) return `منذ ${Math.floor(seconds / 86400)} يوم`;

    return date.toLocaleDateString('ar-SA');
}

setInterval(fetchNotifications, 30000);

document.addEventListener('DOMContentLoaded', function() {
    fetchNotifications();
});
</script>
</body>
</html>