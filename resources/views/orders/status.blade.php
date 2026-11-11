@extends('layouts.app')

@section('title', 'Status Order - Tracking')

@section('navbar')
<div class="top-navbar">
    <a href="{{ route('catalog.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="page-title">STATUS ORDER</span>
    <div class="user-icon">
        <i class="bi bi-shield-check text-success"></i>
    </div>
</div>
@endsection

@section('content')
@php
    // ✅ Mengambil nomor WhatsApp dinamis dari pengaturan Admin
    $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
    $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
    $adminWa = preg_replace('/[^0-9]/', '', $data['whatsapp'] ?? '081234567890');
    if (str_starts_with($adminWa, '0')) {
        $adminWa = '62' . substr($adminWa, 1);
    }
@endphp

{{-- ✅ PENGECEKAN KEAMANAN: Jika Order Tidak Ditemukan --}}
@if(!$order)
    <div class="container py-5 px-3">
        <div class="mx-auto text-center" style="max-width: 500px;">
            <div style="width: 100px; height: 100px; background: var(--cream-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-muted);"></i>
            </div>
            <h4 class="mt-3 fw-bold text-brown">Pesanan Tidak Ditemukan</h4>
            <p class="text-muted mb-4">Silakan masukkan kode order yang valid atau buat pesanan baru.</p>

            <form action="{{ route('orders.status') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 justify-content-center mb-4">
                <input type="text" name="ref" class="form-control-warm font-mono text-uppercase" placeholder="ORD-XXXXX" style="max-width: 300px;" required>
                <button type="submit" class="btn-brown px-4 font-mono fw-bold py-2">[CEK]</button>
            </form>

            <a href="{{ route('catalog.index') }}" class="btn-brown-outline px-4 py-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

@else
{{-- ✅ Jika Order Ditemukan, Tampilkan Desain Lengkap --}}
@php
    $stageMap = [
        'MENUNGGU_KONFIRMASI' => 1,
        'DIPROSES' => 2,
        'DIKIRIM' => 3,
        'SELESAI' => 5,
        'DIBATALKAN' => 0
    ];

    $currentStage = $stageMap[$order->status] ?? 1;
    $totalStages = 5;
    $isCancelled = ($order->status === 'DIBATALKAN');
@endphp

<!-- ✅ WRAPPER RESPONSIF -->
<div class="container py-4 px-3 px-md-4">
    <div class="mx-auto" style="max-width: 750px;">

        <!-- Lacak Pesanan Mandiri -->
        <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-brown font-mono" style="font-size: 0.85rem; letter-spacing: 1px;">LACAK PESANAN MANDIRI</h5>
                <span class="badge-stock d-flex align-items-center gap-1" style="background: var(--green-bg); color: var(--green-stock); padding: 4px 10px; border-radius: 50px; font-size: 0.75rem;">
                    <span style="width: 6px; height: 6px; background: var(--green-stock); border-radius: 50%;"></span>
                    LIVE TRACKING
                </span>
            </div>

            <form action="{{ route('orders.status') }}" method="GET">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <input type="text" name="ref" class="form-control-warm flex-grow-1 font-mono text-uppercase"
                           placeholder="ORD-DTS-YYYYMMDD-XXXX"
                           value="{{ $order->order_ref }}">
                    <button type="submit" class="btn-brown px-4 font-mono fw-bold py-2" style="border-radius: 10px;">[CEK]</button>
                </div>
            </form>
            <small class="text-muted font-mono mt-2 d-block" style="font-size: 0.7rem;">
                Format: ORD-DTS-YYYYMMDD-XXXX
            </small>
        </div>

        <!-- ID Referensi -->
        <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-mono fw-bold" style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 1px;">ID REFERENSI</span>
                @if(!$isCancelled)
                    <span class="badge-status" style="padding: 4px 10px; border-radius: 50px;">TAHAP {{ $currentStage }} DARI {{ $totalStages }}</span>
                @else
                    <span class="badge-status" style="background: #fde8e8; color: #c53030; padding: 4px 10px; border-radius: 50px;">DIBATALKAN</span>
                @endif
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="font-mono fw-bold text-brown" style="font-size: 1.2rem;">#{{ $order->order_ref }}</div>
                <small class="text-muted font-mono" style="font-size: 0.85rem;">
                    {{ $order->created_at->format('d M Y, H:i') }} WIB
                </small>
            </div>
        </div>

        <!-- Status Banner Dinamis -->
        @php
            $bannerText = match($order->status) {
                'MENUNGGU_KONFIRMASI' => 'MENUNGGU KONFIRMASI KASIR / TAHAP 1',
                'DIPROSES' => 'SEDANG DIKUKUS FRESH / TAHAP 2',
                'DIKIRIM' => 'SEDANG DALAM PENGANTARAN / TAHAP 3',
                'SELESAI' => 'PESANAN TELAH SELESAI / TAHAP 5',
                'DIBATALKAN' => 'PESANAN DIBATALKAN',
                default => 'MEMPROSES PESANAN...'
            };
            $showSpinner = in_array($order->status, ['MENUNGGU_KONFIRMASI', 'DIPROSES', 'DIKIRIM']);
        @endphp

        <div class="card-warm p-3 p-md-4 mb-4 d-flex align-items-center gap-3" style="border-radius: 16px; background: var(--cream); border-left: 4px solid {{ $isCancelled ? '#c53030' : 'var(--brown-dark)' }};">
            @if($showSpinner)
                <div class="spinner-border spinner-border-sm text-brown" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            @elseif($isCancelled)
                <i class="bi bi-x-circle-fill" style="font-size: 1.5rem; color: #c53030;"></i>
            @else
                <i class="bi bi-check-circle-fill" style="font-size: 1.5rem; color: var(--green-stock);"></i>
            @endif

            <div class="fw-bold text-brown" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                {{ $bannerText }}
            </div>
        </div>

        <!-- Estimasi Tiba -->
        @if(!$isCancelled && $order->status !== 'SELESAI')
        <div class="card-warm p-3 p-md-4 mb-4 d-flex align-items-center gap-3" style="border-radius: 16px;">
            <div style="width: 70px; height: 70px; background: var(--cream-dark); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                <i class="bi bi-stopwatch" style="font-size: 2rem; color: var(--brown-dark);"></i>
            </div>
            <div class="flex-grow-1">
                <div class="section-label mb-1">ESTIMASI TIBA</div>
                <div class="fw-bold text-brown font-mono" style="font-size: 1.5rem;">
                    @if($order->fulfillment_method === 'PICKUP')
                        SIAP AMBIL
                    @else
                        15-25 MENIT
                    @endif
                </div>
                <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-clock"></i>
                    {{ $order->fulfillment_method === 'PICKUP' ? 'Pesanan sedang disiapkan di dapur.' : '~20 Menit Lagi Menuju Lokasi' }}
                </small>
            </div>
        </div>
        @endif

        <!-- Log Status Perjalanan -->
        <div class="mb-4">
            <h5 class="fw-bold text-brown mb-3 font-mono" style="font-size: 0.85rem; letter-spacing: 1px;">
                LOG STATUS PERJALANAN PESANAN
            </h5>

            <div class="position-relative ps-4">
                <div style="position: absolute; left: 19px; top: 20px; bottom: 20px; width: 2px; background: var(--border-color);"></div>

                @php
                    $stages = [
                        1 => ['title' => '1. Menunggu Konfirmasi', 'desc' => 'Pesanan masuk dan berhasil diverifikasi oleh kasir outlet.', 'time' => $order->created_at->format('H:i') . ' WIB'],
                        2 => ['title' => '2. Diproses / Dikukus Fresh', 'desc' => 'Sedang dikukus segar di klakat bambu dapur Sukajadi.', 'time' => $order->updated_at->format('H:i') . ' WIB'],
                        3 => ['title' => '3. Menunggu Kurir / Siap Kirim', 'desc' => 'Pengepakan thermal bag dan serah terima ke driver.', 'time' => 'Estimasi'],
                        4 => ['title' => '4. Dalam Pengantaran ke Rumah', 'desc' => 'Kurir meluncur ke titik dropoff pemesan.', 'time' => 'Estimasi'],
                        5 => ['title' => '5. Pesanan Selesai Diterima', 'desc' => 'Pembayaran dilunaskan dan pesanan dinikmati.', 'time' => 'Estimasi']
                    ];
                @endphp

                @foreach($stages as $index => $stage)
                    @php
                        $isActive = ($currentStage == $index);
                        $isCompleted = ($currentStage > $index) || ($currentStage == $index && $order->status === 'SELESAI');
                        $isPending = ($currentStage < $index);

                        if($isCancelled) {
                            $bgColor = 'var(--cream-dark)';
                            $borderColor = 'var(--border-color)';
                            $iconHtml = '<span class="fw-bold text-muted" style="font-size: 0.9rem;">' . $index . '</span>';
                            $textClass = 'text-muted';
                        } elseif ($isCompleted) {
                            $bgColor = 'var(--green-stock)';
                            $borderColor = 'var(--green-stock)';
                            $iconHtml = '<i class="bi bi-check-lg text-white" style="font-size: 1.2rem;"></i>';
                            $textClass = 'text-brown';
                        } elseif ($isActive) {
                            $bgColor = 'var(--brown-dark)';
                            $borderColor = 'var(--brown-dark)';
                            $iconHtml = '<div style="width: 16px; height: 16px; border: 2px solid white; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
                            $textClass = 'text-brown';
                        } else {
                            $bgColor = 'var(--cream-dark)';
                            $borderColor = 'var(--border-color)';
                            $iconHtml = '<span class="fw-bold text-muted" style="font-size: 0.9rem;">' . $index . '</span>';
                            $textClass = 'text-muted';
                        }
                    @endphp

                    <div class="d-flex gap-3 mb-4 position-relative">
                        <div style="width: 40px; height: 40px; background: {{ $bgColor }}; border: 2px solid {{ $borderColor }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; z-index: 1; {{ $isActive && !$isCompleted ? 'animation: pulse 2s infinite;' : '' }}">
                            {!! $iconHtml !!}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start">
                                <div class="fw-bold {{ $textClass }}" style="font-size: 0.95rem;">{{ $stage['title'] }}</div>
                                <small class="text-muted font-mono" style="font-size: 0.75rem;">{{ $stage['time'] }}</small>
                            </div>

                            @if($isActive && !$isCompleted)
                                <span class="badge-stock d-inline-block mt-1" style="background: var(--brown-dark); color: white; font-size: 0.65rem; padding: 3px 8px; border-radius: 4px;">AKTIF</span>
                            @endif

                            <small class="text-muted d-block mt-1" style="font-size: 0.85rem; line-height: 1.4;">{{ $stage['desc'] }}</small>

                            @if($isActive && !$isCompleted && $index == 2)
                                <div class="card-warm p-2 mt-2" style="background: var(--cream); font-size: 0.8rem; border-radius: 8px;">
                                    <span class="fw-bold text-brown">Catatan Dapur:</span>
                                    <span class="text-muted">Suhu steam 100°C • Timer 12 menit fresh batch.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Titik Antar & Alamat -->
        <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
            @if($order->fulfillment_method === 'PICKUP')
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-brown font-mono" style="font-size: 0.85rem; letter-spacing: 1px;">LOKASI PENGAMBILAN</h5>
                    <span class="badge-stock" style="background: var(--cream-dark); color: var(--brown-dark); font-size: 0.65rem; padding: 4px 8px; border-radius: 4px;">[ SELF PICKUP ]</span>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <i class="bi bi-shop text-brown mt-1" style="font-size: 1.2rem;"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-brown" style="font-size: 0.95rem;">Dapur Th Sant - Outlet Ciumbuleuit</div>
                        <div class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                            Jl. Ciumbuleuit No. 88, Hegarmanah, Cidadap, Bandung
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="section-label">CATATAN PENGAMBILAN</div>
                    <div class="card-warm p-3" style="background: var(--cream-light); border-radius: 8px;">
                        <div class="fst-italic" style="font-size: 0.9rem; color: var(--text-dark);">
                            "{{ $order->shipping_address }}"
                        </div>
                    </div>
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-brown font-mono" style="font-size: 0.85rem; letter-spacing: 1px;">TITIK ANTAR & ALAMAT</h5>
                    <span class="badge-stock" style="background: var(--green-bg); color: var(--green-stock); font-size: 0.65rem; padding: 4px 8px; border-radius: 4px;">[ LOKASI VALID ]</span>
                </div>

                <!-- ✅ PETA GOOGLE MAPS ASLI -->
                <div class="card-warm mb-3 overflow-hidden" style="background: var(--cream-light); border: 1px solid var(--border-color); border-radius: 12px;">
                    <div style="width: 100%; height: 250px; background: #f0f0f0;">
                        @if(!empty($order->shipping_address))
                            <iframe
                                width="100%"
                                height="100%"
                                frameborder="0"
                                scrolling="no"
                                marginheight="0"
                                marginwidth="0"
                                src="https://maps.google.com/maps?q={{ urlencode($order->shipping_address) }}&output=embed"
                                style="filter: grayscale(20%) sepia(10%); border: none;">
                            </iframe>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <div class="text-center">
                                    <i class="bi bi-geo-alt" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Alamat tidak tersedia</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="p-2 text-center" style="background: white; border-top: 1px solid var(--border-color);">
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                            📍 {{ $order->shipping_address }}
                        </small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <i class="bi bi-geo-alt-fill text-brown mt-1" style="font-size: 1.2rem;"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-brown" style="font-size: 0.95rem;">Rumah (Penerima: {{ $order->customer_name }})</div>
                        <div class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                            {{ $order->shipping_address }}
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                            <strong>Patokan:</strong> Pastikan alamat lengkap dengan patokan yang jelas.
                        </small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Ringkasan Item & Pembayaran -->
        <div class="card-warm mb-4" style="border-radius: 16px; overflow: hidden;">
            <div class="p-3 p-md-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#orderSummary" style="cursor: pointer; background: var(--cream-light);">
                <div>
                    <h5 class="fw-bold mb-0 text-brown font-mono" style="font-size: 0.85rem; letter-spacing: 1px;">RINGKASAN ITEM & PEMBAYARAN</h5>
                    <small class="text-muted" style="font-size: 0.8rem;">
                        <strong>{{ $order->orderItems->count() }} Menu</strong> • Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </small>
                </div>
                <i class="bi bi-chevron-down text-brown"></i>
            </div>

            <div class="collapse" id="orderSummary">
                <div class="p-3 p-md-4">
                    @foreach($order->orderItems as $item)
                        <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px dashed var(--border-color);">
                            <div>
                                <div class="fw-bold text-brown" style="font-size: 0.9rem;">{{ $item->product->name }}</div>
                                <small class="text-muted">{{ $item->quantity }}x @ Rp {{ number_format($item->price_at_purchase ?? $item->product->price, 0, ',', '.') }}</small>
                            </div>
                            <span class="fw-bold text-brown font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between mb-1 mt-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="font-mono text-brown">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success">Diskon</span>
                            <span class="font-mono text-success">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Kemasan</span>
                        <span class="font-mono text-brown">Rp 2.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">{{ $order->fulfillment_method === 'PICKUP' ? 'Pengambilan' : 'Ongkir' }}</span>
                        <span class="font-mono text-brown">Rp {{ $order->fulfillment_method === 'PICKUP' ? '0' : '10.000' }}</span>
                    </div>

                    <div class="d-flex justify-content-between pt-2" style="border-top: 2px dashed var(--border-color);">
                        <span class="fw-bold text-brown">TOTAL</span>
                        <span class="fw-bold text-brown font-mono" style="font-size: 1.2rem;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ SECTION UPLOAD ULANG BUKTI TRANSFER (Jika Payment Ditolak) --}}
        @if(($order->payment_method === 'TRANSFER_BANK' || $order->payment_method === 'QRIS') && isset($order->payment) && $order->payment->payment_status === 'REJECTED')
            <div class="card-warm p-3 p-md-4 mb-4" style="background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 16px;">
                <h5 class="fw-bold mb-3 text-brown" style="font-size: 1rem;">
                    <i class="bi bi-upload me-2"></i> Upload Ulang Bukti Pembayaran
                </h5>

                <div class="alert alert-danger mb-3" style="border-radius: 12px;">
                    <div class="d-flex gap-2">
                        <i class="bi bi-x-circle-fill" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>Pembayaran Sebelumnya Ditolak</strong><br>
                            <small class="text-muted">Catatan Admin: <em>"{{ $order->payment->admin_notes ?? 'Tidak ada catatan' }}"</em></small><br>
                            <small class="mt-2 d-block fw-bold">Silakan upload ulang bukti transfer yang benar di bawah ini agar pesanan dapat segera diproses.</small>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('payments.guest.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="amount_paid" value="{{ $order->total_amount }}">

                    <div class="mb-3">
                        <label class="section-label">UPLOAD ULANG BUKTI TRANSFER <span class="text-danger">*</span></label>
                        <input type="file" name="receipt_image" class="form-control-warm w-100" accept="image/jpeg,image/png,image/jpg" required style="padding: 10px;">
                        <small class="text-muted" style="font-size: 0.75rem;">Format: JPG, PNG. Maksimal 2MB.</small>
                    </div>

                    <button type="submit" class="btn-brown w-100 d-flex justify-content-center align-items-center gap-2 py-3" style="border-radius: 12px; font-weight: 700;">
                        <i class="bi bi-upload"></i> KIRIM ULANG BUKTI PEMBAYARAN
                    </button>
                </form>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <a href="https://wa.me/{{ $adminWa }}?text=Halo%20{{ urlencode($data['business_name'] ?? 'Dapur Th Sant') }},%20saya%20mau%20tanya%20order%20{{ $order->order_ref }}"
                   class="btn-brown w-100 d-flex align-items-center justify-content-center gap-2 py-3" style="border-radius: 12px; text-decoration: none;">
                    <i class="bi bi-chat-square-fill"></i>
                    <span class="fw-bold font-mono">HUBUNGI WHATSAPP</span>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $data['whatsapp'] ?? '081234567890') }}"
                   class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-3" style="background: white; color: var(--brown-dark); border: 1px solid var(--border-color); border-radius: 12px; text-decoration: none;">
                    <i class="bi bi-telephone-fill"></i>
                    <span class="fw-bold font-mono">KONTAK BANTUAN</span>
                </a>
            </div>
        </div>

        <!-- Footer Spec -->
        <div class="text-center py-4 mt-3 border-top" style="border-color: var(--border-color) !important;">
            <small class="font-mono text-muted d-block" style="font-size: 0.7rem;">
                SISTEM MONITORING DAPUR STEAM REALTIME • P6 V11.0
            </small>
            <small class="font-mono text-muted d-block mt-1" style="font-size: 0.7rem;">
                REF ID: {{ $order->order_ref }} // STATUS: {{ $order->status }}
            </small>
        </div>

    </div>
</div>

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(61, 40, 23, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(61, 40, 23, 0); }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush
@endif
@endsection
