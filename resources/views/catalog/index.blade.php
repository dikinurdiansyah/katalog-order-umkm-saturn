@extends('layouts.app')

@section('title', 'Katalog Menu - ' . ($businessName ?? 'Dapur Th Sant'))

@section('content')
<style>
    :root {
        --brown-dark: #3d2817;
        --brown-medium: #5c3d2e;
        --orange-fav: #e76f51;
        --orange-light: #f4a261;
        --cream: #f5f0e8;
        --cream-light: #faf7f2;
        --cream-dark: #e8e0d5;
        --green-stock: #2a9d8f;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
    }

    body { background-color: var(--cream-light); font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; margin: 0; padding: 0; }

    .container-premium { width: 100%; max-width: 100%; margin: 0 auto; padding: 0 20px; box-sizing: border-box; }
    @media (min-width: 768px) { .container-premium { padding: 0 40px; } }
    @media (min-width: 1200px) { .container-premium { padding: 0 64px; } }
    @media (min-width: 1600px) { .container-premium { padding: 0 96px; } }

    .navbar-premium { background: white; box-shadow: 0 2px 12px rgba(61, 40, 23, 0.08); padding: 12px 0; position: sticky; top: 0; z-index: 1000; }
    .navbar-brand-premium { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .navbar-brand-premium img { height: 45px; width: 45px; object-fit: cover; border-radius: 50%; }
    .navbar-brand-premium .brand-text h6 { margin: 0; font-weight: 800; color: var(--brown-dark); font-size: 1.1rem; }
    .navbar-brand-premium .brand-text small { color: var(--text-muted); font-size: 0.75rem; }
    .nav-link-premium { color: var(--text-dark); font-weight: 600; padding: 10px 20px !important; border-radius: 10px; transition: all 0.2s; text-decoration: none; }
    .nav-link-premium:hover, .nav-link-premium.active { background: var(--brown-dark); color: white !important; }
    .search-bar-premium { position: relative; flex: 1; max-width: 500px; }
    .search-bar-premium input { width: 100%; padding: 12px 16px 12px 44px; border: 1.5px solid var(--border-color); border-radius: 30px; background: var(--cream-light); font-size: 0.9rem; transition: all 0.2s; }
    .search-bar-premium input:focus { outline: none; border-color: var(--brown-dark); background: white; box-shadow: 0 0 0 4px rgba(61, 40, 23, 0.1); }
    .search-bar-premium i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .icon-btn-premium { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--cream); color: var(--brown-dark); border: none; position: relative; transition: all 0.2s; text-decoration: none; }
    .icon-btn-premium:hover { background: var(--brown-dark); color: white; transform: translateY(-2px); }
    .badge-cart { position: absolute; top: -5px; right: -5px; background: var(--orange-fav); color: white; font-size: 0.7rem; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }

    .hero-premium { background: linear-gradient(135deg, var(--cream) 0%, var(--cream-light) 100%); border-radius: 24px; padding: 40px 32px; margin: 24px 0; display: flex; align-items: center; gap: 32px; overflow: hidden; position: relative; width: 100%; box-sizing: border-box; }
    @media (min-width: 768px) { .hero-premium { padding: 60px 48px; gap: 48px; margin: 32px 0; } }
    @media (min-width: 1200px) { .hero-premium { padding: 80px 64px; gap: 64px; } }
    .hero-premium::before { content: ''; position: absolute; right: -60px; top: -60px; width: 240px; height: 240px; background: rgba(61, 40, 23, 0.06); border-radius: 50%; }
    .hero-premium::after { content: ''; position: absolute; right: 80px; bottom: -80px; width: 300px; height: 300px; background: rgba(61, 40, 23, 0.04); border-radius: 50%; }
    .hero-content { flex: 1; position: relative; z-index: 2; }
    .hero-badge { display: inline-flex; align-items: center; gap: 6px; background: white; color: var(--green-stock); padding: 8px 16px; border-radius: 25px; font-size: 0.85rem; font-weight: 700; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .hero-title { font-size: 2.2rem; font-weight: 900; color: var(--brown-dark); line-height: 1.1; margin-bottom: 20px; }
    @media (min-width: 768px) { .hero-title { font-size: 2.8rem; } }
    @media (min-width: 1200px) { .hero-title { font-size: 3.2rem; } }
    .hero-title span { color: var(--orange-fav); }
    .hero-desc { color: var(--text-muted); font-size: 1rem; margin-bottom: 28px; line-height: 1.7; max-width: 520px; }
    .hero-features { display: flex; gap: 24px; margin-bottom: 28px; flex-wrap: wrap; }
    .hero-feature { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-dark); font-weight: 600; }
    .btn-pesan-premium { background: var(--brown-dark); color: white; padding: 16px 36px; border-radius: 14px; font-weight: 700; border: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s; box-shadow: 0 6px 20px rgba(61, 40, 23, 0.25); text-decoration: none; }
    .btn-pesan-premium:hover { background: var(--brown-medium); color: white; transform: translateY(-3px); box-shadow: 0 10px 30px rgba(61, 40, 23, 0.3); }
    .hero-image { flex: 1; position: relative; z-index: 2; min-width: 0; }
    @media (min-width: 992px) { .hero-image { flex: 0 0 45%; } }
    .hero-image img { width: 100%; border-radius: 24px; object-fit: cover; height: 300px; box-shadow: 0 20px 60px rgba(61, 40, 23, 0.2); }
    @media (min-width: 768px) { .hero-image img { height: 350px; } }
    @media (min-width: 1200px) { .hero-image img { height: 400px; } }
    .hero-image-badge { position: absolute; top: 20px; left: 20px; display: flex; flex-direction: column; gap: 10px; }
    .badge-best { background: var(--orange-fav); color: white; padding: 8px 16px; border-radius: 25px; font-size: 0.8rem; font-weight: 800; box-shadow: 0 4px 12px rgba(231, 111, 81, 0.3); }
    .badge-fresh { background: var(--green-stock); color: white; padding: 8px 16px; border-radius: 25px; font-size: 0.8rem; font-weight: 800; box-shadow: 0 4px 12px rgba(42, 157, 143, 0.3); }
    .hero-image-info { position: absolute; bottom: 20px; left: 20px; right: 20px; background: rgba(31, 41, 55, 0.9); color: white; padding: 16px 20px; border-radius: 16px; backdrop-filter: blur(10px); }
    .hero-image-info h6 { margin: 0; font-weight: 800; font-size: 1rem; margin-bottom: 4px; }
    .hero-image-info small { opacity: 0.85; font-size: 0.8rem; display: block; margin-bottom: 8px; }
    .hero-image-info .price { font-weight: 900; font-size: 1.3rem; color: var(--orange-light); }

    .filter-bar-premium { display: flex; align-items: center; gap: 20px; margin: 32px 0; flex-wrap: wrap; }
    .search-main { flex: 1; position: relative; min-width: 300px; }
    .search-main input { width: 100%; padding: 16px 24px 16px 52px; border: 2px solid var(--border-color); border-radius: 16px; background: white; font-size: 1rem; transition: all 0.2s; }
    .search-main input:focus { outline: none; border-color: var(--brown-dark); box-shadow: 0 0 0 4px rgba(61, 40, 23, 0.1); }
    .search-main i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; }
    .sort-select { padding: 16px 24px; border: 2px solid var(--border-color); border-radius: 16px; background: white; font-size: 0.95rem; font-weight: 600; min-width: 200px; cursor: pointer; }
    .category-pills-premium { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 32px; scrollbar-width: thin; scrollbar-color: var(--cream-dark) transparent; }
    .category-pills-premium::-webkit-scrollbar { height: 6px; }
    .category-pills-premium::-webkit-scrollbar-thumb { background: var(--cream-dark); border-radius: 3px; }
    .pill-premium { padding: 12px 24px; border-radius: 30px; background: white; border: 2px solid var(--border-color); color: var(--text-dark); font-weight: 700; font-size: 0.95rem; white-space: nowrap; cursor: pointer; transition: all 0.3s; text-decoration: none; }
    .pill-premium:hover, .pill-premium.active { background: var(--brown-dark); color: white; border-color: var(--brown-dark); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(61, 40, 23, 0.2); }

    .section-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 3px solid var(--border-color); }
    .section-title-premium { font-size: 1.5rem; font-weight: 900; color: var(--brown-dark); display: flex; align-items: center; gap: 10px; }
    .section-title-premium::before { content: ''; width: 10px; height: 10px; background: var(--orange-fav); border-radius: 50%; box-shadow: 0 0 0 4px rgba(231, 111, 81, 0.2); }
    .section-subtitle { color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
    .product-card-premium { background: white; border-radius: 20px; overflow: hidden; border: 2px solid var(--border-color); transition: all 0.3s; height: 100%; display: flex; flex-direction: column; position: relative; text-decoration: none; color: inherit; }
    .product-card-premium:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(61, 40, 23, 0.15); border-color: var(--brown-dark); }
    .product-image-premium { position: relative; height: 200px; overflow: hidden; background: var(--cream-dark); }
    .product-image-premium img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .product-card-premium:hover .product-image-premium img { transform: scale(1.08); }
    .product-badge-premium { position: absolute; top: 14px; left: 14px; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .badge-bestseller { background: var(--orange-fav); } .badge-favorit { background: #f4a261; } .badge-crispy { background: var(--green-stock); }
    .product-stock-badge { position: absolute; bottom: 14px; right: 14px; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: rgba(255,255,255,0.95); color: var(--green-stock); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .product-stock-badge.low { color: var(--orange-fav); background: rgba(255, 243, 224, 0.95); }
    .product-stock-badge.habis { background: #e63946; color: white; }
    .product-body-premium { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .product-name-premium { font-weight: 800; font-size: 1.05rem; color: var(--brown-dark); margin-bottom: 8px; line-height: 1.3; }
    .product-desc-premium { color: var(--text-muted); font-size: 0.85rem; line-height: 1.5; margin-bottom: 14px; flex: 1; }
    .product-rating-premium { display: flex; align-items: center; gap: 5px; font-size: 0.85rem; color: var(--orange-light); margin-bottom: 14px; font-weight: 700; }
    .product-footer-premium { display: flex; justify-content: space-between; align-items: center; }
    .product-price-premium { font-weight: 900; font-size: 1.2rem; color: var(--orange-fav); }
    .btn-tambah-premium { background: var(--brown-dark); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; transition: all 0.2s; cursor: pointer; }
    .btn-tambah-premium:hover { background: var(--brown-medium); color: white; transform: scale(1.05); }
    .btn-habis { background: var(--cream-dark); color: var(--text-muted); border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: not-allowed; }

    /* ✅ DIPERBAIKI: FLOATING CART DIKECILKAN AGAR TIDAK "GEDE AMAT" */
    .floating-cart-premium {
        position: fixed;
        bottom: 32px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(61, 40, 23, 0.2);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        z-index: 999;
        max-width: 90%;
        width: auto;
        border: 2px solid var(--border-color);
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        max-width: 500px;
    }
    .floating-cart-icon {
        width: 48px;
        height: 48px;
        background: var(--brown-dark);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        position: relative;
    }
    .floating-cart-info h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--brown-dark);
    }
    .floating-cart-info .price {
        font-weight: 800;
        font-size: 1.2rem;
        color: var(--orange-fav);
    }
    .floating-cart-info small {
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 500;
    }
    .btn-keranjang-premium {
        background: var(--brown-dark);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-keranjang-premium:hover {
        background: var(--brown-medium);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(61, 40, 23, 0.3);
    }

    .features-section-premium { background: white; border-radius: 24px; padding: 32px; margin: 48px 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; box-shadow: 0 8px 32px rgba(61, 40, 23, 0.06); }
    @media (min-width: 1200px) { .features-section-premium { padding: 48px; gap: 48px; } }
    .feature-item-premium { display: flex; gap: 20px; align-items: flex-start; }
    .feature-icon-premium { width: 56px; height: 56px; background: var(--cream); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
    .feature-item-premium h6 { font-weight: 800; color: var(--brown-dark); margin-bottom: 6px; font-size: 1.05rem; }
    .feature-item-premium p { color: var(--text-muted); font-size: 0.9rem; margin: 0; line-height: 1.6; }

    .footer-premium { background: var(--brown-dark); color: white; padding: 56px 0 32px; margin-top: 64px; }
    .footer-brand { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
    .footer-brand img { height: 48px; width: 48px; border-radius: 50%; object-fit: cover; }
    .footer-brand h5 { margin: 0; font-weight: 900; font-size: 1.2rem; }
    .footer-desc { color: rgba(255,255,255,0.75); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px; }
    .footer-title { font-weight: 800; font-size: 1.1rem; margin-bottom: 20px; }
    .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 10px; }
    .footer-links a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.9rem; transition: color 0.2s; font-weight: 500; }
    .footer-links a:hover { color: white; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 40px; padding-top: 28px; display: flex; justify-content: space-between; color: rgba(255,255,255,0.5); font-size: 0.85rem; }
    .empty-state-premium { text-align: center; padding: 80px 20px; }
    .empty-state-premium i { font-size: 5rem; color: var(--cream-dark); margin-bottom: 20px; }
    .empty-state-premium h5 { color: var(--text-muted); margin-bottom: 10px; font-weight: 700; }

    @media (max-width: 991px) {
        .hero-premium { flex-direction: column; text-align: center; }
        .hero-features { justify-content: center; }
        .hero-desc { margin: 0 auto 28px auto; }
        .hero-image { width: 100%; margin-top: 24px; }
        .features-section-premium { grid-template-columns: 1fr; }
        .floating-cart-premium { flex-direction: column; text-align: center; width: 90%; }
    }

    @media (max-width: 767px) {
        .navbar-premium .icon-btn-premium { display: none !important; }
        .container-premium { padding: 0 12px !important; }
        .row.g-4 { margin: 0 -6px !important; }
        .col-6 { padding: 0 6px !important; margin-bottom: 12px !important; }
        .hero-title { font-size: 1.6rem !important; }
        .hero-premium { padding: 24px 16px !important; margin: 16px 0 !important; }
        .hero-image img { height: 220px !important; }
        .product-card-premium { margin-bottom: 12px !important; border-radius: 12px !important; }
        .product-image-premium { height: 140px !important; }
        .product-body-premium { padding: 10px !important; }
        .product-name-premium { font-size: 0.85rem !important; min-height: 2.4em !important; }
        .product-desc-premium { display: none !important; }
        .product-rating-premium { font-size: 0.7rem !important; margin-bottom: 8px !important; }
        .product-price-premium { font-size: 1rem !important; }
        .product-footer-premium { flex-direction: column !important; align-items: stretch !important; gap: 8px !important; }
        .product-footer-premium form, .product-footer-premium .btn-habis { width: 100% !important; }
        .btn-tambah-premium { width: 100% !important; padding: 10px 12px !important; font-size: 0.85rem !important; min-height: 40px !important; border-radius: 8px !important; justify-content: center !important; white-space: nowrap !important; }
        .nav-link-premium { padding: 8px 12px !important; font-size: 0.85rem; }
        .footer-premium { padding-bottom: 80px !important; }

        /* ✅ FLOATING CART MOBILE - LEBIH COMPACT */
        .floating-cart-premium {
            bottom: 88px !important;
            left: 12px !important;
            right: 12px !important;
            transform: none !important;
            max-width: calc(100% - 24px) !important;
            padding: 14px 18px !important;
            flex-direction: row !important;
            text-align: left !important;
        }
        .floating-cart-icon {
            width: 42px !important;
            height: 42px !important;
            font-size: 1.1rem !important;
        }
        .floating-cart-info h6 {
            font-size: 0.85rem !important;
        }
        .floating-cart-info .price {
            font-size: 1.1rem !important;
        }
        .floating-cart-info small {
            display: none !important; /* Sembunyikan teks estimasi di mobile */
        }
        .btn-keranjang-premium {
            padding: 10px 18px !important;
            font-size: 0.85rem !important;
        }
    }

    @media (max-width: 480px) {
        .search-bar-premium { display: none; }
        .hero-title { font-size: 1.4rem !important; }
    }

    .bottom-nav-mobile { position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 2px solid var(--border-color); display: none; justify-content: space-around; padding: 8px 0 12px; z-index: 1000; box-shadow: 0 -4px 12px rgba(0,0,0,0.08); }
    @media (max-width: 767px) { .bottom-nav-mobile { display: flex !important; } }
    .bottom-nav-item-mobile { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: var(--text-muted); font-size: 0.65rem; font-weight: 600; gap: 4px; flex: 1; padding: 4px 8px; min-height: 56px; }
    .bottom-nav-item-mobile i { font-size: 1.4rem; }
    .bottom-nav-item-mobile.active { color: var(--brown-dark); font-weight: 700; }
    .bottom-nav-item-mobile.active i { color: var(--brown-dark); }
</style>

<nav class="navbar-premium">
    <div class="container-premium">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <a href="{{ route('catalog.index') }}" class="navbar-brand-premium">
                @if($logoUrl)
                    <img src="{{ asset($logoUrl) }}" alt="{{ $businessName }}">
                @else
                    <div style="width:45px;height:45px;background:var(--brown-dark);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:900;font-size:1.2rem;">DTS</div>
                @endif
                <div class="brand-text">
                    <h6>{{ $businessName }}</h6>
                    <small>Dimsum & Kuliner UMKM</small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2 d-none d-lg-flex">
                <a href="{{ route('catalog.index') }}" class="nav-link-premium {{ request()->routeIs('catalog.*') ? 'active' : '' }}">Katalog</a>
                <a href="{{ route('cart.index') }}" class="nav-link-premium {{ request()->routeIs('cart.*') ? 'active' : '' }}">Keranjang</a>
                <a href="{{ route('orders.status') }}" class="nav-link-premium {{ request()->routeIs('orders.status*') ? 'active' : '' }}">Status Order</a>
                <a href="{{ route('contact.index') }}" class="nav-link-premium {{ request()->routeIs('contact.*') ? 'active' : '' }}">Kontak Kami</a>
            </div>

            <form action="{{ route('catalog.index') }}" method="GET" class="search-bar-premium d-none d-md-block">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu dimsum...">
            </form>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cart.index') }}" class="icon-btn-premium">
                    <i class="bi bi-bag"></i>
                    @php
                        $navCartCount = 0;
                        $navCart = session('cart', []);
                        foreach($navCart as $item) { $navCartCount += intval($item['quantity'] ?? 0); }
                    @endphp
                    @if($navCartCount > 0)
                        <span class="badge-cart">{{ $navCartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('login') }}" class="icon-btn-premium">
                    <i class="bi bi-person"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-premium">
    @php $heroProduct = $products->first(); @endphp
    @if($heroProduct)
    <div class="hero-premium">
        <div class="hero-content">
            @php
                $heroCatName = strtolower($heroProduct->category->name ?? '');
                $heroBadgeText = 'Kukus Dadakan Fresh Setiap Pagi';
                $heroBadgeIcon = 'bi-fire';

                if (str_contains($heroCatName, 'minuman')) {
                    $heroBadgeText = 'Fresh & Dingin Siap Saji';
                    $heroBadgeIcon = 'bi-snow';
                } elseif (str_contains($heroCatName, 'goreng') || str_contains($heroCatName, 'crispy')) {
                    $heroBadgeText = 'Crispy Fresh Setiap Hari';
                    $heroBadgeIcon = 'bi-fire';
                }
            @endphp
            <div class="hero-badge"><i class="bi {{ $heroBadgeIcon }}"></i> {{ $heroBadgeText }}</div>
            <h1 class="hero-title">
                @if(str_contains($heroCatName, 'minuman'))
                    Minuman Fresh & Dingin<br><span>Siap Segarkan Hari Ini</span>
                @elseif(str_contains($heroCatName, 'goreng') || str_contains($heroCatName, 'crispy'))
                    Gorengan Crispy Fresh<br><span>Siap Kirim Hari Ini</span>
                @else
                    Dimsum Fresh Kukus<br><span>Siap Kirim Hari Ini</span>
                @endif
            </h1>
            <p class="hero-desc">Dibuat fresh setiap pagi dengan daging ayam asli & rempah pilihan. 100% Halal, higienis, dan tanpa pengawet dengan garansi hangat sampai meja Anda.</p>
            <div class="hero-features">
                <div class="hero-feature"><i class="bi bi-patch-check-fill text-success"></i> 100% Halal MUI</div>
                <div class="hero-feature"><i class="bi bi-fire text-danger"></i> Chili Oil Khas Gurih</div>
                <div class="hero-feature"><i class="bi bi-truck text-primary"></i> Pengiriman Instant</div>
            </div>
            <a href="#menu" class="btn-pesan-premium">Pesan Sekarang <i class="bi bi-arrow-down"></i></a>
            <small class="ms-3 text-muted"><i class="bi bi-circle-fill text-success" style="font-size:8px;"></i> 28 Porsi Siap Dikukus Menit Ini</small>
        </div>
        <div class="hero-image">
            @if($heroProduct->image_url)
                <img src="{{ asset($heroProduct->image_url) }}" alt="{{ $heroProduct->name }}">
            @else
                <div style="width:100%;height:300px;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;border-radius:24px;">
                    <i class="bi bi-image" style="font-size:5rem;color:var(--text-muted);"></i>
                </div>
            @endif
            <div class="hero-image-badge">
                <span class="badge-best"><i class="bi bi-star-fill"></i> Best Seller #1</span>
                @php
                    $heroBadgeText = 'Kukus Dadakan';
                    $heroBadgeIcon = 'bi-fire';

                    if (str_contains($heroCatName, 'minuman')) {
                        $heroBadgeText = 'Fresh & Dingin';
                        $heroBadgeIcon = 'bi-snow';
                    } elseif (str_contains($heroCatName, 'goreng') || str_contains($heroCatName, 'crispy')) {
                        $heroBadgeText = 'Crispy Fresh';
                        $heroBadgeIcon = 'bi-fire';
                    }
                @endphp
                <span class="badge-fresh"><i class="bi {{ $heroBadgeIcon }}"></i> {{ $heroBadgeText }}</span>
            </div>
            <div class="hero-image-info">
                <h6>{{ $heroProduct->name }}</h6>
                <small>{{ Str::limit($heroProduct->description, 50) }}</small>
                <div class="price">Rp{{ number_format($heroProduct->price, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('catalog.index') }}" method="GET">
        <div class="filter-bar-premium">
            <div class="search-main">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siomay, mentai, hakau, katering...">
            </div>
            <select name="sort" class="sort-select" onchange="this.form.submit()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Urutkan: Paling Populer</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
            </select>
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
        </div>
    </form>

    <div class="category-pills-premium">
        <a href="{{ route('catalog.index') }}" class="pill-premium {{ !request('category') ? 'active' : '' }}">Semua Menu ({{ $products->total() }})</a>
        @foreach($categories as $cat)
            <a href="{{ route('catalog.index', ['category' => $cat->id]) }}" class="pill-premium {{ request('category') == $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
        @endforeach
    </div>

    <div class="section-header-premium" id="menu">
        <h2 class="section-title-premium">Pilihan Menu Terlaris</h2>
        <span class="section-subtitle">Menampilkan {{ $products->count() }} hidangan siap saji</span>
    </div>

    @if($products->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('catalog.show', $product) }}" class="product-card-premium">
                    <div class="product-image-premium">
                        @if($product->image_url)
                            <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}">
                        @else
                            <div style="width:100%;height:100%;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-image" style="font-size:4rem;color:var(--text-muted);"></i>
                            </div>
                        @endif

                        {{-- ✅ DIPERBAIKI: Badge sekarang PINTAR, menyesuaikan dengan Nama Kategori, BUKAN random --}}
                        @php
                            $badgeText = '';
                            $badgeClass = '';
                            $catName = strtolower($product->category->name ?? '');

                            if (str_contains($catName, 'crispy') || str_contains($catName, 'goreng')) {
                                $badgeText = 'Crispy Renyah';
                                $badgeClass = 'badge-crispy';
                            } elseif (str_contains($catName, 'best') || str_contains($catName, 'terlaris')) {
                                $badgeText = 'Best Seller';
                                $badgeClass = 'badge-bestseller';
                            } elseif (str_contains($catName, 'favorit')) {
                                $badgeText = 'Favorit';
                                $badgeClass = 'badge-favorit';
                            } elseif ($product->stock <= 10 && $product->stock > 0) {
                                $badgeText = 'Segera Habis!';
                                $badgeClass = 'badge-favorit';
                            }
                        @endphp

                        @if($badgeText && $product->stock > 0)
                            <span class="product-badge-premium {{ $badgeClass }}">{{ $badgeText }}</span>
                        @endif

                        @if($product->stock <= 10 && $product->stock > 0)
                            <span class="product-stock-badge low"><i class="bi bi-exclamation-triangle-fill"></i> Sisa {{ $product->stock }}</span>
                        @elseif($product->stock == 0)
                            <span class="product-stock-badge habis"><i class="bi bi-x-circle-fill"></i> Habis Terjual</span>
                        @else
                            <span class="product-stock-badge"><i class="bi bi-check-circle-fill"></i> Tersedia ({{ $product->stock }})</span>
                        @endif
                    </div>

                    <div class="product-body-premium">
                        <h6 class="product-name-premium">{{ $product->name }}</h6>
                        <p class="product-desc-premium">{{ Str::limit($product->description ?? 'Dimsum fresh dibuat setiap pagi dengan bahan pilihan berkualitas.', 60) }}</p>

                        <div class="product-rating-premium">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                            <span style="color:var(--text-muted);margin-left:4px;">4.8</span>
                        </div>

                        <div class="product-footer-premium">
                            <div>
                                <div class="product-price-premium">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                            </div>
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add') }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-tambah-premium">
                                        <i class="bi bi-plus"></i> Tambah
                                    </button>
                                </form>
                            @else
                                <button class="btn-habis" disabled><i class="bi bi-x-circle"></i> Habis</button>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center my-5">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-state-premium">
            <i class="bi bi-emoji-frown"></i>
            <h5>Menu tidak ditemukan</h5>
            <p>Coba kata kunci lain atau pilih kategori berbeda</p>
            <a href="{{ route('catalog.index') }}" class="btn-pesan-premium mt-4">Lihat Semua Menu</a>
        </div>
    @endif

    <div class="features-section-premium">
        <div class="feature-item-premium">
            <div class="feature-icon-premium">🔥</div>
            <div>
                <h6>Kukus & Kirim Hangat</h6>
                <p>Pesanan dikukus langsung saat order masuk dan dikemas thermal khusus agar tetap nikmat saat disantap.</p>
            </div>
        </div>
        <div class="feature-item-premium">
            <div class="feature-icon-premium">🌿</div>
            <div>
                <h6>Tanpa Pengawet & MSG</h6>
                <p>Daging ayam segar pilihan diolah bersama kaldu asli alami, aman dan sehat untuk seluruh anggota keluarga.</p>
            </div>
        </div>
        <div class="feature-item-premium">
            <div class="feature-icon-premium">🎉</div>
            <div>
                <h6>Pesanan Acara & Katering</h6>
                <p>Siap melayani pesanan porsi besar untuk arisan, meeting kantor, dan ulang tahun dengan harga katering khusus.</p>
            </div>
        </div>
    </div>
</div>

{{-- ✅ FLOATING CART BAR - SUDAH DIKECILKAN --}}
@php
    $cart = session('cart', []);
    $cartCount = 0;
    $cartTotal = 0;

    foreach($cart as $item) {
        $qty = intval($item['quantity'] ?? 0);
        $price = floatval($item['price'] ?? 0);
        $cartCount += $qty;
        $cartTotal += ($price * $qty);
    }
@endphp

@if($cartCount > 0)
<div class="floating-cart-premium" onclick="window.location.href='{{ route('cart.index') }}'">
    <div class="floating-cart-icon">
        <i class="bi bi-bag-fill"></i>
        <span class="badge-cart" style="position:absolute;top:-8px;right:-8px;font-size:0.75rem;width:24px;height:24px;">{{ $cartCount }}</span>
    </div>
    <div class="floating-cart-info">
        <h6>{{ $cartCount }} Menu Dipilih</h6>
        <div class="price">Rp{{ number_format($cartTotal, 0, ',', '.') }}</div>
        <small><i class="bi bi-truck"></i> Pengiriman ke Sentra Kuliner • Estimasi 25-35 Menit</small>
    </div>
    <div class="btn-keranjang-premium">Lihat Keranjang <i class="bi bi-arrow-right"></i></div>
</div>
@endif

<footer class="footer-premium">
    <div class="container-premium">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    @if($logoUrl)
                        <img src="{{ asset($logoUrl) }}" alt="{{ $businessName }}">
                    @else
                        <div style="width:48px;height:48px;background:var(--orange-fav);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:900;font-size:1.3rem;">DTS</div>
                    @endif
                    <h5>{{ $businessName }}</h5>
                </div>
                <p class="footer-desc">Sajian Dimsum halal, fresh, dan aneka kuliner UMKM pilihan khas nusantara dibuat dengan resep otentik penuh kehangatan.</p>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-patch-check-fill text-success"></i>
                    <small style="color:rgba(255,255,255,0.75);">100% Halal & Higienis</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title">Navigasi</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('catalog.index') }}">Katalog Menu</a></li>
                    <li><a href="{{ route('cart.index') }}">Keranjang Pesanan</a></li>
                    <li><a href="{{ route('orders.status') }}">Lacak Status Order</a></li>
                    <li><a href="{{ route('contact.index') }}">Kontak & Lokasi</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-title">Jam Operasional</h6>
                <ul class="footer-links">
                    <li class="d-flex justify-content-between"><span>Senin - Jumat</span><span>09:00 - 21:00</span></li>
                    <li class="d-flex justify-content-between"><span>Sabtu - Minggu</span><span>08:30 - 22:00</span></li>
                    <li class="mt-2"><i class="bi bi-circle-fill text-success" style="font-size:8px;"></i> <span style="color:rgba(255,255,255,0.75);">Dapur Buka (Menerima Pesanan)</span></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-title">Dukungan & Bantuan</h6>
                <p style="color:rgba(255,255,255,0.75);font-size:0.9rem;">Pertanyaan pesanan khusus, katering, atau kemitraan UMKM:</p>
                <ul class="footer-links">
                    <li><i class="bi bi-whatsapp"></i> WhatsApp: 0812-3456-7890</li>
                    <li><i class="bi bi-geo-alt"></i> Sentra Kuliner UMKM, Jakarta</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2026 {{ $businessName }}. Hak Cipta Dilindungi Undang-Undang.</div>
            <div>Diberdayakan oleh Platform Kuliner Digital UMKM</div>
        </div>
    </div>
</footer>

<nav class="bottom-nav-mobile">
    <a href="{{ route('catalog.index') }}" class="bottom-nav-item-mobile {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
        <i class="bi bi-grid-3x3-gap-fill"></i><span>KATALOG</span>
    </a>
    <a href="{{ route('cart.index') }}" class="bottom-nav-item-mobile {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <i class="bi bi-bag-fill"></i><span>KERANJANG</span>
    </a>
    <a href="{{ route('orders.status') }}" class="bottom-nav-item-mobile {{ request()->routeIs('orders.status*') ? 'active' : '' }}">
        <i class="bi bi-truck"></i><span>STATUS</span>
    </a>
    <a href="{{ route('contact.index') }}" class="bottom-nav-item-mobile {{ request()->routeIs('contact.*') ? 'active' : '' }}">
        <i class="bi bi-at"></i><span>KONTAK</span>
    </a>
</nav>

@endsection
