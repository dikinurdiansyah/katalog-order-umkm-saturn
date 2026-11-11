<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brown-dark: #3d2817;
            --brown-medium: #5c3d2e;
            --brown-light: #8b6f47;
            --cream: #f5f0eb;
            --cream-light: #faf7f4;
            --cream-dark: #e8dfd6;
            --green-stock: #2d6a4f;
            --green-bg: #d8f3dc;
            --orange-fav: #e76f51;
            --warning-bg: #fef3e2;
            --text-dark: #2c1810;
            --text-muted: #8b7355;
            --border-color: #e0d5c9;
        }

        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        body { background: var(--cream-light); color: var(--text-dark); }

        /* Top Bar */
        .admin-topbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .status-live {
            background: var(--green-bg);
            color: var(--green-stock);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-live::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--green-stock);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .topbar-search { flex: 1; max-width: 400px; margin: 0 24px; }

        .topbar-search input {
            width: 100%;
            background: var(--cream-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .topbar-search input:focus { outline: none; border-color: var(--brown-dark); }

        .topbar-right { display: flex; align-items: center; gap: 16px; }

        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.9rem; color: var(--brown-dark); }
        .user-id { font-size: 0.7rem; color: var(--text-muted); }

        .user-avatar {
            width: 40px; height: 40px;
            background: var(--brown-dark); color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }

        /* Layout */
        .admin-layout { display: flex; min-height: calc(100vh - 65px); }

        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--border-color);
            padding: 24px 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 0 24px 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
            text-align: center;
        }

        /* ✅ LOGO LEBIH BESAR */
        .sidebar-brand-logo {
            height: 80px;
            width: 80px;
            object-fit: contain;
            margin-bottom: 12px;
            border-radius: 12px;
        }

        .sidebar-brand-placeholder {
            width: 80px;
            height: 80px;
            background: var(--brown-dark);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 2rem;
            font-weight: 700;
        }

        .sidebar-brand-title {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--brown-dark);
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .sidebar-brand-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover { background: var(--cream); }

        .sidebar-menu li a.active {
            background: var(--brown-dark);
            color: white;
            border-left-color: var(--orange-fav);
        }

        .sidebar-menu li a .menu-left { display: flex; align-items: center; gap: 12px; }

        .badge-new {
            background: var(--orange-fav);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Main Content */
        .admin-main { flex: 1; padding: 32px; background: var(--cream-light); }

        /* Cards */
        .admin-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title-custom {
            font-weight: 700;
            font-size: 1rem;
            color: var(--brown-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* KPI Cards */
        .kpi-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            height: 100%;
        }

        .kpi-label { font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px; margin-bottom: 8px; }
        .kpi-value { font-size: 2rem; font-weight: 800; color: var(--brown-dark); margin-bottom: 8px; }
        .kpi-sub { font-size: 0.8rem; color: var(--text-muted); }

        /* Buttons */
        .btn-admin {
            background: var(--brown-dark);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-admin:hover { background: var(--brown-medium); color: white; }

        .btn-admin-outline {
            background: transparent;
            color: var(--brown-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-admin-outline:hover { background: var(--cream); }

        /* Tables */
        .table-admin { background: white; border-radius: 12px; overflow: hidden; }
        .table-admin thead { background: var(--cream); }

        .table-admin th {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 1px;
            border: none;
            padding: 12px 16px;
        }

        .table-admin td { padding: 16px; vertical-align: middle; border-color: var(--border-color); }

        /* Badges */
        .badge-admin { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .badge-pending { background: var(--warning-bg); color: #b45309; }
        .badge-processing { background: #dbeafe; color: #1e40af; }
        .badge-shipping { background: #e0e7ff; color: #3730a3; }
        .badge-completed { background: var(--green-bg); color: var(--green-stock); }
        .badge-cancelled { background: #fde8e8; color: #c53030; }

        /* Tabs */
        .admin-tabs {
            display: flex; gap: 8px; margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color); padding-bottom: 16px;
        }
        .admin-tab {
            padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
            color: var(--text-muted); text-decoration: none; transition: all 0.2s;
        }
        .admin-tab:hover { background: var(--cream); }
        .admin-tab.active { background: var(--brown-dark); color: white; }

        /* Icons & Pagination */
        .bi, [class*="bi-"] { font-size: 1em !important; vertical-align: middle; }
        .pagination { margin-top: 1rem; justify-content: center; }
        .pagination .page-link {
            color: var(--brown-dark); background-color: white;
            border: 1px solid var(--border-color); font-size: 0.85rem; padding: 0.375rem 0.75rem;
        }
        .pagination .page-link:hover { background-color: var(--cream); border-color: var(--brown-dark); }
        .pagination .page-item.active .page-link { background-color: var(--brown-dark); border-color: var(--brown-dark); color: white; }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar { display: none; }
            .admin-main { padding: 16px; }
            .topbar-search { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- ✅ LOGIKA DINAMIS LOGO & NAMA USAHA (DIPERBAIKI) -->
    @php
        $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
        $siteData = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];

        // ✅ PERBAIKAN: Ambil logo dan pastikan tidak ada kata 'storage/'
        $logoUrl = $siteData['logo_url'] ?? null;
        if ($logoUrl) {
            $logoUrl = str_replace('storage/', '', $logoUrl);
        }

        $businessName = $siteData['business_name'] ?? 'Dapur Th Sant';
    @endphp

    <!-- Top Bar -->
    <div class="admin-topbar">
        <div class="topbar-left">
            <div class="status-live">
                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Dapur Buka • Menerima Pesanan
            </div>
        </div>

        <div class="topbar-search">
            <input type="text" placeholder="🔍 Cari pesanan, menu, no handphone...">
        </div>

        <div class="topbar-right">
            <a href="{{ route('catalog.index') }}" class="btn-admin-outline btn-sm" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> Lihat Toko Publik
            </a>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-id">{{ ucfirst(strtolower(auth()->user()->role)) }}</div>
            </div>
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>
    </div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                @if($logoUrl)
                    {{-- ✅ PERBAIKAN: Hapus 'storage/' dari path asset --}}
                    <img src="{{ asset($logoUrl) }}" alt="Logo {{ $businessName }}" class="sidebar-brand-logo">
                @else
                    <div class="sidebar-brand-placeholder">
                        <span class="font-mono">#</span>
                    </div>
                @endif
                <div class="sidebar-brand-title">{{ $businessName }}</div>
                <div class="sidebar-brand-sub">Panel Administrasi</div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <span>Dashboard</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-box-seam"></i>
                            <span>Kelola Produk</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-clipboard-data"></i>
                            <span>Daftar Order</span>
                        </span>
                        <span class="badge-new">BARU</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.promos') }}" class="{{ request()->routeIs('admin.promos*') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-tag"></i>
                            <span>Kelola Promo</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contact') }}" class="{{ request()->routeIs('admin.contact*') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-shop"></i>
                            <span>Kontak & Outlet</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <span class="menu-left">
                            <i class="bi bi-people-fill"></i>
                            <span>Manajemen User</span>
                        </span>
                    </a>
                </li>
            </ul>

            <div class="px-3 pt-4 border-top">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 8px; height: 8px; background: var(--green-stock); border-radius: 50%;"></div>
                    <strong style="font-size: 0.9rem;">{{ auth()->user()->name }}</strong>
                    <span class="badge-admin badge-completed ms-auto">Online</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-admin-outline w-100 btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
