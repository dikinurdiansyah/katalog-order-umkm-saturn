<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur Th Sant')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

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
            --orange-bg: #fde8e0;
            --warning-sisa: #e9c46a;
            --warning-bg: #fef3e2;
            --text-dark: #2c1810;
            --text-muted: #8b7355;
            --border-color: #e0d5c9;
        }

        * { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        body {
            background-color: var(--cream-light);
            color: var(--text-dark);
            padding-bottom: 80px;
            overflow-x: hidden;
            margin: 0;
        }

        /* --- ✅ RESPONSIVE CONTAINER --- */
        .app-container {
            max-width: 100%;
            margin: 0 auto;
            background: var(--cream-light);
            min-height: 100vh;
            position: relative;
        }

        /* Tampilan Desktop & Tablet */
        @media (min-width: 768px) {
            body {
                background-color: var(--cream-light);
                padding-bottom: 0 !important;
            }
            .app-container {
                max-width: 100%;
                border-radius: 0;
                margin: 0;
                box-shadow: none;
                border: none;
                overflow-x: hidden;
                min-height: 100vh;
            }
            .bottom-nav { display: none !important; }
            .desktop-nav { display: flex !important; }
        }

        /* --- NAVBAR --- */
        .top-navbar {
            background: var(--cream-light);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
        }

        .top-navbar .logo-section { display: flex; align-items: center; gap: 12px; }
        .top-navbar .logo-icon {
            width: 40px; height: 40px; background: var(--cream); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: 2px solid var(--brown-dark);
        }
        .top-navbar .logo-text { font-size: 1.1rem; font-weight: 800; color: var(--brown-dark); letter-spacing: 1px; }
        .top-navbar .user-icon {
            width: 40px; height: 40px; background: var(--brown-dark); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;
        }

        /* Desktop Nav Links */
        .desktop-nav { display: none; gap: 8px; }
        .desktop-nav-link {
            padding: 8px 16px; border-radius: 8px; text-decoration: none;
            color: var(--text-muted); font-weight: 600; font-size: 0.9rem; transition: all 0.2s;
        }
        .desktop-nav-link:hover, .desktop-nav-link.active { background: var(--cream); color: var(--brown-dark); }

        .back-btn {
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            color: var(--brown-dark); font-size: 1.3rem; text-decoration: none;
        }
        .page-title { font-size: 1.1rem; font-weight: 700; color: var(--brown-dark); letter-spacing: 1px; text-transform: uppercase; }

        /* Bottom Navigation (Mobile Only) */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; background: white;
            border-top: 1px solid var(--border-color); display: flex;
            justify-content: space-around; padding: 8px 0 12px; z-index: 1000;
        }
        .bottom-nav-item {
            display: flex; flex-direction: column; align-items: center;
            text-decoration: none; color: var(--text-muted); font-size: 0.7rem; font-weight: 500; gap: 4px; flex: 1;
        }
        .bottom-nav-item i { font-size: 1.4rem; }
        .bottom-nav-item.active { color: var(--brown-dark); font-weight: 700; }
        .bottom-nav-item.active i { color: var(--brown-dark); }

        /* Buttons & Cards */
        .btn-brown {
            background: var(--brown-dark); color: white; border: none; border-radius: 12px;
            padding: 14px 24px; font-weight: 600; font-size: 0.95rem; transition: all 0.2s;
        }
        .btn-brown:hover { background: var(--brown-medium); color: white; transform: translateY(-1px); }
        .btn-brown-outline {
            background: transparent; color: var(--brown-dark); border: 2px solid var(--brown-dark);
            border-radius: 12px; padding: 12px 24px; font-weight: 600;
        }
        .btn-brown-outline:hover { background: var(--brown-dark); color: white; }
        .card-warm { background: var(--cream); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }

        /* Badges & Utils */
        .badge-stock { background: var(--green-bg); color: var(--green-stock); padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; }
        .badge-sisa { background: var(--warning-bg); color: #b45309; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; }
        .badge-status { background: var(--cream-dark); color: var(--brown-dark); padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; }
        .alert-warm { border: none; border-radius: 12px; padding: 12px 16px; font-size: 0.9rem; }
        .alert-warm-success { background: var(--green-bg); color: var(--green-stock); }
        .alert-warm-error { background: #fde8e8; color: #c53030; }
        .form-control-warm { background: var(--cream); border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 16px; font-size: 0.95rem; }
        .form-control-warm:focus { background: white; border-color: var(--brown-dark); box-shadow: 0 0 0 3px rgba(61, 40, 23, 0.1); outline: none; }
        .qty-control { display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden; background: white; }
        .qty-control button { width: 36px; height: 36px; border: none; background: transparent; color: var(--brown-dark); font-size: 1.1rem; font-weight: 700; }
        .qty-control input { width: 40px; text-align: center; border: none; font-weight: 600; font-size: 0.95rem; }
        .qty-control input:focus { outline: none; }
        .text-brown { color: var(--brown-dark); }
        .section-label { font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
        .divider { border: none; border-top: 1px dashed var(--border-color); margin: 16px 0; }

        /* ✅ PERBAIKAN: PERKECIL FLOATING CART BAR AGAR TIDAK "GEDE AMAT" */
        .bottom-cart-bar, .floating-cart-summary, .sticky-cart-bottom {
            max-width: 500px !important; /* Batasi lebar maksimal agar proporsional */
            margin: 0 auto !important;
            padding: 12px 20px !important;
            border-radius: 16px 16px 0 0 !important;
            box-shadow: 0 -4px 20px rgba(61, 40, 23, 0.1) !important;
        }
        .bottom-cart-bar .cart-info, .floating-cart-summary .cart-info {
            font-size: 0.8rem !important;
            line-height: 1.3 !important;
        }
        .bottom-cart-bar .cart-total, .floating-cart-summary .cart-total {
            font-size: 1.1rem !important;
        }
        .bottom-cart-bar .btn-checkout, .floating-cart-summary .btn-checkout {
            padding: 10px 18px !important;
            font-size: 0.85rem !important;
            border-radius: 10px !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- ✅ LOGIKA DINAMIS LOGO & NAMA USAHA -->
    @php
        $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
        $siteData = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
        $logoUrl = $siteData['logo_url'] ?? null;
        $businessName = $siteData['business_name'] ?? 'DAPUR TH SANT';
    @endphp

    <div class="app-container">
        <!-- Top Navbar -->
        @yield('navbar')

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="px-3 px-md-4 pt-3">
                <div class="alert alert-warm alert-warm-success mb-0">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="px-3 px-md-4 pt-3">
                <div class="alert alert-warm alert-warm-error mb-0">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- ✅ Bottom Navigation (Sembunyikan saat di halaman Detail Produk) -->
        @if(!request()->routeIs('catalog.show'))
        <nav class="bottom-nav">
            <a href="{{ route('catalog.index') }}" class="bottom-nav-item {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap-fill"></i><span>KATALOG</span>
            </a>
            <a href="{{ route('cart.index') }}" class="bottom-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <i class="bi bi-bag-fill"></i><span>KERANJANG</span>
            </a>
            <a href="{{ route('orders.status') }}" class="bottom-nav-item {{ request()->routeIs('orders.status*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i><span>STATUS</span>
            </a>
            <a href="{{ route('contact.index') }}" class="bottom-nav-item {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                <i class="bi bi-at"></i><span>KONTAK</span>
            </a>
        </nav>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
