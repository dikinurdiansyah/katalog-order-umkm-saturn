<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dapur Th Sant - @yield('title', 'Katalog & Pesan Dimsum')</title>

    <!-- CSS Framework: Bootstrap 5 & Font Awesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Design System Tokens (Warna & Tipografi Standar Sprint 1) -->
    <style>
        :root {
            --primary-color: #0D9488;      /* Teal Emerald */
            --primary-dark: #0F172A;       /* Slate Navy */
            --bg-base: #F8FAFC;            /* Slate Light */
            --accent-color: #D97706;       /* Warm Amber */
        }

        body {
            background-color: var(--bg-base);
            color: #0F172A;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: var(--primary-dark);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-teal {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-teal:hover {
            background-color: #0f766e;
            color: #ffffff;
        }

        .footer-custom {
            background-color: var(--primary-dark);
            color: #94A3B8;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- 1. Top Navbar / Header Shell -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">
                🥟 Dapur <span style="color: var(--primary-color);">Th Sant</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="/"><i class="fa-solid fa-utensils me-1"></i> Katalog Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/status-order"><i class="fa-solid fa-receipt me-1"></i> Cek Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin"><i class="fa-solid fa-user-shield me-1"></i> Panel Admin</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="/cart" class="btn btn-outline-light position-relative btn-sm px-3">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Keranjang
                        <span class="badge bg-danger rounded-pill">0</span>
                    </a>
                    <a href="/login" class="btn btn-teal btn-sm px-3">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. Kontainer Konten Dinamis (Wadah Halaman) -->
    <main class="container my-5 flex-grow-1">
        @yield('content')
    </main>

    <!-- 3. Footer Shell -->
    <footer class="footer-custom py-4 border-top">
        <div class="container text-center">
            <p class="mb-1 fw-semibold text-white">© 2026 Dapur Th Sant • Local Digital Solution</p>
            <small>Uji Kompetensi Keahlian (UKK) RPL - Fase Sprint 1: Foundation Ready</small>
        </div>
    </footer>

    <!-- Bootstrap Script Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
