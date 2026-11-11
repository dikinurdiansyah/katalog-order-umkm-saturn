@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->order_ref)

@section('content')

<style>
    .order-header {
        background: linear-gradient(135deg, #3d2817 0%, #5c3d2e 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .timeline-stage { text-align: center; position: relative; flex: 1; }
    .timeline-stage::before {
        content: ''; position: absolute; top: 20px; left: 50%; right: -50%;
        height: 2px; background: var(--border-color); z-index: 0;
    }
    .timeline-stage:last-child::before { display: none; }
    .timeline-stage.completed::before { background: var(--green-stock); }
    .stage-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px; position: relative; z-index: 1;
        background: var(--cream-dark); color: var(--text-muted);
    }
    .timeline-stage.active .stage-icon {
        background: var(--orange-fav); color: white;
        animation: pulse 2s infinite;
    }
    .timeline-stage.completed .stage-icon {
        background: var(--green-stock); color: white;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(231, 111, 81, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(231, 111, 81, 0); }
    }
    .audit-log { border-left: 2px solid var(--border-color); padding-left: 20px; margin-left: 10px; }
    .audit-item { position: relative; margin-bottom: 24px; }
    .audit-item::before {
        content: ''; position: absolute; left: -26px; top: 4px;
        width: 10px; height: 10px; border-radius: 50%; background: var(--cream-dark);
    }
    .audit-item.completed::before { background: var(--green-stock); }

    @media print {
        body * { visibility: hidden; }
        #area-cetak-struk, #area-cetak-struk * { visibility: visible; }
        #area-cetak-struk {
            position: absolute; left: 0; top: 0; width: 80mm;
            font-family: 'Courier New', Courier, monospace; font-size: 12px;
            color: #000; background: #fff; padding: 5mm; margin: 0; line-height: 1.4;
        }
        @page { margin: 0; size: 80mm auto; }
        .struk-line {
            border-top: 1px dashed #000 !important; margin: 8px 0;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .struk-text-right { text-align: right; }
        .struk-text-center { text-align: center; }
        .struk-bold { font-weight: bold; }
    }
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb font-mono" style="font-size: 0.75rem;">
        <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}" class="text-decoration-none">← KEMBALI KE DAFTAR ORDER</a></li>
        <li class="breadcrumb-item active">DETAIL PESANAN #{{ $order->order_ref }}</li>
    </ol>
</nav>

<div class="order-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-2" style="font-size: 1.5rem;">Pesanan #{{ $order->order_ref }}</h2>
            <div class="d-flex gap-2 mb-2 flex-wrap">
                <span class="badge" style="background: var(--orange-fav); color: white;">{{ $order->fulfillment_method === 'PICKUP' ? 'PICKUP' : 'INSTANT COURIER' }}</span>
                <span class="badge" style="background: rgba(255,255,255,0.2);">Batch Pengukuhan #04</span>
            </div>
            <div class="font-mono" style="font-size: 0.75rem; opacity: 0.9;">
                SUMBER: WEB TOKO PUBLIK<br>
                DIBUAT: {{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                <div class="font-mono" style="font-size: 0.7rem; opacity: 0.8; margin-bottom: 4px;">METODE PEMBAYARAN</div>
                <div class="fw-bold mb-2">{{ str_replace('_', ' ', $order->payment_method) }} {{ $order->payment_provider ? '(' . $order->payment_provider . ')' : '' }}</div>
                <span class="badge" style="background: {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : '#e76f51' }}; color: white; font-size: 0.7rem;">
                    {{ $order->payment?->payment_status === 'VERIFIED' ? '✅ LUNAS' : ($order->payment ? '⏳ MENUNGGU VERIFIKASI' : '📤 BELUM UPLOAD BUKTI') }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">PROSES PESANAN AKTIF: TAHAP {{ $currentStage }} DARI 5</h5>
            <small class="text-muted font-mono">KLOTER PENGUKUSAN #4 • DAPUR PUSAT SUKAJADI</small>
        </div>

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="d-flex gap-2 align-items-center">
            @csrf
            <select name="status" class="form-select form-select-sm" style="width: 280px;">
                <option value="MENUNGGU_KONFIRMASI" {{ $order->status == 'MENUNGGU_KONFIRMASI' ? 'selected' : '' }}>1. Menunggu Konfirmasi</option>
                <option value="DIPROSES" {{ $order->status == 'DIPROSES' ? 'selected' : '' }}>2 & 3. Diproses / Siap Dikirim</option>
                <option value="DIKIRIM" {{ $order->status == 'DIKIRIM' ? 'selected' : '' }}>4. Dalam Pengantaran</option>
                <option value="SELESAI" {{ $order->status == 'SELESAI' ? 'selected' : '' }}>5. Selesai (Pesanan Diterima)</option>
                <option value="DIBATALKAN" {{ $order->status == 'DIBATALKAN' ? 'selected' : '' }}>6. Dibatalkan</option>
            </select>
            <button type="submit" class="btn" style="background: var(--brown-dark); color: white;">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>

    <div class="d-flex justify-content-between position-relative px-2">
        @php
            $nowWIB = \Carbon\Carbon::now()->timezone('Asia/Jakarta');
            $createdWIB = \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Jakarta');
            $updatedWIB = \Carbon\Carbon::parse($order->updated_at)->timezone('Asia/Jakarta');

            $timeKonfirmasi = $createdWIB;
            $timeDikukus = $createdWIB->copy()->addMinutes(15);
            $timeSiapKirim = $createdWIB->copy()->addMinutes(30);
            $timePengantaran = $createdWIB->copy()->addMinutes(45);
            $timeSelesai = $createdWIB->copy()->addMinutes(60);

            $stages = [
                ['num' => 1, 'title' => 'KONFIRMASI', 'time' => $timeKonfirmasi->format('H:i') . ' WIB', 'sub' => 'Terkonfirmasi', 'icon' => 'check'],
                ['num' => 2, 'title' => 'SEDANG DIKUKUS', 'time' => $timeDikukus->format('H:i') . ' WIB', 'sub' => 'Kloter #4', 'icon' => 'cup-hot'],
                ['num' => 3, 'title' => 'SIAP DIKIRIM', 'time' => $timeSiapKirim->format('H:i') . ' WIB', 'sub' => 'Pengepakan Mika', 'icon' => 'box'],
                ['num' => 4, 'title' => 'PENGANTARAN', 'time' => $timePengantaran->format('H:i') . ' WIB', 'sub' => 'Dalam Perjalanan', 'icon' => 'truck'],
                ['num' => 5, 'title' => 'SELESAI', 'time' => $timeSelesai->format('H:i') . ' WIB', 'sub' => 'Verifikasi Pembayaran', 'icon' => 'check-circle'],
            ];
        @endphp

        @foreach($stages as $stage)
            @php
                $isCompleted = ($currentStage > $stage['num']) || ($currentStage == $stage['num'] && $order->status === 'SELESAI');
                $isActive = ($currentStage == $stage['num'] && $order->status !== 'SELESAI');
            @endphp
            <div class="timeline-stage {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                <div class="stage-icon">
                    @if($isCompleted) <i class="bi bi-check-lg"></i>
                    @elseif($isActive) <i class="bi bi-{{ $stage['icon'] }}-fill"></i>
                    @else <i class="bi bi-{{ $stage['icon'] }}"></i>
                    @endif
                </div>
                <div class="fw-bold" style="font-size: 0.75rem; color: var(--brown-dark);">{{ $stage['num'] }}. {{ $stage['title'] }}</div>
                <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted);">{{ $stage['time'] }}</div>
                <div style="font-size: 0.65rem; color: {{ $isActive ? 'var(--orange-fav)' : 'var(--text-muted)' }}; font-weight: 600;">{{ $stage['sub'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card mb-4">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-person-fill"></i> Informasi Pemesan</div>
                <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">CUST-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h4 class="fw-bold mb-2" style="font-size: 1.3rem;">{{ $order->customer_name }}</h4>
            <div class="mb-2">
                <i class="bi bi-telephone-fill text-muted me-2"></i>
                <span class="fw-bold">{{ $order->customer_phone }}</span>
                <span class="badge ms-2" style="background: var(--green-bg); color: var(--green-stock); font-size: 0.65rem;">Terverifikasi</span>
            </div>
            <div class="mb-3">
                <i class="bi bi-envelope-fill text-muted me-2"></i>
                <span>{{ $order->customer_email ?? 'N/A' }}</span>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}?text=Halo%20{{ urlencode($order->customer_name) }},%20terkait%20order%20{{ $order->order_ref }}"
               target="_blank" class="btn w-100" style="background: var(--brown-dark); color: white;">
                <i class="bi bi-chat-square-text-fill"></i> Chat WhatsApp Pelanggan
            </a>
        </div>

        <div class="admin-card">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-geo-alt-fill"></i> Alamat & Rute Pengantaran</div>
            </div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">TUJUAN PENGIRIMAN</div>
                    <div class="fw-bold mt-1" style="font-size: 0.9rem;">{{ $order->shipping_address ?? 'Alamat tidak tersedia' }}</div>
                </div>
                @if($order->fulfillment_method !== 'PICKUP')
                <span class="badge" style="background: var(--cream); color: var(--brown-dark); font-size: 0.7rem;">Est. 3.2 km</span>
                @endif
            </div>

            @if($order->fulfillment_method !== 'PICKUP')
            <div class="mb-3" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                <div id="orderMap" style="height: 250px; width: 100%;"></div>
            </div>
            <div class="d-flex align-items-start gap-2 p-3 border rounded" style="background: var(--cream-light);">
                <i class="bi bi-geo-alt-fill" style="font-size: 1.2rem; color: var(--brown-dark); margin-top: 2px;"></i>
                <div>
                    <div class="fw-bold" style="font-size: 0.9rem; color: var(--brown-dark);">Detail Rute</div>
                    <small class="text-muted"> Dari: Dapur Th Sant</small><br>
                    <small class="text-muted">🎯 Ke: {{ $order->shipping_address ?? 'Bandung, Jawa Barat' }}</small>
                </div>
            </div>
            @else
            <div class="p-3 rounded" style="background: var(--cream-light);">
                <small class="text-muted font-mono">📍 Pelanggan akan mengambil sendiri di outlet</small>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card mb-4">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-cup-hot-fill"></i> Rincian Menu</div>
                <span class="badge" style="background: var(--cream); color: var(--brown-dark);">{{ $order->orderItems->count() }} Item • {{ $order->orderItems->sum('quantity') }} Porsi</span>
            </div>

            @foreach($order->orderItems as $item)
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                <div style="width: 80px; height: 80px; background: var(--cream-dark); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($item->product && $item->product->image_url)
                        <img src="{{ asset($item->product->image_url) }}" class="w-100 h-100" style="object-fit: cover; border-radius: 8px;">
                    @else
                        <i class="bi bi-image" style="font-size: 2rem; color: var(--text-muted);"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="fw-bold" style="font-size: 1rem;">{{ $item->product ? $item->product->name : 'Produk Dihapus' }}</div>
                        <div class="fw-bold" style="font-size: 1.1rem; color: var(--brown-dark);">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                    <small class="text-muted">Kuantitas: <strong>{{ $item->quantity }} Porsi</strong> @ Rp {{ number_format($item->price_at_purchase ?? ($item->product ? $item->product->price : 0), 0, ',', '.') }}</small>
                </div>
            </div>
            @endforeach
        </div>

        <div class="admin-card mb-4">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-receipt"></i> Rincian Pembayaran</div>
                <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">INV-{{ strtoupper($order->order_ref) }}</span>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Subtotal Produk</span>
                <span class="fw-bold">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
            </div>
            <div class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Biaya Kemasan</span>
                <span class="fw-bold">Rp 2.000</span>
            </div>
            @if($order->fulfillment_method !== 'PICKUP')
            <div class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Ongkos Kirim</span>
                <span class="fw-bold">Rp {{ number_format($order->total_amount - $order->subtotal_amount - $order->discount_amount - 2000, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0)
            <div class="mb-2 d-flex justify-content-between text-danger">
                <span><i class="bi bi-tag-fill"></i> Diskon Promo</span>
                <span class="fw-bold">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold" style="font-size: 1.1rem;">TOTAL</div>
                <div class="fw-bold" style="font-size: 1.8rem; color: var(--brown-dark);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- ✅ BAGIAN INI YANG DIPERBAIKI SECARA TOTAL (SUPER ROBUST) -->
        <div class="admin-card mb-4" style="border-left: 4px solid {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : 'var(--orange-fav)' }};">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-bank"></i> BUKTI PEMBAYARAN ({{ $order->payment_method }})</div>
                <span class="badge" style="background: {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : ($order->payment?->payment_status === 'REJECTED' ? '#c53030' : 'var(--orange-fav)') }}; color: white;">
                    @if($order->payment?->payment_status === 'VERIFIED') ✅ LUNAS / TERVERIFIKASI
                    @elseif($order->payment?->payment_status === 'REJECTED') ❌ DITOLAK
                    @elseif($order->payment) ⏳ MENUNGGU VERIFIKASI
                    @else 📤 BELUM UPLOAD
                    @endif
                </span>
            </div>

            @if($order->payment && $order->payment->receipt_image_path)
                @php
                    $rawPath = $order->payment->receipt_image_path;
                    // 1. Bersihkan prefix 'storage/' agar tidak dobel (storage/storage/...)
                    $cleanPath = str_replace('storage/', '', $rawPath);

                    // 2. Asumsi standar Laravel (menggunakan symlink public/storage)
                    $imageUrl = asset('storage/' . $cleanPath);

                    // 3. FALLBACK: Jika file ternyata tersimpan langsung di folder public/ (bukan di storage/app/public)
                    if (!file_exists(public_path('storage/' . $cleanPath)) && file_exists(public_path($cleanPath))) {
                        $imageUrl = asset($cleanPath);
                    }
                @endphp

                <div class="text-center mb-3 p-3" style="background: var(--cream-light); border-radius: 8px;">
                    <img src="{{ $imageUrl }}"
                         class="img-fluid rounded"
                         style="max-height: 350px; max-width: 100%;"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/350x200/ffebee/e53935?text=GAMBAR+TIDAK+DITEMUKAN';">

                    <div class="mt-2">
                        <a href="{{ $imageUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Ukuran Penuh
                        </a>
                    </div>
                    <div class="mt-2 font-mono" style="font-size: 0.75rem; color: var(--text-muted);">
                        Jumlah yang Ditransfer: <strong>Rp {{ number_format($order->payment->amount_paid ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <!-- Debug info kecil jika masih error, bisa dihapus nanti kalau sudah beres -->
                    <div class="mt-1" style="font-size: 0.65rem; color: #999;">
                        Path DB: {{ $rawPath }}
                    </div>
                </div>
            @else
                <div class="text-center py-4" style="background: var(--cream-light); border-radius: 8px; border: 2px dashed var(--border-color);">
                    <i class="bi bi-hourglass-split text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-2 text-muted">Pelanggan Belum Upload Bukti Pembayaran</h6>
                    <small class="text-muted">Menunggu pelanggan mengunggah bukti transfer.</small>
                </div>
            @endif

            @if($order->payment && $order->payment->payment_status === 'PENDING_REVIEW')
            <div class="row g-2 mt-3">
                <div class="col-12 mb-2">
                    <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 700;">CATATAN (Jika Ditolak)</label>
                    <textarea id="rejectionNote" class="form-control form-control-sm" rows="2" placeholder="Contoh: Nominal tidak sesuai..."></textarea>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalTolak">
                        <i class="bi bi-x-circle"></i> TOLAK PEMBAYARAN
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn w-100" style="background: var(--green-stock); color: white;" data-bs-toggle="modal" data-bs-target="#modalTerima">
                        <i class="bi bi-check-circle"></i> TERIMA & PROSES ORDER
                    </button>
                </div>
            </div>
            @elseif($order->payment && $order->payment->payment_status === 'VERIFIED')
            <div class="p-3 rounded mt-3" style="background: var(--green-bg);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill" style="color: var(--green-stock); font-size: 1.5rem;"></i>
                    <div>
                        <div class="fw-bold" style="color: var(--green-stock);">Pembayaran Terverifikasi</div>
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                            Diverifikasi pada: {{ \Carbon\Carbon::parse($order->payment->verified_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                        </small>
                    </div>
                </div>
            </div>
            @elseif($order->payment && $order->payment->payment_status === 'REJECTED')
            <div class="p-3 rounded mt-3" style="background: #fde8e8;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill" style="color: #c53030; font-size: 1.5rem;"></i>
                    <div>
                        <div class="fw-bold" style="color: #c53030;">Pembayaran Ditolak</div>
                        @if($order->payment->admin_notes)
                            <small class="text-muted">Catatan: {{ $order->payment->admin_notes }}</small>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-header-custom">
        <div class="card-title-custom"><i class="bi bi-clock-history"></i> Riwayat Aktivitas Pesanan</div>
        <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">Update Otomatis Realtime</span>
    </div>

    <div class="audit-log">
        <div class="audit-item completed">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="fw-bold" style="font-size: 0.9rem;">Status Diperbarui → <span class="text-success">{{ $order->status }}</span></div>
                <small class="text-muted font-mono">{{ $updatedWIB->format('d/m/Y H:i:s') }} WIB</small>
            </div>
            <small class="text-muted">Diubah oleh: {{ auth()->user()?->name ?? 'Admin' }} (Admin Dapur).</small>
        </div>

        <div class="audit-item completed">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="fw-bold" style="font-size: 0.9rem;">Pesanan Diterima & Dikonfirmasi Dapur</div>
                <small class="text-muted font-mono">{{ $createdWIB->format('d/m/Y H:i:s') }} WIB</small>
            </div>
            <small class="text-muted">Pesanan masuk melalui web checkout. Metode: {{ $order->payment_method }}.</small>
        </div>

        <div class="audit-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="fw-bold" style="font-size: 0.9rem;">Pesanan Dibuat oleh Pelanggan</div>
                <small class="text-muted font-mono">{{ $createdWIB->format('d/m/Y H:i:s') }} WIB</small>
            </div>
            <small class="text-muted">Checkout oleh {{ $order->customer_name }} ({{ $order->customer_phone }}).</small>
        </div>
    </div>
</div>

<div class="admin-card" style="background: var(--brown-dark); color: white;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 40px; height: 40px; background: var(--orange-fav); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div>
                    <div class="fw-bold">Tindakan Cepat #{{ $order->order_ref }}</div>
                    <small style="opacity: 0.8; font-size: 0.8rem;">Status saat ini: {{ $order->status }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}?text=Halo%20{{ urlencode($order->customer_name) }},%20status%20order%20{{ $order->order_ref }}%20sudah%20{{ urlencode($order->status) }}"
                   target="_blank" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-whatsapp"></i> Kirim Update via WA
                </a>
                <button class="btn btn-outline-light btn-sm" onclick="cetakStruk()">
                    <i class="bi bi-printer-fill"></i> Cetak Struk Thermal
                </button>
                @if($order->status !== 'SELESAI' && $order->status !== 'DIBATALKAN')
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ $order->status === 'MENUNGGU_KONFIRMASI' ? 'DIPROSES' : ($order->status === 'DIPROSES' ? 'DIKIRIM' : 'SELESAI') }}">
                    <button type="submit" class="btn btn-sm" style="background: var(--orange-fav); color: white;">
                        <i class="bi bi-check-lg"></i> Tandai {{ $order->status === 'MENUNGGU_KONFIRMASI' ? 'Diproses' : ($order->status === 'DIPROSES' ? 'Siap Kirim' : 'Selesai') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4 font-mono" style="font-size: 0.7rem; color: var(--text-muted);">
    BLUEPRINT: ADMIN_SPEC_v1.0 // DAPUR TH SANT • {{ $nowWIB->format('d/m/Y H:i') }} WIB
</div>

<!-- ========================================== -->
<!-- ✅ AREA CETAK STRUK DENGAN WAKTU REALTIME -->
<!-- ========================================== -->
<div id="area-cetak-struk" style="display: none;">
    <div style="text-align: center; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; width: 80mm; padding: 5mm;">

        @php
            $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
            $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
            $receiptLogo = $data['logo_url'] ?? null;
        @endphp

        @if($receiptLogo)
            <div style="margin-bottom: 8px;">
                <img src="{{ asset($receiptLogo) }}" alt="Logo" style="max-width: 35mm; height: auto; display: block; margin: 0 auto;">
            </div>
        @else
            <div style="font-size: 28px; margin-bottom: 5px;">🥟</div>
        @endif

        <div style="font-size: 16px; font-weight: bold; margin-bottom: 3px;">DAPUR TH SANT</div>
        <div style="font-size: 11px; margin-bottom: 2px;">Dimsum Kukus & Goreng Fresh</div>
        <div style="font-size: 10px; margin-bottom: 2px;">Jl. Ciumbuleuit No. 88, Bandung</div>
        <div style="font-size: 10px; margin-bottom: 8px;">WA: 0812-3456-7890</div>

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>No. Order:</span>
            <span class="struk-bold">{{ $order->order_ref }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Tanggal:</span>
            <span id="struk-tanggal">{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Pelanggan:</span>
            <span>{{ Str::limit($order->customer_name, 15) }}</span>
        </div>

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

        @foreach($order->orderItems as $item)
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>{{ $item->quantity }}x {{ Str::limit($item->product ? $item->product->name : 'Produk', 15) }}</span>
        </div>
        <div class="struk-text-right" style="font-size: 11px; margin-top: -4px; margin-bottom: 4px;">
            @ Rp {{ number_format($item->price_at_purchase ?? ($item->product ? $item->product->price : 0), 0, ',', '.') }}
        </div>
        @endforeach

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Subtotal</span>
            <span>Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Diskon</span>
            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Kemasan</span>
            <span>Rp 2.000</span>
        </div>
        @if($order->fulfillment_method !== 'PICKUP')
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Ongkir</span>
            <span>Rp {{ number_format($order->total_amount - $order->subtotal_amount - $order->discount_amount - 2000, 0, ',', '.') }}</span>
        </div>
        @endif

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

        <div style="display: flex; justify-content: space-between; font-size: 13px;" class="struk-bold">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Metode:</span>
            <span class="struk-bold">{{ str_replace('_', ' ', $order->payment_method) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 11px;">
            <span>Status:</span>
            <span class="struk-bold">{{ $order->status }}</span>
        </div>

        @if($order->shipping_address && $order->fulfillment_method !== 'PICKUP')
        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>
        <div class="struk-bold" style="font-size: 11px;">ALAMAT PENGIRIMAN:</div>
        <div style="font-size: 10px;">{{ $order->shipping_address }}</div>
        @endif

        @if($order->notes)
        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>
        <div class="struk-bold" style="font-size: 11px;">CATATAN DAPUR:</div>
        <div style="font-size: 10px;">{{ $order->notes }}</div>
        @endif

        <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>
        <div class="struk-text-center" style="margin-top: 10px; font-size: 10px;">
            <div>Terima kasih atas pesanan Anda!</div>
            <div style="margin-top: 5px;">Barang yang sudah dibeli tidak dapat ditukar.</div>
            <br>
            <div style="font-size: 9px;">Powered by Dapur Th Sant System</div>
        </div>
    </div>
</div>

@if($order->payment)
<div class="modal fade" id="modalTerima" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <strong>menerima</strong> pembayaran ini?</p>
                <div class="alert alert-info" style="background: var(--cream-light); border: 1px solid var(--border-color); border-radius: 12px;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Order akan otomatis berubah status menjadi <strong>DIPROSES</strong>.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <form method="POST" action="{{ route('admin.payments.verify', $order->payment) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="payment_status" value="VERIFIED">
                    <input type="hidden" name="admin_notes" value="Pembayaran diverifikasi oleh {{ auth()->user()->name }}">
                    <button type="submit" class="btn btn-success" style="background: var(--green-stock); border: none; border-radius: 10px;">
                        <i class="bi bi-check-circle-fill me-2"></i>Ya, Terima & Proses
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <strong>menolak</strong> pembayaran ini?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Alasan Penolakan (Wajib)</label>
                    <textarea class="form-control" id="modalRejectionNote" rows="3" placeholder="Contoh: Nominal tidak sesuai..." required></textarea>
                </div>
                <div class="alert alert-warning" style="background: var(--warning-bg); border: 1px solid #b45309; border-radius: 12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Stok produk akan otomatis dikembalikan.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <form method="POST" action="{{ route('admin.payments.verify', $order->payment) }}" class="d-inline" id="formTolak">
                    @csrf
                    <input type="hidden" name="payment_status" value="REJECTED">
                    <input type="hidden" name="admin_notes" id="modalRejectionNoteInput">
                    <button type="submit" class="btn btn-danger" style="border-radius: 10px;">
                        <i class="bi bi-x-circle-fill me-2"></i>Ya, Tolak Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    function cetakStruk() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const formattedDate = `${day}/${month}/${year} ${hours}:${minutes}`;

        const tanggalElement = document.getElementById('struk-tanggal');
        if (tanggalElement) {
            tanggalElement.textContent = formattedDate;
        }

        const strukArea = document.getElementById('area-cetak-struk');
        strukArea.style.display = 'block';
        window.print();
        setTimeout(() => {
            strukArea.style.display = 'none';
        }, 1000);
    }

    @if($order->fulfillment_method !== 'PICKUP')
    document.addEventListener('DOMContentLoaded', function() {
        // ✅ AMBIL KOORDINAT DARI DATABASE AGAR SINKRON DENGAN PENGATURAN TOKO
        const dapurLat = {{ $data['latitude'] ?? -6.8635 }};
        const dapurLng = {{ $data['longitude'] ?? 107.6105 }};

        const customerLat = {{ $order->shipping_latitude ?? -6.9035 }};
        const customerLng = {{ $order->shipping_longitude ?? 107.6195 }};

        const map = L.map('orderMap').setView([dapurLat, dapurLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([dapurLat, dapurLng]).addTo(map)
            .bindPopup('<b>🏠 Dapur Th Sant</b><br>{{ $data["address"] ?? "Jl. Ciumbuleuit No. 88, Bandung" }}');

        L.marker([customerLat, customerLng]).addTo(map)
            .bindPopup('<b>🎯 Tujuan Pengiriman</b><br>{{ addslashes($order->shipping_address ?? "Bandung") }}');

        const routingControl = L.Routing.control({
            waypoints: [
                L.latLng(dapurLat, dapurLng),
                L.latLng(customerLat, customerLng)
            ],
            routeWhileDragging: false,
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: true,
            show: false,
            lineOptions: {
                styles: [{color: '#e76f51', opacity: 0.8, weight: 5}]
            },
            createMarker: function() { return null; }
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            const summary = e.routes[0].summary;
            const distanceKm = (summary.totalDistance / 1000).toFixed(2);
            const timeMin = Math.round(summary.totalTime / 60);
            const badge = document.querySelector('.badge[style*="Est."]');
            if (badge) {
                badge.textContent = `Est. ${distanceKm} km • ${timeMin} menit`;
            }
        });
    });
    @endif

    var modalTolak = document.getElementById('modalTolak');
    if (modalTolak) {
        modalTolak.addEventListener('show.bs.modal', function () {
            document.getElementById('modalRejectionNote').focus();
        });
    }

    var formTolak = document.getElementById('formTolak');
    if (formTolak) {
        formTolak.addEventListener('submit', function(e) {
            var note = document.getElementById('modalRejectionNote').value;
            if(!note.trim()) {
                e.preventDefault();
                alert('Harap isi alasan penolakan terlebih dahulu!');
                return;
            }
            document.getElementById('modalRejectionNoteInput').value = note;
        });
    }
</script>
@endpush
