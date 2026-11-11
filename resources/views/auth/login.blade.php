<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - {{ $businessName ?? 'Dapur Th Sant' }}</title>

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
            --text-dark: #2c1810;
            --text-muted: #8b7355;
            --border-color: #e0d5c9;
            --green-stock: #2d6a4f;
            --green-bg: #d8f3dc;
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        body {
            background: linear-gradient(135deg, var(--cream-light) 0%, var(--cream) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .login-topbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(61, 40, 23, 0.04);
        }

        .login-topbar-left { display: flex; align-items: center; gap: 16px; }

        .login-logo-icon {
            width: 40px; height: 40px; background: var(--brown-dark); color: white;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.2rem;
        }

        .login-topbar-title { font-weight: 800; font-size: 1.1rem; color: var(--brown-dark); }
        .login-topbar-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }

        .login-topbar-right {
            font-size: 0.8rem; color: var(--text-muted); background: var(--cream);
            padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border-color);
            display: flex; align-items: center; font-weight: 500;
        }

        /* Main Container */
        .login-container {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 40px 20px;
        }

        .login-card {
            background: white; border: 1px solid var(--border-color); border-radius: 24px;
            overflow: hidden; display: grid; grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 60px rgba(61, 40, 23, 0.1); max-width: 1000px; width: 100%;
        }

        /* Left Side */
        .login-left {
            background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
            padding: 60px 50px; display: flex; flex-direction: column;
            justify-content: center; align-items: center; border-right: 1px solid var(--border-color);
        }

        .login-logo-placeholder {
            width: 100%; max-width: 320px; background: white;
            border-radius: 20px; display: flex; flex-direction: column;
            align-items: center; justify-content: center; padding: 40px 30px;
            margin-bottom: 32px; box-shadow: 0 8px 24px rgba(61, 40, 23, 0.06);
        }

        .login-logo-circle {
            width: 140px; height: 140px; background: var(--cream-light);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; border: 4px solid white;
            box-shadow: 0 8px 20px rgba(61, 40, 23, 0.08);
        }

        .login-logo-circle img { max-width: 100px; height: auto; object-fit: contain; }

        .login-logo-text {
            font-size: 0.9rem; color: var(--text-dark); text-align: center;
            font-weight: 600; line-height: 1.5;
        }

        .login-telemetri { width: 100%; max-width: 320px; }

        .telemetri-title {
            font-size: 0.75rem; color: var(--text-muted); margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
        }

        .telemetri-title::before {
            content: ''; width: 8px; height: 8px; background: var(--green-stock);
            border-radius: 50%; animation: pulse 2s infinite;
        }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        .telemetri-item {
            background: white; border: 1px solid var(--border-color); border-radius: 12px;
            padding: 14px 18px; margin-bottom: 12px; display: flex;
            justify-content: space-between; align-items: center; transition: all 0.2s;
        }

        .telemetri-item:hover { border-color: var(--brown-dark); transform: translateX(4px); }

        .telemetri-label { font-size: 0.85rem; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }
        .telemetri-label i { color: var(--brown-dark); font-size: 1.1rem; }
        .telemetri-value { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--brown-dark); font-size: 0.95rem; }

        /* Right Side */
        .login-right { padding: 60px 50px; display: flex; flex-direction: column; justify-content: center; }

        .login-title { font-size: 2rem; font-weight: 800; color: var(--brown-dark); margin-bottom: 8px; letter-spacing: -0.5px; }
        .login-subtitle { color: var(--text-muted); font-size: 1rem; margin-bottom: 32px; }
        .login-divider { border: none; border-top: 1px solid var(--border-color); margin: 32px 0; }

        .form-group-custom { margin-bottom: 24px; }

        .form-label-text {
            display: block; font-size: 0.85rem; font-weight: 600;
            color: var(--text-dark); margin-bottom: 8px;
        }

        .form-control-custom {
            background: white; border: 1.5px solid var(--border-color); border-radius: 10px;
            padding: 14px 16px; font-size: 0.95rem; width: 100%; transition: all 0.2s;
        }

        .form-control-custom:focus {
            outline: none; border-color: var(--brown-dark);
            box-shadow: 0 0 0 4px rgba(61, 40, 23, 0.08);
        }

        .input-group-custom { display: flex; }
        .input-group-custom .form-control-custom { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; flex: 1; }

        .btn-toggle {
            background: var(--cream); border: 1.5px solid var(--border-color); border-radius: 0 10px 10px 0;
            padding: 0 16px; font-size: 1rem; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
        }
        .btn-toggle:hover { background: var(--cream-dark); color: var(--brown-dark); }

        .checkbox-custom { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        .checkbox-custom input { width: 18px; height: 18px; accent-color: var(--brown-dark); cursor: pointer; }
        .checkbox-custom label { font-size: 0.9rem; font-weight: 500; color: var(--text-dark); margin: 0; cursor: pointer; }

        .btn-login-submit {
            background: var(--brown-dark); color: white; border: none; border-radius: 10px;
            padding: 16px; font-weight: 700; font-size: 1rem; width: 100%;
            transition: all 0.3s; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-login-submit:hover {
            background: var(--brown-medium); transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(61, 40, 23, 0.25);
        }

        .login-footer-text {
            text-align: center; margin-top: 32px; padding-top: 24px;
            border-top: 1px solid var(--border-color); font-size: 0.9rem; color: var(--text-muted);
        }

        .login-page-footer {
            text-align: center; padding: 24px; font-size: 0.8rem; color: var(--text-muted);
            border-top: 1px solid var(--border-color); background: white;
        }

        .alert { border-radius: 10px; border: none; padding: 14px 18px; font-size: 0.9rem; }
        .alert-success { background: var(--green-bg); color: var(--green-stock); }
        .alert-danger { background: #fde8e8; color: #c53030; }

        /* Responsive */
        @media (max-width: 900px) {
            .login-card { grid-template-columns: 1fr; }
            .login-left { display: none; }
            .login-right { padding: 40px 32px; }
        }
        @media (max-width: 576px) {
            .login-container { padding: 20px 16px; }
            .login-right { padding: 32px 24px; }
            .login-title { font-size: 1.6rem; }
            .login-topbar { padding: 14px 20px; }
            .login-topbar-right { display: none; }
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

    <!-- Top Bar -->
    <div class="login-topbar">
        <div class="login-topbar-left">
            @if($logoUrl)
                <img src="{{ asset($logoUrl) }}" alt="Logo" style="height: 36px; width: auto; object-fit: contain;">
            @else
                <div class="login-logo-icon">{{ substr($businessName, 0, 1) }}</div>
            @endif
            <div>
                <div class="login-topbar-title">{{ $businessName }}</div>
                <div class="login-topbar-sub">Admin Portal</div>
            </div>
        </div>
        <div class="login-topbar-right">
            <i class="bi bi-shield-lock" style="color: var(--green-stock); margin-right: 6px;"></i>
            Secure Connection
        </div>
    </div>

    <!-- Main Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Left Side -->
            <div class="login-left">
                <div class="login-logo-placeholder">
                    <div class="login-logo-circle">
                        @if($logoUrl)
                            <img src="{{ asset($logoUrl) }}" alt="Logo {{ $businessName }}">
                        @else
                            <span style="font-size: 2.5rem; font-weight: 800; color: var(--brown-dark);">DTS</span>
                        @endif
                    </div>
                    <div class="login-logo-text">
                        Selamat Datang di Portal Admin<br>
                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">Kelola operasional dapur dengan mudah</span>
                    </div>
                </div>

                <div class="login-telemetri">
                    <div class="telemetri-title">STATUS SISTEM HARI INI</div>
                    <div class="telemetri-item">
                        <div class="telemetri-label"><i class="bi bi-bag-check"></i> Total Pesanan</div>
                        <div class="telemetri-value">{{ number_format($produksiHariIni ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="telemetri-item">
                        <div class="telemetri-label"><i class="bi bi-activity"></i> Status Server</div>
                        <div class="telemetri-value" style="color: var(--green-stock);">Online</div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="login-right">
                <h1 class="login-title">Masuk ke Dashboard</h1>
                <p class="login-subtitle">Silakan masukkan kredensial admin Anda untuk melanjutkan.</p>

                <hr class="login-divider">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group-custom">
                        <label class="form-label-text">Email atau Username</label>
                        <input type="email" name="email" class="form-control-custom"
                               value="{{ old('email', 'admin@dapurthsant.com') }}" required autofocus
                               placeholder="nama@dapurthsant.com">
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-text">Kata Sandi</label>
                        <div class="input-group-custom">
                            <input type="password" name="password" class="form-control-custom"
                                   id="passwordInput" required placeholder="••••••••">
                            <button type="button" class="btn-toggle" onclick="togglePassword()" title="Lihat Password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="checkbox-custom">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ingat saya di perangkat ini</label>
                        </div>
                        <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: var(--brown-dark); font-weight: 500;">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-login-submit">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>

                <div class="login-footer-text">
                    Bukan admin? <a href="{{ route('owner.login') }}" class="text-decoration-none" style="color: var(--brown-dark); font-weight: 600;">Masuk sebagai Owner</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Footer -->
    <div class="login-page-footer">
        &copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const btn = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="bi bi-eye"></i>';
            }
        }
    </script>
</body>
</html>
