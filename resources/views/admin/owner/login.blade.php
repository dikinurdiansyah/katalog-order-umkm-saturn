<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Owner - {{ $businessName ?? 'Dapur Th Sant' }}</title>

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
            --text-dark: #2c1810;
            --text-muted: #8b7355;
            --border-color: #e0d5c9;
        }

        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; box-sizing: border-box; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        body {
            background: linear-gradient(135deg, var(--cream-light) 0%, var(--cream) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 40px 20px;
        }

        .owner-login-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(61, 40, 23, 0.08);
            border: 1px solid var(--border-color);
        }

        .system-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 24px;
            margin-bottom: 32px;
        }

        .badge-mode {
            background: var(--green-bg);
            color: var(--green-stock);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-card {
            background: var(--cream-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .kpi-card:hover {
            border-color: var(--brown-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(61, 40, 23, 0.06);
        }

        .kpi-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--brown-dark);
            margin-bottom: 4px;
        }

        .kpi-sub { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }

        .form-control-owner {
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: white;
        }

        .form-control-owner:focus {
            border-color: var(--brown-dark);
            box-shadow: 0 0 0 4px rgba(61, 40, 23, 0.06);
            outline: none;
        }

        .input-group-text {
            background: var(--cream-light);
            border: 1.5px solid var(--border-color);
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: var(--text-muted);
        }

        .input-group .form-control-owner {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            z-index: 10;
        }

        .btn-toggle-password:hover { color: var(--brown-dark); }

        .btn-owner {
            background: var(--brown-dark);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 16px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-owner:hover {
            background: var(--brown-medium);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(61, 40, 23, 0.2);
        }

        .alert-info-custom {
            background: var(--cream-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
        }

        @media (max-width: 768px) {
            body { padding: 20px 16px; }
            .owner-login-container { padding: 32px 24px; border-radius: 16px; }
            .kpi-value { font-size: 1.4rem; }
            .system-header .col-md-5 { text-align: left !important; margin-top: 16px; }
        }
    </style>
</head>
<body>

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

    <div class="owner-login-container">
        <!-- System Header -->
        <div class="system-header">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        @if($logoUrl)
                            <img src="{{ asset($logoUrl) }}" alt="Logo"
                                 style="height: 48px; width: auto; object-fit: contain; background: white; border-radius: 50%; padding: 4px; border: 1px solid var(--border-color);">
                        @else
                            <div style="width: 48px; height: 48px; background: var(--brown-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-building" style="color: white; font-size: 1.5rem;"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold" style="font-size: 1.2rem; color: var(--brown-dark);">{{ $businessName }}</div>
                            <small class="text-muted" style="font-size: 0.85rem;">Portal Monitoring Eksekutif</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <span class="badge-mode">
                        <i class="bi bi-shield-check"></i> Mode Hanya Baca
                    </span>
                </div>
            </div>
        </div>

        <h1 class="fw-bold mb-2" style="font-size: 1.8rem; color: var(--brown-dark);">Selamat Datang, Pemilik</h1>
        <p class="text-muted mb-4" style="font-size: 0.95rem; max-width: 600px;">
            Pantau performa bisnis, laporan omzet, dan inventaris secara real-time. Akun ini dirancang khusus untuk monitoring tanpa hak ubah data operasional.
        </p>

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- KPI Preview -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-label">Omzet Bulan Ini</div>
                    <div class="kpi-value">Rp 42,85 Jt</div>
                    <div class="kpi-sub" style="color: var(--green-stock);">
                        <i class="bi bi-arrow-up"></i> +14.2% dari bulan lalu
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-label">Margin Bersih</div>
                    <div class="kpi-value">38.2%</div>
                    <div class="kpi-sub">Status: Optimal</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-label">Pesanan Selesai</div>
                    <div class="kpi-value">684 Order</div>
                    <div class="kpi-sub">99.4% Tingkat Kepuasan</div>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('owner.login.post') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold" style="color: var(--text-dark); font-size: 0.9rem; margin-bottom: 8px;">
                    Email atau ID Pemilik
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-owner"
                           placeholder="owner@dapurthsant.com" value="{{ old('email', 'owner@dapurthsant.com') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold" style="color: var(--text-dark); font-size: 0.9rem; margin-bottom: 8px;">
                    Kata Sandi
                </label>
                <div class="input-group position-relative">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control form-control-owner"
                           id="ownerPassword" placeholder="••••••••" required style="padding-right: 45px;">
                    <button class="btn-toggle-password" type="button" onclick="toggleOwnerPassword()" title="Lihat Kata Sandi">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" style="accent-color: var(--brown-dark);">
                    <label class="form-check-label text-muted" style="font-size: 0.9rem; cursor: pointer;" for="remember">
                        Ingat perangkat ini
                    </label>
                </div>
                <a href="#" class="text-decoration-none" style="font-size: 0.9rem; color: var(--brown-dark);" onclick="alert('Silakan hubungi Administrator Utama untuk mengatur ulang kata sandi.')">
                    Lupa kata sandi?
                </a>
            </div>

            <button type="submit" class="btn btn-owner mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-muted" style="font-size: 0.9rem; text-decoration: none;">
                    Bukan Owner? <span style="color: var(--brown-dark); font-weight: 600;">Masuk sebagai Admin</span>
                </a>
            </div>
        </form>

        <!-- Clean Professional Footer -->
        <div class="text-center mt-5 pt-4 border-top" style="border-color: var(--border-color) !important;">
            <small class="text-muted" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.<br>
                <span style="font-size: 0.75rem; opacity: 0.7;">Sistem Manajemen & Monitoring Kuliner UMKM</span>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleOwnerPassword() {
            const input = document.getElementById('ownerPassword');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
