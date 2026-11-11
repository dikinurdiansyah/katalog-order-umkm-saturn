@extends('layouts.admin')

@section('title', 'Kelola Kontak & Outlet')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.8rem; color: var(--brown-dark);">
                Kelola Informasi Kontak & Profil Outlet
            </h1>
            <p class="text-muted mb-0">
                Perubahan informasi di halaman ini akan langsung diperbarui pada halaman Kontak Pelanggan dan footer website.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('contact.index') }}" target="_blank" class="btn-admin-outline">
                <i class="bi bi-eye"></i> Lihat Halaman Publik
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle-fill"></i> Ada kesalahan pada form:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Status Info -->
    <div class="admin-card mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="badge bg-success" style="padding: 8px 12px;">
                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Sistem Aktif
            </div>
            <div>
                <div class="fw-bold" style="color: var(--brown-dark);">Outlet: {{ $data['business_name'] ?? 'Dapur Th Sant' }}</div>
                <small class="text-muted">Update terakhir: {{ now()->format('d M Y, H:i') }} WIB</small>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.contact.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- 01 Identitas Usaha -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom">
                            <span class="badge bg-dark me-2">01</span>
                            Identitas Usaha & WhatsApp
                        </div>
                    </div>

                    <!-- Upload Logo -->
                    <div class="mb-4 p-4 border rounded text-center" style="background: var(--cream-light); border-style: dashed !important;">
                        <label class="form-label fw-bold d-block mb-3">Upload Logo Website</label>

                        @if(!empty($data['logo_url']))
                            <div class="mb-3">
                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                                <img src="{{ asset($data['logo_url']) }}" alt="Logo Saat Ini"
                                     style="max-height: 80px; width: auto; object-fit: contain; background: white; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                <div class="text-muted mt-2" style="font-size: 0.85rem;">Logo saat ini</div>
                            </div>
                        @endif

                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg" style="max-width: 350px; margin: 0 auto;">
                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-2">Format: PNG/JPG. Maksimal 2MB. Direkomendasikan rasio 1:1.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Usaha *</label>
                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ $data['business_name'] ?? 'Dapur Th Sant' }}" required>
                        @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Muncul pada title bar, header, dan invoice.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Slogan / Tagline</label>
                        <input type="text" name="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ $data['tagline'] ?? 'Dimsum Kukus & Goreng Fresh Halal Tiap Hari Tanpa Pengawet' }}">
                        @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Resmi *</label>
                            <div class="input-group">
                                <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ $data['whatsapp'] ?? '081234567890' }}" required placeholder="081234567890">
                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            </div>
                            @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Format: 08xxxxxxxxxx</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hotline Cadangan</label>
                            <div class="input-group">
                                <input type="text" name="hotline" class="form-control @error('hotline') is-invalid @enderror" value="{{ $data['hotline'] ?? '' }}" placeholder="081234567890">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            </div>
                            @error('hotline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Template Pesan WhatsApp</label>
                        <textarea name="wa_template" class="form-control @error('wa_template') is-invalid @enderror" rows="3">{{ $data['wa_template'] ?? 'Halo Admin Dapur Th Sant, saya ingin pesan/tanya seputar menu dimsum kukus & frozen pack...' }}</textarea>
                        @error('wa_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Otomatis diisi saat pelanggan menekan tombol 'Chat via WhatsApp'.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Operasional</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $data['email'] ?? 'kontak@dapurthsant.com' }}">
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- 02 Lokasi Dapur -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom">
                            <span class="badge bg-dark me-2">02</span>
                            Lokasi Dapur & Outlet Utama
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Lengkap Gerai *</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ $data['address'] ?? 'Jl. Ciumbuleuit No. 88, Hegarmanah, Kec. Cidadap, Kota Bandung, Jawa Barat 40141' }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Patokan / Catatan untuk Driver & Pembeli</label>
                        <input type="text" name="landmark" class="form-control @error('landmark') is-invalid @enderror" value="{{ $data['landmark'] ?? '150m utara Kampus Unpar, seberang mini market kuning' }}">
                        @error('landmark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Lokasi di Peta</label>
                        <!-- Peta Interaktif Leaflet -->
                        <div id="mapLocation" style="height: 250px; border-radius: 8px; margin-bottom: 15px; z-index: 1; border: 1px solid var(--border-color);"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Latitude</label>
                                <div class="input-group">
                                    <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ $data['latitude'] ?? '-6.879' }}" placeholder="-6.879">
                                    <button class="btn btn-outline-secondary" type="button" onclick="getCurrentLocation()" title="Gunakan lokasi browser saat ini">
                                        <i class="bi bi-geo-alt-fill"></i> Saya
                                    </button>
                                </div>
                                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Longitude</label>
                                <div class="input-group">
                                    <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ $data['longitude'] ?? '107.605' }}" placeholder="107.605">
                                    <button class="btn btn-outline-secondary" type="button" onclick="searchLocation()" title="Cari berdasarkan nama tempat">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                </div>
                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">
                            💡 Klik pada peta atau seret marker biru untuk mendapatkan koordinat otomatis.
                        </small>
                    </div>
                </div>

                <!-- 03 Jadwal Operasional -->
                <div class="admin-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom">
                            <span class="badge bg-dark me-2">03</span>
                            Jadwal Operasional Dapur
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hari Operasional</label>
                            <input type="text" name="operational_days" class="form-control @error('operational_days') is-invalid @enderror" value="{{ $data['operational_days'] ?? 'Selasa - Minggu' }}">
                            @error('operational_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jam Buka</label>
                            <input type="text" name="opening_hours" class="form-control @error('opening_hours') is-invalid @enderror" value="{{ $data['opening_hours'] ?? '09:00' }}">
                            @error('opening_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jam Tutup</label>
                            <input type="text" name="closing_hours" class="form-control @error('closing_hours') is-invalid @enderror" value="{{ $data['closing_hours'] ?? '21:00' }}">
                            @error('closing_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hari Khusus / Dapur Offline</label>
                        <input type="text" name="offline_days" class="form-control @error('offline_days') is-invalid @enderror" value="{{ $data['offline_days'] ?? 'Senin (Khusus Pesanan Katering & PO Terjadwal)' }}">
                        @error('offline_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Pelanggan tidak bisa order instan regular pada jadwal ini.</small>
                    </div>
                </div>
            </div>

            <!-- 04 Media Sosial -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom">
                            <i class="bi bi-share me-2"></i> Media Sosial & Link
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Username Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="text" name="instagram" class="form-control @error('instagram') is-invalid @enderror" value="{{ $data['instagram'] ?? 'dapurthsant.dimsum' }}">
                            </div>
                            @error('instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Username TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                <input type="text" name="tiktok" class="form-control @error('tiktok') is-invalid @enderror" value="{{ $data['tiktok'] ?? 'dapurthsant_official' }}">
                            </div>
                            @error('tiktok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Link Aggregator (Linktree, dll)</label>
                        <input type="url" name="link_aggregator" class="form-control @error('link_aggregator') is-invalid @enderror" value="{{ $data['link_aggregator'] ?? 'https://linktr.ee/dapurthsant' }}">
                        @error('link_aggregator')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Kompilasi satu tautan untuk bio Instagram & profil WhatsApp Bisnis.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Actions -->
        <div class="d-flex justify-content-end gap-3 mt-4 mb-5">
            <button type="button" class="btn-admin-outline" onclick="window.history.back()">
                <i class="bi bi-x-lg"></i> Batal
            </button>
            <button type="submit" class="btn-admin">
                <i class="bi bi-save"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Leaflet CSS & JS untuk Peta Interaktif -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Inisialisasi Peta
let map, marker;
const defaultLat = {{ $data['latitude'] ?? -6.879 }};
const defaultLng = {{ $data['longitude'] ?? 107.605 }};

function initMap() {
    // Buat peta
    map = L.map('mapLocation').setView([defaultLat, defaultLng], 16);

    // Tambahkan tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Tambahkan marker yang bisa diseret
    marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

    // Event saat marker diseret
    marker.on('dragend', function(e) {
        const position = e.target.getLatLng();
        updateCoordinates(position.lat, position.lng);
    });

    // Event saat peta diklik
    map.on('click', function(e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
}

// Update koordinat di input field
function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
    marker.setLatLng([lat, lng]);
}

// Dapatkan lokasi user saat ini (GPS Browser)
function getCurrentLocation() {
    if (navigator.geolocation) {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            updateCoordinates(lat, lng);
            map.setView([lat, lng], 18);
            btn.innerHTML = originalHtml;
        }, function(error) {
            alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan di browser Anda.');
            btn.innerHTML = originalHtml;
        });
    } else {
        alert('Browser Anda tidak mendukung fitur geolocation.');
    }
}

// Cari lokasi berdasarkan nama alamat (Geocoding)
function searchLocation() {
    const address = prompt('Masukkan alamat atau nama tempat (contoh: "Unpar Bandung"):');
    if (address) {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    updateCoordinates(lat, lng);
                    map.setView([lat, lng], 18);
                } else {
                    alert('Lokasi tidak ditemukan. Coba gunakan kata kunci yang lebih spesifik.');
                }
                btn.innerHTML = originalHtml;
            })
            .catch(error => {
                alert('Terjadi kesalahan saat mencari lokasi.');
                btn.innerHTML = originalHtml;
            });
    }
}

// Inisialisasi peta saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush
