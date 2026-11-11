<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Produk & Order - Owner Monitoring</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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

        body { background: var(--cream-light); font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 0.9rem; }
        .font-mono { font-family: 'JetBrains Mono', 'Courier New', monospace; }

        .owner-topbar { background: white; border-bottom: 1px solid var(--border-color); padding: 12px 24px; }
        .status-badge { background: var(--orange-fav); color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }

        .owner-sidebar { background: white; border-right: 1px solid var(--border-color); min-height: calc(100vh - 65px); padding: 24px 16px; }
        .nav-link-owner { display: block; padding: 10px 16px; color: var(--text-dark); text-decoration: none; border-radius: 8px; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500; }
        .nav-link-owner:hover { background: var(--cream); }
        .nav-link-owner.active { background: var(--brown-dark); color: white; }

        .owner-main { padding: 24px; }
        .page-header { background: var(--brown-dark); color: white; padding: 24px; border-radius: 12px; margin-bottom: 24px; }

        .kpi-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .kpi-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px; margin-bottom: 8px; }
        .kpi-value { font-size: 1.8rem; font-weight: 800; color: var(--brown-dark); margin-bottom: 4px; }
        .kpi-sub { font-size: 0.8rem; color: var(--text-muted); }

        .chart-container { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px; }

        .table-owner { background: white; border-radius: 12px; overflow: hidden; }
        .table-owner thead { background: var(--cream); }
        .table-owner th { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1px; border: none; padding: 12px 16px; }
        .table-owner td { padding: 14px 16px; vertical-align: middle; border-color: var(--border-color); font-size: 0.85rem; }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="owner-topbar">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; background: var(--brown-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-building" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.9rem; color: var(--brown-dark);">DAPUR TH SANT</div>
                        <small class="text-muted" style="font-size: 0.7rem;">OWNER MONITORING PORTAL v1.0</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <span class="status-badge">[ STATUS: HANYA BACA (READ-ONLY) ]</span>
            </div>
            <div class="col-md-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-sm" style="background: var(--cream); border: 1px solid var(--border-color);" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> Bpk. Santoso (Owner)
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('owner.logout') }}">[ KELUAR DARI PORTAL ]</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 owner-sidebar">
                <div class="mb-3"><small class="text-muted font-mono" style="font-size: 0.7rem;">NAVI // ARCHITECTURE</small></div>
                <a href="{{ route('owner.dashboard') }}" class="nav-link-owner"><i class="bi bi-speedometer2"></i> 01. Ringkasan Eksekutif</a>
                <a href="{{ route('owner.reports') }}" class="nav-link-owner active"><i class="bi bi-file-earmark-bar-graph"></i> 02. Laporan Produk & Order</a>
                <a href="{{ route('catalog.index') }}" class="nav-link-owner" target="_blank"><i class="bi bi-shop"></i> 03. Katalog Publik (Etalase) <span class="badge bg-secondary" style="font-size: 0.65rem;">EXT</span></a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 owner-main">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="fw-bold mb-2" style="font-size: 1.8rem;">LAPORAN PRODUK & ORDER</h1>
                            <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">Analisis mendalam mengenai performa penjualan produk, margin, dan tingkat keberhasilan pemenuhan pesanan (fulfillment rate).</p>
                        </div>
                    </div>
                </div>

                <!-- ✅ TAMBAHAN: Form Filter & Export Laporan -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid var(--border-color) !important;">
                    <div class="card-body p-3">
                        <form action="{{ route('owner.export.laporan') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1 font-mono">DARI TANGGAL</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1 font-mono">SAMPAI TANGGAL</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2" style="height: 38px; font-weight: 600;">
                                    <i class="bi bi-file-earmark-excel-fill"></i> Export Laporan ke Excel (CSV)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="kpi-label">TOTAL OMZET TERKUMULASI</div>
                            <div class="kpi-value" style="font-size: 1.5rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                            <div class="kpi-sub">Dari 5 Produk Teratas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="kpi-label">TINGKAT KEBERHASILAN (SLA)</div>
                            <div class="kpi-value">{{ $fulfillmentRate }}%</div>
                            <div class="kpi-sub">{{ $successOrders }} Selesai / {{ $totalOrders }} Total Order</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="kpi-label">PRODUK MARGIN TERTINGGI</div>
                            <div class="kpi-value" style="font-size: 1.3rem;">{{ $topMarginProduct->name ?? 'N/A' }}</div>
                            <div class="kpi-sub">Estimasi Margin: {{ $topMargin }}%</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card">
                            <div class="kpi-label">TOTAL DISKON PROMO</div>
                            <div class="kpi-value" style="font-size: 1.5rem; color: var(--orange-fav);">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
                            <div class="kpi-sub">ROI Promo: {{ $promoROI }}x Lipat</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Top Products by Volume -->
                    <div class="col-lg-7">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">[ METRIK VOLUME ]</div>
                                    <div class="fw-bold" style="color: var(--brown-dark);">5 PRODUK TERLARIS (BEST SELLER)</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-owner mb-0">
                                    <thead>
                                        <tr>
                                            <th>PERINGKAT</th>
                                            <th>NAMA PRODUK</th>
                                            <th>HARGA SATUAN</th>
                                            <th class="text-end">TOTAL TERJUAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $index => $product)
                                        <tr>
                                            <td>
                                                @if($index === 0) <span class="badge" style="background: #ffd700; color: #000;">🥇 1</span>
                                                @elseif($index === 1) <span class="badge" style="background: #c0c0c0; color: #000;">🥈 2</span>
                                                @elseif($index === 2) <span class="badge" style="background: #cd7f32; color: #fff;">🥉 3</span>
                                                @else <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $product->name }}</td>
                                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                            <td class="text-end font-mono fw-bold" style="color: var(--brown-dark);">{{ $product->total_sold }} Porsi</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data penjualan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Summary -->
                    <div class="col-lg-5">
                        <div class="chart-container">
                            <div class="font-mono" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 16px;">[ METRIK KEPUASAN & EFISIENSI ]</div>
                            <div class="fw-bold mb-4" style="color: var(--brown-dark);">RINGKASAN STATUS PESANAN</div>

                            <div class="mb-4 p-3" style="background: var(--green-bg); border-radius: 8px; border-left: 4px solid var(--green-stock);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="color: var(--green-stock);">✅ PESANAN SELESAI</span>
                                    <span class="fw-bold" style="font-size: 1.5rem; color: var(--green-stock);">{{ $successOrders }}</span>
                                </div>
                                <small class="text-muted">Pesanan berhasil diterima pelanggan</small>
                            </div>

                            <div class="mb-4 p-3" style="background: #dbeafe; border-radius: 8px; border-left: 4px solid #1e40af;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="color: #1e40af;">📦 TOTAL PESANAN MASUK</span>
                                    <span class="fw-bold" style="font-size: 1.5rem; color: #1e40af;">{{ $totalOrders }}</span>
                                </div>
                                <small class="text-muted">Seluruh order yang tercatat di sistem</small>
                            </div>

                            <div class="p-3" style="background: #fde8e8; border-radius: 8px; border-left: 4px solid #c53030;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="color: #c53030;">❌ PESANAN DIBATALKAN</span>
                                    <span class="fw-bold" style="font-size: 1.5rem; color: #c53030;">{{ $cancelledOrders }}</span>
                                </div>
                                <small class="text-muted">Order yang dibatalkan (stok dikembalikan)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="row mt-5 pt-4 border-top" style="border-color: var(--border-color) !important;">
                    <div class="col-md-12 text-center">
                        <small class="text-muted font-mono" style="font-size: 0.7rem;">
                            DAPUR TH SANT • SISTEM MONITORING PEMILIK (OWNER MONITORING BLUEPRINT)
                            &nbsp;&nbsp;|&nbsp;&nbsp; TERHUBUNG KE PANGKALAN DATA UTAMA
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
