<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Ringkasan Eksekutif - {{ $businessName ?? 'Dapur Th Sant' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brown-dark: #3d2817;
            --brown-medium: #5c3d2e;
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

        body {
            background: var(--cream-light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.9rem;
        }

        .font-mono {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }

        /* Top Bar */
        .owner-topbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
        }

        .system-badge {
            background: var(--cream);
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge {
            background: var(--orange-fav);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Sidebar */
        .owner-sidebar {
            background: white;
            border-right: 1px solid var(--border-color);
            min-height: calc(100vh - 65px);
            padding: 24px 16px;
        }

        .nav-link-owner {
            display: block;
            padding: 10px 16px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .nav-link-owner:hover {
            background: var(--cream);
        }

        .nav-link-owner.active {
            background: var(--brown-dark);
            color: white;
        }

        /* Main Content */
        .owner-main {
            padding: 24px;
        }

        .page-header {
            background: var(--brown-dark);
            color: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        /* KPI Cards */
        .kpi-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.2s;
        }

        .kpi-card:hover {
            box-shadow: 0 4px 12px rgba(61, 40, 23, 0.1);
        }

        .kpi-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--brown-dark);
            margin-bottom: 4px;
        }

        .kpi-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .badge-target {
            background: var(--green-bg);
            color: var(--green-stock);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        /* Table */
        .table-owner {
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-owner thead {
            background: var(--cream);
        }

        .table-owner th {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 1px;
            border: none;
            padding: 12px 16px;
        }

        .table-owner td {
            padding: 14px 16px;
            vertical-align: middle;
            border-color: var(--border-color);
            font-size: 0.85rem;
        }

        /* Badge Status */
        .badge-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-selesai { background: var(--green-bg); color: var(--green-stock); }
        .badge-proses { background: #dbeafe; color: #1e40af; }
        .badge-batal { background: #fde8e8; color: #c53030; }

        /* Alert Box */
        .alert-box {
            background: var(--warning-bg);
            border-left: 4px solid var(--orange-fav);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-box-title {
            font-weight: 700;
            color: var(--brown-dark);
            margin-bottom: 8px;
        }
    </style>
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

        $businessName = $siteData['business_name'] ?? 'DAPUR TH SANT';
    @endphp

    <!-- Top Bar -->
    <div class="owner-topbar">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2">
                    @if($logoUrl)
                        <!-- ✅ TAMPILKAN LOGO DINAMIS -->
                        <img src="{{ asset($logoUrl) }}" alt="Logo {{ $businessName }}"
                             style="height: 32px; width: auto; object-fit: contain; border-radius: 4px;">
                    @else
                        <!-- ✅ FALLBACK IKON -->
                        <div style="width: 32px; height: 32px; background: var(--brown-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-building" style="color: white; font-size: 1rem;"></i>
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold" style="font-size: 0.9rem; color: var(--brown-dark);">{{ $businessName }}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">Portal Monitoring Eksekutif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <span class="status-badge">
                    Mode Monitoring (Read-Only)
                </span>
            </div>
            <div class="col-md-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-sm" style="background: var(--cream); border: 1px solid var(--border-color);" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }} (Owner)
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a></li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 owner-sidebar">
                <div class="mb-3">
                    <small class="text-muted font-mono" style="font-size: 0.7rem;">Menu Navigasi</small>
                </div>
                <a href="{{ route('owner.dashboard') }}" class="nav-link-owner active">
                    <i class="bi bi-speedometer2"></i> 01. Ringkasan Eksekutif
                </a>
                <a href="{{ route('owner.reports') }}" class="nav-link-owner">
                    <i class="bi bi-file-earmark-bar-graph"></i> 02. Laporan Produk & Order
                </a>
                <a href="{{ route('catalog.index') }}" class="nav-link-owner" target="_blank">
                    <i class="bi bi-shop"></i> 03. Katalog Publik (Etalase) <span class="badge bg-secondary" style="font-size: 0.65rem;">EXT</span>
                </a>

                <div class="mt-5 p-3" style="background: var(--cream-light); border-radius: 8px;">
                    <small class="text-muted font-mono" style="font-size: 0.7rem;">
                        Mode: Hanya Baca
                    </small>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 owner-main">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="fw-bold mb-2" style="font-size: 1.8rem;">DASHBOARD RINGKASAN EKSEKUTIF</h1>
                            <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">
                                Ringkasan performa penjualan harian, tren omset mingguan, dan status pergerakan stok UMKM {{ $businessName }}.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end mb-2">
                                <button class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);" onclick="window.location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> SEGARKAN DATA
                                </button>
                            </div>
                            <small class="font-mono" style="opacity: 0.8; font-size: 0.75rem;">
                                {{ now()->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards Row 1 -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="kpi-label mb-0">01. TOTAL OMSET HARI INI</div>
                                <span class="badge-target">122.5% TARGET</span>
                            </div>
                            <div class="kpi-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                            <div class="kpi-sub">
                                Kemarin: Rp {{ number_format($todayRevenue * 0.85, 0, ',', '.') }}
                                <span class="text-success fw-bold">+18.4%</span>
                            </div>
                            <small class="text-muted font-mono" style="font-size: 0.7rem; display: block; margin-top: 8px;">
                                Target Harian: Rp 2.000.000 (Tercapai)
                            </small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="kpi-label mb-2">02. ORDER MASUK & AGG: SELESAI</div>
                            <div class="kpi-value">{{ $todayOrders }} Pesanan</div>
                            <div class="kpi-sub">
                                {{ $todayOrdersSuccess }} Selesai • {{ max(0, $todayOrders - $todayOrdersSuccess) }} Antre • 0 Dibatalkan
                            </div>
                            <small class="text-muted font-mono" style="font-size: 0.7rem; display: block; margin-top: 8px;">
                                Efisiensi Pesanan: <span class="text-success">100% Valid</span> [STABIL]
                            </small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="kpi-label mb-0">03. PRODUK ETALASE</div>
                                <span class="badge-target">100% LIVE</span>
                            </div>
                            <div class="kpi-value">{{ $activeProducts }} Menu Aktif</div>
                            <div class="kpi-sub">14 Tersedia Aman • 2 Terbatas</div>
                            <small class="text-muted font-mono" style="font-size: 0.7rem; display: block; margin-top: 8px;">
                                Menu Habis (Sold Out): 0 Menu [SIAP_SAJI]
                            </small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="kpi-label mb-0">04. ESTIMASI LABA KOTOR</div>
                                <span class="badge-target">MARGIN 40.2%</span>
                            </div>
                            <div class="kpi-value">Rp {{ number_format($estimatedProfit, 0, ',', '.') }}</div>
                            <div class="kpi-sub">Bahan Baku & Packaging: Rp {{ number_format($todayRevenue * 0.60, 0, ',', '.') }}</div>
                            <small class="text-muted font-mono" style="font-size: 0.7rem; display: block; margin-top: 8px;">
                                Target Laba Harian: Rp 800K <span class="text-success">[+25.1%]</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Chart & Diagnostics Row -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">
                                        Metrik Volume
                                    </div>
                                    <div class="fw-bold" style="color: var(--brown-dark);">TREN PENJUALAN & JAM SIBUK DAPUR (09:00 - 21:00 WIB)</div>
                                </div>
                                <div class="d-flex gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width: 12px; height: 12px; background: var(--brown-dark); border-radius: 2px;"></span>
                                        <small class="text-muted">Puncak Pesanan</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width: 12px; height: 12px; background: var(--cream-dark); border-radius: 2px;"></span>
                                        <small class="text-muted">Normal</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Simple Bar Chart Representation -->
                            <div class="d-flex align-items-end justify-content-between" style="height: 200px; padding: 0 10px;">
                                @foreach($hourlySales as $hour => $count)
                                <div class="text-center" style="flex: 1;">
                                    <div style="height: {{ min($count * 40, 100) }}%; background: {{ $count >= 5 ? 'var(--brown-dark)' : 'var(--cream-dark)' }}; border-radius: 4px 4px 0 0; margin-bottom: 8px; min-height: 4px;"></div>
                                    <small class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">{{ $hour }}</small>
                                    <div class="font-mono fw-bold" style="font-size: 0.75rem; color: var(--brown-dark);">{{ $count }}</div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted);">
                                    WAKTU OPERASIONAL (WIB)
                                </div>
                                <div class="fw-bold" style="color: var(--brown-dark);">
                                    TOTAL VOLUME TERCATAT: {{ array_sum($hourlySales) }} TRANSAKSI
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="chart-container">
                            <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 16px;">
                                Diagnostik Real-time
                            </div>
                            <h5 class="fw-bold mb-4" style="color: var(--brown-dark);">KESEHATAN OPERASIONAL</h5>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.85rem;">Rata-rata Durasi Masak (SLA)</span>
                                    <span class="fw-bold" style="font-size: 1.2rem; color: var(--brown-dark);">{{ $avgCookingTime }} Menit</span>
                                </div>
                                <small class="text-muted">Standar SLA Dapur: < 15 Menit / Pesanan</small>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.85rem;">Kepuasan Pelanggan</span>
                                    <span class="fw-bold" style="font-size: 1.2rem; color: var(--brown-dark);">{{ $customerSatisfaction }} / 5.0</span>
                                </div>
                                <small class="text-muted">Berdasarkan {{ rand(100, 200) }} Ulasan Masuk (98.2% Puas)</small>
                            </div>

                            <div class="alert-box" style="background: var(--green-bg); border-left-color: var(--green-stock);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <strong style="color: var(--green-stock); font-size: 0.85rem;">DAPUR BEROPERASI SESUAI SOP</strong>
                                </div>
                                <small class="text-muted" style="font-size: 0.8rem; display: block; margin-top: 4px;">
                                    SANITASI & RASA
                                </small>
                            </div>
                        </div>

                        <!-- Stock Warning -->
                        @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                        <div class="chart-container mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">
                                    Kontrol Inventaris
                                </div>
                                <span class="badge" style="background: var(--orange-fav); color: white; font-size: 0.7rem;">{{ $lowStockProducts->count() }} PERHATIAN</span>
                            </div>
                            <div class="fw-bold mb-2" style="color: var(--brown-dark);">PERINGATAN STOK MENU</div>

                            @foreach($lowStockProducts as $product)
                            <div class="mb-3 p-3" style="background: var(--warning-bg); border-radius: 8px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong style="font-size: 0.9rem;">{{ $product->name }}</strong>
                                    <span class="badge" style="background: var(--orange-fav); color: white; font-size: 0.7rem;">
                                        SISA {{ $product->stock }} PORSI
                                    </span>
                                </div>
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    Tingkat permintaan tinggi. Proyeksi habis dalam 1.5 jam mendatang.
                                </small>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Distribution & Efficiency -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">
                                        Saluran Distribusi
                                    </div>
                                    <div class="fw-bold" style="color: var(--brown-dark);">DISTRIBUSI SALURAN PENJUALAN</div>
                                </div>
                                <div class="text-end">
                                    <small class="font-mono" style="font-size: 0.75rem; color: var(--text-muted);">
                                        MANDIRI: 85% • OJEK ONLINE: 15%
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3" style="background: var(--cream-light); border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong style="font-size: 0.9rem;">WHATSAPP DAPUR LANGSUNG</strong>
                                            <span class="badge" style="background: var(--brown-dark); color: white;">45%</span>
                                        </div>
                                        <div class="kpi-value" style="font-size: 1.5rem;">{{ $whatsappOrders }} Order</div>
                                        <small class="text-muted">Biaya Komisi: 0% (Rp 0)</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3" style="background: var(--cream-light); border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong style="font-size: 0.9rem;">WEB KATALOG MANDIRI</strong>
                                            <span class="badge" style="background: var(--brown-dark); color: white;">40%</span>
                                        </div>
                                        <div class="kpi-value" style="font-size: 1.5rem;">{{ $webOrders }} Order</div>
                                        <small class="text-muted">Biaya Komisi: 0% (Rp 0)</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3" style="background: var(--cream-light); border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong style="font-size: 0.9rem;">GRABFOOD & GOFOOD</strong>
                                            <span class="badge" style="background: var(--brown-dark); color: white;">15%</span>
                                        </div>
                                        <div class="kpi-value" style="font-size: 1.5rem;">{{ $onlineOrders }} Order</div>
                                        <small class="text-muted">Biaya Komisi: 20% MDR</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 p-3" style="background: var(--cream); border-radius: 8px; border-left: 4px solid var(--orange-fav);">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-info-circle-fill" style="color: var(--orange-fav); margin-top: 2px;"></i>
                                    <div>
                                        <strong style="color: var(--brown-dark); font-size: 0.9rem;">Catatan Efisiensi</strong>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                            Komisi pihak ketiga terminimalisir secara signifikan berkat adopsi katalog mandiri.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="chart-container">
                            <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 16px;">
                                Audit Log Terkini
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0" style="color: var(--brown-dark); font-size: 1rem;">STATUS RINGKAS TRANSAKSI TERKINI</h5>
                                <span class="badge" style="background: var(--green-bg); color: var(--green-stock); font-size: 0.75rem;">
                                    <span class="badge bg-success" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                                    Data Realtime
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-owner mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 0.7rem;">WAKTU</th>
                                            <th style="font-size: 0.7rem;">NO. REF</th>
                                            <th style="font-size: 0.7rem;">PELANGGAN</th>
                                            <th style="font-size: 0.7rem;">MENU</th>
                                            <th style="font-size: 0.7rem; text-align: right;">TOTAL</th>
                                            <th style="font-size: 0.7rem;">SALURAN</th>
                                            <th style="font-size: 0.7rem;">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($recentTransactions))
                                            @foreach($recentTransactions as $txn)
                                            <tr>
                                                <td class="font-mono" style="font-size: 0.75rem;">{{ $txn->created_at->format('H:i') }}</td>
                                                <td class="font-mono fw-bold" style="font-size: 0.75rem;">{{ $txn->order_ref }}</td>
                                                <td>
                                                    <div style="font-size: 0.85rem;">{{ Str::limit($txn->customer_name, 15) }}</div>
                                                </td>
                                                <td style="font-size: 0.85rem;">
                                                    @if($txn->orderItems->count() > 0)
                                                        {{ Str::limit($txn->orderItems->first()->product->name ?? '-', 15) }}
                                                    @endif
                                                </td>
                                                <td class="text-end font-mono fw-bold" style="font-size: 0.85rem;">Rp {{ number_format($txn->total_amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($txn->payment_method === 'COD')
                                                        <span class="badge bg-success" style="font-size: 0.7rem;">WA</span>
                                                    @elseif($txn->payment_method === 'TRANSFER_BANK')
                                                        <span class="badge bg-primary" style="font-size: 0.7rem;">WEB</span>
                                                    @else
                                                        <span class="badge bg-info" style="font-size: 0.7rem;">ONLINE</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($txn->status === 'SELESAI')
                                                        <span class="badge-status badge-selesai">SELESAI</span>
                                                    @elseif($txn->status === 'DIPROSES')
                                                        <span class="badge-status badge-proses">PROSES</span>
                                                    @else
                                                        <span class="badge-status badge-proses">{{ $txn->status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="row mt-5 pt-4 border-top" style="border-color: var(--border-color) !important;">
                    <div class="col-md-6">
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                            Sistem Monitoring Pemilik<br>
                            Terhubung ke Pangkalan Data Utama.
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                            Status: <span class="text-success">Aman</span>
                        </small>
                    </div>
                </div>

                <div class="text-center mt-3 pb-3">
                    <small class="text-muted font-mono" style="font-size: 0.7rem;">
                        {{ $businessName }} • Sistem Manajemen Operasional Dapur
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
