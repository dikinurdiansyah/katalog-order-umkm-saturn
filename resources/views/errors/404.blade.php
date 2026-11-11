@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --brown-dark: #3d2817; --cream: #f5f0eb; --cream-light: #faf7f4; --text-muted: #8b7355; }
        body { font-family: 'Inter', sans-serif; background: var(--cream-light); color: var(--brown-dark); display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 20px; }
        .error-code { font-size: 8rem; font-weight: 800; color: var(--brown-dark); line-height: 1; margin-bottom: 10px; }
        .error-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 10px; }
        .error-desc { color: var(--text-muted); margin-bottom: 30px; max-width: 400px; margin-left: auto; margin-right: auto; }
        .btn-back { background: var(--brown-dark); color: white; padding: 12px 32px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-back:hover { background: #5c3d2e; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div>
        <div class="error-code">404</div>
        <h1 class="error-title">Ups! Halaman Tidak Ditemukan</h1>
        <p class="error-desc">Maaf, halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada.</p>
        <a href="{{ url('/') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
